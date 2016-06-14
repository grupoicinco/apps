<?php
/**
 * @author 	Guido A. Orellana
 * @name	Plugin reparaciones
 * @since	agosto 2014
 * 
 */
class Plugin_reclamos extends PL_Controller {
	
	function __construct(){
		parent::__construct();
		
		//Load the plugin data
		$this->plugin_action_table			= 'PLUGIN_RECLAIMS';
		$this->plugin_button_create			= "Crear Nuevo Registro de Reclamos";
		$this->plugin_button_cancel			= "Cancelar";
		$this->plugin_button_update			= "Guardar Cambios";
		$this->plugin_button_delete			= "Eliminar";
		$this->plugin_page_title			= "Reclamos";
		$this->plugin_page_create			= "Crear Nuevo Registro de Reclamos";
		$this->plugin_page_read				= "Mostrar Registro de Reclamos";
		$this->plugin_page_update			= "Editar Registro de Reclamos";
		$this->plugin_page_delete			= "Eliminar";
		
		$this->template_display				= "plugin_reclamos"; //Si no se describe, se pone como default "plugin_display"
		
		$this->plugin_display_array[0]		= "ID";
		$this->plugin_display_array[1]		= "Nombre del cliente";
		$this->plugin_display_array[2]		= "Correo del cliente";
		$this->plugin_display_array[3]		= "Tel&eacute;fono del cliente";
		$this->plugin_display_array[4]		= "Código del producto";
		$this->plugin_display_array[5]		= "Fecha del reclamo";
		$this->plugin_display_array[6]		= "Descripción del reclamo";
		$this->plugin_display_array[7]		= "Préstamo de valija";
		$this->plugin_display_array[8]		= "Personal que recibe el producto";
		$this->plugin_display_array[9]		= "Valija prestada";
		$this->plugin_display_array[10]		= "Número de tarjeta de crédito";
		$this->plugin_display_array[11]		= "Nombre de la tarjeta";
		$this->plugin_display_array[12]		= "Código de seguridad de la tarjeta";
		$this->plugin_display_array[13]		= "Marca de la tarjeta";
		$this->plugin_display_array[14]		= "Sucursal";
		
		$this->plugins_model->initialise($this->plugin_action_table);
		$this->load->model('cms/cms_plugin_reclamos', 'plugin_reclamos');
		
		//Extras to send
		$this->display_pagination			= TRUE; //Mostrar paginación en listado
		$this->pagination_per_page			= 10; //Numero de registros por página
		$this->pagination_total_rows		= $this->plugin_reclamos->total_rows("MONTH(RECLAIM_DATE) = '".date('m')."' AND YEAR(RECLAIM_DATE) = '".date('Y')."'"); //Número total de items a desplegar
		
		$this->display_filter				= FALSE; //Mostrar filtro de búsqueda 'SEARCH' o según listado 'LIST' o no mostrar FALSE
		
		//Obtener el profiler del plugin
		$this->output->enable_profiler(FALSE);
	}
	
	/**
	 * Funciones para editar Querys o Datos a enviar desde cada plugin
	 */
	//Función para desplegar listado, desde aquí se puede modificar el query
	public function _plugin_display($filterArray){
		//Variables a enviar
		$offset = (isset($filterArray[2]))?$filterArray[2]:0; //Obtener el primer valor a desplegar del listado
		$search = (isset($filterArray[1]) && $filterArray[1] != 'display_all')?ltrim($filterArray[1], 0):NULL; //Busqueda especifica
		$this->pagination_total_rows = $this->plugin_reclamos->total_reclaims(date('m'), date('Y'), $search); //Total de filas
		
		$result_array = array();
		$result_array = $this->plugin_reclamos->get_reclaims(date('m'), date('Y'), $search, $this->pagination_per_page, $offset);
		
		
		return $this->_html_plugin_display($result_array);
	}
	
	/**
	 * Función para desplegar listado completo de datos guardados, enviar los títulos en array con clave header y el cuerpo en un array con clave body.
	 * Para editar fila es a la función 'update_table_row'
	 * 
	 * @param	$result_array 		array 		Array con la listado devuelto por query de la DB
	 * @return	$data_array 		array 		Arreglo con la información del header y body
	 */
	public function _html_plugin_display($result_array){
		
		//Header data
		$data_array['header'][1]			= "Reclamo";
		$data_array['header'][2]			= $this->plugin_display_array[1];
		$data_array['header'][3]			= $this->plugin_display_array[5];
		$data_array['header'][4]			= NULL;
		
		//Body data
		$data_array['body'] = '';
		foreach($result_array as $field):
		$data_array['body']					.= '<tr>';
		$data_array['body']					.= '<td><a href="'.base_url('cms/'.strtolower($this->current_plugin).'/update_table_row/'.$field->ID).'">'.str_pad($field->ID, 5, 0, STR_PAD_LEFT).'</a></td>';
		$data_array['body']					.= '<td>'.$field->RECLAIM_CLIENT_NAME.'</td>';
		$data_array['body']					.= '<td>'.mysql_date_to_dmy($field->RECLAIM_DATE).'</td>';
		$data_array['body']					.= '<td><a href="'.site_url('cms/plugin_reclamos/pdf/'.$field->ID).'" target="_blanck" class="btn btn-default btn-sm"><span class="glyphicon glyphicon-print"></span> Imprimir</a></td>';
		$data_array['body']					.= '</tr>';
		endforeach;
		
		return $data_array;
	}
	
	/*
	 * Función para crear nuevo contenido, desde aquí se especifican los campos a enviar en el formulario.
	 * El formulario se envía mediante objectos preestablecidos de codeigniter. 
	 * El formulario se envía con un array con la clave form_html.
	 * Se puede encontrar una guía en: http://ellislab.com/codeigniter/user-guide/helpers/form_helper.html
	 */
	public function _html_plugin_create(){
		
		//Listado del staff
		$staff = $this->plugin_reclamos->staff_list();
		$store = $this->plugin_reclamos->store_list();
        
		//Formulario
		$data_array['form_html']			=  "<div class='form-group'>".form_label($this->plugin_display_array[1],'',array('class' => 'form-label col-lg-2'))."<div class='col-lg-10'>".form_input(array('name' => 'RECLAIM_CLIENT_NAME', 'class' => 'form-control'))."</div></div>";
		$data_array['form_html']			.= "<div class='form-group'>".form_label($this->plugin_display_array[2],'',array('class' => 'form-label col-lg-2'))."<div class='col-lg-10'>".form_input(array('name' => 'RECLAIM_CLIENT_EMAIL', 'class' => 'form-control'))."</div></div>";
		$data_array['form_html']			.= "<div class='form-group'>".form_label($this->plugin_display_array[3],'',array('class' => 'form-label col-lg-2'))."<div class='col-lg-10'>".form_input(array('name' => 'RECLAIM_CLIENT_PHONE', 'class' => 'form-control'))."</div></div>";
		$data_array['form_html']			.= "<div class='form-group'>".form_label($this->plugin_display_array[4],'',array('class' => 'form-label col-lg-2'))."<div class='col-lg-10'>".form_input(array('name' => 'RECLAIM_PRODUCT', 'class' => 'form-control'))."</div></div>";
		$data_array['form_html']			.= "<div class='form-group'>".form_label($this->plugin_display_array[5],'',array('class' => 'form-label col-lg-2'))."<div class='col-lg-10'>".form_input(array('name' => 'RECLAIM_DATE', 'class' => 'form-control', 'readonly' => 'readonly', 'value' => date('Y').'-'.date('m').'-'.date('d')))."</div></div>";
		$data_array['form_html']			.= "<div class='form-group'>".form_label($this->plugin_display_array[6],'',array('class' => 'form-label col-lg-2'))."<div class='col-lg-10'>".form_textarea(array('name' => 'RECLAIM_DESCRIPTION', 'class' => 'form-control textarea'))."</div></div>";
		$data_array['form_html']			.= "<div class='form-group'>".form_label($this->plugin_display_array[8],'',array('class' => 'form-label col-lg-2'))."<div class='col-lg-10'>".form_dropdown('RECLAIM_RECEIVER', $staff)."</div></div>";
		$data_array['form_html']			.= "<div class='form-group'>".form_label($this->plugin_display_array[14],'',array('class' => 'form-label col-lg-2'))."<div class='col-lg-10'>".form_dropdown('RECLAIM_STORE', $store)."</div></div>";
		$data_array['form_html']			.= "<div class='form-group'>".form_label($this->plugin_display_array[7],'',array('class' => 'form-label col-lg-2'))."<div class='col-lg-10'>".form_dropdown('RECLAIM_LOANER', array('SI' => 'SI', 'NO' => 'NO'), array('NO' => 'NO'), "disabled='disabled'")."</div></div>";
		
		return $data_array;
    }
	public function _html_plugin_update($result_data){
		
		//Listado del staff
		$staff = $this->plugin_reclamos->staff_list();
		$store = $this->plugin_reclamos->store_list();
        
		//Formulario si no se ha enviado ya el proceso
		if($result_data->PROCESS_STAGE == 'RECEPCION'):
		$data_array['form_html']			=  "<div class='form-group'>".form_label($this->plugin_display_array[1],'',array('class' => 'form-label col-lg-2'))."<div class='col-lg-10'>".form_input(array('name' => 'RECLAIM_CLIENT_NAME', 'class' => 'form-control', 'value' => $result_data->RECLAIM_CLIENT_NAME))."</div></div>";
		$data_array['form_html']			.= "<div class='form-group'>".form_label($this->plugin_display_array[2],'',array('class' => 'form-label col-lg-2'))."<div class='col-lg-10'>".form_input(array('name' => 'RECLAIM_CLIENT_EMAIL', 'class' => 'form-control', 'value' => $result_data->RECLAIM_CLIENT_EMAIL))."</div></div>";
		$data_array['form_html']			.= "<div class='form-group'>".form_label($this->plugin_display_array[3],'',array('class' => 'form-label col-lg-2'))."<div class='col-lg-10'>".form_input(array('name' => 'RECLAIM_CLIENT_PHONE', 'class' => 'form-control', 'value' => $result_data->RECLAIM_CLIENT_PHONE))."</div></div>";
		$data_array['form_html']			.= "<div class='form-group'>".form_label($this->plugin_display_array[4],'',array('class' => 'form-label col-lg-2'))."<div class='col-lg-10'>".form_input(array('name' => 'RECLAIM_PRODUCT', 'class' => 'form-control', 'value' => $result_data->RECLAIM_PRODUCT))."</div></div>";
		$data_array['form_html']			.= "<div class='form-group'>".form_label($this->plugin_display_array[5],'',array('class' => 'form-label col-lg-2'))."<div class='col-lg-10'>".form_input(array('name' => 'RECLAIM_DATE', 'class' => 'form-control', 'readonly' => 'readonly', 'value' => $result_data->RECLAIM_DATE))."</div></div>";
		$data_array['form_html']			.= "<div class='form-group'>".form_label($this->plugin_display_array[6],'',array('class' => 'form-label col-lg-2'))."<div class='col-lg-10'>".form_textarea(array('name' => 'RECLAIM_DESCRIPTION', 'class' => 'form-control', 'value' => $result_data->RECLAIM_DESCRIPTION))."</div></div>";
		$data_array['form_html']			.= "<div class='form-group'>".form_label($this->plugin_display_array[8],'',array('class' => 'form-label col-lg-2'))."<div class='col-lg-10'>".form_dropdown('RECLAIM_RECEIVER', $staff, $result_data->RECLAIM_RECEIVER)."</div></div>";
		$data_array['form_html']			.= "<div class='form-group'>".form_label($this->plugin_display_array[14],'',array('class' => 'form-label col-lg-2'))."<div class='col-lg-10'>".form_dropdown('RECLAIM_STORE', $store, $result_data->RECLAIM_STORE)."</div></div>";
		$data_array['form_html']			.= "<div class='form-group'>".form_label($this->plugin_display_array[7],'',array('class' => 'form-label col-lg-2'))."<div class='col-lg-10'>".form_dropdown('RECLAIM_LOANER', array('SI' => 'SI', 'NO' => 'NO'), array('NO' => 'NO'), "disabled='disabled'")."</div></div>";
		else:
		$data_array['form_html'] 			= NULL;
		$this->fw_alerts->add_new_alert(2020, 'WARNING');
		endif;
		
		return $data_array;
	}
	
	//Funciones de los posts a enviar
	public function post_new_val(){
		$submit_posts 					= $this->input->post();
		$submit_posts['PROCESS_STAGE']	= 'RECEPCION';
		
		return $this->_set_new_val($submit_posts);
	}
	public function post_update_val($data_id){
		$submit_posts 					= $this->input->post();
		$submit_posts['ID']				= $data_id;
		$submit_posts['PROCESS_STAGE']	= 'RECEPCION';
		
		return $this->_set_update_val($submit_posts);
	}
	
	/**
	 * Funciones específicas del plugin
	 */
	 public function pdf($order){
	 	$this->load->library('fpdf');
	  	$this->load->library('FW_export', array('order' => $order));
		
		$data = $this->plugin_reclamos->get_reclaim($order); //Obtener datos del reclamo
		$store = $this->plugin_reclamos->get_store($data->RECLAIM_STORE);
		
		//Guardar datos en variables
		
		$string 			= iconv('UTF-8', 'windows-1252', strip_tags(html_entity_decode($data->RECLAIM_DESCRIPTION)));
	 	$nombre_cliente 	= iconv('UTF-8', 'windows-1252', html_entity_decode($data->RECLAIM_CLIENT_NAME));
		$telefono_cliente	= $data->RECLAIM_CLIENT_PHONE;
		$email_cliente		= $data->RECLAIM_CLIENT_EMAIL;
		
		$orden_fecha		= mysql_date_to_dmy("$data->RECLAIM_DATE");
		$codigo_orden		= "R-".str_pad($data->ID, 4, "0", STR_PAD_LEFT);
		$orden_producto		= $data->RECLAIM_PRODUCT;
		$order_receiver		= str_pad($data->SALESMAN_SAC_CODE, 3, "0", STR_PAD_LEFT)." - ".iconv('UTF-8', 'windows-1252', html_entity_decode($data->SALESMAN_NAME))." ".iconv('UTF-8', 'windows-1252', html_entity_decode($data->SALESMAN_LASTNAME));
		
		
	 	//Iniciar el PDF
	 	$pdf = $this->fw_export->pdf_header();
				
		$pdf->SetFont('Courier','B',10);
		$pdf->Cell(0,7,"Detalle del servicio",1,2,'L',FALSE);
		
		$pdf->SetFont('Courier','',10);
		$pdf->drawTextBox($string, 190, 130);
		
		//Firma
		$pdf->SetY(230);
		$pdf->SetFont('Courier','',8);
		$pdf->Cell(110, 5, "Nombre", "T", 0);
		$pdf->Cell(5, 5, "");
		$pdf->Cell(75, 5, "Firma", "T", 1);
		
		//Linea para cortar
		$pdf->Image(base_url('library/cms/img/cut.png'),10,240, 5);
		$pdf->SetY(242.5);
		$pdf->Cell(5,2,"",0,0);
		$pdf->Cell(0, 2, "", "T", 1);
		
		
		$pdf->Image(base_url('library/cms/img/TUMI-logo-negro.png'),10,247,20);
		
		//Encabezado datos codo
		$pdf->SetFont('Courier','B',10);
		$pdf->SetFillColor(214,214,214);
		$pdf->Cell(0, 3, "", 0, 2);
		$pdf->Cell(90,5,"");
		$pdf->Cell(100,5,"Datos de la orden","LTR",2,'L',true);
		
		//Cuerpo del codo
		$pdf->Ln(0);
		$pdf->Cell(90,4,"");
		$pdf->Cell(100,4,"","LR",2);
		$pdf->Ln(0);
		$pdf->SetFont('Courier','',8);
		$pdf->Cell(90, 3.5, iconv('UTF-8', 'windows-1252', html_entity_decode($store->STORE_ADDRESS1)), 0, 0);
		$pdf->SetFont('Courier','B',8);
		$pdf->Cell(40, 3.5, "Número de Orden", "L", 0, "R");
		$pdf->SetFont('Courier','',8);
		$pdf->Cell(60, 3.5, "$codigo_orden", "R", 1);
		$pdf->Cell(90, 3.5, iconv('UTF-8', 'windows-1252', html_entity_decode($store->STORE_ADDRESS2)), 0, 0);
		$pdf->SetFont('Courier','B',8);
		$pdf->Cell(40, 3.5, "Fecha de la orden", "L", 0, "R");
		$pdf->SetFont('Courier','',8);
		$pdf->Cell(60, 3.5, "$orden_fecha", "R", 1);
		$pdf->Cell(25,3.5, "T. ".$store->STORE_PHONE, 0, 0);
		$pdf->Cell(65, 3.5, "E. ".$store->STORE_EMAIL, 0, 0);
		$pdf->SetFont('Courier','B',8);
		$pdf->Cell(40, 3.5, "SKU#", "L", 0, "R");
		$pdf->SetFont('Courier','',8);
		$pdf->Cell(60, 3.5, "$orden_producto", "R", 1);
		$pdf->Cell(90, 3.5, "", 0, 0);
		$pdf->SetFont('Courier','B',8);
		$pdf->Cell(40, 3.5, "Recibió", "L", 0, "R");
		$pdf->SetFont('Courier','',8);
		$pdf->Cell(60, 3.5, "$order_receiver", "R", 1);
		$pdf->Cell(90, 3.5, "", 0, 0);
		$pdf->Cell(100, 3.5, "", "LBR", 1);
		
		/*//Cuadro a entregar
		$pdf->Ln(2);
		$pdf->SetFillColor(214,214,214);
		$pdf->SetFont('Courier','B',10);
		$pdf->Cell(100,7,"Contraseña de entrega","LT",0,'L',true);
		$pdf->Cell(60,7,"Fecha","T",0,'R',true);
		$pdf->SetFont('Courier','',10);
		$pdf->Cell(0,7,"08/06/2014","TR",2,'L',true);
		$pdf->Ln(0);
		$pdf->SetFont('Courier','B',10);
		$pdf->Cell(40,7,"Número de Orden","L",0,'R',FALSE);
		$pdf->SetFont('Courier','',10);
		$pdf->Cell(40,7,"R-00003",0,0,'L',FALSE);
		$pdf->SetFont('Courier','',10);
		$pdf->Cell(110,7,"Guido Orellana","R",2,'R',FALSE);
		$pdf->Ln(0);
		$pdf->Cell(190,7,"Recibió 003 - Juan José Monroy el producto con SKU# 0481818POL","LBR",2,'L',FALSE);
		$pdf->SetFont('Courier','',7);
		$pdf->Cell(190,5,"Boulevard Rafael Landívar 10-05 z.16 Paseo Cayalá Local I-109 T. 2493-8136 E. tumicayala@grupoi5.com");*/
		
		
		$pdf->Output();
	 }
}
