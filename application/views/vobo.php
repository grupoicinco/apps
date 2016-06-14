<header class="navbar navbar-static-top navbar-inverse">
	<div class="container-fluid">
		<div class="navbar-header"></div>
		<button data-target="#bs-example-navbar-collapse-1" data-toggle="collapse" class="navbar-toggle" type="button">
		<span class="sr-only">Toggle navigation</span>
		<span class="icon-bar"></span>
		<span class="icon-bar"></span>
		<span class="icon-bar"></span>
		</button>
		<div id="bs-example-navbar-collapse-1" class="collapse navbar-collapse">
			<ul class="nav navbar-nav navbar-right visible-lg visible-md">
				<li><a href="http://facebook.com/tumitravel" target="_blank"><i class="fa fa-facebook"></i></a></li>
				<li><a href="http://instagram.com/tumitravel" target="_blank"><i class="fa fa-instagram"></i></a></li>
				<li><a href="https://www.youtube.com/TumiTravel" target="_blank"><i class="fa fa-youtube"></i></a></li>
			</ul>
			
			<ul class="nav navbar-nav visible-xs">
				<li><a href="http://facebook.com/tumitravel" target="_blank"><i class="fa fa-facebook"></i></a></li>
				<li><a href="http://instagram.com/tumitravel" target="_blank"><i class="fa fa-instagram"></i></a></li>
				<li><a href="https://www.youtube.com/TumiTravel" target="_blank"><i class="fa fa-youtube"></i></a></li>
        	</ul>
		</div>
	</div>
</header>
<div class="container-fluid">
	
	<div class="row-fluid">
		<div class="col-lg-4">
			<a href="#" class="thumbnail">
				<iframe src="http://snapwidget.com/sl/?u=dHVtaXRyYXZlbHxpbnwzNTB8M3wzfHxub3w1fG5vbmV8b25TdGFydHx5ZXN8bm8=&ve=090914" title="Instagram Widget" class="snapwidget-widget" allowTransparency="true" frameborder="0" scrolling="no" style="border:none; overflow:hidden; width:375px; height:375px"></iframe>
			</a>
		</div>
		<div class="col-lg-8">
			<div class="row-fluid">
				<div class="col-lg-12">
					<img src="<?=base_url('library/images/TUMI-transparencia.png')?>" />
				</div>
			</div>
			<?php if($valid):?>
			<div class="row-fluid">
				<div class="col-lg-12">
					<div class="panel">
						<div class="panel-body">
							<?php echo form_open(base_url('vobo/response/'.$this->uri->segment(3)), array('role' => 'form'))?>
								<?php echo form_hidden('CLIENT_ID', $this->uri->segment(3));?>
								<?php echo form_hidden('ORDER_ID', $this->uri->segment(4));?>
								<div class="form-group">
									<label for="comentario" class="control-label">Comentario</label>
									<p class="help-block">Escríbenos un comentario acerca de tu decisión del proceso de reparación. Queremos entenderla mejor para darte un servicio de clase mundial. Luego presiona si aceptas o deniegas el continuar con la orden.</p>
								</div>
								<div class="form-group <?=$error?>">
									<textarea name="VOBO_COMMENT" class="form-control" rows="6" <?php if(empty($error)):?>placeholder="Deja tu comentario, luego presiona si aceptas o deniegas continuar la orden."<?php endif?>></textarea>
									<?php if(!empty($error)):?><p class="help-block">Por favor, déjanos un comentario de tu decisión para poder continuar.</p><?php endif?>
								</div>
								<div class="form-group">
									<input name="RESPONSE" type="submit" value="APROBADA" class="btn btn-lg btn-success" />
									<input name="RESPONSE" type="submit" value="DENEGADA" class="btn btn-lg btn-danger" />
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
			<?php endif?>
		</div>
	</div>
</div>