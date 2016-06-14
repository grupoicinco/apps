<?php
/**
 * 
 */
class Vobo extends CI_Controller {
	
	function __construct() {
		parent::__construct();
		$this->load->model('plugins/vobo_model', 'vobo_model');
	}
	
	public function index($clientid, $passcode){
		
		$validation = $this->vobo_model->validate($clientid, $passcode);
		$data['error']	= NULL; //El formulario tiene error
		
		//Establecer si es valido o no el código
		if($validation > 0):
			$data['valid'] = TRUE;
		else:
			$data['valid'] = FALSE;
			$this->fw_alerts->add_new_alert(9991, 'ERROR'); //Mostrar error de validación
		endif;
		
		$this->load->template('vobo', $data);
	}
	public function response($orderid){
		$this->load->library('form_validation');
		$data['error']	= NULL; //El formulario tiene error
		
		//Si el mensaje fue enviado
		if ($this->form_validation->run('VOBO_RESPONSE') != FALSE):
			if($this->fw_posts->client_vobo_response($orderid)):
				$data['sent']	= TRUE;
				$this->vobo_model->response($orderid, $this->input->post('RESPONSE')); //Enviar la respuesta de aprobación
			else:
				$data['sent']	= FALSE;
			endif;
			$this->load->template('vobo_response', $data);
		else:
			$data['error']	= 'has-error';
			$data['valid']	= TRUE;
			$this->load->template('vobo', $data);
		endif;
	}
}
