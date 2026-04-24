@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="card shadow-sm">
        <div class="card-body p-4">
            <div class="h4 mb-1">Dashboard</div>
            <div class="text-muted">Último login: {{ optional(auth()->user()->ultimo_login)->format('Y-m-d H:i') }}</div>
        </div>
    </div>
@endsection
