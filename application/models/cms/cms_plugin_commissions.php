<?php
class Cms_plugin_commissions extends MY_Model {

    public function __construct()
    {
        parent::__construct();
		$this->load->library('upload');
		$this->set_table('PLUGIN_SALESMAN_COMMISSIONS');
		
    }
    public function initialise($current_table)
    {
        $this->_table = $current_table;
    }
    
    public function display_result(){
        $query = $this->db->get($this->_table);
        
        return $query->result();
    }

}