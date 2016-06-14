<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog" role="document">
	  	<div class="modal-content">
	  		<div class="modal-header">
	  			<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	  			<h4 class="modal-title text-danger" id="myModalLabel">Nota importante!</h4>
	  		</div>
	  		<div class="modal-body">
	  			<p>Al cambiar un empleado deshabilitado y volverlo a habilitar, automaticamente se cambiar&aacute; la fecha de inicio de labores por la fecha al d&iacute;a de hoy, perdi&eacute;ndose la fecha de inicio de labores anterior sin posibilidad de recuperarse.</p>
	  			<p>Se recomienda copiar la fecha de inicio de labores que aparece actualmente en el empleado, en caso se desee volver a colocar la fecha de inicio de labores m&aacute;s adelante.</p>
	  		</div>
	  	</div>
	</div>
</div>
<script type="text/javascript">
	$(document).ready(function(){
		$('select#SALESMAN_ENABLED').on('change', function(){
			var enabled_value	= $(this).val();
			
			if(enabled_value == 'SI'){
				$('#myModal').modal();
			}
		});
	});
</script>