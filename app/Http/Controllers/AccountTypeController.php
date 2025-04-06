<?php

namespace App\Http\Controllers;

use App\Models\AccountType;
use Illuminate\Http\Request;

class AccountTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $accountTypes = AccountType::all();
        return view('account_types.index', compact('accountTypes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('account_types.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        AccountType::create($request->all());

        return redirect()->route('account_types.index')
            ->with('success', 'Account type created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(AccountType $accountType)
    {
        return view('account_types.show', compact('accountType'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AccountType $accountType)
    {
        return view('account_types.edit', compact('accountType'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AccountType $accountType)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $accountType->update($request->all());

        return redirect()->route('account_types.index')
            ->with('success', 'Account type updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AccountType $accountType)
    {
        $accountType->delete();

        return redirect()->route('account_types.index')
            ->with('success', 'Account type deleted successfully.');
    }
} 