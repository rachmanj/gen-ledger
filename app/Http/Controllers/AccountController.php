<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\AccountType;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class AccountController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Account::query()
                ->select([
                    'accounts.id',
                    'accounts.account_number',
                    'accounts.name',
                    'accounts.account_type_id',
                    'accounts.normal_balance',
                    'accounts.opening_balance',
                    'accounts.opening_balance_date',
                    'accounts.description',
                    'accounts.parent_account_id',
                    'accounts.is_active',
                    'accounts.created_at',
                    'accounts.updated_at'
                ])
                ->with(['accountType', 'parentAccount']);
            
            return DataTables::of($query)
                ->addColumn('action', function ($account) {
                    return view('accounts.actions', compact('account'))->render();
                })
                ->addColumn('status', function ($account) {
                    return $account->is_active ? 
                        '<span class="badge badge-success">Active</span>' : 
                        '<span class="badge badge-danger">Inactive</span>';
                })
                ->addColumn('account_type', function ($account) {
                    return $account->accountType->name;
                })
                ->addColumn('parent_account', function ($account) {
                    return $account->parentAccount ? $account->parentAccount->name : '-';
                })
                ->orderColumn('account_number', function ($query, $order) {
                    $query->orderBy('accounts.account_number', $order);
                })
                ->orderColumn('name', function ($query, $order) {
                    $query->orderBy('accounts.name', $order);
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }

        $accountTypes = AccountType::all();
        $parentAccounts = Account::where('is_active', true)->get();
        return view('accounts.index', compact('accountTypes', 'parentAccounts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $accountTypes = AccountType::all();
        $parentAccounts = Account::where('is_active', true)->get();
        return view('accounts.create', compact('accountTypes', 'parentAccounts'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'account_number' => 'required|string|max:20|unique:accounts',
            'name' => 'required|string|max:255',
            'account_type_id' => 'required|exists:account_types,id',
            'normal_balance' => 'required|in:debit,credit',
            'description' => 'nullable|string',
            'parent_account_id' => 'nullable|exists:accounts,id',
            'is_active' => 'boolean',
        ]);

        Account::create($request->all());

        return redirect()->route('accounts.index')
            ->with('success', 'Account created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Account $account)
    {
        if (request()->ajax()) {
            return response()->json($account);
        }
        return view('accounts.show', compact('account'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Account $account)
    {
        $accountTypes = AccountType::all();
        $parentAccounts = Account::where('id', '!=', $account->id)
            ->where('is_active', true)
            ->get();
        return view('accounts.edit', compact('account', 'accountTypes', 'parentAccounts'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Account $account)
    {
        $request->validate([
            'account_number' => 'required|string|max:20|unique:accounts,account_number,' . $account->id,
            'name' => 'required|string|max:255',
            'account_type_id' => 'required|exists:account_types,id',
            'normal_balance' => 'required|in:debit,credit',
            'description' => 'nullable|string',
            'parent_account_id' => 'nullable|exists:accounts,id',
            'is_active' => 'boolean',
        ]);

        $account->update($request->all());

        return redirect()->route('accounts.index')
            ->with('success', 'Account updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Account $account)
    {
        $account->delete();

        return redirect()->route('accounts.index')
            ->with('success', 'Account deleted successfully.');
    }
} 