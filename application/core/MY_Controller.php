<?php 
/**
 * Controller maestro que establece login al ser extendido por algun controlador en la carpeta "controllers"
 * 
 * @author Guido Orellana <guido@grupoperinola.com>
 * @since 8 nov 2012
*/

class MY_Controller extends CI_Controller {
	
	function __construct(){
		parent::__construct();
		$this->load->model('cms/cms_login_model');
		//Validar ingreso de usuarios
		if($this->session->userdata('logged_in') != TRUE):
		redirect('cms/panel_login');
		endif;
	}
}
require(APPPATH.'libraries/PL_Controller.php');