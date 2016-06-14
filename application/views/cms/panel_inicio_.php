<div id="content" class="container-fluid">
	<div class="page-header">
		<h1>Dashboard <small>Informaci&oacute;n general del sitio.</small></h1>
	</div>
	
	<div class="row">
		<div class="col-lg-7 widget">
			<h6 class="title">Dashboard</h6>
			<table class="table table-striped table-hover">
				<tbody>
					<?php foreach($dashboard as $key => $data):?>
					<tr>
						<td><strong><?php echo $nomenclatura[$key]?></strong></td>
						<td><?php echo $data?></td>
					</tr>
					<?php endforeach?>
				</tbody>
			</table>
		</div>
		
		<div class="col-lg-5 widget">
            <h6 class="title">&iquest;Necesita Asistencia?</h6>
            <p>Si necesita asistencia en el manejo de este sistema de administración, puede contactarnos por medio de este formulario.</p>
                <form class="form-horizontal" role="form" method="post" action="">
    				<div class="form-group <?php echo (form_error('inputName') != '')?'has-error':''?>">
    					<label class="control-label col-lg-2" for="inputName">Nombre</label>
    					<div class="col-lg-10">
    						<input type="text" name="inputName" id="inputName" class="form-control" placeholder="Nombre" value="<?php echo set_value('inputName'); ?>">
							<?php echo form_error('inputName'); ?>
    					</div>
    				</div>
    				<div class="form-group <?php echo (form_error('inputEmail') != '')?'has-error':''?>">
    					<label class="control-label col-lg-2" for="inputEmail">Email</label>
    					<div class="col-lg-10">
    						<input type="text" id="inpuEmail" name="inputEmail" class="form-control" placeholder="Email" value="<?php echo set_value('inputEmail'); ?>">
							<?php echo form_error('inputEmail'); ?>
    					</div>
    				</div>
    				<div class="form-group <?php echo (form_error('inputMessage') != '')?'has-error':''?>">
    					<label class="control-label col-lg-2" for="inputMessage">Mensaje</label>
    					<div class="col-lg-10">
    						<textarea id="inputMessage" name="inputMessage" class="form-control" placeholder="Mensaje"><?php echo set_value('inputMessage'); ?></textarea>
							<?php echo form_error('inputMessage'); ?>
    					</div>
    				</div>
   				    <div class="form-actions">
					    <button type="submit" class="btn btn-primary">Enviar duda</button>
				    </div>
    			</form>
        </div>
	</div>

	<script type="text/javascript">
      google.load("visualization", "1", {packages:["corechart"]});
      google.setOnLoadCallback(drawChart);
      function drawChart() {
        var data = google.visualization.arrayToDataTable([
          ['Día', 'Visitas'],
          
          <?php 
          foreach($analyticsResponse['DATA'] as $i => $visits):
			$day 	= ($i + 1);
			$visits	= (empty($visits))?'0':$visits;
			$date	= date('Y-m-').$day;
			$coma 	= ($date === date('Y-m-d'))?'':',';
          	?>
          ['<?=$day?>',  <?=$visits?>]<?=$coma?>
          <?php endforeach;?>
        ]);

        var options = {
        	<?php $month = array(NULL, 'Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre');?>
          	title: 'Visitas en el mes de <?php echo $month[date('n')]?> [<?=array_sum($analyticsResponse['DATA'])?>]',
		    vAxis: {title: "Visitas"},
		    hAxis: {title: "Día de <?php echo $month[date('n')]?>"}

        };

        var chart = new google.visualization.LineChart(document.getElementById('line_chart_div'));
        chart.draw(data, options);
      }
    </script>
	<div class="row">
		<div class="col-lg-4">
			<div class="row">
				<div class="col-lg-12 widget">
					<h6 class="title">Accesos Google Analytics</h6>
					<?php if(!empty($analytics)):?>
					<div class="row">
						<div class="col-lg-4"><b>Acceso</b></div>
						<div class="col-lg-8">&raquo; <a target="_blank" href="<?php echo $analytics->ANALYTICS_URL?>"><?php echo $analytics->ANALYTICS_URL?></a></div>
					</div>
					<div class="row">
						<div class="col-lg-4"><b>Usuario</b></div>
						<div class="col-lg-8">&raquo; <?php echo $analytics->ANALYTICS_USER?></div>
					</div>
					<div class="row">
						<div class="col-lg-4"><b>Contrase&ntilde;a</b></div>
						<div class="col-lg-8">&raquo; <?php echo $analytics->ANALYTICS_PASSWORD?></div>
					</div>
					<?php endif;?>
				</div>
			</div>
			<div class="row">
				<div class="col-lg-12 widget">
					<h6 class="title">Documentaci&oacute;n</h6>
					<table class="table table-hover">
						<tr>
							<td><strong>Gu&iacute;a de uso</strong></td>
							<td><a href="" class="btn btn-block btn-primary">Descargar <i class="icon-download icon-white"></i></a></td>
						</tr>
					</table>
				</div>
			</div>
		</div>
		<div class="col-lg-8 widget">
			<h6 class="title">Gr&aacute;fico de visitas</h6>
			<?php if($google_api_connect === ''):?>
				<?php if(isset($analyticsResponse) && $analyticsResponse['ERROR'] === FALSE):?>
				<div class="row">
					<div class="col-lg-12 scrollx">
						<table class="table table-bordered">
							<tr>
								<td>
									<div id="line_chart_div" style="height: 330px;"></div>
								</td>
							</tr>
						</table>
					</div>
				</div>
				<?php else:?>
				<div class="alert alert-danger alert-block">
					<h4>Error en la conexión con Analytics</h4>
					<strong>Se obtuvo el siguiente error al tratar de conectar: </strong><?php echo $analyticsResponse['ERROR']?></div>
				<?php endif;?>
			<?php else:?>
				<p>Permite el acceso a la cuenta de Google Analytics para desplegar los datos desde este panel de control. <br>
				Puedes ingresar desde aqu&iacute; presionando el siguiente bot&oacute;n: </p>
				<p><?php echo $google_api_connect;?></p>
			<?php endif;?>
		</div>
	</div>
</div>