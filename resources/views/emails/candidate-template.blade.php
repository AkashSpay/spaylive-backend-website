<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Spay Recruitment</title>
</head>

<body style="margin:0; padding:0; background-color:#f4f6f9; font-family: Arial, Helvetica, sans-serif;">

    <table width="100%" cellpadding="0" cellspacing="0" style="padding:30px 0; background-color:#f4f6f9;">
        <tr>
            <td align="center">

                <!-- Main Container -->
                <table width="600" cellpadding="0" cellspacing="0" style="background:#ffffff; border-radius:10px; overflow:hidden; box-shadow:0 4px 15px rgba(0,0,0,0.05);">

                    <!-- HEADER -->
                    <tr>
                        <td align="center" style="background:linear-gradient(90deg,#2563eb,#1d4ed8); padding:35px 20px;">
                            
                            <img src="{{ asset('images/logo.png') }}"
                                 alt="Spay Logo"
                                 width="140"
                                 style="display:block; margin:0 auto 15px auto;">

                            <h1 style="margin:0; font-size:22px; color:#ffffff; letter-spacing:0.5px;">
                                Spay Recruitment
                            </h1>

                        </td>
                    </tr>

                    <!-- BODY -->
                    <tr>
                        <td style="padding:40px 35px; color:#333333; font-size:15px; line-height:1.7;">

                            <p style="margin:0 0 20px 0;">
                                Dear <strong>{{ $candidate->name }}</strong>,
                            </p>

                            <div style="margin-bottom:25px;">
                                {!! nl2br(e($messageBody)) !!}
                            </div>

                            <p style="margin-top:30px;">
                                Regards,<br>
                                <strong style="color:#2563eb;">Spay HR Team</strong>
                            </p>

                        </td>
                    </tr>

                    <!-- DIVIDER -->
                    <tr>
                        <td style="padding:0 35px;">
                            <hr style="border:none; border-top:1px solid #e5e7eb;">
                        </td>
                    </tr>

                    <!-- FOOTER -->
                    <tr>
                        <td align="center" style="padding:25px 30px; font-size:12px; color:#6b7280; background:#f9fafb;">

                            <p style="margin:0 0 8px 0;">
                                © {{ date('Y') }} Spay Fintech Pvt Ltd. All rights reserved.
                            </p>

                            <p style="margin:0;">
                                This is an automated message from Spay Recruitment System.
                            </p>

                        </td>
                    </tr>

                </table>

                <!-- Spacer -->
                <table width="600">
                    <tr>
                        <td height="20"></td>
                    </tr>
                </table>

            </td>
        </tr>
    </table>

</body>
</html>