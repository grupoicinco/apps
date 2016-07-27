<?php
/**
 * Desplegar datos para el header y footer
 */
class FW_layout_data {
	
	var $FW;
	function __construct(){
		$this->FW			=& get_instance();
		$this->FW->load->model('plugins/layout_model', 'layout_model');
		$this->FW->load->helper('utilities');
	}
	
	/**
	 * Función que despliega los datos para el header
	 */
	public function header_data(){
		$data['external_files'] = array(
									load_external_file('bootstrap.min.css', 'css'),
									load_external_file('bootstrap-theme.css', 'css'),
									load_external_file('font-awesome.min.css', 'css'),
									load_external_file('bootstrap-addons.css', 'css'),
									load_external_file('tumi.css', 'css'),
									
									load_external_file('https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js', 'js', false),
									load_external_file('bootstrap.min.js', 'js'),
									load_external_file('https://www.google.com/recaptcha/api.js', 'js', FALSE)
									
									
									);
		return $data;
	}
}
