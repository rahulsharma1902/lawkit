<script type="text/javascript" src="https://js.stripe.com/v3/"></script>
<style>
	#card-element {
	  background-color: white;
	  padding: 0.7em;
	  border: 1px solid #ccc;
	  border-radius: 5px;
	}
</style>

<div class="row mt-4 justify-content-md-center" id="main-row">
	<div class="col-12 col-md-10" id="main-left">
				<div class="row text-center">
			<h4 class="text-primary title">Datos de usuario</h4>
			<ul class="nav nav-pills nav-fill w-100" id="myTab" role="tablist">
			  <li class="nav-item" role="presentation">
			    <a class="nav-link active" id="myaccount-tab" data-toggle="tab" href="#myaccount" role="tab" aria-controls="myaccount" aria-selected="true"><i data-feather="user"></i><span class="link-text">Mi cuenta</span></a>
			  </li>
			  <li class="nav-item" role="presentation">
			    <a class="nav-link" id="mypayments-tab" data-toggle="tab" href="#mypayments" role="tab" aria-controls="mypayments" aria-selected="false"><i data-feather="dollar-sign"></i><span class="link-text">Mis pagos</span></a>
			  </li>
			  <li class="nav-item" role="presentation">
			    <a class="nav-link" id="accountusage-tab" data-toggle="tab" href="#accountusage" role="tab" aria-controls="accountusage" aria-selected="false"><i class="fa fa-bar-chart"></i><span class="link-text ml-1">Uso de cuenta</span></a>
			  </li>
			  <?php if($this->session->userdata('role') == 1) : ?>
			  <li class="nav-item" role="presentation">
			    <a class="nav-link" id="adminAcc-tab" data-toggle="tab" href="#adminAcc" role="tab" aria-controls="adminAcc" aria-selected="false"><i class="fa fa-shield"></i><span class="link-text ml-1">Admin</span></a>
			  </li>
			  <?php endif; ?>
			</ul>
			<div class="tab-content w-100 mt-3" id="myTabContent">
			  <div class="tab-pane fade show active" id="myaccount" role="tabpanel" aria-labelledby="myaccount-tab">
			  	<form class="hidden" id="frm-change-image-profile">
				    <input type="file" class="custom-file-input" id="img-profile" name="image" accept="image/png,jpeg">
				</form>
				<div class="row mt-4">
				    <div class="col-md-3">
				        <div class="photo-profile">
				            <div class="icon-user update-photo" <?= !empty($profile->photo) ? ' style="background-image:url(/assets/images/photo_customers/'.$profile->photo.')"' : NULL;?>>
				                <?php if(empty($profile->photo)):?>
				                    <i data-feather="user"></i>
				                <?php endif;?>
				            </div>
				        </div>
						
				    </div>
				    <div class="col">
				        <form method="post" action="<?=base_url();?>account/profile" id="update_profile_form">
				            <input type="hidden" name="photo" id="photo" value="<?=$profile->photo;?>">
				            <div class="form-group">
				                <label>Correo electrónico</label>
				                <input type="email" class="lawkit-input" name="email" id="email" value="<?=$profile->email;?>" readonly>
				            </div>
				            <div class="row">
				                <div class="col-md">
				                    <div class="form-group">
				                        <label>Nombre</label>
				                        <input type="text" class="lawkit-input" name="fname" id="fname"  value="<?=$profile->fname;?>">
				                    </div>
				                </div>
				                <div class="col-md">
				                    <div class="form-group">
				                        <label>Apellidos</label>
				                        <input type="text" class="lawkit-input" name="lname" id="lname"  value="<?=$profile->lname;?>">
				                    </div>
				                </div>
				            </div>
							<div class="row">
								<div class="col-md-6">
								<?php if($coupon != '') : ?>
									<label for="">Cupón para compartir</label>
									<input type="text" id="meCode" value="<?=$coupon->id;?>" class="lawkit-input">
								<?php endif; ?>
								</div>
							</div>
							<div class="row mt-5">
								<div class="col-md-6">
									<button class="lawkit-btn bg-lk-blue-o2">Actualizar mis datos</button>
								</div>
								<div class="col-md-6">
									<a href="<?=base_url();?>/account/facturacion" class="lawkit-btn bg-lk-blue-o2">Agregar datos de facturación</a>
								</div>
							</div>
				        </form>
				    </div>
				</div>
				<div class="row mt-4">
					<div class="col-md-3"></div>
					<div class="col-md-8">
						<label>Actualizar contraseña</label>
						<div class="alert alert-info">
			  			Al confirmar tu nueva contraseña, recibirás un correo electrónico con tus datos actualizados
			  		</div>
						<form method="post" action="<?=base_url();?>account/forgotten_pwd" class="form-inline">
			  		<div class="form-group mx-md-1">
			  			<input type="password" class="lawkit-input" name="current_pwd" id="current_pwd" placeholder="Tu contraseña actual">
			  		</div>
			  		<div class="form-group mx-md-1">
			  			<input type="password" class="lawkit-input" name="new_pwd" id="new_pwd" placeholder="Nueva contraseña">
			  		</div>
			  		<div class="form-group mx-md-1">
			  			<input type="password" class="lawkit-input" name="conf_pwd" id="conf_pwd" placeholder="Confirmar contraseña">
			  		</div>
			  		<button class="btn btn-outline-danger">Actualizar</button>
			  		
			  	</form>
					</div>
				</div>
			  </div><!--//end #myaccount tab-->
			  <div class="tab-pane fade" id="mypayments" role="tabpanel" aria-labelledby="mypayments-tab">

			  	<div class="row">
					<div class="col-12 col-md-10">
						<!-- bills table -->
						<?php if($customer_suscription['status'] == 'trialing'): ?>
			  		<div class="alert alert-info">
			  			<p><i data-feather="info"></i> Tu suscripción a los servicios de Lawkit se encuentra en periodo de prueba la cual finalizará el día <b><?=$customer_suscription['trial_end'];?></b>, después de esta fecha, se te pedirá realizar un pago por la cantidad de $999.00 MXN para continuar usando nuestros servicios.</p>
			  		</div>
			  	<?php elseif($customer_suscription['status'] == 'canceled'): ?>
					<div class="alert alert-warning">
						<p><strong>Tu suscripción ha sido cancelada</strong></p>
						<p>Adquiere tu plan de acceso a los servicios de lawkit nuevamente por solo $999.00 MXN cada mes, haciendo clic aquí <i data-feather="arrow-right"></i> <a href="<?=base_url();?>account/upgrade">Actualizar mi cuenta</a></p>
					</div>			  		
			  	<?php endif;?>

				<?php if(count($invoices) > 0): ?>
					<table class="table">
						<thead>
							<tr>
								<th>NUM.</th>
								<th>DESCRIPCIÓN</th>
								<th>CANT.</th>
								<th>TOTAL</th>
								<th>DESCARGAR</th>
								<th>PROX. FACT.</th>
								<th>ESTATUS</th>
								<th>FACTURA</th>
							</tr>
						</thead>
						<tbody>
				<?php
					foreach($invoices as $key => $invoice):
						$period_start = date("d M. Y", $invoice->lines->data[0]->period->start);
						$period_end   = date("d M. Y", $invoice->lines->data[0]->period->end);
						$amount       = strtoupper($invoice->lines->data[0]->currency) . " " .number_format(($invoice->lines->data[0]->amount/100),2);
						$qty          = $invoice->lines->data[0]->quantity;
						$number       = $invoice->number;
						switch($invoice->status){
							case 'paid':
							$status = '<span class="text-success"><i data-feather="check"></i> Pagado</span>';
							break;
							case 'draft':
							$status = '<span class="text-muted"><i data-feather="circle"></i> Por cobrar</span>';
							break;
							case 'open':
							$status = '<span class="text-info"><i data-feather="circle"></i> Abierto</span>';
							break;
							default: $status = '<span class="text-info"><i data-feather="circle"></i> '.$invoice->status.'</span>';
							break;
						}
				?>
					<tr>
						<td><?=$number;?></td>
						<td>
							<p class="text-muted m-0"><small><?=$period_start . " - " . $period_end;?></small></p>
							<p class="m-0"><?=$invoice->lines->data[0]->description;?></p>
						</td>
						<td>
							<?=$qty;?>
						</td>
						<td>
							<?=$amount;?>
						</td>
						<td>
							<a href="<?=$invoice->invoice_pdf;?>" target="_self" class="btn btn-light btn-sm">
								<i data-feather="download"></i> PDF
							</a>
						</td>
						<td><?=$period_end;?></td>
						<td>
							<?=$status;?>
						</td>
						<td>
							<?php switch($invoice->status){
								case 'open':
								$btn = '<a href="'.$invoice->hosted_invoice_url.'" target="_blank" class="btn btn-outline-danger btn-sm btn-block">Pagar</a>';
								break;
								case 'paid':
								$btn = '<a href="'.$invoice->hosted_invoice_url.'" target="_blank" class="btn btn-outline-success btn-sm btn-block">Ver</a>';
								break;
								case 'draft':
								$btn = '<button type="button" class="btn btn-outline-secondary btn-sm btn-block">Por cobrar</button>';
								break;
								default: $btn = '<button type="button" class="btn btn-outline-secondary btn-sm btn-block">Unknow</button>';
							}
							echo $btn;
							?>
						</td>
					</tr>
				<?php endforeach;?>
						</tbody>
					</table>
				<?php else: ?>
					<div class="card border-light mb-3 rounded">
					  <div class="card-header">Facturas</div>
					  <div class="card-body">
					    <h5 class="card-title">No estás suscrito a ningún plan de Lawkit</h5>
					    <a href="<?=base_url();?>account/upgrade" class="text-primary">Actualizar mi cuenta a PRO</a>
					  </div>
					</div>
				<?php endif; ?>

				<br>
				<?php if(count($cards) <= 0):?>
				<div class="d-inline-block w-100 mb-2">
					<div class="float-right">
						<button type="button" data-toggle="modal" data-target="#add-card-m" id="add-card-btn" class="lawkit-btn bg-lk-blue-o2">Añadir tarjeta</button>
					</div>
				</div>
				<?php endif; ?>

				<?php if($alreadyCancelled == ''): ?>
					<div class="d-inline-block w-100">
						<div class="float-right">
							<a href="<?=base_url();?>account/cancel_subscription" style="font-size: 12px;">Cancelar mi suscripción</a>
						</div>
					</div>
				<?php endif; ?>


					<?php if(count($cards) > 0):?>
						<h5 class="text-dark">Mis tarjetas</h5>
						<div id="accordion" role="tablist" aria-multiselectable="true" class="my-cards">
							<?php foreach($cards as $i => $card):?>
								<div class="card">
									<div class="card-header" role="tab" id="card_<?=$i;?>"  data-toggle="collapse" data-parent="#accordion" data-target="#collapse_<?=$i;?>">							
										<div class="row">
											<div class="col-md-2">
												<?php if($card->brand == 'Visa') { ?>
												<img src="<?=base_url('assets/images/visa.png');?>">
												<?php } elseif($card->brand == 'MasterCard') { ?>
													<img src="<?=base_url('assets/images/master.png');?>">
												<?php } elseif($card->brand == 'American Express') { ?>
													<img src="<?=base_url('assets/images/amex.png');?>">
												<?php } ?>										
											</div>
											<div class="col-md-5">
												<p>****<?=$card->last4;?></p>
											</div>
											<div class="col-md-3">
												<p class="text-muted mr-2">Expira: </p>
												<p><?=$card->exp_month;?>/<?=$card->exp_year;?></p>
											</div>
											<div class="col-md-2">
												<a href="<?=base_url('account/carddelete?id=') . $card->id;?>" id="delete-card-btn" card-id="<?=$i;?>" target="_self" class="btn btn-light btn-sm"><i class="fa fa-trash"></i></a>
											</div>
										</div>
									</div>
								</div>
							<?php endforeach; ?>
						</div>
					<?php endif;?>

				 <!-- Cards edition modal -->
						<!-- end bills table -->
					</div>
					<div class="col-12 col-md-2">
						<p>Códigos de descuento disponibles</p>
						<ul class="list-group">
							<?php if(count($yourCoupons) > 0): ?>
							 	<?php foreach($yourCoupons as $coupon): ?>
									<li class="list-group-item <?=($coupon->valid == 1 && isset($coupon->aplica) && $coupon->aplica !== "") ? 'bg-success' : '';?>">
										<p class="m-0"><?=$coupon->code;?></p>
										<p class="m-0">
											<small><?=isset($coupon->off) ? $coupon->off . "%" : "";?></small>
										</p>
										<p class="m-0">
											<small><?=isset($coupon->aplica) ? $coupon->aplica : "";?></small>
										</p>
								</li>
								<?php endforeach; ?>
							<?php else: ?>
								<li class="list-group-item">No cuentas con cupones de descuento</li>
							<?php endif; ?>
							<p>
								<span class="color-primary" data-toggle="modal" data-target="#mdCuponM">Ingresar cupón</span>
							</p>
						</ul>
					</div>
				</div>

			  	
          
			  </div><!--//end #mypayments tab-->
			  <div class="tab-pane fade" id="accountusage" role="tabpanel" aria-labelledby="accountusage-tab">
			  	   <div class="row">
                  <div class="col-md-6">
                     <div class="card h-100 lk-card">
                         <div class="card-body">
                           <div class="row no-gutters align-items-center">
                             <div class="col mr-2">
                               <div class="font-weight-bold text-uppercase mb-1 text-gray-800">Cálculadora de términos</div>
                               <div class="h5 mb-0 font-weight-bold text-gray-600"><?=$total_calendars;?></div>
                               <div class="mt-2 mb-0 text-muted">
                                 <span>Calendarios de término creados</span>
                               </div>
                             </div>
                             <div class="col-auto">
                               <img src="<?=base_url('/assets/icons/calendar_illustration.svg');?>">
                             </div>
                           </div>
                         </div>
                       </div>
                  </div>
                  <div class="col-md-6">
                     <div class="card h-100 lk-card">
                         <div class="card-body">
                           <div class="row no-gutters align-items-center">
                             <div class="col mr-2">
                               <div class="font-weight-bold text-uppercase mb-1 text-gray-800">Buscador de sentencias</div>
                               <div class="h5 mb-0 font-weight-bold text-gray-600"><?=$count_searches;?></div>
                               <div class="mt-2 mb-0 text-muted">
                                 <span>Búsquedas realizadas</span>
                               </div>
                             </div>
                             <div class="col-auto">
                               <img src="<?=base_url('/assets/icons/search_illustration.svg');?>">
                             </div>
                           </div>
                         </div>
                       </div>
                  </div>
               </div>
               <div class="row mt-4" style="margin: 10px;">
         <h4 class="text-primary text-center title">Historial de búsqueda</h4>
         <div class="table-responsive" style="max-height:300px; overflow-y:scroll;">
            <table class="table table-sm text-center">
               <thead>
                  <tr>
                     <th>Circuito</th>
					 <th>Tipo de órgano</th>
					 <th>Materias</th>
					 <th>Búsqueda</th>
                  </tr>
               </thead>
               <tbody>
                  <?php if(count($wishlist) > 0):
                     foreach($wishlist as $wish): 
						$search = ["+"];
						$replace = [" "];
						$wish->circuito = str_replace($search, $replace, $wish->circuito);
						$wish->organo1 = str_replace($search, $replace, $wish->organo1);
					 ?>
                     <tr>
						<td><?=$wish->circuito;?></td>
                        <td><?=$wish->organo1;?></td>
						<td><?=$wish->materia;?></td>
						<td><?=$wish->words;?></td>
                     </tr>
                  <?php endforeach;
                  else: ?>
                     <tr>
                        <td colspan="8">
                           <div class="alert alert-info"><i data-feather="alert-triangle"></i> <b>No has guardado sentencias favoritas</b>
                           </div>
                        </td>
                     </tr>
                  <?php endif; ?>
               </tbody>
            </table>
         </div>
      </div>
			  </div><!--//end #accountusage tab-->
			  <?php if($this->session->userdata('role') == 1): ?>
			  <div class="tab-pane fade" id="adminAcc" role="tabpanel" aria-labelledby="adminAcc-tab">
				<form id="frmSendCoupon">
					<div class="form-group">
						<label class="control-label">Ingresa el cupón para compartir con todos los usuarios de lawkit</label>
						<input type="text" class="lawkit-input" name="coupon" id="coupon">
					</div>
					<div class="form-group">
						<button class="lawkit-btn bg-lk-blue-o2 mt-2" type="submit">ENVIAR CÓDIGO</button>
					</div>
				</form>
			  </div>
			  <?php endif; ?>
			</div>
		</div>
	</div>
</div>

<!-- modal add card -->
<div class="modal fade" tabindex="-1" id="add-card-m">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Tarjetas de crédito</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      	<div id="card-element-errors" role="alert" class="text-danger"></div>
        <form id="frmSaveCard" method="post" action="<?=base_url('account/save_card');?>">	
			<input type="hidden" name="cardToken" id="cardToken">		
			<div id="card-element" class="mb-3"></div>
			<div class="d-flex">
				<div>
					<small>Powered by <a href="https://stripe.com/es-mx" target="_blank">Stripe</a></small>
				</div>
				<div class="ml-auto">
					<img src="<?=base_url();?>assets/images/stripe-pago.png" style="max-width:4em;">
				</div>
			</div>
			<button class="lawkit-btn bg-lk-blue-o2 mt-2 btn-pay" type="button">GUARDAR TARJETA</button>
		</form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
      </div>
    </div>
  </div>
</div>



<!-- modal para ingresar cupon manual -->
<div class="modal fade" tabindex="-1" id="mdCuponM">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Ingresa un cupón</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      	<div id="card-element-errors" role="alert" class="text-danger"></div>
        <form id="frmSaveMdCoupon" method="post" action="<?=base_url('account/save_user_coupon');?>">
			<div class="form-group">
				<label for="" class="control-label">Ingresa el cupón</label>
				<input type="text" class="lawkit-input" name="MdCupon" id="MdCupon">
			</div>
			<button class="lawkit-btn bg-lk-blue-o2 mt-2 btn-coupon" type="button">GUARDAR CUPÓN</button>
		</form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
      </div>
    </div>
  </div>
</div>



<!-- save card -->

<script type="text/javascript">

  //cambiar a live en produccion
  var stripe = Stripe('pk_live_51HSqIiEp2krUL6QU2DfRiR4FeaWDWAQYEEXLYuwb7et6c3xUzfX1jyQ3Pst8BW9CKL5kmf2gYCXVetHjmNCiGzTH00CRKr7FVS');
  var elements = stripe.elements();

  var card = elements.create('card');
  card.mount('#card-element')

  card.on("change", function(event){
  	 getBrand();
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

  function getBrand(){
		const tok = $('select#cardBrand option:selected').val();
		if(tok == 0){
			displayError.textContent = "Por favor, seleccione el proveedor de su tarjeta"
		}
		else displayError.textContent = '';
  }


  $('form input').on('keydown', function(e){
  	if( e.keyCode == 13 )
  	{
  		e.preventDefault()
  		return false;
  	}
  })


  $('button.btn-coupon').on('click', function(){
	const c = $('input#MdCupon').val();
	if(c.length <= 0){
		Swal.fire({
			text:'Ingresa el cupón para guardar'
		})
		return;
	}
	$('form#frmSaveMdCoupon').submit();
  })



	$("button.btn-pay").on("click", function(){
		var btn = $(this);
		btn.addClass('disabled').attr('disabled', true).html('Espera...')
		stripe.createToken(card).then(function(res){
			if(res.error){
				btn.removeClass("disabled").removeAttr('disabled').html('Guardar tarjeta');
				Swal.fire({
					type: 'error',
					title: `oops...`,
					text: result.error.message
				})
			} else {
				$('#cardToken').val(res.token.id);
				btn.addClass('disabled').attr('disbled', true).html('...')
				$.ajax({
					url: '/account/savecard',
					type: 'POST',
					dataType:'json',
					data:$('#frmSaveCard').serialize(),
					success: function(data){
						window.location = window.location
					}
				})
			}
		});
	})

	$('#meCode').on('click', function(){
		var codeText = document.getElementById('meCode')
		codeText.select();
		codeText.setSelectionRange(0,99999);
		document.execCommand('copy')
		Swal.fire({
			text: "Código copiado al portapapeles"
		})
	})



	$('form#frmSendCoupon').on('submit', function(e){
		e.preventDefault();
		const coupon = $('input#coupon').val();
		if(coupon.length <= 0){
			Swal.fire({
				text: 'Ingresa el cupón para compartir con todos los usuarios de lawkit'
			})
			return false;
		}
	})



</script>