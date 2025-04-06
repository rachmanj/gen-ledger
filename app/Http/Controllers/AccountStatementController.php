<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        $request->validate([
            'account_number' => 'required|exists:accounts,account_number',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'project_code' => 'nullable|string'
        ]);

        try {
            $account = Account::where('account_number', $request->account_number)->firstOrFail();
            $statementData = $this->generateStatement($account->id, $request->start_date, $request->end_date, $request->project_code);

            return response()->json([
                'success' => true,
                'data' => $statementData
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    private function generateStatement($accountId, $startDate, $endDate, $projectCode = null)
    {
        $account = Account::findOrFail($accountId);
        
        // Get opening balance (transactions before start date)
        $openingBalance = $this->calculateOpeningBalance($account->id, $startDate, $projectCode);
        
        // Get transactions within the period
        $transactions = $this->getTransactions($account->id, $startDate, $endDate, $projectCode);
        
        // Calculate running balance
        $runningBalance = $openingBalance;
        $statementLines = collect();
        
        // Add opening balance line
        $statementLines->push([
            'date' => $startDate,
            'description' => 'Opening Balance',
            'doc_num' => '',
            'project_code' => $projectCode ?? '',
            'debit' => 0,
            'credit' => 0,
            'balance' => $runningBalance
        ]);

        // Process each transaction
        foreach ($transactions as $transaction) {
            if ($account->normal_balance === 'debit') {
                $runningBalance += $transaction->debit_amount - $transaction->credit_amount;
            } else {
                $runningBalance += $transaction->credit_amount - $transaction->debit_amount;
            }

            $statementLines->push([
                'date' => $transaction->posting_date,
                'description' => $transaction->description,
                'doc_num' => $transaction->doc_num,
                'project_code' => $transaction->project_code,
                'debit' => $transaction->debit_amount,
                'credit' => $transaction->credit_amount,
                'balance' => $runningBalance
            ]);
        }

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

    private function calculateOpeningBalance($accountId, $startDate, $projectCode = null)
    {
        $query = JournalEntryLine::join('journal_entries', 'journal_entries.id', '=', 'journal_entry_lines.journal_entry_id')
            ->where('journal_entry_lines.account_id', $accountId)
            ->where('journal_entries.posting_date', '<', $startDate);

        if ($projectCode) {
            $query->where('journal_entry_lines.project_code', $projectCode);
        }

        $result = $query->select(
            DB::raw('SUM(debit_amount) as total_debit'),
            DB::raw('SUM(credit_amount) as total_credit')
        )->first();

        $account = Account::find($accountId);
        
        if ($account->normal_balance === 'debit') {
            return ($result->total_debit ?? 0) - ($result->total_credit ?? 0);
        } else {
            return ($result->total_credit ?? 0) - ($result->total_debit ?? 0);
        }
    }

    private function getTransactions($accountId, $startDate, $endDate, $projectCode = null)
    {
        $query = JournalEntryLine::join('journal_entries', 'journal_entries.id', '=', 'journal_entry_lines.journal_entry_id')
            ->where('journal_entry_lines.account_id', $accountId)
            ->whereBetween('journal_entries.posting_date', [$startDate, $endDate]);

        if ($projectCode) {
            $query->where('journal_entry_lines.project_code', $projectCode);
        }

        return $query->select(
            'journal_entries.posting_date',
            'journal_entries.doc_num',
            'journal_entry_lines.description',
            'journal_entry_lines.project_code',
            'journal_entry_lines.debit_amount',
            'journal_entry_lines.credit_amount'
        )
        ->orderBy('journal_entries.posting_date')
        ->orderBy('journal_entries.id')
        ->get();
    }
} 