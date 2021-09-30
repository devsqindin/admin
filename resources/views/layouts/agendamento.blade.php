<!DOCTYPE html>
<html lang="en">
<head>
  <title>Desbankei</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
  <link rel="stylesheet" href="{{asset('dist/css/circular-std.css')}}">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.maskedinput/1.4.1/jquery.maskedinput.min.js" integrity="sha512-d4KkQohk+HswGs6A1d6Gak6Bb9rMWtxjOa0IiY49Q3TeFd5xAzjWXDCBW9RS7m86FQ4RzM2BdHmdJnnKRYknxw==" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.2/jquery.validate.min.js" integrity="sha512-UdIMMlVx0HEynClOIFSyOrPggomfhBKJE28LKl8yR3ghkgugPnG6iLfRfHwushZl1MOPSY6TsuBDGPK2X4zYKg==" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.2/additional-methods.min.js" integrity="sha512-6Uv+497AWTmj/6V14BsQioPrm3kgwmK9HYIyWP+vClykX52b0zrDGP7lajZoIY1nNlX4oQuh7zsGjmF7D0VZYA==" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.2/localization/messages_pt_BR.min.js" integrity="sha512-+FCfxJrlkCXOsuGOQ48Dc2at83izZqTjDgLjUWD5VhRZe6AHkIWy4EvmcVEir4xNGTZ0g8Au3fyV3NbkbVJvIA==" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.27.0/moment.min.js" integrity="sha512-rmZcZsyhe0/MAjquhTgiUcb4d9knaFc7b5xAfju483gbEXTkeJRUMIPk6s3ySZMYUHEcjKbjLjyddGWMrNEvZg==" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js" integrity="sha512-T/tUfKSV1bihCnd+MxKD0Hm1uBBroVYBOYSk1knyvQ9VyZJpc/ALb4P0r6ubwVPSGB2GvjeoMAJJImBG12TiaQ==" crossorigin="anonymous"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css" integrity="sha512-mSYUmp1HYZDFaVKK//63EcZq4iFWFjxSL+Z3T/aCt4IO9Cejm03q3NKKYN6pFQzY0SBOr8h+eCIAZHPXcpZaNw==" crossorigin="anonymous" />
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/locales/bootstrap-datepicker.pt-BR.min.js" integrity="sha512-mVkLPLQVfOWLRlC2ZJuyX5+0XrTlbW2cyAwyqgPkLGxhoaHNSWesYMlcUjX8X+k45YB8q90s88O7sos86636NQ==" crossorigin="anonymous"></script>
  @stack('css')
  <style type="text/css">
    .bt1 {
      width: 90%;
      border-radius: 10px;
      padding: 20px 0;
      border: 0;
      text-align: center;
      background: #1d1de8;
      font-size: 18px;
      display: block;
      margin: 0 auto;
      font-weight: 800;
      color: #fff;
    }
    .dvcenter {
      width: 95%;
      -webkit-box-shadow: 1px 1px 10px 0px rgba(0,0,0,0.75);
      -moz-box-shadow: 1px 1px 10px 0px rgba(0,0,0,0.75);
      box-shadow: 1px 1px 10px 0px rgba(0,0,0,0.75);
    }
    input {
      border: none !important;
    }
    * {
      font-family: 'CircularStd' !important;
    }
  </style>
</head>
<body style="overflow-y:hidden;overflow-x:hidden">
  
<div class="container-fluid" style="background-color:#1d1de8;padding:7px 0;">
  <p>&nbsp;</p>
  <p align="center">
    <img src="/mini-logo.png" style="height:30px;">
  </p>
</div>
<div style='background-color:#eee;height:92vh' class='pt-3 overflow-auto'>
    @yield('content')
    <div class='text-center mt-5'>
       {{-- img --}}
    </div>
</div>
@stack('js')
</body>
</html>
