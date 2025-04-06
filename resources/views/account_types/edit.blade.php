@extends('layouts.master')

@section('title', 'Edit Account Type')

@section('content')
    <div class="container-fluid">
        <div class="row mb-3">
            <div class="col">
                <h2>Edit Account Type</h2>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <form action="{{ route('account-types.update', $accountType) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                            name="name" value="{{ old('name', $accountType->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description"
                            rows="3">{{ old('description', $accountType->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="text-end">
                        <a href="{{ route('account-types.index') }}" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">Update Account Type</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
