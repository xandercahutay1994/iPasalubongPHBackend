<!DOCTYPE html>
<html>
  <head>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <script src="{{ asset('js/bootstrap.min.js') }}"></script>  
  </head>
  <body>
    <h3> Hello {{ $email }} from iPasalubongPH </h3>
    <br>
    @if ($type === 'ac')
      <h3 class='text-primary'> Your account is now successfully {{ $type }}tivated!Please go to iPasalubongPH.ph and login your account.</h3>
    @else
      <h3 class='text-primary'> Your account is now successfully {{ $type }}tivated!Please contact us if you want to activate it again.</h3>
    @endif
    <br><br>
    Thank you !
    <br><br>
    Regards,<br>
    iPasalubongPH
</body>
</html>
