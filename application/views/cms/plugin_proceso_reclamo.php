<div id="content" class="container-fluid">
	<div class="page-header">
		<h1> <?php echo $page_title; ?><small></small></h1>
	</div>
	<?php if(!empty($create_new_row)):?>
	<div class="row">
		<div class="col-lg-12">
			<div class="well">
			<div class="row">
				<div class="col-lg-4">
					<form class="form-search left" method="POST" action="<?php echo $this->config->site_url('cms/'.strtolower($this->current_plugin).'/search_filter_redirect')?>">
						<div class="input-group">
							<input type="text" class="form-control" name="SEARCH" placeholder="Reclamo o nombre">
							<span class="input-group-btn">
								<input type="submit" class="btn btn-default" value="Buscar" />
							</span>
						</div>
					</form>
				</div>
				<div class="col-lg-5"></div>
				<div class="col-lg-3">
					<div class="dropdown">
						<button class="btn btn-block btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown">
							Estado del proceso
							<span class="caret"></span>
						</button>
						<ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu1">
							<li role="presentation"><a role="menuitem" tabindex="-1" href="<?php echo $this->config->site_url('cms/'.strtolower($this->current_plugin).'/index/'.(($this->uri->segment(4) == "")?'display_all':$this->uri->segment(4)))?>/RECEPCION/<?php echo $this->uri->segment(6)?>">Recepci&oacute;n</a></li>
							<li role="presentation"><a role="menuitem" tabindex="-1" href="<?php echo $this->config->site_url('cms/'.strtolower($this->current_plugin).'/index/'.(($this->uri->segment(4) == "")?'display_all':$this->uri->segment(4)))?>/APROBACION/<?php echo $this->uri->segment(6)?>">Aprobaci&oacute;n</a></li>
							<li role="presentation"><a role="menuitem" tabindex="-1" href="<?php echo $this->config->site_url('cms/'.strtolower($this->current_plugin).'/index/'.(($this->uri->segment(4) == "")?'display_all':$this->uri->segment(4)))?>/REPARACION/<?php echo $this->uri->segment(6)?>">Reparaci&oacute;n</a></li>
							<li role="separator" class="divider"></li>
							<li role="presentation"><a role="menuitem" tabindex="-1" href="<?php echo $this->config->site_url('cms/'.strtolower($this->current_plugin).'/index/'.(($this->uri->segment(4) == "")?'display_all':$this->uri->segment(4)))?>/ENTREGA/<?php echo $this->uri->segment(6)?>">Finalizado</a></li>
						</ul>
					</div>
				</div>
			</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-lg-12">
			<?php echo $pagination;?>
		</div>
	</div>
	<?php endif?>
	<div class="row">
		<div class="col-lg-12">
			<table class="table table-striped table-hover">
				<thead>
					<tr>
						<?php foreach($header as $i => $th):?>
						<?php if($i > 0):?>
						<th><?=$th?></th>
						<?php endif; endforeach?>
					</tr>
				</thead>
				<tbody>
					<?php echo $body?>
				</tbody>
			</table>
		</div>
	</div>
</div>
<script type="text/javascript">
	function listfilter_function(){
		var filter = $('select#LISTFILTER').val();
		
		location.href = '<?php echo $this->config->site_url('cms/'.strtolower($this->current_plugin).'/index')?>/'+filter+'/<?php echo $this->uri->segment(5)?>';
	}
</script>