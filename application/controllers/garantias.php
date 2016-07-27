<?php
/**
 * 
 */
class Garantias extends CI_Controller {
	
	function __construct() {
		parent::__construct();
		$this->load->model('cms/cms_plugin_reclamos', 'reclamos_model');
		$this->load->model('plugins/garantias_model', 'garantias_model');
	}
	
	public function index(){
		
	}
	public function encuesta_servicio($validation = NULL, $id = NULL){
		$this->load->library('form_validation');
		//Div con errores
		$this->form_validation->set_error_delimiters('<p class="text-danger">', '</p>');
		
		//Validar el formulario
		$this->form_validation->set_rules('POLL_RECOMMENDATION', '&iquest;Qu&eacute; tanto recomendar&iacute;a TUMI?', 'required');
		$this->form_validation->set_rules('POLL_SATISFACTION', '&iquest;Qu&eacute; tan satisfecho est&aacute;?', 'required');
		$this->form_validation->set_rules('POLL_SATISFACTION_DESC', '&iquest;Por qu&eacute;?', 'required');
		$this->form_validation->set_rules('POLL_INFO', '&iquest;C&oacute;mo fu&eacute; la comunicaci&oacute; recibida?', 'required');
		$this->form_validation->set_rules('POLL_INFO_DESC', '&iquest;Por qu&eacute;?', 'required');
		$this->form_validation->set_rules('POLL_COMMENT', 'Comment Desc', '');
		
		
		$reclamo			= (!empty($id))?$this->reclamos_model->get_reclaim($id):FALSE;

		$data['disabled']	= ($reclamo && !empty($reclamo->PROCESS_PASSCODE) && $reclamo->PROCESS_PASSCODE == $validation)?'':' disabled="disabled"';		
		$data['alert']		= ($reclamo && !empty($reclamo->PROCESS_PASSCODE) && $reclamo->PROCESS_PASSCODE == $validation)?'':'<div class="alert alert-danger" role="alert"><p><strong>Error:</strong> Hay un problema al validar este formulario con tu reclamo, verifica haber ingresado mediante el bot&oacute;n en el correo que te enviamos. En caso no hayas recibido un correo, comun&iacute;cate con nosotros a trav&eacute;s del correo: servicio@tumi.com.gt</p></div>';
		$data['id']			= $id;
		$data['passcode']	= $validation;

		if ($this->form_validation->run() == FALSE):
			$this->load->template('encuesta_servicio', $data);
		else:
			$inputs = $this->input->post();
			foreach($inputs as $i => $input):
				$inputs[$i]	= ascii_to_entities($input);
			endforeach;
			
			if($this->garantias_model->insert($inputs)):
				$this->reclamos_model->update(array('PROCESS_PASSCODE' => NULL, 'RECLAIM_SERVICE_POLL_SENT' => 'SI'), $id);
				$data['alert']	= '<div class="alert alert-success" role="alert"><p><strong>Muchas Gracias!</strong> Agradecemos habernos apoyado con tu opini&oacute;n; si deseas comunicarte con nosotros, puedes hacerlo escribiendo al correo electr&oacute;nico: servicio@tumi.com.gt</p></div>';
			else:
				$data['alert']	= '<div class="alert alert-danger" role="alert"><p><strong>Error!</strong> Hubo alg&uacute;n error al enviar los datos de la encuesta, por favor comun&iacute;cate al correo electr&oacute;nico: servicio@tumi.com.gt</p></div>';
			endif;
			
			$this->load->template('encuesta_servicio_response', $data);
		endif;
	}
	public function encuesta_servicio_post(){
		echo "<html>
  <head>
    <!--Load the AJAX API-->
    <script type='text/javascript' src='https://www.gstatic.com/charts/loader.js'></script>
    <script type='text/javascript'>
google.charts.load('current', {'packages':['gauge']});
      google.charts.setOnLoadCallback(drawChart);
      function drawChart() {

        var data = google.visualization.arrayToDataTable([
          ['Label', 'Value'],
          ['Memory', 80],
          ['CPU', 55],
          ['Network', 68]
        ]);

        var options = {
          width: 400, height: 120,
          redFrom: 90, redTo: 100,
          yellowFrom:75, yellowTo: 90,
          minorTicks: 5
        };

        var chart = new google.visualization.Gauge(document.getElementById('chart_div'));

        chart.draw(data, options);
      }
    </script>
  </head>
  <body>
    <div id='chart_div' style='width: 400px; height: 120px;'></div>
  </body>
</html>";
	}
}
