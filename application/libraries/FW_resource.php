<?php
/**
 * Librería para obtener los datos de la tabla FRAMEWORK_RESOURCE
 */
 class Fw_resource{
 	
	var $FW;
	public function __construct(){
		$this->FW			=& get_instance();
		
		$this->FW->load->model('cms/cms_resource_model', 'resource_model');
	}
	
	public function request($request){
		return $this->FW->resource_model->request_resource_value($request);
	}
 }
