@php
$qtdNotificacoes = 10;
$avatar = URL::to('/').'/avatar.png';
$logged = Auth::user();
if ($logged) {
  $nomeUsuario = $logged->nome;
} else {
  $nomeUsuario = '';
}
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta http-equiv="x-ua-compatible" content="ie=edge">

  <title>{{$nomeTela}} | Qindin</title>

  <!-- Font Awesome Icons -->
  <link rel="stylesheet" href="{{asset('plugins/fontawesome-free/css/all.min.css')}}">
  <!-- IonIcons -->
  <link rel="stylesheet" href="http://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="{{asset('dist/css/adminlte.min.css')}}">
  <link rel="stylesheet" href="{{asset('plugins/crud.css')}}">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css" integrity="sha512-mSYUmp1HYZDFaVKK//63EcZq4iFWFjxSL+Z3T/aCt4IO9Cejm03q3NKKYN6pFQzY0SBOr8h+eCIAZHPXcpZaNw==" crossorigin="anonymous" />
  <!-- Google Font: Source Sans Pro -->
  <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  @stack('css')
  <style type="text/css">
    .main-sidebar {
      background-color: #1717ed !important;
    }
    .mt-2 .nav-link {
      color: #fff !important;
    }
  </style>
</head>
<!--
BODY TAG OPTIONS:
=================
Apply one or more of the following classes to to the body tag
to get the desired effect
|---------------------------------------------------------|
|LAYOUT OPTIONS | sidebar-collapse                        |
|               | sidebar-mini                            |
|---------------------------------------------------------|
-->
<body class="hold-transition sidebar-mini">
<div class="wrapper">
  <!-- Navbar -->
<!--   <nav class="main-header navbar navbar-expand navbar-white navbar-light"> -->
    <!-- Left navbar links -->
<!--     <ul class="navbar-nav"> -->
<!--       <li class="nav-item"> -->
<!--         <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a> -->
<!--       </li> -->
<!--       <li class="nav-item d-none d-sm-inline-block">
        <a href="/admin/clientes" class="nav-link">Home</a>
      </li> -->
<!--       <li class="nav-item d-none d-sm-inline-block">
        <a href="/admin/logout" class="nav-link">Sair</a>
      </li> -->
<!--     </ul> -->

    <!-- SEARCH FORM -->
<!--     <form class="form-inline ml-3">
      <div class="input-group input-group-sm">
        <input class="form-control form-control-navbar" type="search" placeholder="Buscar" aria-label="Search">
        <div class="input-group-append">
          <button class="btn btn-navbar" type="submit">
            <i class="fas fa-search"></i>
          </button>
        </div>
      </div>
    </form> -->

    <!-- Right navbar links -->
<!--     <ul class="navbar-nav ml-auto"> -->
      <!-- Messages Dropdown Menu -->
<!--       <li class="nav-item dropdown">
        <a class="nav-link" data-toggle="dropdown" href="#">
          <i class="far fa-comments"></i>
          <span class="badge badge-danger navbar-badge">3</span>
        </a>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
          <a href="#" class="dropdown-item">
            <!-- Message Start 
            <div class="media">
              <img src="dist/img/user1-128x128.jpg" alt="User Avatar" class="img-size-50 mr-3 img-circle">
              <div class="media-body">
                <h3 class="dropdown-item-title">
                  Brad Diesel
                  <span class="float-right text-sm text-danger"><i class="fas fa-star"></i></span>
                </h3>
                <p class="text-sm">Call me whenever you can...</p>
                <p class="text-sm text-muted"><i class="far fa-clock mr-1"></i> 4 Hours Ago</p>
              </div>
            </div>
            <!-- Message End 
          </a>
          <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item">
            <!-- Message Start 
            <div class="media">
              <img src="dist/img/user8-128x128.jpg" alt="User Avatar" class="img-size-50 img-circle mr-3">
              <div class="media-body">
                <h3 class="dropdown-item-title">
                  John Pierce
                  <span class="float-right text-sm text-muted"><i class="fas fa-star"></i></span>
                </h3>
                <p class="text-sm">I got your message bro</p>
                <p class="text-sm text-muted"><i class="far fa-clock mr-1"></i> 4 Hours Ago</p>
              </div>
            </div>
            <!-- Message End 
          </a>
          <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item">
            <!-- Message Start 
            <div class="media">
              <img src="dist/img/user3-128x128.jpg" alt="User Avatar" class="img-size-50 img-circle mr-3">
              <div class="media-body">
                <h3 class="dropdown-item-title">
                  Nora Silvester
                  <span class="float-right text-sm text-warning"><i class="fas fa-star"></i></span>
                </h3>
                <p class="text-sm">The subject goes here</p>
                <p class="text-sm text-muted"><i class="far fa-clock mr-1"></i> 4 Hours Ago</p>
              </div>
            </div>
            <!-- Message End 
          </a>
          <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item dropdown-footer">See All Messages</a>
        </div>
      </li> -->
      
      <!-- Messages Dropdown Menu -->
<!--       <li class="nav-item dropdown">
        <a class="nav-link" data-toggle="dropdown" href="#">
          Olá, <b>{{$nomeUsuario}}</b>! =) -->
<!--           <span class="badge badge-danger navbar-badge">3</span> -->
<!--         </a> -->
<!--         <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
          <a href="#" class="dropdown-item">
            <!-- Message Start 
            
            <!-- Message End 
          </a>
          <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item dropdown-footer">See All Messages</a>
        </div>
      </li> -->
      
      <!-- Notifications Dropdown Menu -->
<!--       <li class="nav-item dropdown">
        <a class="nav-link" data-toggle="dropdown" href="#">
          <i class="far fa-bell"></i>
          <span class="badge badge-warning navbar-badge">{{$qtdNotificacoes}}</span>
        </a>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
          <span class="dropdown-item dropdown-header">{{$qtdNotificacoes}} Notificações</span>
          <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item">
            <i class="fas fa-envelope mr-2"></i> 4 new messages
            <span class="float-right text-muted text-sm">3 mins</span>
          </a>
          <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item">
            <i class="fas fa-users mr-2"></i> 8 friend requests
            <span class="float-right text-muted text-sm">12 hours</span>
          </a>
          <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item">
            <i class="fas fa-file mr-2"></i> 3 new reports
            <span class="float-right text-muted text-sm">2 days</span>
          </a>
          <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item dropdown-footer">See All Notifications</a>
        </div>
      </li>
      <li class="nav-item">
        <a class="nav-link" data-widget="control-sidebar" data-slide="true" href="#" role="button"><i
            class="fas fa-th-large"></i></a>
      </li> -->
    </ul>
  </nav>
  <!-- /.navbar -->

  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="/" class="brand-link">
<!--        <img src="{{asset('/mini-logo.png')}}" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8"> -->
      <p align="center" style="margin:0;"><span class="brand-text font-weight-light"><img src="{{asset('/mini-logo.png')}}" style="height:40px;margin:20px 0;"></span></p>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar user panel (optional) -->
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
<!--         <div class="image">
          <img src="{{$avatar}}" class="img-circle elevation-2" alt="User Image">
        </div> -->
        <div class="info">
          <a href="#" class="d-block" style="color:#fff;">Olá, <b>{{$nomeUsuario}}</b>! =)</a>
        </div>
      </div>

      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
<!--           <li class="nav-item">
            <a href="{{URL::to('/admin/dashboard')}}" class="nav-link">
              <i class="nav-icon fas fa-th"></i>
              <p>
                Inicial
              </p>
            </a>
          </li> -->
          @if(Auth()->user()->temPermissao('clientes','acesso'))
          <li class="nav-item">
            <a href="{{URL::to('/admin/clientes')}}" class="nav-link">
              <i class="nav-icon fa fa-heart" aria-hidden="true"></i>
              <p>
                Clientes
              </p>
            </a>
          </li>
          @endif
          @if(Auth()->user()->temPermissao('operadores','acesso'))
          <li class="nav-item">
            <a href="{{URL::to('/admin/operadores')}}" class="nav-link">
              <i class="nav-icon fa fa-user" aria-hidden="true"></i>
              <p>
                Operadores
              </p>
            </a>
          </li>
          @endif
          @if(Auth()->user()->temPermissao('motivos','acesso'))
          <li class="nav-item">
            <a href="{{URL::to('/admin/motivos')}}" class="nav-link">
              <i class="nav-icon fa fa-envelope" aria-hidden="true"></i>
              <p>
                Motivos
              </p>
            </a>
          </li>
          @endif
          @if(Auth()->user()->temPermissao('faq','acesso'))
          <li class="nav-item">
            <a href="{{URL::to('/admin/perguntas')}}" class="nav-link">
              <i class="nav-icon fa fa-question-circle" aria-hidden="true"></i>
              <p>
                FAQ Aplicativo
              </p>
            </a>
          </li>
          @endif
          @if(Auth()->user()->temPermissao('taxas','acesso'))
          <li class="nav-item">
            <a href="{{URL::to('/admin/taxas')}}" class="nav-link">
              <i class="nav-icon fa fa-credit-card" aria-hidden="true"></i>
              <p>
                Taxas e Tarifas
              </p>
            </a>
          </li>
          @endif
          <li class="nav-item" style="position: fixed;bottom: 20px;">
            <a href="{{URL::to('/admin/logout')}}" class="nav-link">
              <i class="nav-icon fa fa-power-off" aria-hidden="true"></i>
              <p>
                Sair
              </p>
            </a>
          </li>
          {{-- <li class="nav-header">LABELS</li>
          <li class="nav-item">
            <a href="#" class="nav-link">
              <i class="nav-icon far fa-circle text-danger"></i>
              <p class="text">Important</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="#" class="nav-link">
              <i class="nav-icon far fa-circle text-warning"></i>
              <p>Warning</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="#" class="nav-link">
              <i class="nav-icon far fa-circle text-info"></i>
              <p>Informational</p>
            </a>
          </li> --}}
        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper" style="padding: 2%; background:#fff;">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0 text-dark" style="font-size:36px;"><b>{{$nomeTela}}</b></h1>
            <ol class="breadcrumb">
              <li class="breadcrumb-item">Home</li>
              <li class="breadcrumb-item active">{{$nomeTela}}</li>
            </ol>
          </div><!-- /.col -->
<!--           <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">{{$nomeTela}}</li>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <div class="content">
    @yield('content')
    </div>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->
  </aside>
  <!-- /.control-sidebar -->

  <!-- Main Footer -->
  <footer class="main-footer">
    <strong>Copyright &copy; <?php echo date("Y") ?> <a href="#">Qindin</a>.</strong>
    Todos os direitos reservados.
    <div class="float-right d-none d-sm-inline-block">
      <b>Versão</b> 1.1.3
    </div>
  </footer>
</div>
<!-- ./wrapper -->

<!-- REQUIRED SCRIPTS -->

<!-- jQuery -->
<script src="{{asset('plugins/jquery/jquery.min.js')}}"></script>
<!-- Bootstrap -->
<script src="{{asset('plugins/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
<!-- AdminLTE -->
<script src="{{asset('dist/js/adminlte.js')}}"></script>

<!-- OPTIONAL SCRIPTS -->
<script src="{{asset('plugins/chart.js/Chart.min.js')}}"></script>
<script src="{{asset('dist/js/demo.js')}}"></script>
<script src="{{asset('dist/js/pages/dashboard3.js')}}"></script>
<script src="{{asset('plugins/simple-mask-money.js')}}"></script>
<script src="{{asset('plugins/moeda2.js')}}"></script>
<script src="{{asset('plugins/moment-with-locales.min.js')}}"></script>
@stack('js')
</body>
</html>
