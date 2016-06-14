<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<head>
	<meta http-equiv="Content-Type" content="<?php echo $RESOURCE_THEME_HTMLTYPE?>; charset=<?php echo $RESOURCE_THEME_CHARSET?>" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
	<title><?php echo $engine_show_header_website.'::'.$engine_show_header_pagename; ?></title>
	<!-- FRAMEWORK STYLESHEETS -->
	<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />
    <link rel="stylesheet" href="<?php echo base_url('library/cms/css/bootstrap.min.css'); ?>" type="text/css" media="screen" />
    <link rel="stylesheet" href="<?php echo base_url('library/cms/css/bootstrapaddons.css'); ?>" type="text/css" media="screen" />
    <link rel="stylesheet" href="<?php echo base_url('library/cms/css/menu.css'); ?>" type="text/css" media="screen" />
    <link rel="stylesheet" href="<?php echo base_url('library/cms/css/preview.css'); ?>" type="text/css" media="screen" />
    <link rel="stylesheet" href="<?php echo base_url('library/cms/css/prometheus.css'); ?>" type="text/css" media="screen" />
	<link rel="stylesheet" type="text/css" href="<?php echo base_url('library/cms/css/prettify.css')?>"></link>
	<link rel="stylesheet" type="text/css" href="<?php echo base_url('library/cms/src/bootstrap-wysihtml5.css')?>"></link>
	<link rel="stylesheet" href="<?php echo base_url('library/cms/js/fileUploader/css/jquery.fileupload-ui.css')?>" type="text/css" media="screen" />
	<link rel="stylesheet" type="text/css" href="<?php echo base_url('library/cms/css/prettify.css')?>"></link>
	<link rel="stylesheet" type="text/css" href="<?php echo base_url('library/cms/src/bootstrap-wysihtml5.css')?>"></link>
	<link rel="stylesheet" type="text/css" href="<?php echo base_url('library/cms/js/datetimepicker/build/css/bootstrap-datetimepicker.min.css')?>"></link>
	    
    <!-- FRAMEWORK JAVASCRIPT -->
	<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js" ></script>
	<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jqueryui/1.11.1/jquery-ui.min.js" ></script>
    <script src="<?php echo base_url('library/cms/js/moment-develop/moment.js')?>"></script>
	<script type="text/javascript">var base_url = '<?php echo base_url(); ?>';</script>
    <script src="<?php echo base_url('library/cms/js/modernizr.js')?>"></script>
    <script src="<?php echo base_url('library/cms/js/bootstrap.min.js')?>"></script>
    <script src="<?php echo base_url('library/cms/js/excanvas.min.js')?>"></script>
    <script src="<?php echo base_url('library/cms/js/menu.js')?>"></script>
    <script src="<?php echo base_url('library/cms/js/wysihtml5-0.3.0.js')?>"></script>
	<script src="<?php echo base_url('library/cms/js/prettify.js')?>"></script>
	<script src="<?php echo base_url('library/cms/src/bootstrap-wysihtml5.js')?>"></script>	
	<script src="<?php echo base_url('library/cms/js/fileUploader/js/vendor/jquery.ui.widget.js')?>"></script>
	<script src="<?php echo base_url('library/cms/js/fileUploader/js/jquery.iframe-transport.js')?>"></script>
	<script src="<?php echo base_url('library/cms/js/fileUploader/js/jquery.fileupload.js')?>"></script>
	<script src="<?php echo base_url('library/cms/js/uploader.js')?>"></script>
	<script src="<?php echo base_url('library/cms/js/datetimepicker/build/js/bootstrap-datetimepicker.min.js')?>"></script>
	<script src="<?php echo base_url('library/cms/js/instafeed.min.js')?>"></script>
	<script src="<?php echo base_url('library/cms/js/slick.min.js')?>"></script>
    <script type="text/javascript" src="https://www.google.com/jsapi"></script>

	<script type="text/javascript">
	$(function(){
		$('.textarea').wysihtml5();
		$(prettyPrint);
		
		$(".alert").alert()
	});
	$(document).ready(function() {
	    $("form").keypress(function(e) {
	        if (e.which == 13) {
	            return false;
	        }
	    });
	});
	</script>

</head>

<!-- BODY BEGGINS -->	
<body>
	
<!-- ========================
            Header
============================= -->
<header>

<nav class="navbar navbar-inverse" role="navigation">
	<!-- Brand and toggle get grouped for better mobile display -->
	<div class="navbar-header col-lg-2">
  	<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
		<span class="sr-only">Toggle navigation</span>
		<span class="icon-bar"></span>
		<span class="icon-bar"></span>
		<span class="icon-bar"></span>
  	</button>
  	<a class="navbar-brand" href="<?php echo $this->config->site_url('cms')?>"><img src="<?php echo base_url('library/cms/img/caja_verde.png')?>" alt="" /> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;CajaVerde</a>
	</div>

	<!-- breadcrumb -->
	<div class="navbar-left visible-lg visible-md">
		<?php
		if($this->session->userdata('logged_in')):
			echo set_breadcrumb();
		endif;?>
	</div>
	
	<!-- Collect the nav links, forms, and other content for toggling -->
	<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
	<?php if($this->session->userdata('logged_in')):?>
	<ul class="nav navbar-nav visible-xs">
    	<?php foreach($CMS_PAGES_LIST as $btn):?>
    	<?php if($btn->FLAG_SECURE <= $this->session->userdata('ACCESS_LEVEL')):?>
    	<?php $active = ($btn->PATH === $engine_show_header_pagename)? 'class="active"':'';?>
    	<?php $href = (!empty($btn->CHILDS))?'javascript:void(0);':$this->config->site_url("cms/".strtolower($btn->PATH))?>
        <li <?=$active?>>
            <a href="<?=$href?>"><?=$btn->LABEL?></a>
            <?php if (count($btn->CHILDS) > 0):?>
            <span><?=count($btn->CHILDS)?></span>
            <ul>
            	<?php foreach($btn->CHILDS as $btnchild):?>
                <li><a href="<?=$this->config->site_url("cms/".strtolower($btnchild->PATH))?>"><?=$btnchild->LABEL?></a></li>
                <?php endforeach?>
            </ul>
            <?php endif;?>
        </li>
		<?php endif;?>
        <?php endforeach;?>
	</ul>
	
	<ul class="nav navbar-nav navbar-right">
		<li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo $this->session->userdata('USERNAME')?> <b class="caret"></b></a>
			<ul class="dropdown-menu">
				<li><a href="<?php echo $this->config->site_url('cms/panel_perfil/')?>"><i class="glyphicon glyphicon-edit"></i> Editar Perfil</a></li>
				<?php if($this->session->userdata('ACCESS_LEVEL') >= 999):?>
				<li><a href="<?php echo $this->config->site_url('cms/panel_usuarios/')?>"><div class="glyphicon glyphicon-user"></div> Administrar Usuarios</a></li>
				<?php endif?>
				<li class="divider"></li>
				<li><a href="<?php echo $this->config->site_url("cms/panel_login/logoff")?>"><i class="glyphicon glyphicon-ban-circle"></i> Cerrar Sesión</a></li>
			</ul>
		</li>
	</ul>
	<?php endif;?>
	</div><!-- /.navbar-collapse -->
</nav>
</header>


<?php echo $this->fw_alerts->display_alert_message();?>

<!-- ========================
            Sidebar
============================= -->
<aside class="visible-lg visible-md">
	<?php if($this->session->userdata('logged_in')):?>
    <!-- search -->
    <form class="form-search">
        <div class="input-prepend">
        	<!--
            <button type="submit" class="btn"></button>
            <input type="text" class="search-query"> -->
        </div>
    </form>

    <!-- menu -->
    <ul class="sideMenu">
    	<?php foreach($CMS_PAGES_LIST as $btn):?>
    	<?php if($btn->FLAG_SECURE <= $this->session->userdata('ACCESS_LEVEL')):?>
    	<?php $active = ($btn->PATH === $engine_show_header_pagename)? 'class="active"':'';?>
    	<?php $href = (!empty($btn->CHILDS))?'javascript:void(0);':$this->config->site_url("cms/".strtolower($btn->PATH))?>
        <li <?=$active?>>
            <a href="<?=$href?>"><?=$btn->LABEL?></a>
            <?php if (count($btn->CHILDS) > 0):?>
            <span><?=count($btn->CHILDS)?></span>
            <ul>
            	<?php foreach($btn->CHILDS as $btnchild):?>
            	<?php $nonsent	= ($NONSENT_PAYROLLS > 0 && $btnchild->PATH == 'plugin_payrolls')?' <span class="badge">'.$NONSENT_PAYROLLS.'</span>':'';?>
                <li><a href="<?=$this->config->site_url("cms/".strtolower($btnchild->PATH))?>"><? echo $btnchild->LABEL; echo $nonsent?></a></li>
                <?php endforeach?>
            </ul>
            <?php endif;?>
        </li>
        <?php endif;?>
        <?php endforeach;?>
    </ul>
	<?php endif;?>
</aside>