<!DOCTYPE html>
<html>
<head>
    <title>New Leave Application</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <style type="text/css">
        body, table, td, a {
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
        }
        table, td {
            mso-table-lspace: 0pt;
            mso-table-rspace: 0pt;
        }
        img {
            -ms-interpolation-mode: bicubic;
        }
        body {
            margin: 0 !important;
            padding: 0 !important;
            width: 100% !important;
            background-color: #f4f4f4;
            font-family: Arial, sans-serif;
        }
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 5px;
            overflow: hidden;
        }
        .header {
            text-align: center;
            padding: 20px;
        }
        .header img {
            width: 125px;
            height: auto;
        }
        .content {
            padding: 20px 30px;
            font-size: 16px;
            color: #333333;
        }
        .footer {
            background-color: #FFECD1;
            text-align: center;
            padding: 20px;
            font-size: 14px;
            color: #666666;
        }
    </style>
</head>
<body>
    <table border="0" cellpadding="0" cellspacing="0" width="100%">
        <tr>
            <td align="center">
                <table class="email-container">
                    <tr>
                        <td class="header">
                            <h1>New Leave Application</h1>
                        </td>
                    </tr>
                    <tr>
                        <td class="content">
                            <p>You have applied for a leave of <strong>{{ $content['no_of_days'] }}</strong> day(s).</p>
                            <p><strong>Details:</strong><br>
                                From Date: <strong>{{ $content['leave_from_date'] }}</strong><br>
                                To Date: <strong>{{ $content['leave_to_date'] }}</strong><br>
                                Number of Days: <strong>{{ $content['no_of_days'] }}</strong>
                            </p>
                            <p>Kindly wait for approval before proceeding.</p>
                        </td>
                    </tr>
                    <tr>
                     
                    </tr>
                   
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
