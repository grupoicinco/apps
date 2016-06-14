<div id="content" class="container-fluid">
	<div class="page-header">
		<h1> <?php echo $page_title; ?> <small><?=$page_subtitle?></small></h1>
	</div>
	<div class="row">
		<div class="col-lg-12">
			<?php echo $form_html?>
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