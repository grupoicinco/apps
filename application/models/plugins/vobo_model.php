<?php
/**
 * Modelo para datos del header y footer 
 */
class Vobo_model extends MY_Model {
	
	function __construct() {
		parent::__construct();
	}
	
	public function validate($clientid, $passcode){
		
		$query = $this->db->from('PLUGIN_RECLAIMS')
				->where('ID', $clientid)
				->where('PROCESS_PASSCODE', $passcode)
				->count_all_results();
		
		return $query;
	}
	
	public function response($orderid, $approval){
		$approved 	= ($approval == 'APROBADA')? 'SI': 'NO'; //Saber si el proceso es aprobado
		$stage 		= ($approval == 'APROBADA')? 'REPARACION': 'ENTREGA'; //Si el proceso no es aprobado, establecer entrega de producto de vuelta.
		$enddate 	= ($approval == 'APROBADA')? NULL: date('Y-m-d');
		
		$data = array(
				'PROCESS_APPROVED' 	=> $approved,
				'PROCESS_PASSCODE' 	=> NULL,
				'PROCESS_STAGE'		=> $stage,
				'PROCESS_FINISHED'	=> $enddate
			);
			
		$this->db->where('ID', $orderid);
		$this->db->update('PLUGIN_RECLAIMS', $data); 
	}
}
