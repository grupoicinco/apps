<?php
/**
 * Plugin que carga archivos enviados por POST para el uploader
 */
 class Plugin_uploader extends PL_Controller {
     
     function __construct() {
        parent::__construct();
		
		$this->upload_config['upload_path'] 		= './user_files/uploads/';
		$this->upload_config['allowed_types'] 		= 'gif|jpg|png';
		$this->upload_config['overwrite'] 			= TRUE;
		$this->load->library('upload');
     }
	 
	 public function index(){
	 	
		//Cargar imagen
		$this->upload->initialize($this->upload_config); //Definir la configuración del upload
		if($this->upload->do_upload('uploader')):
		 	echo json_encode($this->upload->data());
		else:
			echo json_encode(array('ERROR' => $this->upload->display_errors('', '')));
		endif;
	 }
 }
 
