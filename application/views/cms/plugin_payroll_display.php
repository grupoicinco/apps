<div id="content" class="container-fluid">
	<div class="page-header">
		<h1> <?php echo $page_title; ?><small></small></h1>
	</div>
	<?php if(!empty($create_new_row)):?>
	<div class="row">
		<div class="col-lg-12">
			<div class="well">
			<div class="row">
				<div class="col-lg-3">
					<a class="btn btn-primary" href="<?=base_url("cms/".$current_plugin."/create_new_row")?>"><?=$create_new_row?></a>
				</div>
				<div class="col-lg-1">&nbsp;</div>
				<div class="col-lg-4">
					<?php if($this->display_filter == 'SEARCH'):?>
					<form class="form-search" style="float:right;" method="POST" action="<?php echo $this->config->site_url('cms/'.strtolower($this->current_plugin).'/search_filter_redirect')?>">
						<input type="text" class="input-medium search-query" name="SEARCH">
						<button type="submit" class="btn">Search</button>
					</form>
					<?php endif;
					if($this->display_filter == 'LIST'):
						$js = 'id="LISTFILTER" onChange="listfilter_function();" class="form-control"';
						echo form_dropdown('LISTFILTER', $filteroptions,$currentFilter,$js);
					endif;?>
				</div>
				<div class="col-lg-4">
					<?php echo $pagination;?>
				</div>
			</div>
			</div>
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