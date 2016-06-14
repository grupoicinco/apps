<?php
class Cms_plugin_model extends MY_Model {

    public function __construct()
    {
        parent::__construct();
		$this->load->library('upload');
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
	  * Plugin Email
	  */
	  public function update_email_data($update_array){
	  	//Actualizar cada dato en el framework resource
	  	foreach($update_array as $label => $value):
			$data['RESOURCE_DETAIL'] = $value;
			$this->db->where('RESOURCE_LABEL', $label);
			$result[] = $this->db->update('FRAMEWORK_RESOURCE', $data);
		endforeach;
		$message_key = (in_array(FALSE, $result))?4011:4010;
		return $message_key;
	  }
}