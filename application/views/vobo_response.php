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
					<?php if($sent):?>
					<div class="panel panel-success">
						<div class="panel-heading">Mensaje enviado</div>
						<div class="panel-body">El mensaje ha sido enviado exitosamente.</div>
					</div>
					<?php else:?>
					<div class="panel panel-danger">
						<div class="panel-heading">Error en envío</div>
						<div class="panel-body">Hubo un error al tratar de enviar tu mensaje, por favor escribe a <a href="mailto:tumicayala@grupoi5.com">tumicayala@grupoi5.com</a> especificando este error.</div>
					</div>
					<?php endif;?>
				</div>
			</div>
		</div>
	</div>
</div>