<div id="content" class="container">
	<div class="page-header">
		<h1>Inicio de sesión <small>Login al sistema.</small></h1>
	</div>
	<form class="form-horizontal col-lg-4" role="form" action="<?php echo $this->config->site_url().'cms/panel_login/session'?>" method="POST">
		<div class="form-group">
			<label class="control-label col-lg-4" for="inputUser">Usuario</label>
			<div class="col-lg-8">
				<input type="text" class="form-control" id="inputUser" placeholder="Usuario" name="login_username"/>
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-lg-4" for="inputPassword">Contrase&ntilde;a</label>
			<div class="col-lg-8">
				<input type="password" class="form-control" id="inputPassword" placeholder="Contrase&ntilde;a" name="login_password" />
			</div>
		</div>
   		<div class="form-group">
   			<div class="col-lg-4">&nbsp;</div>
   			<div class="col-lg-8">
	   			<button type="submit" class="btn btn-primary">Ingresa</button>
   			</div>
   		</div>
   	</form>
</div>