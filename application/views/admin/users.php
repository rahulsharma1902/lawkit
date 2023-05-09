<script>
	$(document).ready(function(){
		$('.viewActivity').on('click', function(){
			let uid = $(this).attr('data-uid');
			var somedata = false;
			$.ajax({
				url: window.location.origin + '/api/customer_activity/uid/' + uid,
				type: 'GET',
				dataType: 'json',
				success:function(response){

					if(response.calendars > 0)
						$('.TotalCalendarsCount').html(response.calendars + ' calendarios creados');

					if(response.searches.count_searches > 0){
						$('.TotalSearchesCount').html(response.searches.count_searches + ' búsquedas')
					}

					if(response.cwords){
						$('<p>').addClass('text-primary font-weight-lighter').text('Palabras buscadas')
						.appendTo('.cwords');
						$('<ul>').addClass('list-group list-group-flush cwords-list')
						.appendTo('.cwords');
						$.each(response.cwords, function(index, val) {
							$('<li>').addClass('list-group-item d-flex justify-content-between align-items-center')
							.append(index)
							.append(
								$('<span>').addClass('badge badge-primary badge-pill').text(val)
								)
							.appendTo('.cwords-list');
						});
					}


					if(response.browsers.length > 0){
						somedata = true;
						$('<p>').addClass('text-primary font-weight-lighter m-0').html('Exploradores de Internet')
						.appendTo($('.browsers'));
						$.each(response.browsers, function(index, val) {
							 $('<div>').addClass('row')
							 .append(
							 	$('<div>').addClass('col-12 col-md-2').text(val.browser)
							 	)
							 .append(
							 	$('<div>').addClass('col-12 col-md-10')
							 	.append(
							 		$('<div>').addClass('progress')
							 		.append(
							 			$('<div>').addClass('progress-bar bg-primary').attr({
							 				role:'progressbar',
							 				style:'width:'+val.percent+'%',
							 				'aria-valuenow':val.percent,
							 				'aria-valuemin':val.percent,
							 				'aria-valuemax':'100'
							 			}).text(val.percent + '%')
							 			)
							 		)
							 	)
							 .appendTo('.browsers')
						});

					}

					if(response.sos.length > 0){
						somedata = true;
						$('<p>').addClass('text-danger font-weight-lighter m-0').html('Sistemas Operativos Usados')
						.appendTo($('.sos'));
						$.each(response.sos, function(index, val) {
							 $('<div>').addClass('row')
							 .append(
							 	$('<div>').addClass('col-12 col-md-2').text(val.os)
							 	)
							 .append(
							 	$('<div>').addClass('col-12 col-md-10')
							 	.append(
							 		$('<div>').addClass('progress')
							 		.append(
							 			$('<div>').addClass('progress-bar bg-danger').attr({
							 				role:'progressbar',
							 				style:'width:'+val.percent+'%',
							 				'aria-valuenow':val.percent,
							 				'aria-valuemin':val.percent,
							 				'aria-valuemax':'100'
							 			}).text(val.percent + '%')
							 			)
							 		)
							 	)
							 .appendTo('.sos')
						});
					}



					if(response.devices.length > 0){
						somedata = true;
						$('<p>').addClass('text-warning font-weight-lighter m-0').html('Dispositivos Usados')
						.appendTo($('.devices'));
						$.each(response.devices, function(index, val) {
							 $('<div>').addClass('row')
							 .append(
							 	$('<div>').addClass('col-12 col-md-2').text(val.device)
							 	)
							 .append(
							 	$('<div>').addClass('col-12 col-md-10')
							 	.append(
							 		$('<div>').addClass('progress')
							 		.append(
							 			$('<div>').addClass('progress-bar bg-warning').attr({
							 				role:'progressbar bg-warning',
							 				style:'width:'+val.percent+'%',
							 				'aria-valuenow':val.percent,
							 				'aria-valuemin':val.percent,
							 				'aria-valuemax':'100'
							 			}).text(val.percent + '%')
							 			)
							 		)
							 	)
							 .appendTo('.devices')
						});
					}
					$('<h5>').html('Búsquedas favoritas');
					$('<table>').addClass('table table-striped table-sm table-bordered')
					.append(
						$('<thead>')
						.append(
							$('<tr>')
							.append(
								$('<td>')
								.append(
									$('<small>').addClass('text-primary font-weight-lighter').html('Búsqueda')
									)
								)
							.append(
								$('<td>')
								.append(
									$('<small>').addClass('text-primary font-weight-lighter').html('Circuito')
									)
								)
							.append(
								$('<td>')
								.append(
									$('<small>').addClass('text-primary font-weight-lighter').html('Tipo de Órgano')
									)
								)
							.append(
								$('<td>')
								.append(
									$('<small>').addClass('text-primary font-weight-lighter').html('Materia')
									)
								)
							.append(
								$('<td>')
								.append(
									$('<small>').addClass('text-primary font-weight-lighter').html('Órgano Jurisdiccional')
									)
								)
							.append(
								$('<td>')
								.append(
									$('<small>').addClass('text-primary font-weight-lighter').html('Tipo de Expediente')
									)
								)
							.append(
								$('<td>')
								.append(
									$('<small>').addClass('text-primary font-weight-lighter').html('Número de Expediente')
									)
								)
							.append(
								$('<td>')
								.append(
									$('<small>').addClass('text-primary font-weight-lighter').html('Documento')
									)
								)
							.append(
								$('<td>')
								.append(
									$('<small>').addClass('text-primary font-weight-lighter').html('Fecha')
									)
								)
							)
						)
					.append(
						$('<tbody>').addClass('searches-content')
						)
					.appendTo('.searches')


					if(response.searches.RowsFavs.length > 0){
						$.each(response.searches.RowsFavs, function(index, val) {
							 $('<tr>')
							 .append(
							 		$('<td>').html(val.words)
							 	)
							 .append(
							 		$('<td>').html(val.circuito)
							 	)
							 .append(
							 		$('<td>').html(val.organo1)
							 	)
							 .append(
							 		$('<td>').html(val.materia)
							 	)
							 .append(
							 		$('<td>').html(val.organo2)
							 	)
							 .append(
							 		$('<td>').html(val.expediente)
							 	)
							 .append(
							 		$('<td>').html(val.numero)
							 	)
							 .append(
							 		$('<td>')
							 		.append(
							 			$('<a>').attr({
							 				href:'https://buscador.lawkit.mx/file/' + val.link,
							 				class:'btn bg-light btn-sm',
							 				target:'_blank'
							 			}).append('Ver')
							 			)
							 	)
							 .append(
							 		$('<td>').html(val.search_date)
							 	)
							 .appendTo('.searches-content')
						});
					}

					if(somedata)
						$('.message').hide();
				}
			})			
		})
		$('#CustActModal').on('hidden.bs.modal', function(){
			$('.TotalSearchesCount').empty();
					$('.TotalCalendarsCount').empty();
					$('.TotalContracts').empty();
					$('.cwords').empty();
					$('.browsers').empty();
					$('.sos').empty();
					$('.devices').empty();
					$('.searches').empty();

		})
	})
</script>
<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="<?=base_url();?>admin/dashboard">Dashboard</a></li>
    <li class="breadcrumb-item active" aria-current="page">Usuarios</li>
  </ol>
</nav>
<hr>
<div class="row mb-md-3">
	<div class="col col-md-6">
		<form method="post" action="<?=base_url();?>admin/users_activity">
			<div class="row">
				<div class="col-md-2">
					<input type="text" readonly class="form-control-plaintext" id="staticEmail2" value="Buscar en">
				</div>
				<div class="col">
					<select class="custom-select custom-select-sm" name="search_field" id="search_field">
						<option value="fname">Nombre</option>
						<option value="lname">Apellidos</option>
						<option value="email">Correo Electrónico</option>
					</select>
				</div>
				<div class="col">
					<input type="search" class="form-control form-control-sm" name="buscar" placeholder="ejemplo: Daniel Puentes">
				</div>
				<div class="col">
					<button type="submit" class="btn bg-light btn-sm">Buscar</button>
				</div>
			</div>
		</form>
	</div>
</div>
<table class="table table-sm table-hover table-striped table-bordered">
				<thead>
					<tr>
						<th>
							<p class="m-0 text-primary">
								<small>Nombre</small>
							</p>
						</th>
						<th>
							<p class="m-0 text-primary">
								<small>Correo electrónico</small>
							</p>
						</th>
						<th>
							<p class="m-0 text-primary">
								<small>Fecha de registro</small>
							</p>
						</th>
						<th>
							<p class="m-0 text-primary">
								<small>Stripe Id</small>
							</p>
						</th>
						<th>
							<p class="m-0 text-primary">
								<small>Stripe Cliente Id</small>
							</p>
						</th>
						<th>
							<p class="m-0 text-primary">
								<small>Actividad</small>
							</p>
						</th>
				</thead>
				<tbody>
					<?php if(count($users) > 0): ?>
						<?php foreach($users as $u):?>
						<tr>
							<td><?=$u->fname;?> <?=$u->lname;?></td>
							<td><?=$u->email;?></td>
							<td><?=$u->created_at;?></td>
							<td><?=$u->stripe_subscription_id;?></td>
							<td><?=$u->stripe_customer_id;?></td>
							<td>
								<button type="button" class="btn bg-light btn-sm viewActivity" data-target="#CustActModal" data-toggle="modal" data-uid="<?=$u->uid;?>"><i data-feather="bar-chart-2"></i></button>
							</td>
						</tr>
					<?php endforeach;?>
					<?php else: ?>
						<tr>
							<td colspan="6">
								<p class="text-primary m-0 text-center">No hay usuarios registrados</p>
							</td>
						</tr>
					<?php endif;?>
				</tbody>
				<tfooter>
					<tr>
						<td id="pagination" align="right" class="text-right" colspan="6"><?=$links;?></td>
					</tr>
				</tfooter>
			</table>


<div class="modal fade" id="CustActModal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-scrollable modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="staticBackdropLabel">Actividad Individual de Usuario</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="message"><i>...Obteniendo datos</i></div>
        <div class="container-fluid">
        	<div class="row">
        		<div class="col-12 col-md-4">
	        		<div class="card">
	        			<div class="card-body TotalSearchesCount">0 Búsquedas</div>
	        		</div>
	        	</div>
	        	<div class="col-12 col-md-4">
	        		<div class="card">
	        			<div class="card-body TotalCalendarsCount">0 Calendarios</div>
	        		</div>
	        	</div>
	        	<div class="col-12 col-md-4">
	        		<div class="card">
	        			<div class="card-body TotalContracts">0 Contratos</div>
	        		</div>
	        	</div>
        	</div>
        </div>
        <div class="cwords my-5 border p-2 overflow-auto rounded shadow-sm h-50"></div>
        <div class="browsers mb-3"></div>
        <div class="sos mb-3"></div>
        <div class="devices mb-3"></div>
        <div class="searches"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn bg-light btn-sm" data-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>
			