<div id="content" class="container-fluid">
	<div class="page-header">
		<h1> Gift Cards <small>Ingresar datos de la tarjeta</small></h1>
	</div>
	<div class="row">
		<div class="col-lg-9">
			<?php echo form_open_multipart('cms/'.strtolower($this->current_plugin).'/post_update_val', array('class' => 'form-horizontal', 'role' => 'form'));?>
			<?php echo $form_html;
			//Botones del formulario
			echo '<div class="form-actions">';
			echo form_submit(array('value' => $this->plugin_button_update, 'class' => 'btn btn-primary', 'name' => 'POST_SUBMIT', 'disabled' => 'disabled')).' ';
			echo anchor('cms/'.strtolower($this->current_plugin), $this->plugin_button_cancel, array('class'=>'btn btn-default')).' ';
			echo '</div>';?>
			</form>
		</div>
		<div class="col-lg-3">
			<div class="well well-sm"><span class="glyphicon glyphicon-credit-card pull-right"></span> US$. <span class="card_balance"></span></div>
		</div>
	</div>
</div>
<script type="text/javascript">
	$(document).ready(function(){
		$('input#gcubn').change(function(){
			var gcubn = $(this).val();
			//VALIDAR LA TARJETA
			$.post( "./plugin_process_gift_cards/validate_gcubn/", { gcubn: gcubn }, function( data ) {
				if(data > 0){
					$('.giftcard_ubn').removeClass('has-error').addClass('has-success has-feedback').find('input').after('<span class="glyphicon glyphicon-ok form-control-feedback"></span>').siblings('span.glyphicon-remove').remove();
					$('input[name="POST_SUBMIT"]').removeAttr('disabled');
					
					//SI LA TARJETA ES VÁLIDA
					$.post("./plugin_process_gift_cards/get_card_balance", {gcubn: gcubn}, function(data){
						$('span.card_balance').html(data);
					});
				}else{
					$('.giftcard_ubn').removeClass('has-success').addClass('has-error has-feedback').find('input').after("<span class='glyphicon glyphicon-remove form-control-feedback'></span>").siblings('span.glyphicon-ok').remove();
					$('input[name="POST_SUBMIT"]').attr('disabled', 'disabled');
						$('span.card_balance').html('');
				}
			});
			
		});
	});
</script>