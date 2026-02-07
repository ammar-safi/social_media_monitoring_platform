<!DOCTYPE html>
<html>

    <head>
        <style>
            .otp {
                font-size: 4rem;
                font-weight: bold;
                width: 100%;
                text-align: center;
                margin-top: 20px;
            }
        </style>
    </head>

    <body>
        <table>
            {{-- 
            
                --- Body --
            
            --}}
            <tr>
                <td>
                    <p style="font-size:1.2em ">
                        <b>
                            Hello {{ $_user_name }}

                            <br>

                            {{ $_message }}
                        </b>

                    <div class="otp">
                        <p>
                            {{ $_otp }}
                        </p>
                    </div>
                    </p>
                </td>
            </tr>

            {{-- 
            
                --- Footer --
            
            --}}

            <tr>
                <td>
                    <p style="color: gray">
                        from : {{ $_sender }}
                        <br>
                        for more info , contact us :
                        <br>
                        ammar.ahmed.safi@gmail.com
                    </p>
                </td>
            </tr>

        </table>
    </body>

</html>
