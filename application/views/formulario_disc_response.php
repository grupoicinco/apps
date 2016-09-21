<nav class="navbar navbar-default navbar-static-top">
	<div class="container-fluid">
		<div class="navbar-header">
			<a class="navbar-brand icinco" href="#">
				<img src="<?=base_url('library/images/icinco-i-vector-white.png')?>" />
			</a>
		</div>
	</div>
</nav>
<div id="content-container" class="container-fluid">
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
		<?php 
		$data = array('id' => 'tumitracerform');
		echo form_open("", $data);
		?>
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">FORMULARIO DISC</h3>
			</div>
			<div class="panel-body">
				<div class="alert alert-success" role="alert">
					<p><strong>¡Felicidades!</strong><br />
						Has terminado exitosamente la prueba DISC de personalidad. Se te ha enviado una copia a tu correo para que puedas ver tu resultado.
					</p>
				</div>
			</div>
		</div>
	</form>
	</div>
</div>
</div>