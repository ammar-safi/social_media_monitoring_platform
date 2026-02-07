<!DOCTYPE html>
<html>

    <body>
        <table>
            {{-- 
            
                --- Header --
            
            --}}
            <tr>
                <td>
                    <h1>
                        A new message from {{ $sender }}
                    </h1>
                </td>
            </tr>

            {{-- 
            
                --- Body --
            
            --}}
            <tr>
                <td>
                    <p style="font-size:1.2em ">
                        <b>
                            Hello {{ $user_name }} , {{ $message }}
                        </b>
                    </p>
                </td>
            </tr>

            {{-- 
            
                --- Footer --
            
            --}}

            <tr>
                <td>
                    <p style="color: gray">
                        for more info , contact us :
                        <br>
                        ammar.ahmed.safi@gmail.com
                    </p>
                </td>
            </tr>

        </table>
    </body>

</html>
