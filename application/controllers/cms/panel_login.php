<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Controlador para comprobaciÃ³n de sesiÃ³n de usuarios
 * @author Guido Orellana <guido@grupoperinola.com>
 * @since 8 nov 2012
 */

class Panel_login extends CI_Controller {
	
	function __construct(){
		parent::__construct();
		$this->load->model('cms/cms_login_model', 'login_model');
	}
	
	public function index(){
        $this->load->templatecms('cms/panel_login');
	}

	public function session(){
		
		//Enviar comprobación de sesión
		$data = array(
			'USERNAME' => $this->input->post('login_username'),
			'PASSWORD' => $this->input->post('login_password')
		);

		$comprobar = $this->login_model->comprobacion($data);
		
		if($comprobar == TRUE):
            $datos = $this->login_model->datos($data);
            $newdata = array(
                        'ID' 						=> $datos->ID,
                        'USERNAME' 					=> $datos->USERNAME,
                        'EMAIL' 					=> $datos->EMAIL,
                        'PASSWORD' 					=> $datos->PASSWORD,
                        'ACCESS_LEVEL' 				=> $datos->ACCESS_LEVEL,
                        'ID_IPADDRESS' 				=> $datos->ID_IPADDRESS,
                        'ID_SESSIONID' 				=> $datos->ID_SESSIONID,
                        'USER_DATEADDED' 			=> $datos->USER_DATEADDED,
                        'GOOGLE_ANALYTICS_ID' 		=> $datos->GOOGLE_ANALYTICS_ID,
                        'TWITTER_ID' 				=> $datos->TWITTER_ID,
                        'BULLETIN_BOARD_MESSAGE' 	=> $datos->BULLETIN_BOARD_MESSAGE,
                        'USER_DATEEXPIRATION' 		=> $datos->USER_DATEEXPIRATION,
                        'USER_WEBSITE' 				=> $datos->USER_WEBSITE,
                        'logged_in' 				=> TRUE,
                        'login_error' 				=> FALSE
                    );
			$this->fw_alerts->add_new_alert(1003, 'SUCCESS');
		else:
			$newdata = array(
				'logged_in' 	=> FALSE,
				'login_error' 	=> TRUE
            );
			$this->fw_alerts->add_new_alert(1001, 'ERROR');
		endif;
			$this->session->set_userdata($newdata);
		
		redirect('cms/panel_inicio');
	}
	public function logoff(){
		$this->session->set_userdata('logged_in', FALSE);
		redirect('cms/panel_login');
	}
	  /**
	   * Generar un xml con la información de planilla
	   * @var $id - ID de la planilla a obtener la información.
	   */
	   public function payroll_xml($id){
		$this->load->model('cms/cms_plugin_payrolls', 'plugin_payrolls');
			header("Access-Control-Allow-Origin: *");
			$xml = $this->plugin_payrolls->get($id, 'xml');
			$this->output->set_content_type('text/xml');
			$this->output->set_output($xml); 
	   }
}
?>