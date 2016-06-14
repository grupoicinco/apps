<?php
class Cms_plugin_vacations extends MY_Model {

    public function __construct()
    {
        parent::__construct();
		$this->load->library('upload');
		$this->set_table('PLUGIN_SALESMAN_VACATIONS');
		
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
	 * Query general de listado de vacaciones
	 */
	 private function vacations_query($select = 'PSV.ID, PSM.ID, PSM.SALESMAN_NAME, PSM.SALESMAN_LASTNAME, PSV.VACATION_RESTDAY, PSV.VACATION_INITIALDATE, PSV.VACATION_ENDDATE'){
	 	$query = $this->db->select($select, FALSE)
		->from($this->_table.' PSV')
		->join('PLUGIN_SALESMAN PSM', 'PSM.ID = PSV.VACATIONS_EMPLOYEE');
		
		return $query;
	 }
	 
	 public function list_vacations($select = NULL, $where = NULL, $order_by = NULL){
	 	$select = ($select == NULL)?
	 		'PSV.ID, PSM.ID AS SALESMAN_ID, PSM.SALESMAN_NAME, PSM.SALESMAN_LASTNAME, PSV.VACATION_RESTDAY, PSV.VACATION_INITIALDATE, PSV.VACATION_ENDDATE':
			$select;
		
	 	$query = $this->vacations_query($select);
		
		$query = ($where != NULL)?
			$query->where($where):
			$query;
		
		$query = ($order_by != NULL)?
			$query->order_by($order_by):
			$query;
		
		return $query->get()->result();
		
	 }
}