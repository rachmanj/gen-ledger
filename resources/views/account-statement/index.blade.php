@extends('layout.main')

@section('title', 'Account Statement')
@section('title_page', 'Account Statement')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
    <li class="breadcrumb-item active">Account Statement</li>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Generate Account Statement</h3>
        </div>
        <div class="card-body">
            <form id="statementForm" method="POST" action="{{ route('account-statement.generate') }}">
                @csrf

                <div class="form-group row mb-3">
                    <label for="account_id" class="col-md-4 col-form-label text-md-right">{{ __('Account') }}</label>
                    <div class="col-md-6">
                        <select id="account_id" class="form-control @error('account_id') is-invalid @enderror"
                            name="account_id" required>
                            <option value="">Select Account</option>
                            @foreach ($accounts as $account)
                                <option value="{{ $account->id }}">{{ $account->account_number }} - {{ $account->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('account_id')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="form-group row mb-3">
                    <label for="start_date" class="col-md-4 col-form-label text-md-right">{{ __('Start Date') }}</label>
                    <div class="col-md-6">
                        <input id="start_date" type="date" class="form-control @error('start_date') is-invalid @enderror"
                            name="start_date" required>
                        @error('start_date')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="form-group row mb-3">
                    <label for="end_date" class="col-md-4 col-form-label text-md-right">{{ __('End Date') }}</label>
                    <div class="col-md-6">
                        <input id="end_date" type="date" class="form-control @error('end_date') is-invalid @enderror"
                            name="end_date" required>
                        @error('end_date')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="form-group row mb-0">
                    <div class="col-md-6 offset-md-4">
                        <button type="submit" class="btn btn-primary" id="generateBtn">
                            <i class="fas fa-sync fa-spin d-none" id="loadingIcon"></i>
                            <span id="buttonText">Generate Statement</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card mt-4" id="statementCard" style="display: none;">
        <div class="card-header">
            <h3 class="card-title" id="statementTitle"></h3>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="statementTable">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Description</th>
                            <th>Doc. Number</th>
                            <th>Project Code</th>
                            <th class="text-end">Debit</th>
                            <th class="text-end">Credit</th>
                            <th class="text-end">Balance</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <style>
        .text-end {
            text-align: right !important;
        }

        .table td.text-end {
            padding-right: 1.5rem;
        }
    </style>
@endpush

@push('scripts')
    <script src="{{ asset('adminlte/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('adminlte/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('adminlte/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('adminlte/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
    <script>
        $(function() {
            let dataTable = null;

            function formatDate(dateString) {
                const date = new Date(dateString);
                const day = String(date.getDate()).padStart(2, '0');
                const month = String(date.getMonth() + 1).padStart(2, '0');
                const year = date.getFullYear();
                return `${day}-${month}-${year}`;
            }

            $('#statementForm').on('submit', function(e) {
                e.preventDefault();

                const $form = $(this);
                const $button = $('#generateBtn');
                const $loadingIcon = $('#loadingIcon');
                const $buttonText = $('#buttonText');

                // Show loading state
                $button.prop('disabled', true);
                $loadingIcon.removeClass('d-none');
                $buttonText.text('Generating...');

                $.ajax({
                    url: $form.attr('action'),
                    type: 'POST',
                    data: $form.serialize(),
                    success: function(response) {
                        if (dataTable) {
                            dataTable.destroy();
                        }

                        $('#statementTitle').html(
                            `Account Statement for ${response.account.account_number} - ${response.account.name} ` +
                            `<small class="text-muted">(${formatDate(response.startDate)} to ${formatDate(response.endDate)})</small>`
                        );

                        dataTable = $('#statementTable').DataTable({
                            data: response.statementLines,
                            columns: [{
                                    data: 'date',
                                    render: function(data) {
                                        return formatDate(data);
                                    }
                                },
                                {
                                    data: 'description'
                                },
                                {
                                    data: 'doc_num'
                                },
                                {
                                    data: 'project_code'
                                },
                                {
                                    data: 'debit',
                                    className: 'text-end',
                                    render: function(data) {
                                        // For API response, data will be numeric
                                        // Format using Indonesian locale (. as thousands separator, , as decimal)
                                        if (typeof data === 'number') {
                                            return data.toLocaleString(
                                                'id-ID', {
                                                    minimumFractionDigits: 2,
                                                    maximumFractionDigits: 2
                                                });
                                        }
                                        // If data is already a formatted string, return as is
                                        return data;
                                    }
                                },
                                {
                                    data: 'credit',
                                    className: 'text-end',
                                    render: function(data) {
                                        // For API response, data will be numeric
                                        // Format using Indonesian locale (. as thousands separator, , as decimal)
                                        if (typeof data === 'number') {
                                            return data.toLocaleString(
                                                'id-ID', {
                                                    minimumFractionDigits: 2,
                                                    maximumFractionDigits: 2
                                                });
                                        }
                                        // If data is already a formatted string, return as is
                                        return data;
                                    }
                                },
                                {
                                    data: 'balance',
                                    className: 'text-end',
                                    render: function(data) {
                                        // For API response, data will be numeric
                                        // Format using Indonesian locale (. as thousands separator, , as decimal)
                                        if (typeof data === 'number') {
                                            return data.toLocaleString(
                                                'id-ID', {
                                                    minimumFractionDigits: 2,
                                                    maximumFractionDigits: 2
                                                });
                                        }
                                        // If data is already a formatted string, return as is
                                        return data;
                                    }
                                }
                            ],
                            order: [
                                [0, 'asc']
                            ],
                            responsive: true,
                            autoWidth: false,
                            language: {
                                processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i>'
                            }
                        });

                        $('#statementCard').show();
                    },
                    error: function(xhr) {
                        alert('Error generating statement. Please try again.');
                    },
                    complete: function() {
                        // Reset button state
                        $button.prop('disabled', false);
                        $loadingIcon.addClass('d-none');
                        $buttonText.text('Generate Statement');
                    }
                });
            });
        });
    </script>
@endpush
