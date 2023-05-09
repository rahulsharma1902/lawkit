<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link href="<?=base_url();?>assets/lawkit/css/style.css" rel="stylesheet">
    <title>Login</title>
  </head>
   <!-- plugins style -->

   <link href="https://fonts.googleapis.com/css?family=IBM+Plex+Sans:400,600" rel="stylesheet">

<link rel="stylesheet" href="<?=base_url();?>assets/plugins/sweet/sweetalert2.min.css">

<!-- plugins js -->

<script src="<?=base_url();?>assets/js/jquery.min.js"></script>

<script src="<?=base_url();?>assets/js/popper.min.js"></script>

<script src="<?=base_url();?>assets/js/bs.min.js"></script>

<script src="<?=base_url();?>assets/js/wow.js"></script>

<script src="<?=base_url();?>assets/js/feather.min.js"></script>

<script src="<?=base_url();?>assets/plugins/sweet/sweetalert2.min.js"></script>

<script type="text/javascript">

   $(document).ready(function() {

     feather.replace();

     new WOW().init();

     $('button.close').on('click', function(){

       $('div.toast').remove();

     })


     $('.btnDeviceSession').on('click', function(){

        const btn = $(this);

        $.ajax({

            url: '/account/devices',

            type: 'POST',

            beforeSend: function(){

               btn.addClass('disabled').attr('disabled', true);

              btn.html('Enviando autorización...');

            },

            success: function(data){

               window.location = window.location

            }

        })

     })

   });

</script>
  <body>
  <?php echo $this->session->flashdata('toast'); ?>
<div class="Login-pages font-pluto">
  <div class="page-inner">
    <img src="<?=base_url();?>assets/lawkit/images/white-dash-bg.png" class="bg-image" style="width:100%;">
    <div class="container container-login">
    <div class="Login-logo">
      <a href="#"><img src="<?=base_url();?>assets/lawkit/images/logo.png"></a>
    </div>

    <div class="login-outer-wrapper">
      <div class="login-box">
        <h2>Inicia sesión</h2>
        <form id="frm-signup" class="form-signin" method="post" action="<?=base_url('account/signin');?>">
        <?php if($redirect != "") : ?>

        <input type="hidden" name="app" value="<?=$redirect;?>">

        <?php endif;?>
          <div class="input-group">
            <label for="InputEmail1" class="form-label">Correo electrónico</label>
            <input type="email" name="email" class="form-control" id="InputEmail1" aria-describedby="emailHelp" value="<?=$this->input->post('email');?>">
          </div>
          <div class="input-group d-flex">
            <label for="InputPassword1" class="form-label w-50">Contraseña</label><label class="form-label w-50 text-right"><a href="#">¿Olvidaste tu contraseña?</a></label>
            <input type="password" name="password" class="form-control w-100" id="InputPassword1" value="<?=$this->input->post('password');?>">
          </div>
          <div class="input-group form-check">
            <label class="check-lable">Recuérdame
              <input type="checkbox">
              <span class="checkmark"></span>
            </label>
          </div>
          <button type="submit" class="btn cta w-100">Iniciar Sesión</button>
          <div class="confr-div">
            <p>Solicitar correo de confirmación</p>
          </div>
        </form>
      </div>
      <p class="sign-ip-link">Aún no tienes una cuenta?. <a href="#" >Inscríbete.</a></p>
    </div>
  </div>
  </div>
</div>
<footer class="text-center page-footer">
  <div class="container">
    <p>© 2023 Lawkit</p>
  </div>
</footer>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.4/jquery.min.js" integrity="sha512-pumBsjNRGGqkPzKHndZMaAG+bir374sORyzM3uulLV14lN5LyykqNk8eEeUlUkB3U0M4FApyaHraT65ihJhDpQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
  </body>
</html>