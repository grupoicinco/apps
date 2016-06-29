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
					<?php echo $pagination;?>
				</div>
			</div>
			<div class="row">
				<div class="col-lg-6">
					<div class="row">
						<div class="col-lg-12 col-sm-12 col-md-12 col-xs-12">
							<h6>Enviar planilla general</h6>
						</div>
					</div>
					<div class="row" id="general_payroll_form">
						<div class="col-lg-5 col-md-5 col-sm-5 col-xs-5">
							<?php 
							$datosfecha	= date_components();
							echo form_dropdown('general_payroll_month', $datosfecha['meses'], date('m'), 'class="form-control"');
							?>
						</div>
						<div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
							<?php echo form_dropdown('general_payroll_year', $datosfecha['aAnteriores'], date('Y'), 'class="form-control"');?>
						</div>
						<div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
							<button class="btn btn-block btn-default" data-toggle="modal" data-target="#gral_payroll_email_confirmation" id="general_form_submit"><span class="glyphicon glyphicon-envelope"></span> Enviar</button>
						</div>
					</div>
					<div class="modal fade" id="gral_payroll_email_confirmation" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
						<div class="modal-dialog" role="document">
							<div class="modal-content">
								<div class="modal-header">
									<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
									<h4 class="modal-title" id="gral_payroll_email_confirmation">¿Enviar la Planilla General?</h4>
								</div>
								<div class="modal-body">
									<p>¿Confirmas que deseas enviar la planilla general por correo electr&oacute;nico a la cuenta encargada?</p>
								</div>
								<div class="modal-footer">
									<button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
									<button type="button" onclick="email_general_payroll();" class="btn btn-primary"><span class="glyphicon glyphicon-envelope"></span> Enviar</button>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-lg-6 text-right">
					<div class="row">
						<div class="col-lg-12 col-sm-12 col-xs-12 col-md-12">
							<?php 
							$date_component		= date_components();
							
							?>
							<h6>Cierre de planilla - Mes abierto <?php echo $date_component['meses'][str_pad($open_month->OPEN_MONTH, 2, "0", STR_PAD_LEFT)].", ".$open_month->OPEN_YEAR?></h6>
						</div>
					</div>
					<div class="row">
						<div class="col-lg-6 col-sm-6 col-xs-6 col-md-6">
							<form method="post" action="<?php echo base_url('cms/plugin_payrolls/close_payroll_period')?>">
								<?php echo form_hidden('CLOSE', 'NO');
								echo form_hidden('MONTH', str_pad($closed_month->CLOSED_MONTH, 2, "0", STR_PAD_LEFT));
								echo form_hidden('YEAR', $closed_month->CLOSED_YEAR);
								echo form_submit('SUBMIT', 'Regresar Mes', 'class="btn btn-default"')?>
							</form>
						</div>
						<div class="col-lg-6 col-sm-6 col-xs-6 col-md-6">
							<form method="post" action="<?php echo base_url('cms/plugin_payrolls/close_payroll_period')?>">
								<?php echo form_hidden('CLOSE', 'SI');
								echo form_hidden('MONTH', str_pad($open_month->OPEN_MONTH, 2, "0", STR_PAD_LEFT));
								echo form_hidden('YEAR', $open_month->OPEN_YEAR);
								echo form_submit('SUBMIT', 'Cerrar Mes', 'class="btn btn-info"')?>
							</form>
						</div>
					</div>
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
	function email_general_payroll(){
		var general_payroll_month = $('#general_payroll_form select[name="general_payroll_month"]').val();
		var general_payroll_year = $('#general_payroll_form select[name="general_payroll_year"]').val();
		window.location.href = "<?=base_url('cms/plugin_payrolls/pdf_general_payroll')?>/"+general_payroll_month+"/"+general_payroll_year;
	}
</script>