<nav class="navbar navbar-default navbar-static-top">
	<div class="container-fluid">
		<div class="navbar-header">
			<a class="navbar-brand icinco" href="#">
				<img src="<?=base_url('library/images/icinco-i-vector-white.png')?>" />
			</a>
		</div>
	</div>
</nav>
<div id="content-container" class="container-fluid">
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
		<?php 
		$data = array('id' => 'tumitracerform');
		echo form_open('recursos_humanos/formulario_disc/'.$passcode.'/'.$testid, $data);
		?>
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">FORMULARIO DISC</h3>
			</div>
			<div class="panel-body">
				<?=$error_message?>
				<p>
					<dl class="dl-horizontal">
						<dt>Nombres:</dt>
						<dd><?php echo $userdata->NAME?></dd>
						<dt>Apellidos:</dt>
						<dd><?php echo $userdata->LASTNAME?></dd>
						<dt>No. Documento:</dt>
						<dd><?php echo $userdata->IDNUMBER?></dd>
						<dt>Fecha de nacimiento:</dt>
						<dd><?php echo mysql_date_to_dmy($userdata->BIRTHDATE)?></dd>
					</dl>
				</p>
				<p><strong>Instrucciones:</strong> En los siguientes grupos, se presentan varias propuestas, escoja una opci&oacute;n que MAS se identifique con su personalidad en el &aacute;rea de MAS, y una que MENOS se identifique con su personalidad en el &aacute;rea de MENOS. Solo puede haber dos marcas por grupo (una MAS y una MENOS).</p>
				<div class="form-group">
					<div class="row">
						
					<?php foreach($questions as $i => $group):?>
					<div class="col-lg-4 col-md-4 col-sm-6 col-xs-12 questions_group">
						<div class="row">
							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6"><p><span class="form-question"><?=$i?>.</span> <span class="text-danger">*</span></p></div>
							<div class="col-lg-3 col-md-3 col-sm-3 col-xs-3"><strong class="discquestion_label">MAS</strong></div>
							<div class="col-lg-3 col-md-3 col-sm-3 col-xs-3"><strong class="discquestion_label">MENOS</strong></div>
						</div>
						<?php foreach($group as $question):?>
						<div class="row">
							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6"><label for=""><?php echo $question->FORM_ADJECTIVE?></label></div>
							<div class="col-lg-3 col-md-3 col-sm-3 col-xs-3"><input <?=$disabled?> type="radio" <?php echo set_radio('MASDISC['.$i.']', $question->FORM_DISCTYPE); ?> id="" name="MASDISC[<?=$i?>]" value="<?php echo $question->FORM_DISCTYPE?>" /></div>
							<div class="col-lg-3 col-md-3 col-sm-3 col-xs-3"><input <?=$disabled?> type="radio" <?php echo set_radio('MENOSDISC['.$i.']', $question->FORM_DISCTYPE); ?> id="" name="MENOSDISC[<?=$i?>]" value="<?php echo $question->FORM_DISCTYPE?>" /></div>
						</div>
						<?php endforeach;
						echo form_error('MASDISC['.$i.']');
						echo form_error('MENOSDISC['.$i.']');?>
						<hr />
						<p>&nbsp;</p>
					</div>
					<?php endforeach;?>
					</div>
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