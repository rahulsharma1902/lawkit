<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    
    <title>Login</title>
  </head>
   <!-- plugins style -->

   <link href="https://fonts.googleapis.com/css?family=IBM+Plex+Sans:400,600" rel="stylesheet">

    <link rel="stylesheet" href="<?=base_url();?>assets/css/bs.min.css">
   <link rel="stylesheet" href="<?=base_url();?>assets/css/animate.min.css">
   <link rel="stylesheet" href="<?=base_url();?>assets/plugins/sweet/sweetalert2.min.css">
   <link href="<?=base_url();?>assets/lawkit/css/style.css" rel="stylesheet">
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
            <label for="InputPassword1" class="form-label w-50">Contraseña</label><label class="form-label w-50 text-right"><a href="<?=base_url();?>account/recovery">¿Olvidaste tu contraseña?</a></label>
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
          <p class="mt-2 text-muted text-center"><small data-toggle="modal" data-target="#modalConfirmation">Solicitar correo de confirmación</small></p>

          </div>
        </form>
      </div>
      <p class="sign-ip-link">¿Aún no tienes tu cuenta? <a href="<?=base_url();?>account/signup" >Inscríbete.</a></p>
    </div>
  </div>
  </div>
</div>

<!--  Confermation email model -->

<div class="modal fade" id="modalConfirmation" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
         <div class="modal-dialog">
            <div class="modal-content">
               <div class="modal-header">
               <h5 class="modal-title" id="exampleModalLabel">Ingresa tu correo electrónico</h5>
               <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
               </button>
               </div>
               <div class="modal-body">
                  <form id="frmConfirmAccount">
                     <div class="form-group">
                        <label class="control-label">Correo electrónico</label>
                        <input type="text" class="form-control" name="email" id="inpEmail">
                        <p class="text-danger hidden" id="frmError"></p>
                     </div>
                  </form>
               </div>
               <div class="modal-footer">
               <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
               <button type="button" class="btn btn-primary btnConfirmAccount">Confirmar cuenta</button>
               </div>
            </div>
         </div>
      </div>



      <script>
         $(document).ready(function(){
            $(".btnConfirmAccount").on("click", function(){
               var p = $("#frmError")
               var btn = $(this)
               var email = $("input#inpEmail")
               btn.attr("disabled", true).addClass("disabled").html("<i>Enviando correo de confirmación...</i>")
               if(email.val() === "") {
                  p.removeClass("hidden")
                  p.text("Ingrese un correo electrónico válido")
                  btn.attr("disabled", false).removeClass("disabled").html("Confirmar cuenta")
                  return false;
               }
               p.addClass("hidden").html("")
               $.ajax({
                  type: 'POST',
                  url: '/account/resend_email_confirmation',
                  dataType: "json",
                  data: $("#frmConfirmAccount").serialize(),
                  success: function(response) {
                     if(response.error) {
                        Swal.fire({
                           type:"error",
                           text:response.message
                        })
                        btn.attr("disabled", false).removeClass("disabled").html("Confirmar cuenta")
                        return;
                     }
                     // Handle a successful response
                     $("#modalConfirmation").modal("hide")
                     Swal.fire({
                        type:"success",
                        text:response.message
                     })
                     btn.attr("disabled", false).removeClass("disabled").html("Confirmar cuenta")
                     email.val("")
                  },
                  error: function(xhr, status, error) {
                  // Handle an error response
                  console.log(xhr.responseText);
                  btn.attr("disabled", false).removeClass("disabled").html("Confirmar cuenta")
                  }
               });
            })
         })
      </script>







<footer class="text-center page-footer">
  <div class="container">
    <p>© 2023 Lawkit</p>
  </div>
</footer>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.4/jquery.min.js" integrity="sha512-pumBsjNRGGqkPzKHndZMaAG+bir374sORyzM3uulLV14lN5LyykqNk8eEeUlUkB3U0M4FApyaHraT65ihJhDpQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
  </body>
</html>