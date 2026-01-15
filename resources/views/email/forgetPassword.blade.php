<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subjectTitle }}</title>
    <style>
        body {
            /* font-family: Arial, sans-serif; */
            /* margin: 0; */
            /* padding: 0; */
            /* background-color: #f4f4f4;  */
            display: flex;
            justify-content: center;
            /* align-items: center; */
            /* height: 100vh;  */

            border-style: solid;
            border-width: thin;
            border-color: #dadce0;
            border-radius: 8px;
            padding: 40px 20px;
            
        }
        /*
        .email-container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
            width: 80%;
            max-width: 600px;
        }
        h1 {
            font-size: 24px;
            color: #333;
        }
        img {
            max-width: 100%;
            height: auto;
            margin: 20px 0;
            border-radius: 8px;
        }
        p {
            font-size: 16px;
            color: #666;
        } */
    </style>
</head>
<body>

    <div class="email-container">
        <h1>{{ $subjectTitle }}</h1>

        <p>{{ $description }}</p>
        <h1>{{ $otp }}</h1>
        
        {{-- <img src="{{ $imageUrl }}" alt="Image"> --}}
    </div>

</body>
</html>
