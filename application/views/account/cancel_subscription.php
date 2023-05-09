
	<div class="row text-center mt-5">
		<div class="col-md-12">
			<h1 style="color: #03a9f4;">¡Nos entristece mucho que te vayas!</h1>
			<i class="fa fa-frown-o" style="font-size: 10rem;"></i>
		</div>
	</div>
	<div class="row mt-4 mb-4">
		<div class="col-md-4 offset-md-4">
			<form role="form" action="<?=base_url();?>account/cancel_subscription" method="post">
				<div class="form-group">
					<p>Selecciona el mótivo por el cual cancelas tu suscripción. Ayudanos a mejorar nuestros servicios...</p>
					<ul class="list-unstyled">
						<li>
							<div class="form-check">
							  <input class="form-check-input" type="radio" name="reason" id="radio1" value="No tiene buena interfaz">
							  <label class="form-check-label" for="radio1">
							    No tiene buena interfaz
							  </label>
							</div>
						</li>
						<li>
							<div class="form-check">
							  <input class="form-check-input" type="radio" name="reason" id="radio2" value="Es complicado, No le entiendo">
							  <label class="form-check-label" for="radio2">
							    Es complicado, No le entiendo
							  </label>
							</div>
						</li>
						<li>
							<div class="form-check">
							  <input class="form-check-input" type="radio" name="reason" id="radio3" value="El sistema tiene muchos fallos">
							  <label class="form-check-label" for="radio3">
							    El sistema tiene muchos fallos
							  </label>
							</div>
						</li>
					</ul>
				</div>
				<div class="form-group">
					 
					<label for="exampleInputEmail1">
						Otro motivo
					</label>
					<textarea class="form-control" name="other" id="other" rows="4"></textarea>
				</div>
				<a role="button" href="<?=base_url();?>" class="btn bg-lk-blue-o2 text-white">
					Regresar
				</a>
				<button type="submit" class="btn btn-danger">
					Cancelar mi suscripción
				</button>
			</form>
		</div>
	</div>
