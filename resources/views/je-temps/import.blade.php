@extends('layout.main')

@push('styles')
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css') }}">
    <!-- Toastr -->
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/toastr/toastr.min.css') }}">
    <style>
        .overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            color: white;
        }
    </style>
@endpush

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span>Import Journal Entry Data</span>
                        <div>
                            <button type="button" class="btn btn-success btn-sm mr-2" id="convertBtn"
                                {{ !$hasData ? 'disabled' : '' }}>
                                <i class="fas fa-exchange-alt"></i> Convert to Journal Entries
                            </button>
                            <button type="button" class="btn btn-danger btn-sm" id="truncateBtn"
                                {{ !$hasData ? 'disabled' : '' }}>
                                <i class="fas fa-trash"></i> Clear All Data
                            </button>
                        </div>
                    </div>

                    <div class="card-body">
                        <form action="{{ route('je-temps.import') }}" method="POST" enctype="multipart/form-data"
                            id="importForm">
                            @csrf
                            <div class="mb-3">
                                <label for="file" class="form-label">Excel File</label>
                                <input type="file" class="form-control" id="file" name="file"
                                    accept=".xlsx,.xls">
                                <div class="form-text">Please upload an Excel file (.xlsx or .xls)</div>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-upload"></i> Import Data
                            </button>
                        </form>

                        <!-- Loading Overlay -->
                        <div id="loading" style="display: none;">
                            <div class="overlay">
                                <i class="fas fa-sync fa-spin"></i>
                                <div class="mt-2">Processing...</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <!-- SweetAlert2 -->
    <script src="{{ asset('adminlte/plugins/sweetalert2/sweetalert2.min.js') }}"></script>
    <!-- Toastr -->
    <script src="{{ asset('adminlte/plugins/toastr/toastr.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            // Handle form submission
            $('#importForm').on('submit', function() {
                if ($('#file').val()) {
                    $('#loading').show();
                }
            });

            // Convert button click handler
            $('#convertBtn').click(function() {
                Swal.fire({
                    title: 'Converting Data',
                    text: 'Please wait while we convert the data...',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    allowEnterKey: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: '{{ route('je-temps.convert') }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                title: 'Success!',
                                html: `
                                    <div class="text-left">
                                        <p>${response.message}</p>
                                        <p><strong>Batch Number:</strong> ${response.batch_number}</p>
                                        <p><strong>Statistics:</strong></p>
                                        <ul>
                                            <li>New Accounts: ${response.statistics.new_accounts}</li>
                                            <li>New Journal Entries: ${response.statistics.new_journal_entries}</li>
                                            <li>New Journal Entry Lines: ${response.statistics.new_journal_entry_lines}</li>
                                            <li>Skipped Entries (duplicate tx_num): ${response.statistics.skipped_entries}</li>
                                        </ul>
                                    </div>
                                `,
                                icon: 'success',
                                confirmButtonText: 'OK'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    location.reload();
                                }
                            });
                        } else {
                            Swal.fire({
                                title: 'Error!',
                                text: response.error ||
                                    'An error occurred during conversion',
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                        }
                    },
                    error: function(xhr) {
                        Swal.fire({
                            title: 'Error!',
                            text: xhr.responseJSON?.error ||
                                'An error occurred during conversion',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                });
            });

            // Handle truncate button
            $('#truncateBtn').on('click', function() {
                Swal.fire({
                    title: 'Are you sure?',
                    text: "This will delete all data from the journal entry table. This action cannot be undone!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete all!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $('#loading').show();
                        $.ajax({
                            url: '{{ route('je-temps.truncate') }}',
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                $('#loading').hide();
                                if (response.success) {
                                    Swal.fire(
                                        'Deleted!',
                                        'All data has been cleared successfully.',
                                        'success'
                                    ).then(() => {
                                        location.reload();
                                    });
                                }
                            },
                            error: function(xhr) {
                                $('#loading').hide();
                                let errorMessage =
                                    'Failed to clear data. Please try again.';
                                if (xhr.responseJSON && xhr.responseJSON.error) {
                                    errorMessage = xhr.responseJSON.error;
                                }
                                Swal.fire(
                                    'Error!',
                                    errorMessage,
                                    'error'
                                );
                            }
                        });
                    }
                });
            });

            // Initialize Toastr
            toastr.options = {
                "closeButton": true,
                "progressBar": true,
                "positionClass": "toast-top-right",
                "timeOut": "3000"
            };

            // Show toastr messages if they exist in session
            @if (session('success'))
                toastr.success('{{ session('success') }}');
            @endif

            @if (session('error'))
                toastr.error('{{ session('error') }}');
            @endif
        });
    </script>
@endpush
