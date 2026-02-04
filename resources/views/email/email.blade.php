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
        table.tr {
            text-align: center
        }
    </style>

    <body>
        <table width="100%" >
            <tr>
                <td>
                    <table class="email-container">
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
                                <h2>
                                    <b> Hello {{ $recipientName }}</b>
                                </h2>
                                <h3>{{ $messageContent }}</h3>
                            </td>
                        </tr>

                        <!-- Footer -->
                        <tr>
                            <td class="email-footer">
                                <p>for more info , contact us : </p>
                                <p>
                                    ammar.ahmed.safi@gmail.com
                                </p>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </body>

</html>
