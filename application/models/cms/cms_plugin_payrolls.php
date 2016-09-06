<?php
class Cms_plugin_payrolls extends MY_Model {

    public function __construct()
    {
        parent::__construct();
		$this->set_table('PLUGIN_SALESMAN_PAYROLL');
		
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
	 public function payroll_query(){
	 	$query = $this->db->select('PSPR.ID, PSPR.PAYROLL_EMPLOYEE, PS.SALESMAN_NAME, PS.SALESMAN_LASTNAME, PS.SALESMAN_SAC_CODE, PS.SALESMAN_EMAIL, PS.SALESMAN_ENABLED, PS.SALESMAN_POSITION, PS.SALESMAN_SALARY, PS.SALESMAN_COMMENCEMENT, PS.SALESMAN_WORKHOURS, PSPR.PAYROLL_INITIALDATE, PSPR.PAYROLL_ENDDATE, PSPR.PAYROLL_SALESGOAL, PSPR.PAYROLL_SALES, PSPR.PAYROLL_STORESALESGOAL, PSPR.PAYROLL_STORESALES, PSPR.PAYROLL_COMMISSION, PSPR.PAYROLL_EXTRAHOURS, PSPR.PAYROLL_EXTRAHOURSSALARY, PSPR.PAYROLL_FESTIVEHOURSSALARY,PSPR.PAYROLL_ESTABLISHEDBONUS, PSPR.PAYROLL_ISR, PSPR.PAYROLL_EXTRADISCOUNT, PSPR.PAYROLL_EXTRADISCOUNTDESCRIPTION, PSPR.PAYROLL_EXTRAINCOME, PSPR.PAYROLL_EXTRAINCOMEDESCRIPTION, PSPR.PAYROLL_TOTALACCRUED, PSPR.PAYROLL_TOTALDISCOUNTS, PSPR.PAYROLL_FESTIVEHOURS, PSPR.PAYROLL_ISSUEDATE, PSPR.PAYROLL_SALARYPAID, PSPR.PAYROLL_14BONUS, PSPR.PAYROLL_AGUINALDO, PSPR.PAYROLL_IGSS, PSPR.PAYROLL_IRTRA, PSPR.PAYROLL_EMAILSENT, PSPR.PAYROLL_SETTLEMENT, PS.SALESMAN_AGE, PS.SALESMAN_CIVILSTATUS, PS.SALESMAN_GENDER, PS.SALESMAN_DPIWRITTEN, PS.SALESMAN_DPI')
		->from('PLUGIN_SALESMAN_PAYROLL PSPR')
		->join('PLUGIN_SALESMAN PS', 'PSPR.PAYROLL_EMPLOYEE = PS.ID');
		
		return $query;
	 }
	 //Obtener listado de planillas
	 public function payroll_list($where = NULL, $limit = NULL, $offset = NULL){
	 	$query = $this->payroll_query();
		
		$query = (!is_null($where))?
		$query->where($where):$query;
		
		
		if ($limit != NULL):
			if ($offset != NULL):
				$query = $query->limit($limit, $offset);
			else:
				$query = $query->limit($limit);
			endif;
		endif;
		
		$query = $query->order_by('PAYROLL_ENDDATE DESC')->get();
		
		return $query->result(); 
	 }
	//Obtener información de la planilla
	public function get_payroll($var, $column = 'PSPR.ID'){
		$query = $this->payroll_query();
		
		$query->where($column, $var);
		
		return $query->get()->row();
	}
	 /**
	  * Obtener la última fecha pagada a un empleado
	  */
	 public function last_paiddate($employeeid){
	 	$query = $this->db->select('max(PAYROLL_ENDDATE) AS PAYROLL_ENDDATE', FALSE)
		->from($this->_table)->where('PAYROLL_EMPLOYEE', $employeeid)->get();
		
		return $query->row();
	 }
	
	/**
	 * Obtener planillas no enviadas
	 */
	 public function payrolls_nonsent(){
	 	$query = $this->db->select("1")
				->from($this->_table)
				->where('PAYROLL_EMAILSENT', 'NO');
		return $query->count_all_results();
	 } 
	 /**
	  * Obtener planillas por año, según tiempo laborado de cada empleado.
	  * @var $payrollemployee = ID del empleado
	  * @var $initialdate - Fecha inicial para obtener planillas
	  * @var $enddate - Fecha final para obtener planillas
	  */
	  public function employee_year_payrolls($payrollemployee,$initialdate, $enddate){
		$query = $this->db->query( "SELECT SUM(PAYMENT_EARNED) AS PAYROLL_YEAR_PAYMENTS_EARNED 
									FROM (SELECT (`PAYROLL_COMMISSION` + `PAYROLL_SALARYPAID`) AS `PAYMENT_EARNED`
									FROM (`PLUGIN_SALESMAN_PAYROLL`)
									WHERE PAYROLL_EMPLOYEE = '".$payrollemployee."' AND PAYROLL_ENDDATE BETWEEN '".$initialdate."' AND '".$enddate."') AS YEAR_PAYROLLS");
		
		return $query->row();
	  }
	  /**
	   * Obtener el último mes cerrado de planillas
	   */
	   public function last_closed_month(){
	   	$query = $this->db->query("	SELECT MAX(ID) AS ID, month(PAYROLL_ENDDATE) AS PAYROLL_MONTH, year(PAYROLL_ENDDATE) AS PAYROLL_YEAR 
	   								FROM PLUGIN_SALESMAN_PAYROLL
	   								WHERE PAYROLL_ENDDATE IN (SELECT MAX(PAYROLL_ENDDATE) FROM PLUGIN_SALESMAN_PAYROLL WHERE PAYROLL_CLOSE = 'SI')");
		return $query->row();
	   }
}