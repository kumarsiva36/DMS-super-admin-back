<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>OTP Mail</title>
    <link href='https://fonts.googleapis.com/css?family=Poppins' rel='stylesheet'>
    <style>
        @import url('https://fonts.googleapis.com/css?family=Poppins');
        .wrapper{width:40%}
        @media(max-width:768px)
        {
            .wrapper{width:100%}
        }
        body div p span a{
            font-family: 'Poppins' !important;
        }
        .center {
            display: block;
            margin-left: auto;
            margin-right: auto;
            width: 59px;
            height: 59px;
        }
    </style>
</head>
<body style="font-family: poppins,sans-serif;">
    <div style="min-width:1000px;overflow:auto;line-height:2">
        <div class="wrapper" style="margin:50px 0;padding:20px 0">
          <div >
            <a href="{{ config('app.logo_url') }}" style="font-size:1.4em;color: #00466a;text-decoration:none;font-weight:600">
                <img src="{{ $message->embed(public_path().'/images/DMS-Logo.png') }}" width="125px" style="margin-bottom: 5px;">
            </a>
            {{-- <img src="{{ asset('images/DMS-Logo.png') }}" width="125px" style="margin-bottom: 5px;"> --}}
          </div>
          <div style="border:1px solid #eee; padding: 0px 10px 05px 10px;  ">
            <p style="font-size:18px;color: #188676; font-weight: 600">Dear User,</p>
            <div>
                {!! $details['content'] !!}
            </div>
        </div>
        <p style="font-size:0.9em; margin-bottom: 0px">{{ trans('WebSite.ThankYou') }}</p>
        <p style="margin-top: 0px; font-weight: bold;" >{{ trans('WebSite.MailSignature') }}</p>
        </div>
      </div>
</body>
</html>
