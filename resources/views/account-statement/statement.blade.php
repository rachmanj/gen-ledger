@extends('layout.main')

@section('title', 'Account Statement')
@section('title_page', 'Account Statement')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route('account-statement.index') }}">Account Statement</a></li>
    <li class="breadcrumb-item active">Statement Details</li>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                Account Statement for {{ $account->account_number }} - {{ $account->name }}
                <small class="text-muted">({{ $startDate }} to {{ $endDate }})</small>
            </h3>
        </div>

        <div class="card-body">
            <!-- Debug info -->
            @if (app()->environment('local'))
                <div class="alert alert-info mb-3">
                    <h5>Debug Info:</h5>
                    <p>First transaction in statement:</p>
                    @if (count($statementLines) > 1)
                        @php $firstTx = $statementLines[1]; @endphp
                        <pre>
                        Date: {{ $firstTx['date'] }}
                        Description: {{ $firstTx['description'] }}
                        Doc Number: {{ $firstTx['doc_num'] }}
                        Original debit value: {{ $firstTx['debit'] }} 
                        Original credit value: {{ $firstTx['credit'] }}
                        Original balance value: {{ $firstTx['balance'] }}
                    </pre>
                    @endif
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Description</th>
                            <th class="text-end">Debit</th>
                            <th class="text-end">Credit</th>
                            <th class="text-end">Balance</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($statementLines as $line)
                            <tr>
                                <td>{{ $line['date'] }}</td>
                                <td>{{ $line['description'] }}</td>
                                <td class="text-end">{{ $line['debit'] }}</td>
                                <td class="text-end">{{ $line['credit'] }}</td>
                                <td class="text-end">{{ $line['balance'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                <a href="{{ route('account-statement.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Statement Generator
                </a>
            </div>
        </div>
    </div>
@endsection
