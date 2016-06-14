
<div id="content" class="container-fluid">
	<div class="page-header">
		<h1>Mi Perfil <small>Datos generales</small></h1>
	</div>
	<form class="form-horizontal col-lg-8" action="<?php echo $this->config->site_url().'cms/panel_perfil/editar'?>" method="POST" name="login_form" id="login_form">
		<div class="form-group">
			<label class="form-label col-lg-2" for="inputEmail">Email</label>
			<div class="col-lg-10">
				<input type="text" id="inputEmail" class="form-control" placeholder="Correo electrónico" name="EMAIL" value="<?php echo $user_email?>"/>
			</div>
		</div>
		<div class="form-group">
			<label class="form-label col-lg-2" for="inputEmail">Usuario</label>
			<div class="col-lg-10">
				<input type="text" id="inputUser" class="form-control" placeholder="Usuario" name="USERNAME" value="<?php echo $user_name?>"/>
			</div>
		</div>
		<div class="form-group">
			<label class="form-label col-lg-2" for="inputEmail">Contrase&ntilde;a</label>
			<div class="col-lg-10">
				<input type="password" class="form-control" id="inputPassword" placeholder="Contrase&ntilde;a" name="PASSWORD" />
			</div>
		</div>
   		<div class="form-actions">
			<div class="col-lg-8">
   				<input type="submit" class="btn btn-primary" value="Editar Información" />
   				<a href="<?php echo $this->config->site_url('cms')?>" class="btn btn-default">Cancelar</a>
   			</div>
   		</div>
   	</form>
</div>