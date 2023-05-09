<?php if($customer_suscripcion && $customer_suscripcion->status == 1): ?>
	<div class="alert alert-warning">
		<p><strong>Tu suscripción ha sido cancelada</strong></p>
		<p>Adquiere tu plan nuevamente por solo $15.00 MXN cada mes, haciendo clic aquí <i data-feather="arrow-right"></i> <a href="<?=base_url();?>account/upgrade">Actualizar a PRO</a></p>
	</div>
<?php endif; ?>
<?php if(count($invoices) > 0): ?>
	<table class="table">
		<thead>
			<tr>
				<th>NUM.</th>
				<th>DESCRIPCIÓN</th>
				<th>CANT.</th>
				<th>TOTAL</th>
				<th>FACTURA</th>
				<th>DESCARGAR</th>
				<th>PROX. FACT.</th>
				<th>ESTATUS</th>
			</tr>
		</thead>
		<tbody>
<?php 
	foreach($invoices as $key => $invoice):
		$period_start = date("d M. Y", $invoice->lines->data[$key]->period->start);
		$period_end   = date("d M. Y", $invoice->lines->data[$key]->period->end);
		$amount       = strtoupper($invoice->lines->data[$key]->currency) . " " .number_format(($invoice->lines->data[$key]->amount/100),2);
		$qty          = $invoice->lines->data[$key]->quantity;
		$number       = $invoice->number;
		$status       = ($invoice->status == 'paid') ? '<span class="text-success"><i data-feather="check"></i> Pagado</span>' : '<span class="text-muted">'.$invoice->status.'</span>';
?>
	<tr>
		<td><?=$number;?></td>
		<td>
			<p class="text-muted"><small><?=$period_start . " - " . $period_end;?></small></p>
			<p><?=$invoice->lines->data[$key]->description;?></p>
		</td>
		<td>
			<?=$qty;?>
		</td>
		<td>
			<?=$amount;?>
		</td>
		<td>
			<a href="<?=$invoice->hosted_invoice_url;?>" target="_blank" class="btn btn-light btn-sm">
				<i data-feather="eye"></i> Ver
			</a>
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