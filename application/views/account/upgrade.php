<script type="text/javascript" src="https://js.stripe.com/v3/"></script>
<style>
	#card-element {
	  background-color: white;
	  padding: 0.7em;
	  border: 1px solid #ccc;
	  border-radius: 5px;
	}
</style>

<?php if($customer_suscription['status'] != 'active'): ?>
	<div class="row">
	<div class="offset-2 col-8">
		<div class="alert alert-danger hidden credit_card_message"></div>
	</div>
</div>
<div class="row my-5">
	<div class="col-md-4 offset-md-2">
		<p><?=$msg_user;?></p>
		<a href="<?=base_url('account/dashboard');?>" class="btn btn-secondary">Dashboard</a>
	</div>
	<div class="col-md-4">
		<div id="card-element-errors" role="alert"></div>
		<p class="text-primary text-center">Monto a pagar por el plan Profesional</p>
		<h1 class="text-primary font-weight-bold">$999.00 MXN<small> / mes</small></h1>
		<div class="d-flex flex-row">
			<div>
				<img src="<?=base_url();?>assets/images/visa.png" style="max-width:2.5em;">
			</div>
			<div>
				<img src="<?=base_url();?>assets/images/master.png" style="max-width:2.5em;">
			</div>
			<div>
				<img src="<?=base_url();?>assets/images/amex.png" style="max-width:2.5em;">
			</div>
		</div>

		<?php if(count($cards) > 0) : ?>
		<form class="toggleHidden" action="" method="post">
			
			<div class="form-group ">
				<label>Seleccione alguna de sus tarjetas guardadas</label>
				<?php foreach($cards as $i => $card):?>
				<div class="form-check">
				  <input class="form-check-input" name="myCard" type="radio" value="<?=$card->id;?>" id="check_<?=$card->id;?>"
				  <?=($this->input->post('myCard') == $card->id) ? 'checked' : null;?>>
				  <label class="form-check-label" for="check_<?=$card->id;?>">
				    ****<?=$card->last4;?> <?=$card->brand;?>
				  </label>
				</div>
			<?php endforeach;?>
			</div>
			<button class="btn btn-lg btn-primary btn-block mt-2" type="submit">
				<i data-feather="lock" class="text-white icon-button-svg-payment"></i> PAGAR $999.00 MXN
			</button>
		</form>
	<?php endif; ?>

<!-- stripe element -->
		<form id="frmStripePayment" class="toggleHidden <?=(count($cards) > 0) ? 'hidden': null;?>">
			<input type="hidden" name="cardToken" id="cardToken">
			<div class="w-100 <?=count($cards) > 0 ? 'hidden' : null;?>">
				<?php if(count($cards) > 0) : ?>
				<label><a href="#" id="cancelNewCard">Cancelar y seleccionar una tarjeta</a></label>
				<?php endif;?>
				<div id="card-element" class="mb-3"></div>
			</div>
			<div class="d-flex">
				<div>
					<small>Tus pagos siempre se realizan de la manera más segura mediante <b>stripe</b> y jamás guardamos los datos de tu tarjeta de crédito.</small>
				</div>
				<div class="ml-auto">
					<img src="<?=base_url();?>assets/images/stripe-pago.png" style="max-width:4em;">
				</div>
			</div>
			<button class="btn btn-lg btn-primary btn-block mt-2 btn-pay" type="button">
				<i data-feather="lock" class="text-white icon-button-svg-payment"></i> PAGAR $999.00 MXN
			</button>
		</form>
	</div>
</div>
<?php else : ?>
	<div class="alert alert-info">
		<?=$information;?>
	</div>
<?php endif;?>




<script type="text/javascript">
	//guarda la card
  //cambiar a live en produccion
  var stripe = Stripe('pk_live_51HSqIiEp2krUL6QU2DfRiR4FeaWDWAQYEEXLYuwb7et6c3xUzfX1jyQ3Pst8BW9CKL5kmf2gYCXVetHjmNCiGzTH00CRKr7FVS');
  var elements = stripe.elements();

  var card = elements.create('card');
  card.mount('#card-element')

  card.on("change", function(event){
    displayError(event)
  })

  
  function displayError(event){
    let displayError = document.getElementById('card-element-errors');
    if (event.error) {
      displayError.textContent = event.error.message;
    } else {
      displayError.textContent = '';
    }
  }

  $('form input').on('keydown', function(e){
  	if( e.keyCode == 13 )
  	{
  		e.preventDefault()
  		return false;
  	}
  })

  $("button.btn-pay").on("click", function(){
		var btn = $(this);
		btn.addClass('disabled').attr('disabled', true).html('Procesando el pago...')
		stripe.createToken(card).then(function(res){
			if(res.error){
				btn.removeClass("disabled").removeAttr('disabled').html('<i data-feather="lock" class="text-white icon-button-svg-payment"></i> PAGAR $999.00 MXN');
				Swal.fire({
					type: 'error',
					title: `oops...`,
					text: result.error.message
				})
			} else {
				$('#cardToken').val(res.token.id);
				$.ajax({
					url: '/account/pay',
					type: 'POST',
					dataType:'json',
					data:$('#frmStripePayment').serialize(),
					success: function(data){
						window.location = window.location.origin
					}
				})
			}
		});
	})

	$('#newCard').on('click', function(){
		$('.toggleHidden').toggleClass('hidden')
		
	})

	$('#cancelNewCard').on('click', function(){
		$('.toggleHidden').toggleClass('hidden')
		
	})


</script>