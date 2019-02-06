<!DOCTYPE html>
<html>
  <head>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <script src="{{ asset('js/bootstrap.min.js') }}"></script>  
  </head>
  <body>
    <h1> Hello {{ $email }} from iPasalubongPH </h1>
    <br>
    <h3 style="color: blue"> This is your code <b style="color:red"> "{{ $code }}"</b> for your signup as a seller.</h3>
    <br><br>
    Thank you !
    <br><br>
    Regards,<br>
    iPasalubongPH
</body>
</html>
