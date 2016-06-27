<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class FW_export {
	var $FW;
	var $string;
	var $serviceDetail;
	var $nombre_cliente;
	var $telefono_cliente;
	var $email_cliente;
	var $orden_fecha;
	var $codigo_orden;
	var $orden_producto;
	var $order_receiver;

	public $filename 			= 'excel-doc';
	public $custom_titles;
	
	
	public function __construct($params = NULL){
		$this->FW			=& get_instance();
		
		//Obtener la librer�a para PDF's		
	 	$this->FW->load->library('fpdf');
	 	$this->FW->load->model('cms/cms_plugin_reclamos', 'plugin_reclamos');
		
		if(count($params) > 0):
		$this->data = $this->FW->plugin_reclamos->get_reclaim($params['order']); //Obtener datos del reclamo
		$this->store = $this->FW->plugin_reclamos->get_store($this->data->RECLAIM_STORE);
		
		//Guardar datos en variables
		
		$this->string 			= iconv('UTF-8', 'windows-1252', strip_tags(html_entity_decode($this->data->RECLAIM_DESCRIPTION)));
		$this->serviceDetail	= iconv('UTF-8', 'windows-1252', strip_tags(html_entity_decode($this->data->PROCESS_DESCRIPTION)));
	 	$this->nombre_cliente 	= iconv('UTF-8', 'windows-1252', html_entity_decode($this->data->RECLAIM_CLIENT_NAME));
		$this->telefono_cliente	= $this->data->RECLAIM_CLIENT_PHONE;
		$this->email_cliente	= $this->data->RECLAIM_CLIENT_EMAIL;
		
		$this->orden_fecha		= mysql_date_to_dmy($this->data->RECLAIM_DATE);
		$this->codigo_orden		= "R".str_pad($this->data->ID, 4, "0", STR_PAD_LEFT);
		$this->orden_producto	= $this->data->RECLAIM_PRODUCT;
		$this->order_receiver	= str_pad($this->data->SALESMAN_SAC_CODE, 3, "0", STR_PAD_LEFT)." - ".iconv('UTF-8', 'windows-1252', html_entity_decode($this->data->SALESMAN_NAME))." ".iconv('UTF-8', 'windows-1252', html_entity_decode($this->data->SALESMAN_LASTNAME));
		$this->order_warranty	= $this->data->PROCESS_WARRANTYAVAIL;
		$this->order_cost		= $this->data->PROCESS_COST;
		
		$this->store_adress1	= iconv('UTF-8', 'windows-1252', strip_tags(html_entity_decode($this->store->STORE_ADDRESS1)));
		$this->store_adress2	= iconv('UTF-8', 'windows-1252', strip_tags(html_entity_decode($this->store->STORE_ADDRESS2)));
		$this->store_email		= $this->store->STORE_EMAIL;
		$this->store_phone		= $this->store->STORE_PHONE;
		
		endif;
	}

	public function make_from_db($db_results) {
		$data 		= NULL;
//		$fields 	= $db_results->field_data();
		$fields		= $db_results->result();

		if ($db_results->num_rows() == 0) {
			show_error('Parece no haber datos para mostrar');
		}
		else {
			$titlesArray = (array) $fields[0];
			$headers = $this->titles($titlesArray);

			foreach ($db_results->result() AS $row) {
				$line = '';
				foreach ($row AS $value) {
					if (!isset($value) OR $value == '') {
						$value = "\t";
					}
					else {
						$value = str_replace('"', '""', $value);
						$value = '"' . $value . '"' . "\t";
					}
					$line .= $value;
				}
				$data .= trim($line) . "\n";
			}
			$data = str_replace("\r", "", $data);
			
			$this->generate($headers, $data);
		}
	}

	public function make_from_array($titles, $array) {
		$data = NULL;

		if (!is_array($array)) {
			show_error('The data supplied is not a valid array');
		}
		else {
			$headers = $this->titles($titles);

			if (is_array($array)) {
				foreach ($array AS $row) {
					$line = '';
					foreach ($row AS $value) {
						if (!isset($value) OR $value == '') {
							$value = "\t";
						}
						else {
							$value = str_replace('"', '""', $value);
							$value = '"' . $value . '"' . "\t";
						}
						$line .= $value;
					}
					$data .= trim($line) . "\n";
				}
				$data = str_replace("\r", "", $data);

				$this->generate($headers, $data);
			}
		}
	}

	private function generate($headers, $data) {
		$this->set_headers();

		echo "$headers\n$data";  
	}

	public function titles($titles) {
		$titles = array_keys($titles);
		if (is_array($titles)) {
			$headers = array();
			
			if (is_null($this->custom_titles)) {
				if (is_array($titles)) {
					foreach ($titles AS $title) {
						$headers[] = $title;
					}
				}
				else {
					foreach ($titles AS $title) {
						$headers[] = $title->name;
					}
				}
			}
			else {
				$keys = array();
				foreach ($titles AS $title) {
					$keys[] = $title->name;
				}
				foreach ($keys AS $key) {
					$headers[] = $this->custom_titles[array_search($key, $keys)];
				}
			}
			return '"'.implode("\"\t\"", $titles).'"';
		}
	}

	private function set_headers() {
		header("Pragma: public");
	    header("Expires: 0");
	    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	    header("Content-Type: application/force-download");
	    header("Content-Type: application/octet-stream");
	    header("Content-Type: application/download");;
	    header("Content-Disposition: attachment;filename=$this->filename.xls");
	    header("Content-Transfer-Encoding: binary ");
	}
	
	 //GENERAR PDF
	 public function pdf_header(){
		
	 	//Iniciar el PDF
	 	$pdf = new PDF();
		$pdf->AddPage();
		
		$pdf->Image(base_url('library/cms/img/TUMI-logo-negro.png'),10,10,40);
		$pdf->SetFont('Courier','B',12);
		$pdf->Cell(0, 5, "Service Order Ticket", 0, 0, "R");
		$pdf->SetFont('Courier','',10);
		$pdf->Ln(14);
		
		$pdf->Code39(148.5,20,$this->codigo_orden,1,10);
		//Datos seg�n Sucursal
		$pdf->SetFont('Courier','',10);
		$pdf->Cell(0, 4, $this->store_adress1, 0, 1);
		$pdf->Cell(0, 4, $this->store_adress2, 0, 1);
		$pdf->Cell(32, 4, "T. ".$this->store_phone, 0, 0);
		$pdf->Cell(0, 4, "E. ".$this->store_email, 0, 1);
		//Encabezado de datos de la orden 
		$pdf->Ln(2);
		$pdf->SetFillColor(214,214,214);
		$pdf->SetFont('Courier','B',10);
		$pdf->Cell(85,5,"Datos del cliente","LTR",0,'L',true);
		$pdf->Cell(5,5,"",0,0,'L',false);
		$pdf->Cell(100,5,"Datos de la orden","LTR",1,'L',true);
		
		//Datos de la orden
		$pdf->SetFont('Courier','',10);
		$pdf->Cell(85,5,"ATTN: $this->nombre_cliente","LR",0,'L');
		
		$pdf->Cell(5,5,"",0,0,'L',false);
		$pdf->SetFont('Courier','B',10);
		$pdf->Cell(40,5,"Aplica Garant�a","L",0,'R');
		$pdf->SetFont('Courier','',10);
		$pdf->Cell(10,5,$this->order_warranty,0,0,'L');
		$pdf->SetFont('Courier','B',10);
		$pdf->Cell(15,5,"Costo",0,0,'R');
		$pdf->SetFont('Courier','',10);
		$pdf->Cell(35,5,"Q.".$this->order_cost,"R",1,'L');
		
		$pdf->Cell(85,5,"T: $this->telefono_cliente","LR",0,'L');
		
		$pdf->Cell(5,5,"",0,0,'L',false);
		$pdf->SetFont('Courier','B',10);
		$pdf->Cell(40,5,"Entrega","L",0,'R');
		$pdf->SetFont('Courier','',10);
		$pdf->Cell(60,5,date('d/m/Y'),"R",1,'L');
		
		$pdf->Cell(85,5,"E: $this->email_cliente","LBR",0,'L');
		
		$pdf->Cell(5,5,"",0,0,'L',false);
		$pdf->SetFont('Courier','B',10);
		$pdf->Cell(40,5,"SKU#","L",0,'R');
		$pdf->SetFont('Courier','',10);
		$pdf->Cell(60,5,"$this->orden_producto","R",1,'L');
		
		$pdf->Cell(85,5,"");
		
		$pdf->Cell(5,5,"",0,0,'L',false);
		$pdf->SetFont('Courier','B',10);
		$pdf->Cell(40,5,"Recibi�","L",0,'R');
		$pdf->SetFont('Courier','',10);
		$pdf->Cell(60,5,$this->order_receiver,"R",1,'L');
		
		//Finalizar los cuadros de datos
		$pdf->Cell(85,5,"",0,0,'L',FALSE);
		$pdf->Cell(5,5,"",0,0,'L',false);
		$pdf->Cell(100,5,"","T",1,'L',FALSE);
		
		return $pdf;
	}
	
	//Obtener formato de PDF de entrega por servicio finalizado
	 public function pdf_service_detail(){
	 	
		//Obtener el encabezado
		$pdf = $this->pdf_header();
		
		//Rellenar con los datos de la orden a entregar
		$pdf->SetFont('Courier','B',10);
		$pdf->Cell(0,7,"Detalle del reclamo",1,2,'L',FALSE);
		$pdf->SetFont('Courier','',10);
		$pdf->drawTextBox($this->string, 190, 50);
		
		$pdf->SetY(130);
		$pdf->SetFont('Courier','B',10);
		$pdf->Cell(0,7,"Detalle del proceso realizado",1,2,'L',FALSE);
		$pdf->SetFont('Courier','',10);
		$pdf->drawTextBox($this->serviceDetail, 190, 50);
		
		return $pdf;
	 }
	 //Crear el PDF del detalle del servicio.
	 public function pdf_service(){
	 	//Agregar parte superior
	 	$pdf = $this->pdf_service_detail();
		
		//Declaraci�n de conformidad
		$pdf->SetY(190);
		$pdf->SetFont('Courier','',8);
		$pdf->MultiCell(0, 5, "Se hace entrega del producto arriba descrito, cumpliendo satisfactoriamente el detalle del proceso realizado. Por lo cual el cliente firma haciendo saber que esta completamente conforme con el servicio entregado y declarando que recibe satisfactoriamente el producto.");
		

		//Firma
		$pdf->SetY(230);
		$pdf->SetFont('Courier','',8);
		$pdf->Cell(110, 5, "Nombre del cliente que recibe", "T", 0);
		$pdf->Cell(5, 5, "");
		$pdf->Cell(75, 5, "Firma del cliente que recibe", "T", 1);
		$pdf->Cell(0, 5, NULL, 0, 1); //Espacio entre lineas
		$pdf->SetFillColor(214,214,214);
		$pdf->SetFont('Courier','B',10);
		$pdf->Cell(0, 5, "Uso exclusivo de la tienda", "LTRB", 1, "L", true);
		$pdf->SetFont('Courier','',8);
		$pdf->Cell(0, 5, NULL, 0, 1); //Espacio entre lineas
		$pdf->Cell(110, 5, "", "LTRB", 0);
		$pdf->Cell(5, 5, "");
		$pdf->Cell(75, 20, "", "LTRB", 0);
		$pdf->Cell(0, 5, NULL, 0, 1); //Espacio entre lineas
		$pdf->Cell(110, 5, "Nombre del empleado que entrega", 0, 1);
		$pdf->Cell(110, 5, NULL, 0, 1); //Espacio entre lineas
		$pdf->Cell(110, 5, "", "LTRB", 1);
		$pdf->Cell(110, 5, "Cargo del empleado que entrega", 0, 0);
		$pdf->Cell(5, 5, "");
		$pdf->Cell(75, 5, "Firma del empleado que entrega", 0, 1);
		
		$pdf->Output();
	 }
	 //Crear el PDF al ser denegado el servicio.
	 public function pdf_service_denied(){
	 	//Agregar parte superior
	 	$pdf = $this->pdf_service_detail();
		
		//Sello completo de orden denegada
		$pdf->SetY(160);
		$pdf->SetX(12);
		$pdf->Rotate(45);
		$pdf->SetTextColor(211,52,52);
		$pdf->SetFont('Helvetica', '', 70);
		$pdf->Write(5, "DENEGADA");
		
		//Declaraci�n de conformidad
		$pdf->SetY(190);
		$pdf->Rotate(0);
		$pdf->SetTextColor(0,0,0);
		$pdf->SetFont('Courier','',8);
		$pdf->MultiCell(0, 5, "Se hace entrega del producto arriba descrito sin ninguna modificaci�n o trabajo realizado al mismo, de acuerdo a la respuesta recibida por parte del cliente arriba descrito denegando que se realizara el proceso propuesto detallado en esta carta. Para lo cual el cliente recibe de vuelta su producto.");
		

		//Firma
		$pdf->SetY(230);
		$pdf->SetFont('Courier','',8);
		$pdf->Cell(110, 5, "Nombre del cliente que recibe", "T", 0);
		$pdf->Cell(5, 5, "");
		$pdf->Cell(75, 5, "Firma del cliente que recibe", "T", 1);
		$pdf->Cell(0, 5, NULL, 0, 1); //Espacio entre lineas
		$pdf->SetFillColor(214,214,214);
		$pdf->SetFont('Courier','B',10);
		$pdf->Cell(0, 5, "Uso exclusivo de la tienda", "LTRB", 1, "L", true);
		$pdf->SetFont('Courier','',8);
		$pdf->Cell(0, 5, NULL, 0, 1); //Espacio entre lineas
		$pdf->Cell(110, 5, "", "LTRB", 0);
		$pdf->Cell(5, 5, "");
		$pdf->Cell(75, 20, "", "LTRB", 0);
		$pdf->Cell(0, 5, NULL, 0, 1); //Espacio entre lineas
		$pdf->Cell(110, 5, "Nombre del empleado que entrega", 0, 1);
		$pdf->Cell(110, 5, NULL, 0, 1); //Espacio entre lineas
		$pdf->Cell(110, 5, "", "LTRB", 1);
		$pdf->Cell(110, 5, "Cargo del empleado que entrega", 0, 0);
		$pdf->Cell(5, 5, "");
		$pdf->Cell(75, 5, "Firma del empleado que entrega", 0, 1);
		
		$pdf->Output();
	 }
	//Obtener formato de PDF de entrega por upgrade
	 public function pdf_upgrade($data_array){
	 	
		//Obtener el encabezado
		$pdf = $this->pdf_header();
		
		//Upgrade string
		$upgradeString = "Se hace entrega a la persona arriba descrita de un descuento de US$. ".$data_array['PROCESS_UPGRADE_DISCOUNT']." para el producto ".$data_array['PROCESS_UPGRADE_PRODUCT_CODE']." - ".$data_array['PROCESS_UPGRADE_PRODUCT_DESCRIPTION'].", seg�n la factura ".$data_array['PROCESS_UPGRADE_RECEIPT_SERIES']." - ".$data_array['PROCESS_UPGRADE_RECEIPT_NUMBER'].".
Para lo cual el cliente firma en conformidad con esta resoluci�n y declara que recibe el producto aqui descrito pagando �nicamente la diferencia seg�n el descuento otorgado.";
		
		//Rellenar con los datos de la orden a entregar
		$pdf->SetFont('Courier','B',10);
		$pdf->Cell(0,7,"Detalle del upgrade",1,2,'L',FALSE);
		$pdf->SetFont('Courier','',10);
		$pdf->drawTextBox($upgradeString, 190, 130);
		
		//Firma
		$pdf->SetY(230);
		$pdf->SetFont('Courier','',8);
		$pdf->Cell(110, 5, "Nombre del cliente que recibe", "T", 0);
		$pdf->Cell(5, 5, "");
		$pdf->Cell(75, 5, "Firma del cliente que recibe", "T", 1);
		$pdf->Cell(0, 5, NULL, 0, 1); //Espacio entre lineas
		$pdf->SetFillColor(214,214,214);
		$pdf->SetFont('Courier','B',10);
		$pdf->Cell(0, 5, "Uso exclusivo de la tienda", "LTRB", 1, "L", true);
		$pdf->SetFont('Courier','',8);
		$pdf->Cell(0, 5, NULL, 0, 1); //Espacio entre lineas
		$pdf->Cell(110, 5, "", "LTRB", 0);
		$pdf->Cell(5, 5, "");
		$pdf->Cell(75, 20, "", "LTRB", 0);
		$pdf->Cell(0, 5, NULL, 0, 1); //Espacio entre lineas
		$pdf->Cell(110, 5, "Nombre del empleado que entrega", 0, 1);
		$pdf->Cell(110, 5, NULL, 0, 1); //Espacio entre lineas
		$pdf->Cell(110, 5, "", "LTRB", 1);
		$pdf->Cell(110, 5, "Cargo del empleado que entrega", 0, 0);
		$pdf->Cell(5, 5, "");
		$pdf->Cell(75, 5, "Firma del empleado que entrega", 0, 1);
		
		$pdf->Output();
	 }
	//PDF Planillas
	public function pdfpayroll($id){
		
		$this->FW->load->model('cms/cms_plugin_payrolls', 'plugin_payrolls');
		$payrolls			= $this->FW->plugin_payrolls->get_payroll($id);
		
		list($iniy, $inim, $inid) = explode("-", $payrolls->PAYROLL_INITIALDATE); //Obtener dia mes y a�o separado de la fecha de inicio
		list($endy, $endm, $endd) = explode("-", $payrolls->PAYROLL_ENDDATE);//Obtener dia mes y a�o separado de la fecha final
		$stringmonth		= date_components(); //Obtener formato en array para nombres de meses. (utilities_helper)
		
		//Iniciar el PDF
	 	$pdf = new PDF();
		$pdf->AddPage();
		$pdf->SetFont('Arial','B',16);
				
		$pdf->Image(base_url('library/cms/img/logo-iCinco-negro.png'),10,7,40);
		$pdf->Image(base_url('library/cms/img/logo-iCinco-negro.png'),10,142,40);
		
	for($i = 0; $i <= 1; $i++):	 	
		
		$pdf->SetFont('Arial','B',10);
		$pdf->Cell(0, 5, "Comprobante de pago", 0, 0, "C");
		$pdf->Ln(5);
		$pdf->Cell(0, 5, "Planilla de salarios", 0, 0, "C");
		
		$pdf->SetFont('Arial','',9);
		$pdf->Ln(10);
		
		//Informaci�n general de planilla
		$pdf->Cell(40,10,'Salario Nominal:',0,0);
		$pdf->Cell(40,10,'Q. '.$payrolls->PAYROLL_SALARYPAID,0,0);
		$pdf->Ln(4);
		$pdf->Cell(40,10,'Fecha de emisi�n:',0,0);
		$pdf->Cell(40,10,date('d/m/Y'),0,1);
		$pdf->SetFillColor(214,214,214);
		$pdf->Cell(0, 6, "Del $inid de ".$stringmonth['meses'][$inim].", $iniy al $endd de ".$stringmonth['meses'][$endm].", $endy", 0, 1, "C", TRUE);
		
		//Informaci�n del empleado
		$pdf->Cell(40,10,'Empleado:',0,0);
		$pdf->Cell(40,10,"$payrolls->SALESMAN_SAC_CODE - $payrolls->SALESMAN_NAME $payrolls->SALESMAN_LASTNAME",0,0);
		$pdf->Ln(4);
		$pdf->Cell(40,10,'Cargo:',0,0);
		$pdf->Cell(40,10,$payrolls->SALESMAN_POSITION,0,0);
		$pdf->Ln(4);
		$pdf->Cell(40,10,'Lugar:',0,0);
		$pdf->Cell(40,10,"GUATEMALA",0,0);
		$pdf->Ln(4);
		$pdf->Cell(40,10,'Jornada:',0,0);
		$pdf->Cell(40,10,$payrolls->SALESMAN_WORKHOURS,0,1);
		
		//Informaci�n de ingresos y egresos
		$pdf->Cell(90, 6, "Ingresos", "LTR", 0, "C", TRUE);
		$pdf->Cell(10, 6, NULL, 0, 0);
		$pdf->Cell(90, 6, "Descuentos", "LTR", 1, "C", TRUE);
		
		$quincena			= ($payrolls->SALESMAN_COMMENCEMENT > $payrolls->PAYROLL_INITIALDATE)?0:number_format($payrolls->PAYROLL_SALARYPAID*-1 / 2,2,".",""); //Obtener quincena pagada anteriormente.
		$pdf->Cell(60, 6, "Salario:", "L", 0);
		$pdf->Cell(30, 6, "Q. $payrolls->PAYROLL_SALARYPAID", "R", 0, "R");
		$pdf->Cell(10, 6, NULL, 0, 0);
		$pdf->Cell(60, 6, "Anticipo de sueldos:", "L", 0);
		$pdf->Cell(30, 6, "Q. $quincena", "R", 1, "R");
		$pdf->Cell(60, 6, "Bono Decreto:", "L", 0);
		$pdf->Cell(30, 6, "Q. $payrolls->PAYROLL_ESTABLISHEDBONUS", "R", 0, "R");
		$pdf->Cell(10, 6, NULL, 0, 0);
		$pdf->Cell(60, 6, "Seguro Social:", "L", 0);
		$pdf->Cell(30, 6, "Q. $payrolls->PAYROLL_IGSS", "R", 1, "R");
		$pdf->Cell(60, 6, "Comisiones:", "L", 0);
		$pdf->Cell(30, 6, "Q. $payrolls->PAYROLL_COMMISSION", "R", 0, "R");
		$pdf->Cell(10, 6, NULL, 0, 0);
		$pdf->Cell(60, 6, "Impuesto Sobre la Renta:", "L", 0);
		$pdf->Cell(30, 6, "Q. $payrolls->PAYROLL_ISR", "R", 1, "R");
		$pdf->Cell(60, 6, "Horas Extra:", "L", 0);
		$pdf->Cell(30, 6, "Q. ".($payrolls->PAYROLL_EXTRAHOURSSALARY + $payrolls->PAYROLL_FESTIVEHOURSSALARY), "R", 0, "R");
		$pdf->Cell(10, 6, NULL, 0, 0);
		if($payrolls->PAYROLL_EXTRADISCOUNT > 0 || $payrolls->PAYROLL_EXTRAINCOME > 0):
		$payrolls->PAYROLL_EXTRADISCOUNTDESCRIPTION = (empty($payrolls->PAYROLL_EXTRADISCOUNTDESCRIPTION))?"Otros":$payrolls->PAYROLL_EXTRADISCOUNTDESCRIPTION;
		$payrolls->PAYROLL_EXTRAINCOMEDESCRIPTION = (empty($payrolls->PAYROLL_EXTRAINCOMEDESCRIPTION))?"Otros":$payrolls->PAYROLL_EXTRAINCOMEDESCRIPTION;
		$pdf->Cell(60, 6, "$payrolls->PAYROLL_EXTRADISCOUNTDESCRIPTION:", "L", 0);
		$pdf->Cell(30, 6, "Q. $payrolls->PAYROLL_EXTRADISCOUNT", "R", 1, "R");
		$pdf->Cell(60, 6, "$payrolls->PAYROLL_EXTRAINCOMEDESCRIPTION:", "L", 0);
		$pdf->Cell(30, 6, "Q. $payrolls->PAYROLL_EXTRAINCOME", "R", 0, "R");
		$pdf->Cell(10, 6, NULL, 0, 0);
		endif;
		$payrolls->PAYROLL_TOTALDISCOUNT = ($payrolls->PAYROLL_TOTALDISCOUNTS + $quincena); //Agregar el pago de quincena al total descontado.
		$pdf->Cell(90, 6, "", "LR", 1);
		$pdf->Cell(60, 6, "Total Devengado", "LBTR", 0, "C");
		$pdf->Cell(30, 6, "Q. $payrolls->PAYROLL_TOTALACCRUED", "LBTR", 0, "R");
		$pdf->Cell(10, 6, NULL, 0, 0);
		$pdf->Cell(60, 6, "Total Descontado", "LBTR", 0, "C");
		$pdf->Cell(30, 6, "Q. $payrolls->PAYROLL_TOTALDISCOUNT", "LBTR", 1, "R");
		$pdf->Ln(5);
		$pdf->Cell(100, 6, NULL, 0, 0);
		$pdf->Cell(60, 6, "Neto a pagar", 0, 0, "C");
		$pdf->Cell(30, 6, "Q. ".number_format(($payrolls->PAYROLL_TOTALACCRUED + $payrolls->PAYROLL_TOTALDISCOUNT), 2, ".", ""), "LBTR", 1, "R");
		$pdf->Cell(0, 6, "Recib� Conforme:", 0, 0, "L");
		$pdf->Ln(15);
		$pdf->Cell(40, 6, "Fecha", "T", 0, "L");
		$pdf->Cell(10, 6, NULL, 0, 0);
		$pdf->Cell(80, 6, "Nombre", "T", 0, "L");
		$pdf->Cell(10, 6, NULL, 0, 0);
		$pdf->Cell(50, 6, "Firma", "T", 1, "L");
		$pdf->Ln(10);
	endfor;
	
	$filename	= $_SERVER['DOCUMENT_ROOT'].('/app/user_files/uploads/planillas/planilla'.$payrolls->ID.'.pdf');
	return $pdf->Output($filename,'F');
}
	/**
	 * Funci�n de finiquito de planilla en pdf.
	 */	
	 public function pdfsettlement($id, $vacaciones, $bono14, $aguinaldo, $indemnizacion, $liquidaciontotal){
		
		$this->FW->load->model('cms/cms_plugin_payrolls', 'plugin_payrolls');
		$payrolls			= $this->FW->plugin_payrolls->get_payroll($id);
		
		list($iniy, $inim, $inid) = explode("-", $payrolls->PAYROLL_INITIALDATE); //Obtener dia mes y a�o separado de la fecha de inicio
		list($endy, $endm, $endd) = explode("-", $payrolls->PAYROLL_ENDDATE);//Obtener dia mes y a�o separado de la fecha final
		list($comy, $comm, $comd) = explode("-", $payrolls->SALESMAN_COMMENCEMENT);//Obtener dia mes y a�o separado de la fecha final
		$stringmonth		= date_components(); //Obtener formato en array para nombres de meses. (utilities_helper)
		
		//Iniciar el PDF
	 	$pdf = new PDF();
		$pdf->AddPage();
		$pdf->SetFont('Arial','B',9);
				
		$pdf->Image(base_url('library/cms/img/logo-iCinco-negro.png'),10,7,40);
		
		$pdf->Cell(0, 5, "ICINCO Inversiones", 0, 0, "C");
		$pdf->Ln(5);
		$pdf->Cell(0, 5, "Finiquito", 0, 0, "C");
		
		$pdf->SetFont('Arial','',9);
		$pdf->Ln(10);
		
		//Declaraci�n de recepci�n de prestaciones.
		$pdf->MultiCell(0, 4, "Yo, ".iconv('UTF-8', 'windows-1252', strip_tags(html_entity_decode($payrolls->SALESMAN_NAME." ".$payrolls->SALESMAN_LASTNAME)))." hago constar que he recibido de ICINCO INVERSIONES todas las prestaciones legales que me corresponden del $comd de ".$stringmonth['meses'][$comm].", $comy al $endd de ".$stringmonth['meses'][$endm].", $endy, tiempo durante el cual prest� mis servicios desempe�ando el puesto de $payrolls->SALESMAN_POSITION, siendo las siguientes:");
		$pdf->Ln(5);
		$pdf->SetFont('Arial','B',9);
		
		//SUELDOS
		$pdf->Cell(0, 5, "I. SUELDOS:", 0, 1, "L");
		$pdf->SetFont('Arial','',9);
		$namesize	= 30;
		$amountsize	= (190 - $namesize);
		$pdf->Cell($namesize, 5, "Ordinario:", 0, 0, "L");
		$pdf->Cell($amountsize, 5, "Q.".number_format($payrolls->PAYROLL_SALARYPAID,2,'.',','), 0, 1, "L");
		$pdf->Cell($namesize, 5, "Extraordinario:", 0, 0, "L");
		$pdf->Cell($amountsize, 5, "Q.".number_format(($payrolls->PAYROLL_EXTRAHOURSSALARY + $payrolls->PAYROLL_FESTIVEHOURSSALARY),2,'.',','), 0, 1, "L");
		$pdf->Cell($namesize, 5, "Comisiones:", 0, 0, "L");
		$pdf->Cell($amountsize, 5, "Q.".number_format($payrolls->PAYROLL_COMMISSION,2,'.',','), 0, 1, "L");
		$pdf->Cell($namesize, 5, "Bono Decreto:", 0, 0, "L");
		$pdf->Cell($amountsize, 5, "Q.".number_format($payrolls->PAYROLL_ESTABLISHEDBONUS,2,'.',','), 0, 1, "L");
		if(!empty($payrolls->PAYROLL_EXTRAINCOMEDESCRIPTION)):
		$pdf->Cell($namesize, 5, $payrolls->PAYROLL_EXTRAINCOMEDESCRIPTION, 0, 0, "L");
		$pdf->Cell($amountsize, 5, "Q.".number_format($payrolls->PAYROLL_EXTRAINCOME,2,'.',','), 0, 1, "L");
		endif;
		if(!empty($payrolls->PAYROLL_EXTRADISCOUNTDESCRIPTION)):
		$pdf->Cell($namesize, 5, $payrolls->PAYROLL_EXTRADISCOUNTDESCRIPTION, 0, 0, "L");
		$pdf->Cell($amountsize, 5, "Q.".number_format($payrolls->PAYROLL_EXTRADISCOUNT,2,'.',','), 0, 1, "L");
		endif;
		$pdf->Cell(0, 5, "Q.".number_format($payrolls->PAYROLL_TOTALACCRUED,2,'.',','), "T", 1, "R");
		//VACACIONES
		$pdf->SetFont('Arial','B',9);
		$pdf->Cell(0, 5, "II. VACACIONES:", 0, 1, "L");
		$pdf->SetFont('Arial','',9);
		$pdf->Cell(0, 5, "Por el per�odo de $comd/$comm/$comy al $endd/$endm/$endy", 0, 1, "L");
		$pdf->SetFont('Arial','B',9);
		$pdf->Cell(20, 5, 'A�o', 0, 0, "L");
		$pdf->Cell(40, 5, 'Dias pendientes', 0, 0, "L");
		$pdf->Cell(60, 5, 'Sueldo Pendiente', 0, 1, "L");
		$pdf->SetFont('Arial','',9);
		foreach($vacaciones['dpendientes'] as $i => $dias):
		$pdf->Cell(20, 5, $i + 1, 0, 0, "L"); //A�o pendiente de vacaciones.
		$pdf->Cell(40, 5, number_format($dias,2,'.',''), 0, 0, "L");
		$pdf->Cell(60, 5, "Q.".number_format($vacaciones['salaries'][$i],2,'.',','), 0, 1, "L");
		endforeach;
		$pdf->Cell(0, 5, "Q.".number_format($vacaciones['total'],2,'.',','), "T", 1, "R");
		//BONO 14
		$pdf->SetFont('Arial','B',9);
		$pdf->Cell(0, 5, "III. BONO 14:", 0, 1, "L");
		$pdf->SetFont('Arial','',9);
		$pdf->Cell(0, 5, "Por el per�odo del ".mysql_date_to_dmy($bono14['commencementdate'])." al $endd/$endm/$endy", 0, 1, "L");
		$namesize	= 30;
		$amountsize	= (190 - $namesize);
		$pdf->Cell($namesize, 5, "Salario Promedio:", 0, 0, "L");
		$pdf->Cell($amountsize, 5, "Q.".number_format($bono14['salariopromedio'],2,'.',','), 0, 1, "L");
		$pdf->Cell($namesize, 5, "Dias pendientes:", 0, 0, "L");
		$pdf->Cell($amountsize, 5, $bono14['diaspendientes'], 0, 1, "L");
		$pdf->Cell(0, 5, "Q.".number_format($bono14['total14bonus'],2,'.',','), "T", 1, "R");
		//AGUINALDO
		$pdf->SetFont('Arial','B',9);
		$pdf->Cell(0, 5, "IV. AGUINALDO:", 0, 1, "L");
		$pdf->SetFont('Arial','',9);
		$pdf->Cell(0, 5, "Por el per�odo del ".mysql_date_to_dmy($aguinaldo['commencementdate'])." al $endd/$endm/$endy", 0, 1, "L");
		$namesize	= 30;
		$amountsize	= (190 - $namesize);
		$pdf->Cell($namesize, 5, "Salario Promedio:", 0, 0, "L");
		$pdf->Cell($amountsize, 5, "Q.".number_format($aguinaldo['salariopromedio'],2,'.',','), 0, 1, "L");
		$pdf->Cell($namesize, 5, "Dias pendientes:", 0, 0, "L");
		$pdf->Cell($amountsize, 5, $aguinaldo['diaspendientes'], 0, 1, "L");
		$pdf->Cell(0, 5, "Q.".number_format($aguinaldo['totalchristmasbonus'],2,'.',','), "T", 1, "R");
		//INDEMNIZACI�N
		if(is_array($indemnizacion) && $indemnizacion != FALSE):
		$pdf->SetFont('Arial','B',9);
		$pdf->Cell(0, 5, "V. INDEMNIZACI�N:", 0, 1, "L");
		$pdf->SetFont('Arial','',9);
		$namesize	= 60;
		$amountsize	= (190 - $namesize);
		$pdf->Cell(0, 5, "Total de tiempo laborado fu� de ".$indemnizacion['yearssincecommen']." a�os y ".$indemnizacion['dayswithoutyears']." d�as.", 0, 1, "L");
		$pdf->Cell($namesize, 5, "Salarios Devengados:", 0, 0, "L");
		$pdf->Cell($amountsize, 5, "Q.".number_format($indemnizacion['salaries'],2,'.',','), 0, 1, "L");
		$pdf->Cell($namesize, 5, "Comisiones Devengadas:", 0, 0, "L");
		$pdf->Cell($amountsize, 5, "Q.".number_format($indemnizacion['commissions'],2,'.',','), 0, 1, "L");
		$pdf->Cell($namesize, 5, "Bono 14 proporcional:", 0, 0, "L");
		$pdf->Cell($amountsize, 5, "Q.".number_format($indemnizacion['bonos14'],2,'.',','), 0, 1, "L");
		$pdf->Cell($namesize, 5, "Aguinaldo proporcional:", 0, 0, "L");
		$pdf->Cell($namesize, 5, "Q.".number_format($indemnizacion['aguinaldos'],2,'.',','), 0, 0, "L");
		$pdf->SetFont('Arial','B',9);
		$pdf->Cell($namesize, 5, "Q.".number_format($indemnizacion['totalganado'],2,'.',','), 0, 0, "L");
		$pdf->Ln(10);
		$pdf->Cell(0, 5, "Promedio:", 0, 1, "L");
		$pdf->Cell(20, 5, "", "B", 0, "L");
		$pdf->Cell(50, 5, "C�lculo", "B", 0, "L");
		$pdf->Cell(30, 5, "Monto", "B", 0, "L");
		$pdf->Cell(10, 5, "", "B", 0, "L");
		$pdf->Cell(30, 5, "Tiempo", "B", 0, "L");
		$pdf->Cell(50, 5, "", 0, 1, "L");
		$pdf->SetFont('Arial','I',8);
		$pdf->Cell(20, 5, "A�os", 0, 0, "L");
		$pdf->Cell(50, 5, "Q.".number_format($indemnizacion['totalganado'],2,'.',',')." (/".$indemnizacion['dayssincecommen']." dias) x 30", 0, 0, "L");
		$pdf->Cell(30, 5, "Q.".number_format($indemnizacion['promediomes'],2,'.',','), 0, 0, "L");
		$pdf->Cell(10, 5, "x", 0, 0, "C");
		$pdf->Cell(30, 5, $indemnizacion['yearssincecommen'], 0, 0, "L");
		$pdf->Cell(50, 5, "Q.".number_format($indemnizacion['indemizaryear'],2,'.',','), 0, 1, "L");
		$pdf->Cell(20, 5, "Dias", 0, 0, "L");
		$pdf->Cell(50, 5, "Q.".number_format($indemnizacion['promediomes'],2,'.',',')." (/365 dias)", 0, 0, "L");
		$pdf->Cell(30, 5, "Q.".number_format($indemnizacion['promediodia'],2,'.',','), 0, 0, "L");
		$pdf->Cell(10, 5, "x", 0, 0, "C");
		$pdf->Cell(30, 5, $indemnizacion['dayswithoutyears'], 0, 0, "L");
		$pdf->Cell(50, 5, "Q.".number_format($indemnizacion['indemizarday'],2,'.',','), 0, 1, "L");
		$pdf->SetFont('Arial','',9);
		$pdf->Cell(0, 5, "Q.".number_format($indemnizacion['totalindemnizar'],2,'.',','), "T", 1, "R");
		endif;
		$pdf->SetFont('Arial','B',9);
		$pdf->Cell(30, 5, "TOTAL:", 0, 0, "L");
		$pdf->Cell(160, 5, "Q.".number_format($liquidaciontotal,2,'.',','), 0, 1, "R");
		$pdf->Ln(3);
		$pdf->SetFont('Arial','',9);
		$pdf->MultiCell(0, 5, "Y para los usos legales que convengan a ICINCO INVERSIONES, extiendo y firmo  este amplio y eficaz finiquito laboral, en la ciudad de Guatemala, al dia $endd de ".$stringmonth['meses'][$endm]." del $endy.");
		$pdf->Ln(30);
		$pdf->SetFont('Arial','B',9);
		$pdf->Cell(30, 5, iconv('UTF-8', 'windows-1252', strip_tags(html_entity_decode($payrolls->SALESMAN_NAME." ".$payrolls->SALESMAN_LASTNAME))), 0, 1, "L");
		
		$filename	= $_SERVER['DOCUMENT_ROOT'].('/app/user_files/uploads/planillas/finiquito'.$payrolls->ID.'.pdf');
		return $pdf->Output($filename,'F');
		
	 }
}