<!DOCTYPE html>
<html>

<head>
    <title></title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
</head>

<body>
    <div class="container-fluid">
        <p>Dear {{ $employee_name ?? 'Employee' }},</p>

        <p>Please find the attached P9 form for your use.</p>

        <p>
            The attached P9 PDF is password protected. Use your
            <strong>{{ $passwordLabel ?? employeeDocumentPasswordLabel($employee ?? null) }}</strong>
            to open it. If you do not have a National ID on file, use your Passport number.
        </p>

        <p>Kind Regards</p>
        
        <p>{{ helper_companyInfo($company ?? null)?->legal_Name ?? companyDisplayName($company ?? null) }}</p>
    </div>
</body>

</html>
