<?php
class Cms_plugin_staff_vacations extends MY_Model {

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
	 	$query = $this->db->select($select)
		->from($this->_table.' PSV')
		->join('PLUGIN_SALESMAN PSM', 'PSM.ID = PSV.VACATIONS_EMPLOYEE');
		
		return $query;
	 }
	 
	 public function list_vacations($select = 'PSV.ID, PSM.ID, PSM.SALESMAN_NAME, PSM.SALESMAN_LASTNAME, PSV.VACATION_RESTDAY, PSV.VACATION_INITIALDATE, PSV.VACATION_ENDDATE', $where = NULL){
	 	$query = $this->vacations_query($select);
		
		//Seleccionar fechas de vacaciones de un empleado en específico.
		$query = ($where != NULL)?
			$query->where($where):
			$query;
		
		return $query->get()->result();
		
	 }
}