<div id="content" class="container-fluid">
	<div class="page-header">
		<h1> <?php echo $page_title; ?><small></small></h1>
	</div>
	<?php if(!empty($create_new_row)):?>
	<div class="row">
		<div class="col-lg-12">
			<div class="well">
			<div class="row">
				<div class="col-lg-8">
					<a class="btn btn-primary" href="<?=base_url("cms/".$current_plugin."/create_new_row")?>"><?=$create_new_row?></a>
				</div>
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