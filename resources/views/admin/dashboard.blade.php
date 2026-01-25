@extends('admin.layouts.app')

@section('content')
    <h2>Dashboard</h2>
    <p>Welcome, {{ auth()->guard('admin')->user()->name }}!</p>
@endsection
