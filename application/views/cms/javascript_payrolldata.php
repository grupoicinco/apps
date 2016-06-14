<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog" role="document">
	  	<div class="modal-content">
	  		<div class="modal-header">
	  			<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	  			<h4 class="modal-title" id="myModalLabel">Planilla de Salarios. ICINCO, S.A.</h4>
	  		</div>
	  		<div class="modal-body">
	  			<dl class="dl-horizontal" style="margin-bottom:5px;">
					<dt>Empleado:</dt>
					<dd id="employee_name"></dd>
					<dt>Salario Nominal:</dt>
					<dd id="salary_paid"></dd>
					<dt>Fecha de emisi&oacute;n:</dt>
					<dd id="issue_date"></dd>
				</dl>
				<div class="row">
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
						<pre id="payroll_date" class="text-center"></pre>
					</div>
				</div>
				<div class="row">
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
						<dl class="dl-horizontal" style="margin-bottom:5px;">
							<dt>Cargo:</dt>
							<dd id="SALESMAN_POSITION"></dd>
							<dt>Jornada:</dt>
							<dd id="SALESMAN_WORKHOURS"></dd>
						</dl>
					</div>
				</div>
				<div class="row">
					<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
						<div class="panel panel-default">
							<div class="panel-heading">Ingresos</div>
							<div class="panel-body">
								<dl class="dl-horizontal" style="margin-bottom:5px;">
									<dt>Salario:</dt>
									<dd id="salary_paid"></dd>
									<dt>Bono decreto:</dt>
									<dd id="payroll_bonus"></dd>
									<dt>Bono venta:</dt>
									<dd id="payroll_commissions"></dd>
									<dt>Horas Extra:</dt>
									<dd id="payroll_extrahours"></dd>
									<dt>Ingreso adicional:</dt>
									<dd id="payroll_extraincome"></dd>
								</dl>
								<hr style="margin:5px 0;" />
								<dl class="dl-horizontal" style="margin-bottom:5px;">
									<dt>Total ingresos:</dt>
									<dd id="payroll_totalincome"></dd>
								</dl>
							</div>
						</div>
					</div>
					<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
						<div class="panel panel-default">
							<div class="panel-heading">Descuentos</div>
							<div class="panel-body">
								<dl class="dl-horizontal" style="margin-bottom:5px;">
									<dt>IGSS:</dt>
									<dd id="igss_discount"></dd>
									<dt>ISR:</dt>
									<dd id="isr_discount"></dd>
									<dt>Descuento adicional:</dt>
									<dd id="extradiscount"></dd>
								</dl>
								<hr style="margin:5px 0;" />
								<dl class="dl-horizontal" style="margin-bottom:5px;">
									<dt>Total descuentos:</dt>
									<dd id="payroll_totaldiscounts"></dd>
								</dl>
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
						<pre><strong>TOTAL Devengado:</strong> <span id="total_devengado"></span></pre>
					</div>
				</div>
	  		</div>
		</div>
	</div>
</div>

<script type="text/javascript">

$("#myModal").on("shown.bs.modal", function (e) {
	
	$loading = "<span class='glyphicon glyphicon-time'></span> Cargando...";
	
	//Mostrar en el modal.
	$('dd#employee_name').html("");
	$('dd#salary_paid').html("");
	$('dd#issue_date').html("");
	$('pre#payroll_date').html($loading);
	$('dd#SALESMAN_POSITION').html("");
	$('dd#SALESMAN_WORKHOURS').html("");
	$('dd#payroll_bonus').html("");
	$('dd#payroll_commissions').html("");
	$('dd#payroll_extrahours').html("");
	$('dd#payroll_extraincome').html("");
	$('dd#payroll_totalincome').html("");
	//Descuentos
	$('dd#igss_discount').html("");
	$('dd#isr_discount').html("");
	$('dd#extradiscount').html("");
	$('dd#payroll_totaldiscounts').html("");
	
	$('pre span#total_devengado').html("");
	
});
$("#myModal").on("shown.bs.modal", function (e) {
	var button 		= $(e.relatedTarget); // Botón que inicio el modal
	var payrollid 	= button.data('payrollid'); //ID de la planilla activada
	var employees	= $.parseJSON('<?=json_encode($employees)?>'); //JSON de los empleados en planilla
	//var employeearr	= $.map(employees, function(el) { return el });
	
	$.get( "<?=base_url('cms/panel_dataexport')?>/payroll_xml/"+payrollid, function( xml ) {
	var xml = new XMLSerializer().serializeToString(xml.documentElement), //Convertir el xml en string
	xmlDoc = $.parseXML( xml ),
	$xml = $( xmlDoc );
	
	//Variables con los datos del XML.
	$idvendedor				= $xml.find("PAYROLL_EMPLOYEE").text();
	$nombrevendedor			= employees[$idvendedor].SALESMAN_NAME;
	$meta_ventas 			= $xml.find("PAYROLL_SALESGOAL").text();
	$salariobase			= "Q."+$xml.find("PAYROLL_SALARYPAID").text();
	$payroll_issuedate		= $xml.find("PAYROLL_ISSUEDATE").text();
	$payroll_date			= $xml.find("PAYROLL_INITIALDATE").text()+' al '+$xml.find("PAYROLL_ENDDATE").text();
	$salesman_position		= employees[$idvendedor].SALESMAN_POSITION;
	$salesman_workhours		= employees[$idvendedor].SALESMAN_WORKHOURS;
	$payroll_estbonus		= "Q."+$xml.find("PAYROLL_ESTABLISHEDBONUS").text();
	$payroll_commissions	= "Q."+$xml.find("PAYROLL_COMMISSION").text();
	$payroll_extrahours		= "Q."+(parseFloat($xml.find("PAYROLL_EXTRAHOURSSALARY").text()) + parseFloat($xml.find("PAYROLL_FESTIVEHOURSSALARY").text()));
	$payroll_extraincome	= "Q."+$xml.find("PAYROLL_EXTRAINCOME").text();
	$payroll_totalincome	= "Q."+$xml.find("PAYROLL_TOTALACCRUED").text();
	//Descuentos
	$igss_discount			= "Q."+$xml.find("PAYROLL_IGSS").text();
	$ISR_discount			= "Q."+$xml.find("PAYROLL_ISR").text();
	$extra_discount			= "Q."+$xml.find("PAYROLL_EXTRADISCOUNT").text();
	$total_discounts		= "Q."+$xml.find("PAYROLL_TOTALDISCOUNTS").text();
	
	$total_devengado		= "Q."+(parseFloat($xml.find("PAYROLL_TOTALACCRUED").text()) + parseFloat($xml.find("PAYROLL_TOTALDISCOUNTS").text())).toFixed(2);
	
	//Mostrar en el modal.
	$('dd#employee_name').html($nombrevendedor);
	$('dd#salary_paid').html($salariobase);
	$('dd#issue_date').html($payroll_issuedate);
	$('pre#payroll_date').html($payroll_date);
	$('dd#SALESMAN_POSITION').html($salesman_position);
	$('dd#SALESMAN_WORKHOURS').html($salesman_workhours);
	$('dd#payroll_bonus').html($payroll_estbonus);
	$('dd#payroll_commissions').html($payroll_commissions);
	$('dd#payroll_extrahours').html($payroll_extrahours);
	$('dd#payroll_extraincome').html($payroll_extraincome);
	$('dd#payroll_totalincome').html($payroll_totalincome);
	//Descuentos
	$('dd#igss_discount').html($igss_discount);
	$('dd#isr_discount').html($ISR_discount);
	$('dd#extradiscount').html($extra_discount);
	$('dd#payroll_totaldiscounts').html($total_discounts);
	
	$('pre span#total_devengado').html($total_devengado);
	
	}, "xml" );
});
</script>