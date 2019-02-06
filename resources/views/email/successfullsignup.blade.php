<!DOCTYPE html>
<html>
  <head>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <script src="{{ asset('js/bootstrap.min.js') }}"></script>  
  </head>
  <body>
    <h3> Hello {{ $email }} from iPasalubongPH </h3>
    <br>
    <span>
      <h4> Subscription Payment: <h5 style="color:blue"> 3000 </h5></h4>
      <h3> Php Wallet Address: <h5 style="color:blue"> 3MnZhA8FHNAKGGYwityHReBA5ZABDD3An8 </h3></h3>
    <b style="color:green;line-height: 35px;text-align: center;font-family: sans-serif;"> Good Day!We received your request to subscribe as a seller. This is how you pay to us on Coins.ph for your account to be activated!</b>
		<br>
      <img src="{{ $message->embed('http://192.168.83.2/piggypenny/public/storage/mailAttach/guide.png') }}" width="200" height="200" style="display: block;margin-right: auto;margin-left: auto;width: 50%">
    </span>
    <br><br>
    Thank you !
    <br><br>
    Regards,<br>
    iPasalubongPH
</body>
</html>
