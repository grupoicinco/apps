<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Clase para poder obtener información mediante ajax.
 * @since abril 2016
 */

class Panel_dataexport extends CI_Controller {
		
	function __construct(){
		parent::__construct();
	}
	/**
	 * Generar un xml con la información de planilla
	 * @var $id - ID de la planilla a obtener la información.
	 */
	public function payroll_xml($id){
		$this->load->model('cms/cms_plugin_payrolls', 'plugin_payrolls');
		header("Access-Control-Allow-Origin: *");
		$xml = $this->plugin_payrolls->get($id, 'xml');
		$this->output->set_content_type('text/xml');
		$this->output->set_output($xml); 
	}
	/**
	 * Función para exportar la planilla a PDF
	 * @var $payrollid - ID de la planilla a obtener información en el pdf.
	 */
	 public function payroll_pdf($payrollid = NULL){
	  	$this->load->library('FW_export', $payrollid);
		
		return $pdf = $this->fw_export->pdfpayroll($payrollid);
	 }
}