@extends('layout.main')

@section('title', 'Accounts')
@section('title_page', 'Accounts')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
    <li class="breadcrumb-item active">Accounts</li>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Accounts List</h3>
            <div class="card-tools">
                <a href="{{ route('accounts.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Add New
                </a>
            </div>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-striped" id="accounts-table">
                <thead>
                    <tr>
                        <th>Account Number</th>
                        <th>Name</th>
                        <th>Account Type</th>
                        <th>Normal Balance</th>
                        <th class="text-right">Opening Balance</th>
                        <th>Opening Balance Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    <!-- Edit Account Modal -->
    <div class="modal fade" id="editAccountModal" tabindex="-1" role="dialog" aria-labelledby="editAccountModalLabel"
        aria-modal="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editAccountModalLabel">Edit Account</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="editAccountForm" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="form-group">
                            <label for="edit_account_number">Account Number</label>
                            <input type="text" class="form-control" id="edit_account_number" name="account_number"
                                required>
                        </div>

                        <div class="form-group">
                            <label for="edit_name">Name</label>
                            <input type="text" class="form-control" id="edit_name" name="name" required>
                        </div>

                        <div class="form-group">
                            <label for="edit_account_type_id">Account Type</label>
                            <select class="form-control" id="edit_account_type_id" name="account_type_id" required>
                                <option value="">Select Account Type</option>
                                @foreach ($accountTypes as $type)
                                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="edit_normal_balance">Normal Balance</label>
                            <select class="form-control" id="edit_normal_balance" name="normal_balance" required>
                                <option value="">Select Normal Balance</option>
                                <option value="debit">Debit</option>
                                <option value="credit">Credit</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="edit_parent_account_id">Parent Account</label>
                            <select class="form-control" id="edit_parent_account_id" name="parent_account_id">
                                <option value="">Select Parent Account (Optional)</option>
                                @foreach ($parentAccounts as $parentAccount)
                                    <option value="{{ $parentAccount->id }}">
                                        {{ $parentAccount->account_number }} - {{ $parentAccount->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="edit_description">Description</label>
                            <textarea class="form-control" id="edit_description" name="description" rows="3"></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="edit_opening_balance">Opening Balance</label>
                                    <input type="number" step="0.01" class="form-control text-right"
                                        id="edit_opening_balance" name="opening_balance">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="edit_opening_balance_date">Opening Balance Date</label>
                                    <input type="date" class="form-control" id="edit_opening_balance_date"
                                        name="opening_balance_date">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="edit_is_active" name="is_active"
                                    value="1">
                                <label class="custom-control-label" for="edit_is_active">Active</label>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="saveAccountChanges">Save changes</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/toastr/toastr.min.css') }}">
@endpush

@push('scripts')
    <script src="{{ asset('adminlte/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('adminlte/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('adminlte/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('adminlte/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('adminlte/plugins/toastr/toastr.min.js') }}"></script>
    <script>
        $(function() {
            // Configure toastr
            toastr.options = {
                "closeButton": true,
                "debug": false,
                "newestOnTop": false,
                "progressBar": true,
                "positionClass": "toast-top-right",
                "preventDuplicates": false,
                "onclick": null,
                "showDuration": "300",
                "hideDuration": "1000",
                "timeOut": "5000",
                "extendedTimeOut": "1000",
                "showEasing": "swing",
                "hideEasing": "linear",
                "showMethod": "fadeIn",
                "hideMethod": "fadeOut"
            };

            var table = $('#accounts-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('accounts.index') }}',
                columns: [{
                        data: 'account_number',
                        name: 'accounts.account_number'
                    },
                    {
                        data: 'name',
                        name: 'accounts.name'
                    },
                    {
                        data: 'account_type',
                        name: 'account_type',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'normal_balance',
                        name: 'accounts.normal_balance'
                    },
                    {
                        data: 'opening_balance',
                        name: 'accounts.opening_balance',
                        className: 'text-right',
                        render: function(data) {
                            return data ? parseFloat(data).toLocaleString('en-US', {
                                minimumFractionDigits: 2,
                                maximumFractionDigits: 2
                            }) : '0.00';
                        }
                    },
                    {
                        data: 'opening_balance_date',
                        name: 'accounts.opening_balance_date',
                        render: function(data) {
                            return data ? new Date(data).toLocaleDateString() : '-';
                        }
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
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

            // Handle edit button click
            $(document).on('click', '.edit-account', function() {
                var accountId = $(this).data('id');
                var url = '{{ route('accounts.show', ':id') }}'.replace(':id', accountId);

                $.get(url, function(account) {
                    $('#editAccountForm').attr('action', '{{ route('accounts.update', ':id') }}'
                        .replace(':id', accountId));
                    $('#edit_account_number').val(account.account_number);
                    $('#edit_name').val(account.name);
                    $('#edit_account_type_id').val(account.account_type_id);
                    $('#edit_normal_balance').val(account.normal_balance);
                    $('#edit_parent_account_id').val(account.parent_account_id);
                    $('#edit_description').val(account.description);
                    $('#edit_opening_balance').val(account.opening_balance);
                    $('#edit_opening_balance_date').val(account.opening_balance_date);
                    $('#edit_is_active').prop('checked', account.is_active);

                    var modal = $('#editAccountModal');
                    modal.modal('show');

                    // Set focus to the first input when modal is shown
                    modal.on('shown.bs.modal', function() {
                        $('#edit_account_number').focus();
                    });
                });
            });

            // Handle save changes
            $('#saveAccountChanges').click(function() {
                var form = $('#editAccountForm');
                var url = form.attr('action');
                var formData = form.serialize();

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        $('#editAccountModal').modal('hide');
                        table.ajax.reload();
                        toastr.success('Account updated successfully');
                    },
                    error: function(xhr) {
                        var errors = xhr.responseJSON.errors;
                        $.each(errors, function(key, value) {
                            toastr.error(value[0]);
                        });
                    }
                });
            });

            // Handle delete button click
            $(document).on('click', '.delete-account', function() {
                var accountId = $(this).data('id');
                var url = '{{ route('accounts.destroy', ':id') }}'.replace(':id', accountId);

                if (confirm('Are you sure you want to delete this account?')) {
                    $.ajax({
                        url: url,
                        type: 'POST',
                        data: {
                            _method: 'DELETE',
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            table.ajax.reload();
                            toastr.success('Account deleted successfully');
                        },
                        error: function(xhr) {
                            toastr.error('Error deleting account');
                        }
                    });
                }
            });
        });
    </script>
@endpush
