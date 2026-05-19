@extends('admin.master')
@section('content')
@section('title')
 Disciplinary Cases
@endsection
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Users with {{ $role->name }} Role</h3>
    </div>
    <div class="card-body">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Status</th>
                    <th>Joined</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                <tr>
                    <td>{{ $user->user_name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->status }}</td>
                    <td>{{ $user->created_at->format('M d, Y') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        
        {{ $users->links() }}
    </div>
</div>
@endsection