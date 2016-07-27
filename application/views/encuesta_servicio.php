<nav class="navbar navbar-default navbar-static-top">
	<div class="container-fluid">
		<div class="navbar-header">
			<a class="navbar-brand" href="#">
				<img src="<?=base_url('library/images/TUMI-logo-blanco.png')?>" />
			</a>
		</div>
	</div>
</nav>
<div id="content-container" class="container-fluid">
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" id="darkheader">
		<h4 class="bold">DEPARTAMENTO DE GARANT&iacute;AS TUMI</h4>
	</div>
</div>
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
		<?php 
		$data = array('id' => 'tumitracerform');
		$hidden = array('RECLAIM_ID' => $id);
		echo form_open('garantias/encuesta_servicio/'.$passcode.'/'.$id, $data, $hidden);
		?>
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">ENCUESTA DE SERVICIO</h3>
			</div>
			<?=$alert?>
			<div class="panel-body">
				<div class="form-group">
					<p><span class="form-question">1. ¿Que tanto recomendar&iacute;a TUMI a un amigo o colega?</span></label> <span class="text-danger">*</span></p>
					<p><label class="pull-left">Jam&aacute;s la recomendar&iacute;a</label><label class="pull-right">La recomendar&iacute;a totalmente</label></p>
					<div class="btn-group btn-group-justified" role="group" data-toggle="buttons">
						<?php 
						for($i = 1; $i <= 10; $i++):
						$active	= set_radio('POLL_RECOMMENDATION', $i);
						?>
						<label<?=$disabled?>  class="btn btn-primary <?php echo (!empty($active))?' active':''?>">
							<input <?=$disabled?> type="radio" <?php echo set_radio('POLL_RECOMMENDATION', $i); ?> name="POLL_RECOMMENDATION" value="<?=$i?>" id="POLL_RECOMMENDATION<?=$i?>" autocomplete="off"> <?=$i?>
						</label>
						<?php
						endfor;
						?>
					</div>
					<?php echo form_error('POLL_RECOMMENDATION'); ?>
					<p>&nbsp;</p>
					<p><span class="form-question">2. En general ¿Qu&eacute; tan satisfecho est&aacute; con el servicio obtenido?</span> <span class="text-danger">*</span></p>
					<input <?=$disabled?> type="radio"<?php echo set_radio('POLL_SATISFACTION', '5'); ?> id="very-satisfied" name="POLL_SATISFACTION" value="5" /> <label for="very-satisfied">Bastante Satisfecho</label><br />
					<input <?=$disabled?> type="radio"<?php echo set_radio('POLL_SATISFACTION', '4'); ?> id="satisfied" name="POLL_SATISFACTION" value="4" /> <label for="satisfied">Satisfecho</label><br />
					<input <?=$disabled?> type="radio"<?php echo set_radio('POLL_SATISFACTION', '3'); ?> id="nor-satisfied" name="POLL_SATISFACTION" value="3" /> <label for="nor-satisfied">Cumpli&oacute; expectativas</label><br />
					<input <?=$disabled?> type="radio"<?php echo set_radio('POLL_SATISFACTION', '2'); ?> id="little-satisfied" name="POLL_SATISFACTION" value="2" /> <label for="little-satisfied">Poco satisfecho</label><br />
					<input <?=$disabled?> type="radio"<?php echo set_radio('POLL_SATISFACTION', '1'); ?> id="not-satisfied" name="POLL_SATISFACTION" value="1" /> <label for="not-satisfied">Nada satisfecho</label><br />
					<input <?=$disabled?> type="text" value="<?php echo set_value('POLL_SATISFACTION_DESC'); ?>" placeholder="¿Por qu&eacute;?" class="form-control" name="POLL_SATISFACTION_DESC" />
					<?php echo form_error('POLL_SATISFACTION'); ?>
					<?php echo form_error('POLL_SATISFACTION_DESC'); ?>
					<p>&nbsp;</p>
					<p><span class="form-question">3. ¿C&oacute;mo fu&eacute; la informaci&oacute;n y comunicaci&oacute;n brindada por el asesor a su caso?</span> <span class="text-danger">*</span></p>
					<input <?=$disabled?> type="radio"<?php echo set_radio('POLL_INFO', '5'); ?> id="info-very-satisfied" name="POLL_INFO" value="5" /> <label for="info-very-satisfied">Excelente</label><br />
					<input <?=$disabled?> type="radio"<?php echo set_radio('POLL_INFO', '4'); ?> id="info-satisfied" name="POLL_INFO" value="4" /> <label for="info-satisfied">Muy Buena</label><br />
					<input <?=$disabled?> type="radio"<?php echo set_radio('POLL_INFO', '3'); ?> id="info-nor-satisfied" name="POLL_INFO" value="3" /> <label for="info-nor-satisfied">Correcta</label><br />
					<input <?=$disabled?> type="radio"<?php echo set_radio('POLL_INFO', '2'); ?> id="info-little-satisfied" name="POLL_INFO" value="2" /> <label for="info-little-satisfied">Poca comunicaci&oacute;n o no fu&eacute; clara</label><br />
					<input <?=$disabled?> type="radio"<?php echo set_radio('POLL_INFO', '1'); ?> id="info-not-satisfied" name="POLL_INFO" value="1" /> <label for="info-not-satisfied">Bastante Lamentable</label><br />
					<input <?=$disabled?> type="text" value="<?php echo set_value('POLL_INFO_DESC'); ?>" placeholder="¿Por qu&eacute;?" class="form-control" name="POLL_INFO_DESC" />
					<?php echo form_error('POLL_INFO'); ?>
					<?php echo form_error('POLL_INFO_DESC'); ?>
					<p>&nbsp;</p>
					<p><span class="form-question">4. ¿Quisiera agregar algo que nos ayude a mejorar el proceso?</span></p>
					<textarea <?=$disabled?> class="form-control" name="POLL_COMMENT"><?php echo set_value('POLL_COMMENT'); ?></textarea>
				</div>
			</div>
		</div>
		<div class="form-group">
			<input <?=$disabled?> type="submit" class="btn btn-default" value="ENVIAR ENCUESTA" />
		</div>
		</form>
	</div>
</div>
</div>