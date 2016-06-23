<?php
class Cms_plugin_reclamos extends MY_Model {

    public function __construct()
    {
        parent::__construct();
		$this->load->library('upload');
		$this->set_table('PLUGIN_RECLAIMS');
		
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
	 private function reclaims_query(){
	 	$query = $this->db->select('PR.ID, PR.RECLAIM_CLIENT_NAME, PR.RECLAIM_CLIENT_EMAIL, PR.RECLAIM_CLIENT_PHONE, PR.RECLAIM_DATE, PR.RECLAIM_PRODUCT, PR.RECLAIM_DESCRIPTION, PS.SALESMAN_SAC_CODE, PS.SALESMAN_NAME, PS.SALESMAN_LASTNAME, PR.PROCESS_STAGE, PR.PROCESS_WARRANTYAVAIL, PR.PROCESS_COST, PR.PROCESS_DELIVERY_DATE, PR.PROCESS_DESCRIPTION, PR.PROCESS_UPGRADE_RECEIPT_SERIES, PR.PROCESS_UPGRADE_RECEIPT_NUMBER, PR.PROCESS_UPGRADE_PRODUCT_CODE, PR.PROCESS_UPGRADE_PRODUCT_DESCRIPTION, PR.PROCESS_UPGRADE_DISCOUNT, PR.RECLAIM_STORE, PSTO.STORE_NAME, PSTO.STORE_SAC_WAREHOUSE')
		->from('PLUGIN_RECLAIMS PR')
		->join('PLUGIN_SALESMAN PS', 'PR.RECLAIM_RECEIVER = PS.ID')
		->join('PLUGIN_STORES PSTO', 'PR.RECLAIM_STORE = PSTO.ID');
		
		return $query;
	 }
	/**
	 * Query del listado de reclamos por mes y año
	 */
	 private function reclaim_query($month, $year, $code = NULL){
	 	$wheretype = (is_numeric($code))? "PR.ID = $code": "PR.RECLAIM_CLIENT_NAME LIKE '%$code%'";
	 	
	 	$query = $this->db->select('PR.ID, PR.RECLAIM_CLIENT_NAME, PR.RECLAIM_CLIENT_EMAIL, PR.RECLAIM_CLIENT_PHONE, PR.RECLAIM_DATE, PR.RECLAIM_PRODUCT, PR.RECLAIM_DESCRIPTION, PS.SALESMAN_SAC_CODE, PS.SALESMAN_NAME, PS.SALESMAN_LASTNAME, PR.PROCESS_STAGE, PR.PROCESS_WARRANTYAVAIL, PR.PROCESS_COST, PR.PROCESS_DELIVERY_DATE, PR.PROCESS_DESCRIPTION, PR.PROCESS_UPGRADE_RECEIPT_SERIES, PR.PROCESS_UPGRADE_RECEIPT_NUMBER, PR.PROCESS_UPGRADE_PRODUCT_CODE, PR.PROCESS_UPGRADE_PRODUCT_DESCRIPTION, PR.PROCESS_UPGRADE_DISCOUNT, PR.RECLAIM_STORE, PSTO.STORE_NAME, PSTO.STORE_SAC_WAREHOUSE')
		->from('PLUGIN_RECLAIMS PR')
		->join('PLUGIN_SALESMAN PS', 'PR.RECLAIM_RECEIVER = PS.ID')
		->join('PLUGIN_STORES PSTO', 'PR.RECLAIM_STORE = PSTO.ID');
		$query = ($code == NULL)?
		$query->where("MONTH(PR.RECLAIM_DATE) = '$month' AND YEAR(PR.RECLAIM_DATE) = '$year'"):
		$query->where($wheretype);
				
		return $query;
	 }
	 
	/**
	 * Query del listado de reclamos por etapa
	 */
	 private function reclaim_stage_query($stage = 'RECEPCION', $code = NULL){
	 	$wheretype = (is_numeric($code))? "PR.ID = $code": "PR.RECLAIM_CLIENT_NAME LIKE '%$code%'";
	 	
	 	$query = $this->db->select('PR.ID, PR.RECLAIM_CLIENT_NAME, PR.RECLAIM_CLIENT_EMAIL, PR.RECLAIM_CLIENT_PHONE, PR.RECLAIM_DATE, PR.RECLAIM_PRODUCT, PR.RECLAIM_DESCRIPTION, PS.SALESMAN_SAC_CODE, PS.SALESMAN_NAME, PS.SALESMAN_LASTNAME, PR.PROCESS_STAGE, PR.PROCESS_WARRANTYAVAIL, PR.PROCESS_COST, PR.PROCESS_DELIVERY_DATE, PR.PROCESS_DESCRIPTION, PR.PROCESS_UPGRADE_RECEIPT_SERIES, PR.PROCESS_UPGRADE_RECEIPT_NUMBER, PR.PROCESS_UPGRADE_PRODUCT_CODE, PR.PROCESS_UPGRADE_PRODUCT_DESCRIPTION, PR.PROCESS_UPGRADE_DISCOUNT, PR.RECLAIM_STORE, PSTO.STORE_NAME, PSTO.STORE_SAC_WAREHOUSE')
		->from('PLUGIN_RECLAIMS PR')
		->join('PLUGIN_SALESMAN PS', 'PR.RECLAIM_RECEIVER = PS.ID')
		->join('PLUGIN_STORES PSTO', 'PR.RECLAIM_STORE = PSTO.ID');
		$query = ($code == NULL)?
		$query->where("PR.PROCESS_STAGE = '$stage'"):
		$query->where($wheretype);
				
		return $query;
	 }
	 /**
	  * Información del reclamo según fecha
	  */
	 public function get_reclaims($month, $year, $code = NULL, $total_rows = NULL, $offset = NULL){
		
		$query = $this->reclaim_query($month, $year, $code)
		->order_by("PR.RECLAIM_DATE DESC, PR.ID DESC")
		->limit($total_rows, $offset)
		->get()->result();
		
		return $query;
	 }
	 /**
	  * Información del reclamo según etapa
	  */
	 public function get_stage_reclaims($stage, $code = NULL, $total_rows = NULL, $offset = NULL){
		
		$query = $this->reclaim_stage_query($stage, $code)
		->order_by("PR.RECLAIM_DATE DESC, PR.ID DESC")
		->limit($total_rows, $offset)
		->get()->result();
		
		return $query;
	 }
	 /**
	  * Inoformación de un reclamo
	  */
	 public function get_reclaim($code = NULL){
		
		$query = $this->reclaim_query(NULL, NULL, $code)
		->limit(1)
		->get()->row();
		
		return $query;
	 }
	  
	 /**
	  * Total de reclamos por fecha
	  */
	  public function total_reclaims($month, $year, $code){
	  	
		$query = $this->reclaim_query($month, $year, $code)->get()
		->num_rows();
				
		return $query;
	  }
	 /**
	  * Total de reclamos por etapa
	  */
	  public function total_stage_reclaims($stage, $code){
	  	
		$query = $this->reclaim_stage_query($stage, $code)->get()
		->num_rows();
				
		return $query;
	  }
	  
	 /**
	  * Listado del staff
	  */
	  public function staff_list(){
	  	$query = $this->db->from('PLUGIN_SALESMAN')
				->where('SALESMAN_ENABLED', 'SI')
				->order_by('SALESMAN_SAC_CODE')
		->get();
		
		$staff = $query->result();
		foreach($staff as $staff)
		$return_array[$staff->ID] = str_pad($staff->SALESMAN_SAC_CODE, 3, "0", STR_PAD_LEFT).' - '.$staff->SALESMAN_NAME.' '.$staff->SALESMAN_LASTNAME;
		
		return $return_array;
	  }
	 /**
	  * Listado de tiendas
	  */
	  public function store_list(){
	  	$query = $this->db->from('PLUGIN_STORES')
				->where('STORE_ENABLED', 'SI')
				->order_by('STORE_SAC_WAREHOUSE')
		->get();
		
		$stores = $query->result();
		foreach($stores as $store)
		$return_array[$store->ID] = str_pad($store->STORE_SAC_WAREHOUSE, 3, "0", STR_PAD_LEFT).' - '.$store->STORE_NAME;
		
		return $return_array;
	  }
	  /**
	   * Obtener información de la sucursal
	   */
	   public function get_store($storeid){
	   	$query = $this->db->from('PLUGIN_STORES')
				->where('ID', $storeid)
				->get()->row();
		return $query;
	   }
	  /**
	   * Obtener reclamos pendientes según fecha 
	   */
	   public function pending_reclaims($date = NULL, $exact = FALSE){
		   
	 	$query = $this->db->select('PR.ID, PR.RECLAIM_CLIENT_NAME, PR.RECLAIM_CLIENT_EMAIL, PR.RECLAIM_CLIENT_PHONE, PR.RECLAIM_DATE, PR.RECLAIM_PRODUCT, PR.RECLAIM_DESCRIPTION, PS.SALESMAN_SAC_CODE, PS.SALESMAN_NAME, PS.SALESMAN_LASTNAME, PR.PROCESS_STAGE, PR.PROCESS_WARRANTYAVAIL, PR.PROCESS_COST, PR.PROCESS_DELIVERY_DATE, PR.PROCESS_DESCRIPTION, PR.PROCESS_UPGRADE_RECEIPT_SERIES, PR.PROCESS_UPGRADE_RECEIPT_NUMBER, PR.PROCESS_UPGRADE_PRODUCT_CODE, PR.PROCESS_UPGRADE_PRODUCT_DESCRIPTION, PR.PROCESS_UPGRADE_DISCOUNT, PR.RECLAIM_STORE')
		->from('PLUGIN_RECLAIMS PR')
		->join('PLUGIN_SALESMAN PS', 'PR.RECLAIM_RECEIVER = PS.ID')
		->where('PR.PROCESS_STAGE !=', 'FINALIZADO')
		->where('PR.PROCESS_STAGE !=', 'ENTREGA')
		->where('PR.PROCESS_DELIVERY_DATE !=', '0000-00-00');

		$query = ($exact)? //Si es fecha exacta
		$query->where('PR.PROCESS_DELIVERY_DATE', $date): //Si no es fecha exacta
		$query->where('PR.PROCESS_DELIVERY_DATE <', $date);
		
		
		$query = $query->order_by('PR.PROCESS_DELIVERY_DATE ASC')->get()->result();
			
		return $query;
	   }
		/**
		 * Obtener reclamos pendientes
		 */
		 public function total_pending_reclaims($total_reclaims = 10){
		 	$query = $this->reclaims_query()
		 	->where('PR.PROCESS_STAGE != "ENTREGA" AND PR.PROCESS_STAGE != "FINALIZADO"')
			->order_by('RECLAIM_DATE', 'DESC')
			->limit($total_reclaims);
			
			return $query->get()->result();
		 }
}