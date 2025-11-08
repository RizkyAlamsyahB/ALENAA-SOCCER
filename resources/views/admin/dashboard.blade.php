@extends('layouts.admin')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h1>Dashboard</h1>
                @auth
                    @if(auth()->user()->hasRole('admin'))
                        <p>Welcome to the Administrator dashboard</p>
                    @elseif(auth()->user()->hasRole('owner'))
                        <p>Welcome to the Owner dashboard</p>
                    @elseif(auth()->user()->hasRole('photographer'))
                        <p>Welcome to the Photographer dashboard</p>
                    @else
                        <p>Welcome to your dashboard</p>
                    @endif
                @endauth
            </div>
        </div>
    </div>
@endsection
