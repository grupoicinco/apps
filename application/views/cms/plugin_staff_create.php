<div id="content" class="container-fluid">
	<div class="page-header">
		<h1> <?php echo $page_title; ?> <small><?=$page_subtitle?></small></h1>
	</div>
	<div class="row">
		<div class="col-lg-12">
			<?php 
			echo "<div class='row'><div class='col-lg-12 col-md-12 col-sm-12'>".validation_errors()."</div></div>";
			echo $form_html;
			echo '<div class="form-actions">'.form_submit(array('value' => $this->plugin_button_update, 'class' => 'btn btn-primary', 'name' => 'POST_SUBMIT')).' '.anchor('cms/'.strtolower($this->current_plugin), $this->plugin_button_cancel, array('class'=>'btn btn-default')).'</div>';
			?>
			</form>
		</div>
	</div>
</div>
<script type="text/javascript">
 	$(function () {
		$('.datetimepicker').datetimepicker({
			pickTime: false
		});
	});
</script>