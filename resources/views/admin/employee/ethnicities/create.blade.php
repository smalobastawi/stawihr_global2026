@extends('admin.master')
@section('title', getPageTitle() . ' | ' . config('app.name'))
@section('content')
    <div class="container">
        <h1>Create Ethnicity</h1>
        <form action="{{ route('ethnicities.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" name="name" id="name" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Create</button>
        </form>
    </div>
@endsection
