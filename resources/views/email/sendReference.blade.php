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
      <h4> Total Payment: <h5 style="color:blue"> {{ $payment }} </h5></h4>
      <h4> Reference Number: <h5 style="color:blue"> {{ $referenceNum }} </h5></h4>
      <h4> Php Wallet Address: <h5 style="color:blue"> 3MnZhA8FHNAKGGYwit4HReBA5ZABDD3An8 </h5></h4>
    <b style="color:green;line-height: 35px;text-align: center;font-family: sans-serif;"> Good Day!We send you the above details for your reference on paying us through Coins.ph regarding to the items you ordered from iPasalubongPH!</b>
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
