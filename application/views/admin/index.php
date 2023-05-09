
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/wordcloud.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/export-data.js"></script>
<script src="https://code.highcharts.com/modules/accessibility.js"></script>

<div class="row">
	<div class="d-md-flex px-3 w-100">
		<div class="">
			<p class="text-primary"><small>Datos desde <?=$humandatefrom;?> hasta <?=$humandateto;?></small></p>
		</div>
		<div class="w-100 ml-auto">
			<div class="float-right">
				<form method="post" action="<?=base_url();?>admin/dashboard">
					<div class="row">
						<div class="col-12 mb-sm-3 col-md-4">
				    		<input type="date" class="form-control form-control-sm" name="datefrom" id="datefrom" value="<?=$datefrom;?>">
						</div>
						<div class="col-12 mb-sm-3 col-md-4">
				    		<input type="date" class="form-control form-control-sm" name="dateto" id="dateto" value="<?=$dateto;?>">
						</div>
						<div class="col mb-sm-3 col-md">
				  			<button type="submit" class="btn bg-light btn-sm " data-toggle="tooltip" data-placement="top" title="Actualizar">
				  				<i data-feather="refresh-cw"></i>
				  			</button>
						</div>
						<div class="col mb-sm-3 col-md">
				  			<a role="button" class="btn bg-light btn-sm " href="<?=base_url();?>admin/dashboard" data-toggle="tooltip" data-placement="top" title="Mes actual">
				  				<i data-feather="zap"></i>
				  			</a>
						</div>
						<div class="col mb-sm-3 col-md">
				  			<button type="button" class="btn bg-light btn-sm " id="report" data-toggle="tooltip" data-placement="top" title="Exportar a PDF">
				  				<i data-feather="file"></i>
				  			</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-md-3">
		<div class="card text-dark bg-light mb-3">
		  <div class="card-header">Usuarios con actividad</div>
		  <div class="card-body">
		    <div class="d-flex">
		    	<div class="card-icon">
		    		<i data-feather="activity"></i>
		    	</div>
		    	<div class="ml-auto">
		    		<h1><?=count($act->qty_customers);?></h1>
		    	</div>
		    </div>
		  </div>
		</div>
	</div>
	<div class="col-md-3">
		<div class="card text-dark bg-light mb-3">
		  <div class="card-header">Clientes Registrados</div>
		  <div class="card-body">
		    <div class="d-flex">
		    	<div class="card-icon">
		    		<i data-feather="users"></i>
		    	</div>
		    	<div class="ml-auto">
		    		<h1><?=count($act->customers_registered);?></h1>
		    	</div>
		    </div>
		  </div>
		</div>
	</div>
	<div class="col-md-3">
		<div class="card text-dark bg-light mb-3">
		  <div class="card-header">Búsquedas Realizadas</div>
		  <div class="card-body">
		    <div class="d-flex">
		    	<div class="card-icon">
		    		<i data-feather="search"></i>
		    	</div>
		    	<div class="ml-auto">
		    		<h1><?=$act->all_searches;?></h1>
		    	</div>
		    </div>
		  </div>
		</div>
	</div>
	<div class="col-md-3">
		<div class="card text-dark bg-light mb-3">
		  <div class="card-header">Calendarios Creados</div>
		  <div class="card-body">
		    <div class="d-flex">
		    	<div class="card-icon">
		    		<i data-feather="calendar"></i>
		    	</div>
		    	<div class="ml-auto">
		    		<h1><?=$all_calendars;?></h1>
		    	</div>
		    </div>
		  </div>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-md-6 col-12">
		<div class="card bg-white border-0">
			<figure class="highcharts-figure">
			    <div id="chart-container"></div>
			</figure>

			<script>
				// Build the chart
			Highcharts.chart('chart-container', {
			    chart: {
			        plotBackgroundColor: '#f8f9fc',
			        plotBorderWidth: null,
			        plotShadow: false,
			        type: 'pie'
			    },
			    exporting:{
			    	enabled:false
			    },
			    title: {
			        text: 'Sistemas Operativos'
			    },
			    tooltip: {
			        pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
			    },
			    accessibility: {
			        point: {
			            valueSuffix: '%'
			        }
			    },
			    credits:{
			    	enabled:false
			    },
			    plotOptions: {
			        pie: {
			            allowPointSelect: true,
			            cursor: 'pointer',
			            dataLabels: {
			                enabled: false
			            },
			            showInLegend: true
			        }
			    },
			    series: [{
			        name: 'Uso',
			        colorByPoint: true,
			        data: <?=$pie_sos;?>
			    }]
			});
			</script>
		</div>
	</div>

	<div class="col-md-6 col-12 mt-xs-3">
		<div class="card bg-white border-0">
			<figure class="highcharts-figure">
			    <div id="bars-container"></div>
			</figure>
		</div>
		<script>
			Highcharts.chart('bars-container', {
    chart: {
        type: 'column',
        plotBackgroundColor: '#f8f9fc',
    },
    title: {
        text: 'Dispositivos'
    },
    credits:{
    	enabled:false
    },
    exporting:{
			    	enabled:false
			    },
    xAxis: {
        categories: [
            'Octubre',
        ],
        crosshair: true
    },
    yAxis: {
        min: 0,
        title: {
            text: 'Ocurrencia de uso'
        }
    },
    tooltip: {
        headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
        pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
            '<td style="padding:0"><b>{point.y} veces</b></td></tr>',
        footerFormat: '</table>',
        shared: true,
        useHTML: true
    },
    plotOptions: {
        column: {
            pointPadding: 0.2,
            borderWidth: 0
        }
    },
    series: <?=$bars_devices;?>
});
      
		</script>
	</div>
</div>

<div class="row mt-3">
	<div class="col-md-6 col-12">
		<div class="card bg-white border-0">
			<figure class="highcharts-figure">
			    <div id="chart-browsers-horizontal-bars"></div>
			</figure>
			<script>
				Highcharts.chart('chart-browsers-horizontal-bars', {
			    chart: {
			        type: 'bar',
			        plotBackgroundColor: '#f8f9fc',
			    },
			    title: {
			        text: 'Navegadores de Internet'
			    },
			    subtitle: {
			        text: 'Fuente: <a target="_blank" href="https://en.wikipedia.org/wiki/List_of_web_browsers">Wikipedia.org</a>'
			    },
			    xAxis: {
			        categories: [<?=$browsers_names;?>],
			        title: {
			            text: null
			        }
			    },
			    exporting:{
			    	enabled:false
			    },
			    yAxis: {
			        min: 0,
			        title: {
			            text: 'Recurrencia',
			            align: 'high'
			        },
			        labels: {
			            overflow: 'justify'
			        }
			    },
			    
			    plotOptions: {
			        bar: {
			            dataLabels: {
			                enabled: false
			            }
			        }
			    },
			    credits: {
			        enabled: false
			    },
			    series: [{
			        name: 'Recurrencia',
			        data: [<?=$browsers_data;?>]
			    }]
			});
			</script>
		</div>
	</div>

	<div class="col-md-6 col-12 mt-xs-3">
		<div class="card bg-white border-0">
			<figure class="highcharts-figure">
			    <div id="chart-anual-tools-activity"></div>
			</figure>
		</div>
		<script>
<?php /*
Highcharts.chart('container', {
    data: {
        csv: document.getElementById('data').innerHTML,
        dateFormat: 'mm/dd/YYYY'
    },
    plotOptions: {
        series: {
            marker: {
                enabled: false
            }
        }
    }
});*/ ?>
		</script>
	</div>
</div>


<div class="row my-5">
	<div class="col">
		<p class="text-primary text-center">PALABRAS MÁS BUSCADAS</p>
		<figure class="highcharts-figure shadow">
			<div id="chart-words"></div>
		</figure>
		<script>
			var text = <?=$act->words;?>;
			var lines = text.split(/[,\. ]+/g),
		    data = Highcharts.reduce(lines, function (arr, word) {
		        var obj = Highcharts.find(arr, function (obj) {
		            return obj.name === word;
		        });
		        if (obj) {
		            obj.weight += 1;
		        } else {
		            obj = {
		                name: word,
		                weight: 1
		            };
		            arr.push(obj);
		        }
		        return arr;
		    }, []);

		Highcharts.chart('chart-words', {
		    accessibility: {
		        screenReaderSection: {
		            beforeChartFormat: '<h5>{chartTitle}</h5>' +
		                '<div>{chartSubtitle}</div>' +
		                '<div>{chartLongdesc}</div>' +
		                '<div>{viewTableButton}</div>'
		        }
		    },
		    series: [{
		        type: 'wordcloud',
		        data: data,
		        name: 'Recurrencia'
		    }],
		    title: {
		        text: 'Palabras clave más usadas en el servicio <b>Buscador de sentencias</b>'
		    },
		    credits:{
			    	enabled:false
			    },
		});
		</script>
	</div>
</div>

<div class="row mt-3">
	<div class="col-12 col-md-6">
		<p class="text-primary text-center">TOP 10 USUARIOS CON MAYORES BÚSQUEDAS REALIZADAS</p>
		<?php if(count($act->all_searches_by_customer) > 0): ?>
			<div class="table-responsive">
				<table class="table table-sm table-hover table-striped table-bordered">
					<thead>
						<tr>
							<th>
								<p class="m-0 text-primary">
									<small>Correo electrónico</small>
								</p>
							</th>
							<th>
								<p class="m-0 text-primary">
									<small>Nombre del Usuario</small>
								</p>
							</th>
							<th>
								<p class="m-0 text-primary">
									<small>Búsquedas Realizadas</small>
								</p>
							</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach($act->all_searches_by_customer as $searches):?>
							<tr>
								<td><?=$searches->email;?></td>
								<td><?=$searches->fname;?> <?=$searches->lname;?></td>
								<td class="text-primary"><?=$searches->total_searches;?></td>
							</tr>
						<?php endforeach;?>
					</tbody>
				</table>
			</div>
		<?php else: ?>
			<div class="alert alert-info">
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
				<strong>No se han realizado búsquedas</strong>
			</div>
		<?php endif;?>
	</div>
	<div class="col-12 col-md-6">
		<p class="text-primary text-center">DETALLE DE ACTIVIDAD POR USUARIO</p>
		<div class="activity_users_ind">
			<table class="table table-sm table-hover table-striped table-bordered">
				<thead>
					<tr>
						<th>
							<p class="m-0 text-primary">
								<small>Correo electrónico</small>
							</p>
						</th>
						<th>
							<p class="m-0 text-primary">
								<small>Nombre</small>
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
							<td><?=$u->email;?></td>
							<td><?=$u->fname;?> <?=$u->lname;?></td>
							<td>
								<button type="button" class="btn bg-light btn-sm" data-toggle="modal" data-target="#ActivityInd"><i data-feather="bar-chart-2"></i> Ver actividad</button>
							</td>
						</tr>
					<?php endforeach;?>
					<?php else: ?>
						<tr>
							<td colspan="3">
								<p class="text-primary m-0 text-center">No hay usuarios registrados</p>
							</td>
						</tr>
					<?php endif;?>
				</tbody>
				<tfooter>
					<tr>
						<td id="pagination" align="right" class="text-right" colspan="3">
							<a href="<?=base_url();?>admin/users_activity" class="btn bg-light">Ver todos</a>
						</td>
					</tr>
				</tfooter>
			</table>
		</div>
	</div>
</div>