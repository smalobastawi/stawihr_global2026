@extends('admin.master')
@section('title', getPageTitle() . ' | ' . config('app.name'))
@section('content')

@section('content')
    <div class="container">
        <h1>Edit Ethnicity</h1>
        <form action="{{ route('ethnicities.update', $ethnicity) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" name="name" id="name" class="form-control" value="{{ $ethnicity->name }}" required>
            </div>
            <button type="submit" class="btn btn-primary">Update</button>
        </form>
    </div>
@endsection
