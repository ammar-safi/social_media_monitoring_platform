<!DOCTYPE html>
<html>

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
