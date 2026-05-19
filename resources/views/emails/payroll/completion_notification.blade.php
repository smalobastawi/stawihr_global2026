<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Payroll Processing Completed</title>
</head>

<body style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">

    <h2 style="color: #333;">Payroll Processing Completed</h2>

    <p>Dear <?php echo auth()->user()->employeeDetails ? auth()->user()->employeeDetails->fullName() : 'User'; ?>,</p>

    <p>Your payroll processing has been completed successfully.</p>

    <div style="background-color: #f8f9fa; padding: 15px; border-radius: 5px; margin: 20px 0;">
        <h3 style="margin-top: 0; color: #28a745;">Processing Summary</h3>
        <ul style="list-style: none; padding: 0;">
            <li><strong>Successfully Processed:</strong> <?php echo $successCount; ?> employees</li>

            <?php if ($errorCount > 0): ?>
            <li><strong>Errors:</strong> <?php echo $errorCount; ?> employees</li>
            <?php endif; ?>
        </ul>
    </div>

    <?php if ($errorCount > 0 && !empty($errors)): ?>
    <div
        style="background-color: #fff3cd; border: 1px solid #ffeaa7; padding: 15px; border-radius: 5px; margin: 20px 0;">
        <h4 style="margin-top: 0; color: #856404;">Errors Encountered:</h4>
        <ul style="margin: 0; padding-left: 20px;">
            <?php foreach ($errors as $error): ?>
            <li><?php echo $error; ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php endif; ?>

    <p>You can now review the processed payroll records and proceed with approval if needed.</p>

    <p>Best regards,<br>
        Payroll System</p>

</body>

</html>
