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
			<a href="#" class="thumbnail">
				<iframe src="http://snapwidget.com/sl/?u=dHVtaXRyYXZlbHxpbnwzNTB8M3wzfHxub3w1fG5vbmV8b25TdGFydHx5ZXN8bm8=&ve=090914" title="Instagram Widget" class="snapwidget-widget" allowTransparency="true" frameborder="0" scrolling="no" style="border:none; overflow:hidden; width:380px; height:375px;"></iframe>
			</a>
		</div>
		
	</div>
</div>