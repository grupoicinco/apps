<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * Librería con mensajes de alerta
 */
class FW_alerts {
	var $FW;
	public function __construct(){
		$this->FW			=& get_instance();
	}
	
	/**
	 * Mensajes a desplegar en las alertas
	 * 
	 * $return_message[key] = Mensaje a desplegar segun el key asignado.
	 */
	private function alerts_messages_array($key){
		$this->FW->load->helper('messages');
		
		$return_message = display_message($key);
		
		return $return_message;
	}
	
	/**
	 * Tipos de alerta a desplegar
	 * 
	 * $return_type[key] = Tipo de alerta a mostrar según key asignado, por default se asigna mensaje de warning.
	 * 
	 * Tipos de alerta:
	 * $this->alert_type['ERROR']		= Alerta roja, que muestra algún error o peligro de mal uso.
	 * $this->alert_type['SUCCESS']		= Alerta verde, que muestra exito en algún proceso
	 * $this->alert_type['INFO']		= Alerta azul, que muestra información que se requiera mostrar.
	 * $this->alert_type['WARNING']		= Alerta amarilla, que muestra datos que pueden causar algún error.
	 * 
	 */
	public function alerts_types_array($key){
		//Login messages
		$return_type[1001]					= $this->alert_type('ERROR');
		$return_type[1002]					= $this->alert_type('WARNING');
		$return_type[1003]					= $this->alert_type('SUCCESS');
		//Mi perfil
		$return_type[2001]					= $this->alert_type('SUCCESS');
		//Permisos
		$return_type[9990]					= $this->alert_type('ERROR');
		$return_type[9991]					= $this->alert_type('ERROR');
		//Email notifications
		$return_type[3001]					= $this->alert_type('SUCCESS');
		$return_type[3002]					= $this->alert_type('ERROR');
		$return_type[3003]					= $this->alert_type('ERROR');
		$return_type[3004]					= $this->alert_type('ERROR');
		//Upload notifications
		$return_type[4001]					= $this->alert_type('SUCCESS');
		$return_type[4002]					= $this->alert_type('ERROR');
		//CRUD notifications
		$return_type[4010]					= $this->alert_type('SUCCESS');
		$return_type[4011]					= $this->alert_type('ERROR');
		$return_type[4012]					= $this->alert_type('SUCCESS');
		$return_type[4013]					= $this->alert_type('SUCCESS');
		
		if(array_key_exists($key, $return_type)):
			return $return_type[$key];
		else:
			return $this->alert_type('WARNING');
		endif;
	}
	
	/**
	 * Funciones especificas para desplegar mensajes
	 */
    public function add_new_alert($alert_key, $alert_type = 'WARNING'){
    	$alerts_string		= $this->FW->session->userdata('FRAMEWORK_RESOURCE_ALERTS');
    	$alerts_string 		.= $alert_key.'#'.$alert_type."||";
		
		$this->create_session_alert($alerts_string);
    }
	private function create_session_alert($alerts_string){
		
    	$this->FW->session->set_userdata('FRAMEWORK_RESOURCE_ALERTS', $alerts_string);
	}
	
	public function display_alert_message(){
		
		if($this->FW->session->userdata('FRAMEWORK_RESOURCE_ALERTS')):
			$messages_array 	= explode("||", $this->FW->session->userdata('FRAMEWORK_RESOURCE_ALERTS'));//Obtenemos cada mensaje en un array
			$html_alert			= ''; //Constriumos la variable que contendrá las alertas.
			
			foreach($messages_array as $message):
				if(!empty($message)):
					$message_parts	= explode("#", $message);
					$html_alert		.=	'<div class="alert alert-block fade in '.$this->alert_type($message_parts[1]).'">';
					$html_alert		.=	'<button type="button" class="close" data-dismiss="alert">&times;</button>';
					$html_alert		.=	$this->alerts_messages_array($message_parts[0]);
					$html_alert		.=	'</div>';
				endif;
			endforeach;
			
			$this->FW->session->unset_userdata('FRAMEWORK_RESOURCE_ALERTS');
			return $html_alert;
		else:
			return null;
		endif;
	}
	private function alert_type($type = 'WARNING'){
		$return_class_type		= array(
									'ERROR'		=> 'alert-danger',
									'SUCCESS'	=> 'alert-success',
									'INFO'		=> 'alert-info',
									'WARNING'	=> 'alert-warning'
									);
		if(array_key_exists($type, $return_class_type)):
			return $return_class_type[$type];
		else:
			return $return_class_type['WARNING'];
		endif;
	}
}
