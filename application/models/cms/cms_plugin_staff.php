<?php
class Cms_plugin_staff extends MY_Model {

    public function __construct()
    {
        parent::__construct();
		$this->load->library('upload');
		$this->set_table('PLUGIN_SALESMAN');
		
    }
    public function initialise($current_table)
    {
        $this->_table = $current_table;
    }
    
    public function display_result(){
        $query = $this->db->get($this->_table);
        
        return $query->result();
    }
	/**
	 * Query general de listado de reclamos
	 */
}