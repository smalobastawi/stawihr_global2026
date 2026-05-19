@extends('admin.master')

@section('title', getPageTitle() . ' | ' . config('app.name'))
@section('content')
    <div class="container">
        <h1>Ethnicities</h1>
        <a href="{{ route('ethnicities.create') }}" class="btn btn-primary">Add New Ethnicity</a>
        <table class="table" id="myTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Employee Count</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($ethnicities as $ethnicity)
                    <tr>
                        <td>{{ $ethnicity->id }}</td>
                        <td>{{ $ethnicity->name }}</td>
                        <td>{{ $ethnicity->employees_count }}</td>
                        <td>

                            <a href="{{ route('ethnicities.edit', $ethnicity) }}" class="btn btn-warning">Edit</a>
                            <form action="{{ route('ethnicities.destroy', $ethnicity) }}" method="POST"
                                style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
