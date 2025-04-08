<?php

namespace App\Http\Controllers;

use App\Imports\JeTempsImport;
use App\Models\JeTemp;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use App\Models\Account;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class JeTempImportController extends Controller
{
    public function index()
    {
        $hasData = JeTemp::exists();
        return view('je-temps.import', compact('hasData'));
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
        ]);

        try {
            Excel::import(new JeTempsImport, $request->file('file'));
            return redirect()->back()->with('success', 'Data imported successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error importing data: ' . $e->getMessage());
        }
    }

    public function truncate()
    {
        try {
            JeTemp::truncate();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function convertToJournalEntries()
    {
        try {
            // Check if there's any data to convert
            if (!JeTemp::exists()) {
                return response()->json(['error' => 'No data to convert'], 400);
            }

            DB::beginTransaction();

            // Get the next batch number
            $lastBatch = JournalEntry::max('batch_number') ?? 0;
            $batchNumber = $lastBatch + 1;

            // Initialize counters
            $newAccountsCount = 0;
            $newJournalEntriesCount = 0;
            $newJournalEntryLinesCount = 0;
            $skippedEntriesCount = 0;

            // Get distinct transactions from je_temps
            $transactions = JeTemp::select('tx_num', 'create_date', 'posting_date', 'doc_num', 'doc_type', 'user_code', 'remarks')
                ->distinct()
                ->get();

            foreach ($transactions as $tx) {
                // Check if journal entry with this tx_num already exists
                if (JournalEntry::where('tx_num', $tx->tx_num)->exists()) {
                    $skippedEntriesCount++;
                    continue;
                }

                // Create journal entry with all fields
                $journalEntry = JournalEntry::create([
                    'entry_date' => $tx->create_date,
                    'posting_date' => $tx->posting_date,
                    'tx_num' => $tx->tx_num,
                    'doc_num' => $tx->doc_num,
                    'doc_type' => $tx->doc_type,
                    'description' => $tx->remarks,
                    'sap_user' => $tx->user_code,
                    'created_by_user_id' => auth()->id(),
                    'status' => 'posted',
                    'batch_number' => $batchNumber
                ]);
                $newJournalEntriesCount++;

                // Get all lines for this transaction
                $lines = JeTemp::where('tx_num', $tx->tx_num)->get();

                foreach ($lines as $line) {
                    // Skip lines with both debit and credit as 0
                    if ($line->debit == 0 && $line->credit == 0) {
                        continue;
                    }

                    // Find or create account
                    $account = Account::firstOrCreate(
                        ['account_number' => $line->account],
                        [
                            'name' => $line->account_name ?? $line->account,
                            'account_type_id' => 1, // Default account type, adjust as needed
                            'normal_balance' => $line->debit > 0 ? 'debit' : 'credit',
                            'is_active' => true
                        ]
                    );

                    // Increment counter if account was created
                    if ($account->wasRecentlyCreated) {
                        $newAccountsCount++;
                    }

                    // Create journal entry line with all fields
                    JournalEntryLine::create([
                        'journal_entry_id' => $journalEntry->id,
                        'account_id' => $account->id,
                        'description' => $line->remarks,
                        'unit_no' => $line->unit_no,
                        'project_code' => $line->project_code,
                        'department_name' => $line->department,
                        'debit_amount' => $line->debit,
                        'credit_amount' => $line->credit,
                        'fc_debit_amount' => $line->fc_debit,
                        'fc_credit_amount' => $line->fc_credit
                    ]);
                    $newJournalEntryLinesCount++;
                }
            }

            DB::commit();
            return response()->json([
                'success' => true, 
                'message' => 'Data converted successfully!', 
                'batch_number' => $batchNumber,
                'statistics' => [
                    'new_accounts' => $newAccountsCount,
                    'new_journal_entries' => $newJournalEntriesCount,
                    'new_journal_entry_lines' => $newJournalEntryLinesCount,
                    'skipped_entries' => $skippedEntriesCount
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error converting JE Temps to Journal Entries: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
} 