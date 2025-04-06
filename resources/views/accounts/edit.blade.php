@extends('layout.main')

@section('title', 'Edit Account')
@section('title_page', 'Edit Account')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route('accounts.index') }}">Accounts</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Edit Account</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('accounts.update', $account->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label for="account_number">Account Number</label>
                    <input type="text" class="form-control @error('account_number') is-invalid @enderror"
                        id="account_number" name="account_number"
                        value="{{ old('account_number', $account->account_number) }}" required>
                    @error('account_number')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                        name="name" value="{{ old('name', $account->name) }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="account_type_id">Account Type</label>
                    <select class="form-control @error('account_type_id') is-invalid @enderror" id="account_type_id"
                        name="account_type_id" required>
                        <option value="">Select Account Type</option>
                        @foreach ($accountTypes as $type)
                            <option value="{{ $type->id }}"
                                {{ old('account_type_id', $account->account_type_id) == $type->id ? 'selected' : '' }}>
                                {{ $type->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('account_type_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="normal_balance">Normal Balance</label>
                    <select class="form-control @error('normal_balance') is-invalid @enderror" id="normal_balance"
                        name="normal_balance" required>
                        <option value="">Select Normal Balance</option>
                        <option value="debit"
                            {{ old('normal_balance', $account->normal_balance) == 'debit' ? 'selected' : '' }}>
                            Debit
                        </option>
                        <option value="credit"
                            {{ old('normal_balance', $account->normal_balance) == 'credit' ? 'selected' : '' }}>
                            Credit
                        </option>
                    </select>
                    @error('normal_balance')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="parent_account_id">Parent Account</label>
                    <select class="form-control @error('parent_account_id') is-invalid @enderror" id="parent_account_id"
                        name="parent_account_id">
                        <option value="">Select Parent Account (Optional)</option>
                        @foreach ($parentAccounts as $parentAccount)
                            <option value="{{ $parentAccount->id }}"
                                {{ old('parent_account_id', $account->parent_account_id) == $parentAccount->id ? 'selected' : '' }}>
                                {{ $parentAccount->account_number }} - {{ $parentAccount->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('parent_account_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description"
                        rows="3">{{ old('description', $account->description) }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1"
                            {{ old('is_active', $account->is_active) ? 'checked' : '' }}>
                        <label class="custom-control-label" for="is_active">Active</label>
                    </div>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary">Update</button>
                    <a href="{{ route('accounts.index') }}" class="btn btn-default">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@endsection
