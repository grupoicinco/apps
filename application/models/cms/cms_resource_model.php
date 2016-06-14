<?php
/**
 * Modelo para edición del perfil
 */
class Cms_resource_model extends MY_Model {
	
	function __construct() {
		parent::__construct();
		$this->set_table("FRAMEWORK_RESOURCE");
	}
	
	public function request_resource_value($resource_label){
		$this->db->select('RESOURCE_DETAIL');
		$this->db->from($this->_table);
		$this->db->where('RESOURCE_LABEL', $resource_label);
		$query = $this->db->get();
		$resource_detail = $query->row();
		
		return $resource_detail->RESOURCE_DETAIL;
	}
}