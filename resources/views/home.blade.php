@extends('layout.main')

@section('title', 'Dashboard')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3>Welcome, {{ auth()->user()->name }}</h3>
                    </div>
                    <div class="card-body">
                        <p>You are logged in as {{ auth()->user()->getRoleNames()->first() }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
