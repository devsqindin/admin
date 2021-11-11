<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Qindin | Log in</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Font Awesome -->
<!--   <link rel="stylesheet" href="{{asset('plugins/fontawesome-free/css/all.min.css')}}"> -->
  <!-- Ionicons -->
<!--   <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css"> -->
  <!-- icheck bootstrap -->
<!--   <link rel="stylesheet" href="{{asset('plugins/icheck-bootstrap/icheck-bootstrap.min.css')}}"> -->
  <!-- Theme style -->
<!--   <link rel="stylesheet" href="{{asset('dist/css/adminlte.min.css')}}"> -->
  
  <!-- Google Font: Source Sans Pro -->
  <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
  
  <!-- Vini -->
  <link rel="stylesheet" href="{{asset('dist/css/reset.css')}}">
  <link rel="stylesheet" href="{{asset('dist/css/style.css')}}">
</head>
<body>
<div class="login">
<!--   <div class="login-logo" style="background-color:#0062cc;"> -->
<!--   <a href="{{URL::to('/')}}/admin"><img src="{{asset('/mini-logo.png')}}" style="height:20px;"></a> -->
<!--   </div> -->
  <!-- /.login-logo -->
  <img src="{{asset('/qindin-simbolo.png')}}" width="50">
  <div class="box">
      <h1>É bom te ver de volta!</h1>

      <form action="{{URL::to('/')}}/admin/login" method="post">
        <div class="input-group mb-3">
          <input type="email" name="email" class="input" placeholder="ID">
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-envelope"></span>
            </div>
          </div>
        </div>
        <div class="input-group mb-3">
          <input type="password" name="password" class="input" placeholder="Senha">
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-lock"></span>
            </div>
          </div>
        </div>

          <!-- /.col -->
          <div class="col-12">
            <button type="submit" class="submit">Login</button>
          </div>
          <!-- /.col -->

        @csrf
      </form>
      <!-- /.social-auth-links -->

     
    </div>
  </div>
  <div class="info">
  <img src="{{asset('/bkg.jpg')}}" width="120%">
    <!-- /.login-card-body -->

<!-- /.login-box -->

<!-- jQuery -->
<script src="{{asset('plugins/jquery/jquery.min.js')}}"></script>
<!-- Bootstrap 4 -->
<script src="{{asset('plugins/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
<!-- AdminLTE App -->
<script src="{{asset('dist/js/adminlte.min.js')}}"></script>

</body>
</html>