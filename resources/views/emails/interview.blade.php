<!-- resources/views/emails/contact.blade.php -->

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subject }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            color: #333;
        }

        .container {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .header {
            text-align: center;
            background-color: #007bff;
            color: #fff;
            padding: 10px 0;
            border-radius: 8px;
        }

        .content {
            margin-top: 20px;
            line-height: 1.6;
        }

        .footer {
            text-align: center;
            font-size: 12px;
            color: #777;
            margin-top: 30px;
        }

        .footer a {
            color: #007bff;
            text-decoration: none;
        }

        .btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
            text-align: center;
        }

        .btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>

<body>
    <div style="background-color:#e7ecfa">
        <div style="margin:0 auto;max-width:600px">
            <table align="center" border="0" cellpadding="0" cellspacing="0" role="presentation" style="width:100%">
                <tbody>
                    <tr>
                        <td style="direction:ltr;font-size:0;padding:0;text-align:center">
                            <div
                                style="font-size:0;text-align:left;direction:ltr;display:inline-block;vertical-align:top;width:100%">
                                <table border="0" cellpadding="0" cellspacing="0" role="presentation"
                                    style="vertical-align:top" width="100%">
                                    <tbody>
                                        <tr>
                                            <td align="left"
                                                style="font-size:0;padding:16px 16px;word-break:break-word">

                                                <div
                                                    style="padding-top:0px;padding-bottom:0px;padding-left:20px;background-color:#0d1a38;text-align:center;height:55px">
                                                    <img style="height:40px;float:left;margin-top:8px" alt="MY Logo"
                                                        src="{{ asset('assets/img/logo/logo.jpg') }}">
                                                    <h1
                                                        style="padding-top:3px;font-family:'Noto Sans',sans-serif;font-size:22px;line-height:22px;font-weight:bold;text-align:center;color:#ffffff;display:inline-block">
                                                        Scheduled Interview</h1>
                                                </div>
                                                <div
                                                    style="padding-top:4px;padding-bottom:4px;padding-left:20px;background-color:white">
                                                    <p
                                                        style="font-family:'Noto Sans',sans-serif;font-size:14px;line-height:22px;font-weight:500;text-align:left;color:#464646">
                                                        <b>Dear {{ $name }},</b>
                                                        <br><br>I hope this message finds you well. We are pleased to
                                                        inform you that
                                                        after reviewing your application, we would like to invite you
                                                        for an
                                                        interview for the position of
                                                        <strong>{{ strtoupper($jobTitle) }}</strong> at
                                                        <strong>Shining Hope For Communities (STAWIHR)'</strong><br><br>

                                                    </p>

                                                    <p
                                                        style="padding-top:10px;font-family:'Noto Sans',sans-serif;font-size:14px;line-height:22px;font-weight:500;text-align:left;color:#464646">
                                                        <b>Interview Details</b>
                                                    </p>
                                                    <ol
                                                        style="padding-left:20px;padding-top:10px;padding-right:10px;font-family:'Noto Sans',sans-serif;font-size:14px;line-height:22px;font-weight:500;text-align:left;color:#464646">
                                                        <li style="padding-left:10px">The Interview will take place on
                                                            <b>{{$interviewDate}}</b> at <b>{{$interviewTime}}</b>
                                                        </li>
                                                        <li style="padding-left:10px">Will be held physically at our
                                                            offices: <b>{{$interviewLocation}}</b>.
                                                        </li>
                                                        <li style="padding-left:10px">Please let us know if this time
                                                            works for you or if you need to reschedule.
                                                    </ol>
                                                    <p
                                                        style="padding-top:10px;font-family:'Noto Sans',sans-serif;font-size:14px;line-height:22px;font-weight:500;text-align:left;color:#464646">
                                                        <b>Kind Regards,</b>
                                                        <br>
                                                        <b>Recruitment Team</b>
                                                        <br>

                                                    </p>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>
