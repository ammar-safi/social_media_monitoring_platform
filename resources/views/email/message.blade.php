<!DOCTYPE html>
<html lang="ar">
<head> 
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>رسالة بريد إلكتروني</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .email-header {
            background-color: #6c757d;
            color: #ffffff;
            padding: 20px;
            text-align: center;
            font-size: 24px;
        }
        .email-body {
            padding: 20px;
            background-color: #f9f9f9;
            color: #333333;
            border-bottom: 1px solid #e1e1e1;
        }
        .email-body p {
            line-height: 1.6;
            color: #555555;
            margin-bottom: 10px;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }
        .email-body .button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
            font-weight: bold;
        }
        .email-footer {
            background-color: #f4f4f4;
            padding: 15px;
            text-align: center;
            color: #777777;
            font-size: 14px;
            border-top: 1px solid #e1e1e1;
        }
        .email-footer a {
            color: #007bff;
            text-decoration: none;
        }
        .email-footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <table role="presentation" cellspacing="0" cellpadding="0" width="100%" bgcolor="#f4f4f4">
        <tr>
            <td>
                <table role="presentation" cellspacing="0" cellpadding="0" class="email-container">
                    <!-- Header -->
                    <tr>
                        <td class="email-header">
                          🧑‍💻 {{ config('app.name') }} رسالة جديدة من 
                        </td>
                    </tr>

                    <!-- Body -->
                    <tr>
                        <td class="email-body">
                            <p> مرحباً   👋🏻 {{ $recipientName }} </p>
                            <p>{{ $messageContent }}</p>
                            <a href="mailto:{{ config('mail.from.address') }}?subject=رد على رسالتك" class="button">💬اتخذ إجراءً الآن</a>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td class="email-footer">
                            <p>🧑‍💻 {{ config('app.name') }} مع تحيات فريق</p>
                            <p>:للمزيد من المعلومات، تواصل معنا عبر البريد الإلكتروني</p>
                            <p>
                                <a href="https://www.facebook.com/info@darrebni.net" target="_blank">
                                 📧 info@darrebni.net 
                                </a>
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>