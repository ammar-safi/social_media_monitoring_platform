<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Document</title>
    </head>

    <style>
        body {
            margin : 5px;
        }
    </style>

    <body>
        <table role="presentation" cellspacing="0" cellpadding="0" width="100%" bgcolor="#f4f4f4">
            <tr>
                <td>
                    <table role="presentation" cellspacing="0" cellpadding="0" class="email-container">
                        <!-- Header -->
                        <tr>
                            <td class="email-header">
                                <h1>
                                    A new message from {{ config('app.name') }}
                                </h1>
                            </td>
                        </tr>

                        <!-- Body -->
                        <tr>
                            <td class="email-body">
                                <h3>
                                    <p> Hello {{ $recipientName }}</p>
                                    <p>{{ $messageContent }}</p>
                                </h3>
                            </td>
                        </tr>

                        <!-- Footer -->
                        <tr>
                            <td class="email-footer">
                                {{-- <p>{{ config('app.name') }} مع تحيات فريق</p> --}}
                                <p>for more info , contact us : </p>
                                <p>
                                    ammar.ahmed.safi@gmail.com.net
                                </p>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </body>

</html>
