<div id="content" class="container-fluid">
	<div class="page-header">
		<h1> <?php echo $page_title; ?> <small><?=$page_subtitle?></small></h1>
	</div>
	<div class="row">
		<div class="col-lg-12">
			<?php 
			echo form_open_multipart('cms/'.strtolower($this->current_plugin).'/post_update_val/'.$data->ID, array('class' => 'form-horizontal', 'role' => 'form'));			
			echo form_hidden("POST_ID", $data->ID);
			echo $form_html;
			if($enable_action_btns == TRUE):?>
			<div class="form-actions">
				<button class="btn btn-primary" data-toggle="modal" data-target="#confirm">Guardar y Enviar Cambios</button>
				<?php if($denied_process):?>
				<button class="btn btn-danger" data-toggle="modal" data-target="#denied">Denegar y Enviar</button>
				<?php endif;?>
				<?php echo anchor('cms/'.strtolower($this->current_plugin).'/update_table_row/'.$data->ID, $this->plugin_button_cancel, array('class'=>'btn btn-default')).' ';?>
				<div class="btn-group pull-right">
					<a type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><span class="glyphicon glyphicon-print"></span> Imprimir ticket de entrega <span class="caret"></span></a>
					<ul class="dropdown-menu" role="menu">
						<li><?php echo anchor('cms/'.strtolower($this->current_plugin).'/pdf_service/'.$data->ID, "Entrega de producto reparado", array('target'=>'_blank'));?></li>
						<li><?php echo anchor('#', "Upgrade a producto nuevo", array('type' => 'button', 'data-toggle' => 'modal', 'data-target' => "#UpgradeData"));?></li>
						<li><?php echo anchor('cms/'.strtolower($this->current_plugin).'/pdf_service_denied/'.$data->ID, "Cancelaci&oacute;n de proceso", array('target'=>'_blank'));?></li>
					</ul>
				</div>
			</div>
			<?php endif;?>
			<!-- Modal de confirmaci�n de env�o -->
			<div class="modal fade" id="confirm" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="false">
				<div class="modal-dialog">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
							<h4 class="modal-title">�Enviar Datos?</h4>
						</div>
						<div class="modal-body">
							<p class="lead">Los datos en el formulario se enviar&aacute;n por correo electr&oacute;nico al cliente. �Estas seguro de enviar los datos agregados en el formulario? </p>
						</div>
						<div class="modal-footer">
							<?php echo form_submit(array('value' => $this->plugin_button_update, 'class' => 'btn btn-primary', 'name' => 'POST_SUBMIT')).' ';?>
							<?php echo anchor('cms/'.strtolower($this->current_plugin), $this->plugin_button_cancel, array('class'=>'btn btn-default', 'data-dismiss' => 'modal')).' ';?>
						</div>
					</div>
				</div>
			</div>
			<!-- Modal de denegar proceso -->
			<div class="modal fade" id="denied" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="false">
				<div class="modal-dialog">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
							<h4 class="modal-title">�Denegar Proceso?</h4>
						</div>
						<div class="modal-body">
							<p class="lead">El proceso se cancelar&aacute; argumentando que este caso espec&iacute;fico no cubre la garant&iacute;a.<br /> Los datos en el formulario se enviar&aacute;n por correo electr&oacute;nico al cliente. �Estas seguro de enviar los datos agregados en el formulario? </p>
						</div>
						<div class="modal-footer">
							<?php echo form_submit(array('value' => $this->plugin_button_denied, 'class' => 'btn btn-danger', 'name' => 'POST_SUBMIT')).' ';?>
							<?php echo anchor('cms/'.strtolower($this->current_plugin), $this->plugin_button_cancel, array('class'=>'btn btn-default', 'data-dismiss' => 'modal')).' ';?>
						</div>
					</div>
				</div>
			</div>
			</form>
		</div>
	</div>
</div>

<!-- Modal del formulario de upgrade -->
<div class="modal fade" id="UpgradeData" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
<?php echo form_open_multipart('cms/'.strtolower($this->current_plugin).'/pdf_upgrade/'.$data->ID, array('class' => '', 'role' => 'form'));?>
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="MyModalLabel">Datos del upgrade</h4>
			</div>
			<div class="modal-body">
				<p>Rellenar con los datos de facturaci�n del producto a entregar en el upgrade o cr�dito en compra.</p>
				<div class="form-group">
					<label for="serie">N&uacute;mero de factura:</label>
					<div class="row">
						<div class="col-lg-3">
							<input name="PROCESS_UPGRADE_RECEIPT_SERIES" type="text" placeholder="Serie" value="" class="form-control" id="serie" />
						</div>
						<div class="col-lg-9">
							<input name="PROCESS_UPGRADE_RECEIPT_NUMBER" type="number" placeholder="N&uacute;mero" value="" class="form-control" id="numero" />
						</div>
					</div>
				</div>
				<div class="form-group">
					<label for="productcode">Producto:</label>
					<div class="row">
						<div class="col-lg-3">
							<input name="PROCESS_UPGRADE_PRODUCT_CODE" type="text" placeholder="C&oacute;digo" value="" class="form-control" id="productcode" />
						</div>
						<div class="col-lg-9">
							<input name="PROCESS_UPGRADE_PRODUCT_DESCRIPTION" type="text" placeholder="Descripci&oacute;n" value="" class="form-control" id="productdescription" />
						</div>
					</div>
				</div>
				<div class="form-group">
					<label for="upgradediscount">Descuento:</label>
					<div class="input-group">
						<div class="input-group-addon">$</div>
						<input name="PROCESS_UPGRADE_DISCOUNT" type="number" placeholder="0.00" value="" class="form-control" id="upgradediscount" />
					</div>
					<p class="help-block">Colocar el descuento con valor en d�lares, no en porcentaje.</p>
				</div>
			</div>
			<div class="modal-footer">
				<div class="form-group">
					<input type="submit" name="SUBMIT" value="Guardar e Imprimir ticket" class="btn btn-primary" />
					<button type="button" class="btn btn-default" data-dismiss="modal" aria-label="Close">Cancelar</button>
				</div>
			</div>
		</div>
	</div>
</form>
</div>		
					
<script type="text/javascript">
            $(function () {
                $('#datetimepicker').datetimepicker({
                	pickTime: false,
                	minDate: new Date()
                });
            });
        </script>