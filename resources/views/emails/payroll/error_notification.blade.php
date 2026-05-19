@extends('emails.layout')

@section('content')
    <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
        <h2 style="color: #dc3545;">Payroll Processing Errors</h2>

        <p>Dear {{ auth()->user()->name ?? 'User' }},</p>

        <p>Some errors occurred during payroll processing. Please review the details below:</p>

        <div
            style="background-color: #f8d7da; border: 1px solid #f5c6cb; padding: 15px; border-radius: 5px; margin: 20px 0;">
            <h4 style="margin-top: 0; color: #721c24;">Error Details:</h4>
            <ul style="margin: 0; padding-left: 20px;">
                @foreach ($errors as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>

        <p>Please check the payroll records and resolve any issues before proceeding with approval.</p>

        <p>If you need assistance, please contact the system administrator.</p>

        <p>Best regards,<br>
            Payroll System</p>
    </div>
@endsection
