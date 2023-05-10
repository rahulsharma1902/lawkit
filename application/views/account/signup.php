<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
  <!-- Old links -->
  <link rel="stylesheet" href="<?=base_url();?>assets/css/bs.min.css">
  <link rel="stylesheet" href="<?=base_url();?>assets/css/animate.min.css">
  <link rel="stylesheet" href="<?=base_url();?>assets/css/jquery.passwordRequirements.css">
  <link href="<?=base_url();?>assets/plugins/sweet/sweetalert2.min.css" rel="stylesheet">
  <!-- plugins js -->
  <script src="https://code.jquery.com/jquery-1.12.4.min.js"></script>
  <script src="<?=base_url();?>assets/js/bs.min.js"></script>
  <script src="<?=base_url();?>assets/js/feather.min.js"></script>
  <script src="<?=base_url();?>assets/js/jquery.min.js"></script>
  <script src="<?=base_url();?>assets/js/popper.min.js"></script>
  <script src="<?=base_url();?>assets/js/bs.min.js"></script>
  <script src="<?=base_url();?>assets/js/wow.js"></script>
  <script src="<?=base_url();?>assets/js/jquery.passwordRequirements.min.js"></script>
  <script src="<?=base_url();?>assets/plugins/sweet/sweetalert2.min.js"></script>

  <link href="<?=base_url();?>assets/lawkit/css/style.css" rel="stylesheet">
  <script type="text/javascript" src="https://js.stripe.com/v3/"></script>
  <title>Lawkit | Crear cuenta</title>
  <script type="text/javascript">
    var stripe = Stripe('pk_test_51N5MbaSIRfv8P9JxGuCtqVMrF5jXs2UPsT4TiUjMxnvgrHLv74N6rPwBXEt2RawZKm8nZSNjb8E5JpjW9fiJ3Wqt00m4MB5eWg');
//Change stripe key with => pk_live_51HSqIiEp2krUL6QU2DfRiR4FeaWDWAQYEEXLYuwb7et6c3xUzfX1jyQ3Pst8BW9CKL5kmf2gYCXVetHjmNCiGzTH00CRKr7FVS 
    $(document).ready(function () {

      feather.replace();

      $('button.close').on('click', function () {
        $('div.toast').remove();
      })



      $('.pr-password').passwordRequirements({
        numCharacters: 8,
        useLowercase: true,
        useUppercase: true,
        useNumbers: true,
        useSpecial: false
      });

    });
  </script>
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
</head>

<body>
  <?php echo $this->session->flashdata('toast'); ?>
  <div class="Login-pages font-pluto subscription-pages">
    <div class="page-inner">
      <img src="<?=base_url();?>assets/lawkit/images/white-dash-bg.png" class="bg-image" style="width:100%;">
      <div class="container">
        <div class="subscription-inner">
          <div class="row">
            <div class="col-lg-6 col-md-5 text-col">
              <div class="register-logo">
                <a href="#"><img src="<?=base_url();?>assets/lawkit/images/register-logo.png"></a>
              </div>
              <h2>Tu suscripción:</h2>
              <ul class="tick-list">
                <li><b>7 días de prueba gratuita.</b></li>
                <li><b>Sólo $1499.00 pesos al mes.</b><br>
                  Cancela cuando quieras.</li>
                <li><b>Acceso ilimitado a nuestras herramientas.</b><br>
                  Haz búsquedas y cálculos sin límite por el mismo precio.</li>
                <li><b>Calculadora gratuita.</b><br>
                  Inscríbete ahora y tendrás acceso gratuito a la calculadora incluso si no tienes suscripción activa.
                </li>
              </ul>
            </div>
            <div class="col-lg-6 col-md-7 form-col">
              <div class="login-box" id="sign_up-box">
                <h2>Crear cuenta</h2>
                <form method="post" action="<?=base_url('account/signup');?>" id="signup-form">
                  <div class="row">
                    <div class="col-md-6">
                      <div class="input-group">
                        <label for="InputName" class="form-label">Nombre</label>
                        <input type="text" name="fusername" class="form-control" id="fusername"
                          value="<?=$this->input->post('fusername');?>">
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="input-group">
                        <label for="InputlName" class="form-label">Apellido</label>
                        <input type="text" name="lusername" class="form-control" id="lusername" required=""
                          value="<?=$this->input->post('lusername');?>">
                      </div>
                    </div>
                  </div>
                  <div class="input-group">
                    <label for="InputEmail1" class="form-label">Correo electrónico</label>
                    <input type="email" name="email" class="form-control" id="email" aria-describedby="emailHelp"
                      required="" value="<?=$this->input->post('email');?>">
                  </div>
                  <div class="input-group">
                    <label for="InputPassword1" class="form-label w-50">Contraseña</label>
                    <input type="password" name="password" class="form-control w-100" id="password" required=""
                      value="<?=$this->input->post('password');?>">
                  </div>
                  <div class="input-group">
                    <label for="Inputconfirm_Password1" class="form-label w-50">Confirmar contraseña</label>
                    <input type="password" name="password_confirm" class="form-control w-100" id="password_confirm"
                      value="<?=$this->input->post('password_confirm');?>" required>
                  </div>
                  <div class="input-group form-check">
                    <label class="check-lable">Recuérdame
                      <input type="checkbox">
                      <span class="checkmark"></span>
                    </label>
                  </div>
                  <button id="sign_up_cta" type="button" class="btn cta w-100">Iniciar Sesión</button>
                  <div class="confr-div">
                    <p>Aún no tienes una cuenta?. <a href="#">Inscríbete.</a></p>
                  </div>
                  <!-- </form> -->
              </div>
              <div class="login-box" id="payment-box">
                <h2>Pago</h2>
                <p>Ingresa la información de tu tarjeta de crédito. No haremos ningún cobro hasta que termine tu periodo
                  de prueba.</p>
                <!-- <form> -->
                <div class="input-group d-flex">
                  <label class="form-label w-50">Card number</label><label class="form-label w-50 text-right">Powered by
                    <a href="#"> Stripe</a></label>
                  <div id="cc-number"></div>
                </div>
                <div class="row">
                  <div class="col-md-6">
                    <div class="input-group">
                      <label class="form-label">MM/AA</label>
                      <div id="cc-expiry"></div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="input-group">
                      <label class="form-label">CVV</label>
                      <div id="cc-cvc"></div>
                    </div>
                  </div>
                </div>
                <input type="hidden" name="payment_method_id" id="payment_method_id">
                <input type="hidden" name="card_token" id="card_token">
                <div class="input-group form-check">
                  <label for="customSwitch1" class="check-lable">Acepto los <a href="#">términos y condiciones</a> del
                    servicio de LawKit.
                    <input type="checkbox" id="customSwitch1">
                    <span class="checkmark"></span>
                  </label>
                  <div class="stripe-div">
                    <img src="<?=base_url();?>assets/lawkit/images/stripe-pago 1.png">
                  </div>
                </div>
                <div class="row button-row">
                  <div class="col-md-6">
                    <button id="prev_box" class="btn cta cta-white w-100">← Atrás</button>
                  </div>
                  <div class="col-md-6">
                    <button type="submit" class="btn cta w-100 lawkit-btn disabled" disabled>Crear Cuenta</button>
                  </div>
                </div>
                <div class="confr-div">
                  <p>¿Ya tienes una cuenta? <a href="#">Inicia sesión.</a></p>
                </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <footer class="text-center page-footer">
    <div class="container">
      <p>© 2023 Lawkit</p>
    </div>
  </footer>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.4/jquery.min.js"
    integrity="sha512-pumBsjNRGGqkPzKHndZMaAG+bir374sORyzM3uulLV14lN5LyykqNk8eEeUlUkB3U0M4FApyaHraT65ihJhDpQ=="
    crossorigin="anonymous" referrerpolicy="no-referrer"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
    crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/5.0.8/jquery.inputmask.min.js"
    integrity="sha512-efAcjYoYT0sXxQRtxGY37CKYmqsFVOIwMApaEbrxJr4RwqVVGw8o+Lfh/+59TU07+suZn1BWq4fDl5fdgyCNkw=="
    crossorigin="anonymous" referrerpolicy="no-referrer"></script>

  <script>
    $(document).ready(function () {

      $("#sign_up_cta").click(function (e) {
        e.preventDefault();
        $("#sign_up-box").hide();
        $("#payment-box").show();
      });
      $("#prev_box").click(function (e) {
        e.preventDefault();
        $("#payment-box").hide();
        $("#sign_up-box").show();
      });
    });

    var ccNumberMask = new Inputmask("9999 9999 9999 9999");
    ccNumberMask.mask(document.getElementById("cc-number"));
    var ccExpiryMask = new Inputmask("99 / 9999");
    ccExpiryMask.mask(document.getElementById("cc-expiry"));




  </script>
  <script type="text/javascript">
   var elements = stripe.elements();
var style = {
  base: {
    border: '1px solid #E8E8E8',
    iconColor: '#c4f0ff',
    color: '#31325F',
    fontWeight: 400,
    fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
    fontSize: '16px',
    fontSmoothing: 'antialiased',

    '::placeholder': {
      color: '#CFD7E0',
    },
    ':-webkit-autofill': {
      color: '#fce883',
    },
    ':focus': {
      color: '#31325F',
    },
  },
  invalid: {
    iconColor: '#FFC7EE',
    color: '#FFC7EE',
  },
};

var cardNumber = elements.create('cardNumber', {
  style: style,
  classes: {
    base: 'form-control w-full',
    complete: 'is-valid',
    empty: 'is-empty',
    invalid: 'is-invalid',
  },
});

var cardExpiry = elements.create('cardExpiry', {
  style: style,
  classes: {
    base: 'form-control w-full',
    complete: 'is-valid',
    empty: 'is-empty',
    invalid: 'is-invalid',
  },
});

var cardCvc = elements.create('cardCvc', {
  style: style,
  classes: {
    base: 'form-control w-full',
    complete: 'is-valid',
    empty: 'is-empty',
    invalid: 'is-invalid',
  },
});

cardNumber.mount('#cc-number');
cardExpiry.mount('#cc-expiry');
cardCvc.mount('#cc-cvc');

    // Card mount done here

    $("#customSwitch1").on("change", function (event) {
      if (event.target.checked) {
        $('.lawkit-btn').removeClass('disabled').removeAttr("disabled")
      } else {
        $(".lawkit-btn").addClass("disabled").attr('disabled', true);
      }
    })

    var form = document.getElementById('signup-form');
    $('.lawkit-btn').click(function () {
      console.log('try to submit form...... ');
      var btn = $(this)
      console.log(btn);
      btn.addClass("disabled").attr('disabled', true).html('...');
      if ($('#fusername').val().length <= 0) {
        Swal.fire({
          type: 'error',
          title: `oops...`,
          text: 'Debes ingresar un nombre de usuario'
        })
        btn.removeClass("disabled").removeAttr('disabled').html('Crear cuenta');
        return false;
      }
      if ($('#lusername').val().length <= 0) {
        Swal.fire({
          type: 'error',
          title: `oops...`,
          text: 'Debes ingresar apellidos'
        })
        btn.removeClass("disabled").removeAttr('disabled').html('Crear cuenta');
        return false;
      }
      if ($('#email').val().length <= 0) {
        Swal.fire({
          type: 'error',
          title: `oops...`,
          text: 'El campo de correo electrónico es obligatorio'
        })
        btn.removeClass("disabled").removeAttr('disabled').html('Crear cuenta');
        return false;
      }
      if ($('#password_confirm').val() != $('#password').val()) {
        Swal.fire({
          type: 'error',
          title: `oops...`,
          text: 'Las contraseñas ingresadas no coinciden'
        })
        btn.removeClass("disabled").removeAttr('disabled').html('Crear cuenta');
        return false;
      }


      // Start creating payment token
      stripe.createPaymentMethod({
        type: 'card',
        card: cardNumber,
        billing_details: {
          name: $('#fusername').val() + ' ' + $('#lusername').val(),
        },
      }).then(function (result) {
        if (result.error) {
          // Display error.message in your UI
          btn.removeClass("disabled").removeAttr('disabled').html('Crear cuenta');
          Swal.fire({
            type: 'error',
            title: `oops...`,
            text: result.error.message
          })
        } else {
          stripe.createToken(cardNumber).then(function (res) {
            if (res.error) {
              btn.removeClass("disabled").removeAttr('disabled').html('Crear cuenta');
              Swal.fire({
                type: 'error',
                title: `oops...`,
                text: result.error.message
              })
            } else {
              $('#payment_method_id').val(result.paymentMethod.id);
              $('#card_token').val(res.token.id);
              $('#signup-form').submit();
              // console.log(res.token.id);
              // console.log(result.paymentMethod.id);
            }
          });
        }
      });
    });

  </script>

</body>

</html>