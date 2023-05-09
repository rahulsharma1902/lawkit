<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link href="<?=base_url();?>assets/lawkit/css/style.css" rel="stylesheet">
    <!-- Old links -->
    <link rel="stylesheet" href="<?=base_url();?>assets/css/jquery.passwordRequirements.css">
    <link href="<?=base_url();?>assets/plugins/sweet/sweetalert2.min.css" rel="stylesheet">
    <!-- plugins js -->
    <script src="https://code.jquery.com/jquery-1.12.4.min.js"></script>
    <script src="<?=base_url();?>assets/js/bs.min.js"></script>
    <script src="<?=base_url();?>assets/js/feather.min.js"></script>
    <script src="<?=base_url();?>assets/js/jquery.passwordRequirements.min.js"></script>
    <script src="<?=base_url();?>assets/plugins/sweet/sweetalert2.min.js"></script>

    <script type="text/javascript" src="https://js.stripe.com/v3/"></script>
    <title>Lawkit | Crear cuenta</title>
  </head>
  <body>
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
              Inscríbete ahora y tendrás acceso gratuito a la calculadora incluso si no tienes suscripción activa.</li>
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
                      <input type="text" name="fusername" class="form-control" id="InputName">
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="input-group">
                      <label for="InputlName" class="form-label">Apellido</label>
                      <input type="text" name="lusername" class="form-control" id="InputlName">
                    </div>
                  </div>
                </div>
                <div class="input-group">
                  <label for="InputEmail1" class="form-label">Correo electrónico</label>
                  <input type="email" name="email" class="form-control" id="InputEmail1" aria-describedby="emailHelp">
                </div>
                <div class="input-group">
                  <label for="InputPassword1" class="form-label w-50">Contraseña</label>
                  <input type="password" name="password" class="form-control w-100" id="InputPassword1">
                </div>
                <div class="input-group">
                  <label for="Inputconfirm_Password1" class="form-label w-50">Confirmar contraseña</label>
                  <input type="password" name="password_confirm" class="form-control w-100" id="Inputconfirm_Password1">
                </div>
                <div class="input-group form-check">
                  <label class="check-lable">Recuérdame
                    <input type="checkbox">
                    <span class="checkmark"></span>
                  </label>
                </div>
                <button id="sign_up_cta" type="submit" class="btn cta w-100">Iniciar Sesión</button>
                <div class="confr-div">
                  <p>Aún no tienes una cuenta?. <a href="#">Inscríbete.</a></p>
                </div>
              <!-- </form> -->
            </div>
            <div class="login-box" id="payment-box">
              <h2>Pago Prúebalo gratis por….</h2>
              <p>Ingresa la información de tu tarjeta de crédito. No haremos ningún cobro hasta que termine tu periodo de prueba.</p>
              <!-- <form> -->
                <div class="input-group d-flex">
                  <label class="form-label w-50">Card number</label><label class="form-label w-50 text-right">Powered by <a href="#"> Stripe</a></label>
                   <input id="cc-number" type="text" name="cc-number" class="w-full form-control" placeholder="">
                </div>
                <div class="row">
                  <div class="col-md-6">
                    <div class="input-group">
                      <label class="form-label">MM/AA</label>
                        <input id="cc-expiry" type="text" class="w-full form-control">
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="input-group">
                      <label class="form-label">CVV</label>
                     <input id="cc-cvv" type="text" class="w-full form-control" maxlength="3">
                    </div>
                  </div>
                </div>
                <div class="input-group form-check">
                  <label for="customSwitch1" class="check-lable">Acepto los <a href="#">términos y condiciones</a> del servicio de LawKit.
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
                   <button type="button" class="btn cta w-100 lawkit-btn" >Crear Cuenta</button>
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

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.4/jquery.min.js" integrity="sha512-pumBsjNRGGqkPzKHndZMaAG+bir374sORyzM3uulLV14lN5LyykqNk8eEeUlUkB3U0M4FApyaHraT65ihJhDpQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/5.0.8/jquery.inputmask.min.js" integrity="sha512-efAcjYoYT0sXxQRtxGY37CKYmqsFVOIwMApaEbrxJr4RwqVVGw8o+Lfh/+59TU07+suZn1BWq4fDl5fdgyCNkw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>


<script>
  $(document).ready(function(){
  $("#sign_up_cta").click(function(e){
    e.preventDefault();
    $("#sign_up-box").hide();
    $("#payment-box").show();
    });
    $("#prev_box").click(function(e){
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
<script>

const cardNumber = document.getElementById('cc-number').value;
const cardCSV = document.getElementById('cc-cvv').value;
const cardExp = document.getElementById('cc-expiry').value;

  $(document).ready(function() {
    var stripe = Stripe('pk_test_51N5MbaSIRfv8P9JxGuCtqVMrF5jXs2UPsT4TiUjMxnvgrHLv74N6rPwBXEt2RawZKm8nZSNjb8E5JpjW9fiJ3Wqt00m4MB5eWg');
    var elements = stripe.elements();
    var card = elements.create('card', {
      style: {
        base: {
          iconColor: '#666EE8',
          color: '#31325F',
          lineHeight: '40px',
          fontWeight: 300,
          fontFamily: 'Helvetica Neue',
          fontSize: '15px',
          '::placeholder': {
            color: '#CFD7E0',
          },
        },
      }
    });
    card.mount('#card-element');
    
    // Handle form submission
    $('#payment-form').on('submit', function(event) {
      event.preventDefault();

      // Get the card information from the input fields
      const cardNumber = document.getElementById('cc-number').value;
      const cardCSV = document.getElementById('cc-cvv').value;
      const cardExp = document.getElementById('cc-expiry').value;
      
      // Validate the card information using Stripe
      stripe.createToken(card, {
        card: {
          number: cardNumber,
          cvc: cardCSV,
          exp_month: cardExp.split('/')[0],
          exp_year: cardExp.split('/')[1],
        }
      }).then(function(result) {
        if (result.error) {
          // Inform the user if there was an error
          var errorElement = $('#card-errors');
          errorElement.text(result.error.message);
        } else {
          // Send the token to your server
          var token = result.token;
          console.log(token);
          // Add the token to hidden input field
          $('input[name=stripeToken]').val(token.id);
          // Submit the form
          $('#payment-form').get(0).submit();
        }
      });
    });
  });
</script>

</script>

</script>
  </body>
</html>