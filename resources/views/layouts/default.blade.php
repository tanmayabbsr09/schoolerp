<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <!-- CSRF Token -->
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>{{ config('app.name', 'Laravel') }}</title>
  @php 
    $templateBrand = 'public/adminLTE';
    $bowerComp = $templateBrand.'/bower_components/';
    $adminLTEdist = $templateBrand.'/dist/';
  @endphp
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.7 -->
  <link rel="stylesheet" href="{{ asset($bowerComp.'bootstrap/dist/css/bootstrap.min.css') }}">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="{{ asset($bowerComp.'font-awesome/css/font-awesome.min.css') }}">
  <!-- Ionicons -->
  <link rel="stylesheet" href="{{ asset($bowerComp.'Ionicons/css/ionicons.min.css') }}">
  <!-- Theme style -->
  <link rel="stylesheet" href="{{ asset($adminLTEdist.'css/AdminLTE.min.css') }}">
  <!-- AdminLTE Skins. Choose a skin from the css/skins
       folder instead of downloading all of them to reduce the load. -->
  <link rel="stylesheet" href="{{ asset($adminLTEdist.'css/skins/_all-skins.min.css') }}">
  <!-- Morris chart -->
  {{-- <link rel="stylesheet" href="{{ asset($bowerComp.'morris.js/morris.css') }}"> --}}
  <!-- jvectormap -->
  <link rel="stylesheet" href="{{ asset($bowerComp.'jvectormap/jquery-jvectormap.css') }}">
  <!-- Date Picker -->
  <link rel="stylesheet" href="{{ asset($bowerComp.'bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}">
  <!-- Daterange picker -->
  <link rel="stylesheet" href="{{ asset($bowerComp.'bootstrap-daterangepicker/daterangepicker.css') }}">
  <!-- bootstrap wysihtml5 - text editor -->
  <link rel="stylesheet" href="{{ asset($templateBrand.'/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css') }}">

  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->

  <!-- Google Font -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
</head>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

  {{--  INCLUDE HEADER PAGE --}}
  @include('layouts.includes.header')

  <!-- Left side column. contains the logo and sidebar -->
  {{--  INCLUDE HEADER PAGE --}}
  @include('layouts.includes.left-sidebar')

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->

    {{--  INCLUDE HEADER and Breadcrumb --}}
    @include('layouts.includes.header-n-breadcrumb')

    <!-- Main content -->
    <section class="content">
      {{-- Loading Contents Here --}}
      @yield('content')
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
  {{--  INCLUDE Footer PAGE --}}
  @include('layouts.includes.footer')

  <!-- Control Sidebar -->
  {{--  INCLUDE Control right Sidebar --}}
  @include('layouts.includes.control-right-sidebar')

  <!-- /.control-sidebar -->
  <!-- Add the sidebar's background. This div must be placed
       immediately after the control sidebar -->
  <div class="control-sidebar-bg"></div>
</div>
<!-- ./wrapper -->

<!-- jQuery 3 -->
<script src="{{ asset($bowerComp.'jquery/dist/jquery.min.js') }}"></script>
<!-- jQuery UI 1.11.4 -->
<script src="{{ asset($bowerComp.'jquery-ui/jquery-ui.min.js') }}"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
  $.widget.bridge('uibutton', $.ui.button);
</script>
<!-- Bootstrap 3.3.7 -->
<script src="{{ asset($bowerComp.'bootstrap/dist/js/bootstrap.min.js') }}"></script>
<!-- Morris.js charts -->
<script src="{{ asset($bowerComp.'raphael/raphael.min.js') }}"></script>
<script src="{{ asset($bowerComp.'morris.js/morris.min.js') }}"></script>
<!-- Sparkline -->
<script src="{{ asset($bowerComp.'jquery-sparkline/dist/jquery.sparkline.min.js') }}"></script>
<!-- jvectormap -->
<script src="{{ asset($templateBrand.'/plugins/jvectormap/jquery-jvectormap-1.2.2.min.js') }}"></script>
<script src="{{ asset($templateBrand.'/plugins/jvectormap/jquery-jvectormap-world-mill-en.js') }}"></script>
<!-- jQuery Knob Chart -->
<script src="{{ asset($bowerComp.'jquery-knob/dist/jquery.knob.min.js') }}"></script>
<!-- daterangepicker -->
<script src="{{ asset($bowerComp.'moment/min/moment.min.js') }}"></script>
<script src="{{ asset($bowerComp.'bootstrap-daterangepicker/daterangepicker.js') }}"></script>
<!-- datepicker -->
<script src="{{ asset($bowerComp.'bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>
<!-- Bootstrap WYSIHTML5 -->
<script src="{{ asset($templateBrand.'/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js') }}"></script>
<!-- Slimscroll -->
<script src="{{ asset($bowerComp.'jquery-slimscroll/jquery.slimscroll.min.js') }}"></script>
<!-- FastClick -->
<script src="{{ asset($bowerComp.'fastclick/lib/fastclick.js') }}"></script>
<!-- AdminLTE App -->
<script src="{{ asset($adminLTEdist.'js/adminlte.min.js') }}"></script>
<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
<script src="{{ asset($adminLTEdist.'js/pages/dashboard.js') }}"></script>
<!-- AdminLTE for demo purposes -->
<script src="{{ asset($adminLTEdist.'js/demo.js') }}"></script>


<!-- bootstrap datepicker -->
<script src="{{ asset($bowerComp.'/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>
<!-- bootstrap color picker -->
<script src="{{ asset($bowerComp.'/bootstrap-colorpicker/dist/js/bootstrap-colorpicker.min.js') }}"></script>
<!-- bootstrap time picker -->
<script src="{{ asset($templateBrand.'/plugins/timepicker/bootstrap-timepicker.min.js') }}"></script>

<!-- Select2 -->
<script src="{{ asset($bowerComp.'/select2/dist/js/select2.full.min.js') }}"></script>
<!-- InputMask -->
<script src="{{ asset($templateBrand.'/plugins/input-mask/jquery.inputmask.js') }}"></script>
<script src="{{ asset($templateBrand.'/plugins/input-mask/jquery.inputmask.date.extensions.js') }}"></script>
<script src="{{ asset($templateBrand.'/plugins/input-mask/jquery.inputmask.extensions.js') }}"></script>
<!-- iCheck 1.0.1 -->
<script src="{{ asset($templateBrand.'/plugins/iCheck/icheck.min.js') }}"></script>
<script type="text/javascript">
  $(function () {
   //iCheck for checkbox and radio inputs
    $('input[type="checkbox"].minimal, input[type="radio"].minimal').iCheck({
      checkboxClass: 'icheckbox_minimal-blue',
      radioClass   : 'iradio_minimal-blue'
    })
    //Red color scheme for iCheck
    $('input[type="checkbox"].minimal-red, input[type="radio"].minimal-red').iCheck({
      checkboxClass: 'icheckbox_minimal-red',
      radioClass   : 'iradio_minimal-red'
    })
    //Flat red color scheme for iCheck
    $('input[type="checkbox"].flat-red, input[type="radio"].flat-red').iCheck({
      checkboxClass: 'icheckbox_flat-green',
      radioClass   : 'iradio_flat-green'
    })

    //Date picker
    $('#datepicker').datepicker({
      autoclose: true,
      format: 'yyyy-mm-dd'
    });
    $('#datepicker-pre-past').datepicker({
      autoclose: true,
      format: 'yyyy-mm-dd',
      maxViewMode : 0
    });
    $('#datepicker-pre-future').datepicker({
      autoclose: true,
      format: 'yyyy-mm-dd'
    })


  });
</script>
<script type="text/javascript">
  $(document).ready(function(){
        $(document).on("focus", "#datepicker", function(){
            $(this).datepicker({
              autoclose: true,
              format: 'yyyy-mm-dd'
            });
        });

    $('.capsLock').on('keypress keyup', function() {
        var $this = $(this), value = $this.val();
        $this.val( value.toUpperCase() );
    });
    $('.alpha').bind('keypress', function (event){
        var regex = new RegExp("^[a-zA-Z ]+$");
        var key = String.fromCharCode(!event.charCode ? event.which : event.charCode);
        if(event.which == 8 || event.keyCode == 9 || event.keyCode == 46 || event.keyCode == 37 || event.keyCode == 38 || event.keyCode == 39 || event.keyCode == 40){
            return true;
        }
        if (!regex.test(key)) {
           event.preventDefault();
           return false;
        }
    });
    $('.alphaSpl').bind('keypress', function (event){
        var regex = new RegExp("/^[ A-Za-z0-9_@./#&+-]*$/");
        var key = String.fromCharCode(!event.charCode ? event.which : event.charCode);
        
        if (!regex.test(key)) {
           event.preventDefault();
           return false;
        }
    });
    $('.mobile').bind('keypress', function (event){
        var regex = new RegExp("^[0-9]+$");
        var key = String.fromCharCode(!event.charCode ? event.which : event.charCode);
        if(event.which == 8 || event.keyCode == 9 || event.keyCode == 46 || event.keyCode == 37 || event.keyCode == 38 || event.keyCode == 39 || event.keyCode == 40){
            return true;
        }
        if (!regex.test(key)) {
           event.preventDefault();
           return false;
        }
        var mob = $(this).val();

        if (mob.length >= 50) {
          event.preventDefault();
          return false;
        }
    });
    $('.nospecial').bind('keypress', function(e) {
        console.log( e.which );
        if($('.nospecial').val().length == 0){
            var k = e.which;
            var ok = k >= 65 && k <= 90 || // A-Z
                k >= 97 && k <= 122 || // a-z
                k >= 48 && k <= 57; // 0-9

            if (!ok){
                e.preventDefault();
            }
        }
    });
  });
</script>
<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-109667049-1"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-109667049-1');
</script>

@yield('extra-javascript')
</body>
</html>
