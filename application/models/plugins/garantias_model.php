<?php
/**
 * Modelo para datos del header y footer 
 */
class Garantias_model extends MY_Model {
	
	function __construct() {
		parent::__construct();
		$this->set_table('PLUGIN_RECLAIMS_POLL');
	}
	
	/**
	 * Obtener valor sobre 100 de los resultados de la encuesta
	 */
	 public function poll_results(){
	 	$results = $this->db->select('SUM(POLL_RECOMMENDATION) as POLL_RECOMMENDATION, SUM(POLL_SATISFACTION) as POLL_SATISFACTION, sum(POLL_INFO) as POLL_INFO')
					->from($this->_table)
					->get();
		$result	= $results->row();
		
		$result->POLL_RECOMMENDATION 		= number_format(($result->POLL_RECOMMENDATION * 100)/10, 2);
		$result->POLL_SATISFACTION	 		= number_format(($result->POLL_SATISFACTION * 100)/5, 2);
		$result->POLL_INFO			 		= number_format(($result->POLL_INFO * 100)/5, 2);
		
		return $result;
	 }
}
