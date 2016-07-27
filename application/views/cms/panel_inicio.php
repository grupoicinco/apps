<script type='text/javascript'>
google.charts.load('current', {'packages':['gauge']});
      google.charts.setOnLoadCallback(drawChart);
      function drawChart() {

        var data = google.visualization.arrayToDataTable([
          ['Label', 'Value'],
          ['Recomen', <?=$pollresults->POLL_RECOMMENDATION?>],
          ['Satisfa', <?=$pollresults->POLL_SATISFACTION?>],
          ['Comunica', <?=$pollresults->POLL_INFO?>]
        ]);

        var options = {
          width: 400, height: 120,
          greenFrom: 80, greenTo: 100,
          yellowFrom:20, yellowTo: 80,
          redFrom: 0, redTo: 20,
          minorTicks: 5
        };

        var chart = new google.visualization.Gauge(document.getElementById('chart_div'));

        chart.draw(data, options);
      }
</script>
<div id="content" class="container-fluid">
	<div class="page-header">
		<h1>TUMI <small>Asistente administrativo.</small></h1>
	</div>
	
	<div class="row">
		<div class="col-lg-7 widget">
			<h5>Reclamos pendientes<a class="btn pull-right" role="button" data-toggle="collapse" href="#collapseDescription" aria-expanded="false" aria-controls="collapseExample"><small>Ver m&aacute;s <span class="caret"></span></small></a></h5>
			<p id="collapseDescription" class="collapse">&Uacute;ltimos reclamos pendientes ingresados al sistema que pueden estar atrasados en la entrega como en proceso. </p>
			
			<table class="table table-striped table-hover">
				<thead>
					<tr>
						<th>Reclamo</th>
						<th>Nombre del Cliente</th>
						<th>Fecha</th>
						<th>Etapa</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach($reclaims as $reclaim):?>
					<tr>
						<td><a href="http://grupoi5.com/app/cms/plugin_proceso_reclamo/update_table_row/<?php echo $reclaim->ID?>"><?php echo str_pad($reclaim->ID, 5, "0", STR_PAD_LEFT);?></a></td>
						<td><?php echo $reclaim->RECLAIM_CLIENT_NAME?></td>
						<td><?php $date = date_create($reclaim->RECLAIM_DATE); echo date_format($date, 'd/m/Y')?></td>
						<td><span class="label <?php echo $reclaim->PROCESS_STAGES[0]?>"><?php echo $reclaim->PROCESS_STAGES[1]?><span></span></span></td>
					</tr>
					<?php endforeach;?>
				</tbody>
			</table>
		</div>
		<div class="col-lg-5 widget">
			<h5>Encuesta de Servicio</h5>
			<div id='chart_div' style='width: 100%; height: 120px;'></div>
		</div>
		
	</div>
</div>