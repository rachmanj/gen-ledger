<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class AccountStatementController extends Controller
{
    public function index()
    {
        $accounts = Account::orderBy('account_number')->get();
        return view('account-statement.index', compact('accounts'));
    }

    public function generate(Request $request)
    {
        $request->validate([
            'account_id' => 'required|exists:accounts,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date'
        ]);

        $account = Account::findOrFail($request->account_id);
        
        $statementData = $this->generateStatement($account->id, $request->start_date, $request->end_date);

        if ($request->ajax()) {
            return response()->json($statementData);
        }

        return view('account-statement.statement', $statementData);
    }

    public function apiGenerate(Request $request)
    {
        // Log incoming request
        Log::info('API Generate Statement Request', [
            'request_data' => $request->all(),
            'headers' => $request->header(),
            'method' => $request->method()
        ]);

        try {
            // Validate request
            $request->validate([
                'account_number' => 'required|string',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
                'project_code' => 'nullable|string'
            ]);

            // Find account by account number
            $account = Account::where('account_number', $request->account_number)->first();
            
            if (!$account) {
                return response()->json([
                    'success' => false,
                    'message' => 'Account not found',
                ], 404);
            }

            $startDate = $request->start_date;
            $endDate = $request->end_date;
            $projectCode = $request->project_code ?? null;

            // Use the dedicated API statement generator instead
            $statementData = $this->generateApiStatement($account, $startDate, $endDate, $projectCode);

            return response()->json([
                'success' => true,
                'data' => $statementData
            ]);
        } catch (\Exception $e) {
            Log::error('API Generate Statement Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Format a number using Indonesian locale
     * 
     * @param float $number 
     * @return string
     */
    private function formatNumber($number)
    {
        // Indonesian format: 1.234.567,89 (dot as thousands separator, comma as decimal separator)
        return number_format($number, 2, ',', '.');
    }

    /**
     * Generate account statement data specifically for API responses
     * This method ensures exact parity with what the statement blade view would show
     * 
     * @param Account $account The account model
     * @param string $startDate Start date in Y-m-d format
     * @param string $endDate End date in Y-m-d format
     * @param string|null $projectCode Optional project code filter
     * @return array
     */
    private function generateApiStatement($account, $startDate, $endDate, $projectCode = null)
    {
        // Calculate opening balance properly
        $openingBalance = $this->calculateOpeningBalance($account, $startDate, $projectCode);
        
        // Get transactions within the period
        $transactions = $this->getTransactions($account->id, $startDate, $endDate, $projectCode);
        
        // Track running balance for calculations
        $runningBalance = $openingBalance;
        
        // Initialize statement lines array
        $statementLines = [];
        
        // Add opening balance line
        $statementLines[] = [
            'date' => $startDate,
            'description' => 'Opening Balance',
            'doc_num' => '',
            'doc_type' => '',
            'sap_user' => '',
            'project_code' => $projectCode ?? '',
            'debit' => 0,  // Always 0 for opening balance
            'credit' => 0, // Always 0 for opening balance
            'balance' => $openingBalance // Use the calculated opening balance
        ];
        
        // Process each transaction
        foreach ($transactions as $transaction) {
            // Extract the raw values directly from the database
            $debitAmount = floatval($transaction->debit_amount);
            $creditAmount = floatval($transaction->credit_amount);
            
            // Update running balance based on account's normal balance type
            if ($account->normal_balance === 'debit') {
                $runningBalance += $debitAmount - $creditAmount;
            } else {
                $runningBalance += $creditAmount - $debitAmount;
            }
            
            // Add transaction line
            $statementLines[] = [
                'date' => $transaction->posting_date,
                'description' => $transaction->description,
                'doc_num' => $transaction->doc_num,
                'doc_type' => $transaction->doc_type ?? '',
                'sap_user' => $transaction->sap_user ?? '',
                'project_code' => $transaction->project_code,
                'debit' => $debitAmount,
                'credit' => $creditAmount,
                'balance' => $runningBalance
            ];
        }
        
        // Return the complete response
        return [
            'account' => [
                'account_number' => $account->account_number,
                'name' => $account->name
            ],
            'startDate' => $startDate,
            'endDate' => $endDate,
            'project_code' => $projectCode,
            'statementLines' => $statementLines
        ];
    }

    /**
     * Generate account statement data
     * 
     * @param int $accountId
     * @param string $startDate
     * @param string $endDate
     * @param string|null $projectCode
     * @param bool $forApi Whether this is for API (raw values) or view (formatted values)
     * @return array
     */
    private function generateStatement($accountId, $startDate, $endDate, $projectCode = null, $forApi = false)
    {
        $account = Account::findOrFail($accountId);
        
        // Get opening balance (account opening balance + transactions before start date)
        $openingBalance = $this->calculateOpeningBalance($account, $startDate, $projectCode);
        
        // Get transactions within the period
        $transactions = $this->getTransactions($account->id, $startDate, $endDate, $projectCode);
        
        // Calculate running balance
        $runningBalance = $openingBalance;
        $statementLines = collect();
        
        // Prepare and add opening balance line
        $openingLine = [
            'date' => $startDate,
            'description' => 'Opening Balance',
            'doc_num' => '',
            'doc_type' => '',
            'sap_user' => '',
            'project_code' => $projectCode ?? '',
        ];
        
        if ($forApi) {
            // For API, use raw numeric values - ensure they're actually numeric
            $openingLine['debit'] = floatval(0);
            $openingLine['credit'] = floatval(0);
            $openingLine['balance'] = floatval($openingBalance); // Ensure it's a float
        } else {
            // For view, use formatted values with Indonesian locale
            $openingLine['debit'] = $this->formatNumber(0);
            $openingLine['credit'] = $this->formatNumber(0);
            $openingLine['balance'] = $this->formatNumber($runningBalance);
        }
        
        $statementLines->push($openingLine);

        // Process each transaction
        foreach ($transactions as $transaction) {
            // Extract the raw values directly from the database
            $debitAmount = floatval($transaction->debit_amount);
            $creditAmount = floatval($transaction->credit_amount);
            
            if ($account->normal_balance === 'debit') {
                $runningBalance += $debitAmount - $creditAmount;
            } else {
                $runningBalance += $creditAmount - $debitAmount;
            }

            $line = [
                'date' => $transaction->posting_date,
                'description' => $transaction->description,
                'doc_num' => $transaction->doc_num,
                'doc_type' => $transaction->doc_type ?? '',
                'sap_user' => $transaction->sap_user ?? '',
                'project_code' => $transaction->project_code,
            ];
            
            if ($forApi) {
                // For API, use raw numeric values
                $line['debit'] = $debitAmount;
                $line['credit'] = $creditAmount;
                $line['balance'] = $runningBalance;
            } else {
                // For view, use formatted values with Indonesian locale
                $line['debit'] = $this->formatNumber($debitAmount);
                $line['credit'] = $this->formatNumber($creditAmount);
                $line['balance'] = $this->formatNumber($runningBalance);
            }
            
            $statementLines->push($line);
        }

        // Create the final response
        $response = [
            'account' => [
                'account_number' => $account->account_number,
                'name' => $account->name
            ],
            'startDate' => $startDate,
            'endDate' => $endDate,
            'project_code' => $projectCode,
            'statementLines' => $statementLines
        ];
        
        return $response;
    }

    private function calculateOpeningBalance($account, $startDate, $projectCode = null)
    {
        // Start with 0 balance
        $openingBalance = 0;
        
        // Add account's opening balance if set and the opening balance date is before the start date
        if (!is_null($account->opening_balance) && !is_null($account->opening_balance_date)) {
            $openingBalanceDate = Carbon::parse($account->opening_balance_date);
            $statementStartDate = Carbon::parse($startDate);
            
            if ($openingBalanceDate->lt($statementStartDate)) {
                // Only include opening balance if it's before the start date
                $openingBalance = floatval($account->opening_balance);
                Log::debug('Using account opening balance', [
                    'account_number' => $account->account_number,
                    'opening_balance' => $account->opening_balance,
                    'opening_balance_date' => $account->opening_balance_date,
                    'floatval' => $openingBalance
                ]);
            }
        }
        
        // Add transactions before start date
        $query = JournalEntryLine::join('journal_entries', 'journal_entries.id', '=', 'journal_entry_lines.journal_entry_id')
            ->where('journal_entry_lines.account_id', $account->id)
            ->where('journal_entries.entry_date', '<', $startDate);

        if ($projectCode) {
            $query->where('journal_entry_lines.project_code', $projectCode);
        }

        // If we have account opening balance date, only include transactions after that date
        if (!is_null($account->opening_balance_date)) {
            $query->where('journal_entries.posting_date', '>=', $account->opening_balance_date);
        }

        $result = $query->select(
            DB::raw('SUM(debit_amount) as total_debit'),
            DB::raw('SUM(credit_amount) as total_credit')
        )->first();
        
        // Get raw values without multiplier
        $totalDebit = floatval($result->total_debit ?? 0);
        $totalCredit = floatval($result->total_credit ?? 0);
        
        $transactionBalance = 0;
        if ($account->normal_balance === 'debit') {
            $transactionBalance = $totalDebit - $totalCredit;
        } else {
            $transactionBalance = $totalCredit - $totalDebit;
        }
        
        $openingBalance += $transactionBalance;
        
        Log::debug('Final opening balance calculation', [
            'account_number' => $account->account_number,
            'account_balance' => $account->opening_balance ?? 0,
            'total_debit' => $totalDebit,
            'total_credit' => $totalCredit,
            'transaction_balance' => $transactionBalance,
            'final_opening_balance' => $openingBalance
        ]);
        
        return $openingBalance;
    }

    private function getTransactions($accountId, $startDate, $endDate, $projectCode = null)
    {
        $query = JournalEntryLine::join('journal_entries', 'journal_entries.id', '=', 'journal_entry_lines.journal_entry_id')
            ->where('journal_entry_lines.account_id', $accountId)
            ->whereBetween('journal_entries.posting_date', [$startDate, $endDate]);

        if ($projectCode) {
            $query->where('journal_entry_lines.project_code', $projectCode);
        }

        $transactions = $query->select(
            'journal_entries.posting_date',
            'journal_entries.doc_num',
            'journal_entries.doc_type',
            'journal_entries.sap_user',
            'journal_entry_lines.description',
            'journal_entry_lines.project_code',
            'journal_entry_lines.debit_amount',
            'journal_entry_lines.credit_amount'
        )
        ->orderBy('journal_entries.posting_date')
        ->orderBy('journal_entries.id')
        ->get();
        
        return $transactions;
    }
} 