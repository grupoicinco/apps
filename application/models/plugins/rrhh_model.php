<?php
/**
 * Modelo para datos del header y footer 
 */
class Rrhh_model extends MY_Model {

	function __construct() {
		parent::__construct();
		$this->set_table('PLUGIN_DISC_FORM');
		$this->test_table = 'PLUGIN_DISC_TESTS';
	}
	
	/**
	 * Obtener valor sobre 100 de los resultados de la encuesta
	 */
	 public function disc_form(){
	 	$query = $this->db->from($this->_table)
				->get()->result();
		foreach($query as $question):
			$return[$question->FORM_QUESTIONNUM][] = (object) array('FORM_ADJECTIVE' => $question->FORM_ADJECTIVE, 'FORM_DISCTYPE' => $question->FORM_DISCTYPE, 'FORM_ADJECTIVEDESC' => $question->FORM_ADJECTIVEDESC);
		endforeach;
		
		return $return;
	 }
	 
	 /**
	  * Obtener los datos de los usuarios a tomar la prueba disc
	  */
	  public function plugin_disc_tests($passcode = NULL, $id){
	  	//Obtener tipo de usuario tomando la prueba.
	  	$tipo = $this->db->select('TEST_TYPE, TEST_PASSCODE')
				->from($this->test_table)
				->where('ID', $id)->get()->row();
		
		$validation = ($tipo->TEST_PASSCODE == $passcode)?TRUE:FALSE;
		
		if($tipo->TEST_TYPE == 'Empleado' && $validation):
			$query = $this->db->select("PDT.ID, PSM.ID AS USERID, PDT.TEST_PASSCODE, PDT.TEST_DATE, PDT.TEST_MASANSWERS, PDT.TEST_MENOSANSWERS, PDT.TEST_EXPDATE, PSM.SALESMAN_SAC_CODE AS CODE, PSM.SALESMAN_NAME AS NAME, PSM.SALESMAN_LASTNAME AS LASTNAME, PSM.SALESMAN_EMAIL AS USER_EMAIL, PSM.SALESMAN_DPI AS IDNUMBER, PSM.SALESMAN_BIRTHDATE AS BIRTHDATE")
					->from($this->test_table.' PDT')
					->join('PLUGIN_SALESMAN PSM', 'PSM.ID = PDT.TEST_PERSON')
					->where('PDT.ID', $id)->get();
			
			return $query->row();
		elseif($tipo == 'Aspirante' && $validation):
		else:
			return FALSE;
		endif;
	  }
	   
	   /** Actualiza un registro de la tabla
	    * $data - arrego asosciativo con columnas => nuevos valores
	    * $id - id del registro que se quiere actualizar
	    */
		public function update($data, $id = NULL, $where = NULL, $table = NULL) {
			$table	= (empty($table))?$this->_table:$table;
			if(is_null($where)):$this->db->where('ID', $id);else:$this->db->where($where);endif;			
			return $this->db->update($table, $data);
			
		}
		/**
		 * Obtener datos de la pauta
		 * @var $disctype - String con el nombre de la pauta DISC
		 * @var $pautaval - Int con valor de la pauta a obtener
		 */
		public function pauta_data($disctype = NULL, $pautaval = NULL){
		 	$query	= $this->db->select("PAUTADATA_".$disctype."_VAL AS PAUTADATA_VALUE")
		 				->from("PLUGIN_DISC_PAUTADATA")
		 				->where("PAUTADATA_".$disctype."_TYPE", $pautaval)->get();
		 				
		 	return $query->row();
		 }
		
		/**
		 * Obtener la personalidad según la pauta obtenida
		 */
		 public function pauta_personality($pauta){
		 	$position	= ($pauta <= 3666)?"BOTTOM":"TOP";
			
			$query 		= $this->db->select("PERSONALITY_CODE_$position AS PERSONALITY_CODE, PERSONALITY_DESC_$position AS PERSONALITY_DESCRIPTION")
							->from("PLUGIN_DISC_PERSONALITY")
							->where("PERSONALITY_PAUTA_$position", $pauta)->get();
			return $query->row();
		 }
		 /**
		  * Obtener las descripciones según personalidad DISC
		  * @var $personalitynum - Int Código según cada tipo de personalidad.
		  * @var $personalities - array con los tipos de descripciones de la personalidad.
		  */
		  public function disc_descriptions($personalitynum = NULL, $personalities = array()){
			
			$select = "";
			foreach($personalities as $personality):
				$select	.= "PDD.DESCRIPTIONS_".$personality."_DESC, ";
			endforeach;
			$select		.= "PDT.TYPEDESC_DESC";
			
			$query		= $this->db->select($select)
							->from("PLUGIN_DISC_DESCRIPTIONS AS PDD")
							->join("PLUGIN_DISC_TYPEDESC AS PDT", 'PDT.ID = PDD.ID')
							->where("PDD.ID", $personalitynum)->get();
			return $query->row();
		  }
		  /**
		   * Obtener listado de las pruebas DISC
		   */
		   public function list_disc_tests($where = NULL){
		   	
				$query = $this->db->select("PDT.ID, PSM.ID AS USERID, PDT.TEST_PASSCODE, PDT.TEST_DATE, PDT.TEST_MASANSWERS, PDT.TEST_MENOSANSWERS, PDT.TEST_EXPDATE, PSM.SALESMAN_SAC_CODE AS CODE, PSM.SALESMAN_NAME AS NAME, PSM.SALESMAN_LASTNAME AS LASTNAME, PSM.SALESMAN_EMAIL AS USER_EMAIL, PSM.SALESMAN_DPI AS IDNUMBER, PSM.SALESMAN_BIRTHDATE AS BIRTHDATE")
						->from($this->test_table.' PDT')
						->join('PLUGIN_SALESMAN PSM', 'PSM.ID = PDT.TEST_PERSON');
				if(!empty($where)):
				$query = $query->where($where);
				endif;
				
				$employees = $query->get()->result();
				
				return $employees;
		   }
}
