@extends('admin.master')
@section('title', getPageTitle() . ' | ' . config('app.name'))
@section('content')
    <div class="container">
        <h1>Ethnicity Details</h1>
        <p><strong>ID:</strong> {{ $ethnicity->id }}</p>
        <p><strong>Name:</strong> {{ $ethnicity->name }}</p>
        <a href="{{ route('ethnicities.index') }}" class="btn btn-secondary">Back</a>
        <a href="{{ route('ethnicities.edit', $ethnicity) }}" class="btn btn-warning">Edit</a>
    </div>
@endsection
