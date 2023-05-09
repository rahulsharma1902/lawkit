<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <title>Lawkit | Panel de control</title>
    <!-- plugins style -->
    <!--<link href="https://fonts.googleapis.com/css?family=IBM+Plex+Sans:400,600" rel="stylesheet"> -->
    <link href="<?=base_url();?>assets/css/animate.min.css" rel="stylesheet">
    <link href="<?=base_url();?>assets/css/bs.min.css" rel="stylesheet">
    <link href="<?=base_url();?>assets/plugins/sweet/sweetalert2.min.css" rel="stylesheet">
    <!--<link href="<?=base_url();?>assets/css/sb-admin.min.css" rel="stylesheet"> -->
    <link rel="stylesheet" href="<?=base_url();?>assets/plugins/datepicker/css/datepicker.css">
    <link href="<?=base_url();?>assets/plugins/font-awesome4.7/css/font-awesomeCustom.css" rel="stylesheet">
  <!-- plugins js -->
    <script src="<?=base_url();?>assets/js/jquery.min.js"></script>
    <script src="<?=base_url();?>assets/js/popper.min.js"></script>
    <script src="<?=base_url();?>assets/js/bs.min.js"></script>
    <script src="<?=base_url();?>assets/js/wow.js"></script>
    <script src="<?=base_url();?>assets/js/feather.min.js"></script>
    <script src="<?=base_url();?>assets/plugins/sweet/sweetalert2.min.js"></script>
    <script type="text/javascript" src="<?=base_url();?>assets/plugins/mask/mask.js"></script>
    <script type="text/javascript" src="<?=base_url();?>assets/plugins/datepicker/js/bootstrap-datepicker.js"></script>
    <script type="text/javascript" src="<?=base_url();?>assets/plugins/counterup/waypoints.min.js"></script>
    <script type="text/javascript" src="<?=base_url();?>assets/plugins/counterup/jquery.counterup.min.js"></script>
    
    <!-- main -->
    <link rel="stylesheet" href="<?=base_url();?>assets/css/style.css?r=<?=rand();?>">
<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-D7L8XQZ668"></script>
<script type="text/javascript">
  $(document).ready(function() {

    /* Redirect to specific tab base on the request URI hash argument */
    if(window.location.hash != "") {
      console.log(window.location.hash);
      $('a[href="' + window.location.hash + '"]').click()
      window.scrollTo(0, 0);
    }

    $('.my-account-col a').each(function(){
      $(this).on('click', function(){
        $('#myTab a[href="#' + $(this).attr('tab') + '"]').tab('show');
      });
    });

  });

  $(function () {
    $('[data-toggle="tooltip"]').tooltip()
  });
</script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-D7L8XQZ668');
</script>
</head>
<body>
    <?php echo $this->session->flashdata('toast'); ?>

<nav class="nav__">
   <div class="nav__container">
      <a href="<?=base_url();?>account/dashboard">
      <img src="<?=base_url();?>assets/images/lawkit-logo-horizontal.png" width="120" loading="lazy" class="filter-invert-1">
      </a>
      <label class="nav__label" for="menu">
      <i data-feather="menu"></i>
      </label>
      <input id="menu" type="checkbox" class="nav__input"/>
      <div class="nav__menu">
         <a class="navbar-brand hide-mobile" href="<?=base_url();?>account/dashboard">
         <img src="<?=base_url();?>assets/images/lawkit-logo-horizontal.png" width="120" loading="lazy" class="filter-invert-1">
         </a>
         <a href="https://buscador.lawkit.mx" class="nav__item">Buscador</a>
         <a href="https://calendario.lawkit.mx" class="nav__item">Calculadora</a>
         <div class="vertical-divider"></div>
         <div class="dropdown__ dropdown-toggle">
            <a href="#" id="userDropdown" role="button">
            <span style="margin-right: 2px;">
            <?=$this->session->userdata('fname') . " " . $this->session->userdata('lname');?>
            </span>
            <?php if(empty($this->session->userdata('photo'))):?>
            <img class="img-profile rounded-circle" src="<?=base_url('assets/images/user-default.png');?>" id="photo-user" width="28" height="28">
            <?php else:?>
            <img class="img-profile rounded-circle" src="<?=base_url();?>assets/images/photo_customers/<?=$this->session->userdata('photo');?>" width="28" height="28">
            <?php endif; ?>
            </a>
            <div class="dropdown-content">
               <div class="row">
                  <div class="col-md-12">
                     <h6>Mi cuenta</h6>
                     <a class="dropdown-item link" tab="myaccount" href="<?=base_url('account/dashboard');?>">Mi cuenta</a>
                     <a class="dropdown-item link" tab="mypayments" href="<?=base_url('account/dashboard');?>">Mis pagos</a>
                     <a class="dropdown-item link" tab="accountusage" href="<?=base_url('account/dashboard');?>">Mis busquedas</a>
                     <a class="dropdown-item link" href="https://calendario.lawkit.mx/accounts/me">Mis términos</a>
                     <a class="dropdown-item link" href="<?=base_url();?>account/logout">Cerrar sesión</a>
                  </div>
               </div>
            </div>
         </div>
         <div class="nav__icons">
            <li class="nav-item">
               <a id="dark-mode-li" class="nav-link" href="#" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Modo oscuro">
               <i id="icon-darkmode" class="fa fa-moon-o"></i>
               </a>
            </li>
            <li class="nav-item" style="display: none;">
               <a href="#" id="mode-remover" class="nav-link hidden start-tooltip" data-toggle="tooltip" data-placement="top" title="Volver a modo normal" style="padding: 0px;">
               </a>
            </li>
            <li class="nav-item">
               <a id="support" class="nav-link" href="#" data-toggle="tooltip" data-placement="top" title="Dudas o sugerencias" data-target="#modal-dou-sugg" >
               <i class="fa fa-question-circle-o"></i>
               </a>
            </li>
         </div>
      </div>
   </div>
</nav>

  <!-- Page Wrapper -->
  <div id="wrapper">

    <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column">

      <!-- Main Content -->
      <div id="content">

        <!-- Dark mode script file -->
        <script src="<?=base_url();?>assets/plugins/darkmode/darkmode.js"></script>
        
        <!-- Dark mode initialization -->
         <script>
            // Plugin Initialization
            var options = {
              light: '<?=base_url();?>assets/css/style.css?r=<?=rand();?>',
              dark: '<?=base_url();?>assets/css/dark.css',
            }
            var DarkMode = new DarkMode(options)

            // Remove mode from LocalStorage if button clicked
            var ModeRemover = document.getElementById('mode-remover')
            ModeRemover.onclick = function() {
              DarkMode.clearSavedMode()
              changeTogglerText()
              getModeRemoverState()
            }

            // Detects mode on LocalStorage, if `true` – display a button
            getModeRemoverState()
            function getModeRemoverState() {
              if(DarkMode.isModeSaved())
                ModeRemover.classList.remove('hidden')
              else
                ModeRemover.classList.add('hidden')
            }
            
            // Function for `mode-toggler` button
            var ModeToggler = document.getElementById('dark-mode-li')
            changeTogglerText()
            ModeToggler.onclick = function() {
              DarkMode.toggleMode()
              changeTogglerText()
            }
            
            // Changes `mode-toggler` text on mode changing
            function changeTogglerText() {
              getModeRemoverState()
              var currentMode = DarkMode.getMode()
              if (currentMode == 'light') {
                $('#icon-darkmode').removeClass();
                $('#icon-darkmode').attr('class', 'fa fa-moon-o');
                 $('#dark-mode-li').attr('data-original-title', 'Modo oscuro');
              } else {
                $('#icon-darkmode').removeClass();
                $('#icon-darkmode').attr('class', 'fa fa-sun-o');
                $('#dark-mode-li').attr('data-original-title', 'Modo claro');
              }
              
            }

            // Hide tooltipe on click itself. A minor bug was keeping the tooltip visible after clicking in the <a>.
            $('[data-toggle="tooltip"]').on('click', function () {
                $(this).tooltip('hide')
            })

            /* Action to open support modal */
            $('a#support').on('click', function(){
              $('#support-modal').modal('show');
            });

            $('li.dropdown').hover(
                function() { $(this).addClass('show'); $(this).find('[data-toggle="dropdown"]').attr('aria-expanded', true); $(this).find('.dropdown-menu').addClass('show'); },
                function() { $(this).removeClass('show'); $(this).find('[data-toggle="dropdown"]').attr('aria-expanded',false); $(this).find('.dropdown-menu').removeClass('show'); }
            );
        </script>

        <div class="container-fluid" style="margin-bottom: 15vh;">
          <div class="modal fade" id="support-modal" tabindex="-1">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title">Comentarios</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                <div class="modal-body">
                  <form id="frm-commments">
                    <div class="form-group">
                      <label class="">Mándanos tus comentarios o sugerencias y el equipo de Lawkit lo revisará a la brevedad.</label>
                      <textarea class="form-control lawkit-input" name="ds_description" id="ds_description" rows="3"></textarea>
                      <input type="hidden" id="comm_cus_id" name="comm_cus_id">
                    </div>
                    <div class="row mt-4 justify-content-end">
                      <div class="col-md-4">
                        <button type="button" class=" lawkit-btn btn-danger" style="width: 100%" data-dismiss="modal">Cancelar</button>
                      </div>
                      <div class="col-md-4">
                        <button type="button" class="lawkit-btn bg-lk-blue-o2 btn-comment" style="width: 100%">Enviar</button>
                      </div>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>
