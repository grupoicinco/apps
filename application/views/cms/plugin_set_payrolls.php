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
	$(document).ready(function(){
		$("#PAYROLL_EMPLOYEE").change(function(){
			var employeeid = $(this).val();
			$.post( "<?php echo base_url('cms/plugin_payrolls/last_payroll_date')?>/"+employeeid, function( data ) {
				$('input#PAYROLL_INITIALDATE').val(data);
			});
		});
	});
</script>