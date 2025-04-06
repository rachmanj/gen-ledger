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
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
@endpush

@push('scripts')
    <script src="{{ asset('adminlte/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('adminlte/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('adminlte/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('adminlte/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
    <script>
        $(function() {
            $('#accounts-table').DataTable({
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
                        data: 'status',
                        name: 'is_active',
                        orderable: false,
                        searchable: false
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
        });
    </script>
@endpush
