<?php
/**
 * @author 	Guido A. Orellana
 * @name	Plugin certificado de cambio
 * @since	octubre 2014
 * 
 */
class Plugin_certificado_cambio extends PL_Controller {
	
	function __construct(){
		parent::__construct();
		
		//Load the plugin data
		$this->plugin_action_table			= 'PLUGIN_CERTIFICADO_CAMBIO';
		$this->plugin_button_create			= "Crear Nuevo Certificado de Cambio";
		$this->plugin_button_cancel			= "Cancelar";
		$this->plugin_button_update			= "Guardar y Enviar Cambios";
		$this->plugin_button_delete			= "Eliminar";
		$this->plugin_page_title			= "Reclamos";
		$this->plugin_page_create			= "Crear Nuevo Registro de Reclamos";
		$this->plugin_page_read				= "Mostrar Registro de Reclamos";
		$this->plugin_page_update			= "Editar Registro de Reclamos";
		$this->plugin_page_delete			= "Eliminar";
		
		$this->template_display				= "plugin_certificado_cambio"; //Si no se describe, se pone como default "plugin_display"
		
		$this->plugin_display_array[0]		= "ID";
		$this->plugin_display_array[1]		= "Número de Factura";
		$this->plugin_display_array[2]		= "Código del producto";
		
		$this->plugins_model->initialise($this->plugin_action_table);
		//$this->load->model('cms/cms_plugin_reclamos', 'plugin_reclamos');
		
		//Extras to send
		$this->display_pagination			= FALSE; //Mostrar paginación en listado
		$this->pagination_per_page			= 10; //Numero de registros por página
		$this->pagination_total_rows		= 100; //Número total de items a desplegar
		$this->uri_segment					= 6;
		$this->base_url						= base_url('cms/'.strtolower($this->current_plugin).'/index/'.(($this->uri->segment(4) == "")?'display_all':$this->uri->segment(4)).'/'.(($this->uri->segment(5) == "")?'RECEPCION':$this->uri->segment(5)).'/');
		
		$this->display_filter				= FALSE; //Mostrar filtro de búsqueda 'SEARCH' o según listado 'LIST' o no mostrar FALSE
		
		$this->enable_action_btns 			= FALSE;
		
		//Obtener el profiler del plugin
		$this->output->enable_profiler(FALSE);
	}
	
	/**
	 * Funciones para editar Querys o Datos a enviar desde cada plugin
	 */
	//Función para desplegar listado, desde aquí se puede modificar el query
	public function _plugin_display($filterArray){
		//Variables a enviar
		
		$this->load->library('clientprint');
		$this->load->library('clientprintjob');
		$this->load->library('installedprinter');
		
		return $this->_html_plugin_display();
	}
	
	/**
	 * Función para desplegar listado completo de datos guardados, enviar los títulos en array con clave header y el cuerpo en un array con clave body.
	 * Para editar fila es a la función 'update_table_row'
	 * 
	 * @param	$result_array 		array 		Array con la listado devuelto por query de la DB
	 * @return	$data_array 		array 		Arreglo con la información del header y body
	 */
	public function _html_plugin_display(){
		
		//Header data
		$data_array['header'][1]			= "Reclamo";
		$data_array['header'][2]			= $this->plugin_display_array[1];
		
		//Body data
		$data_array['body'] = '';
		
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
        
		//Formulario
		$data_array['form_html']			=  "<div class='form-group'>".form_label($this->plugin_display_array[1],'',array('class' => 'form-label col-lg-2'))."<div class='col-lg-10'>".form_input(array('name' => 'RECLAIM_CLIENT_NAME', 'class' => 'form-control'))."</div></div>";
		$data_array['form_html']			.= "<div class='form-group'>".form_label($this->plugin_display_array[2],'',array('class' => 'form-label col-lg-2'))."<div class='col-lg-10'>".form_input(array('name' => 'RECLAIM_CLIENT_EMAIL', 'class' => 'form-control'))."</div></div>";
		$data_array['form_html']			.= "<div class='form-group'>".form_label($this->plugin_display_array[3],'',array('class' => 'form-label col-lg-2'))."<div class='col-lg-10'>".form_input(array('name' => 'RECLAIM_CLIENT_PHONE', 'class' => 'form-control'))."</div></div>";
		$data_array['form_html']			.= "<div class='form-group'>".form_label($this->plugin_display_array[4],'',array('class' => 'form-label col-lg-2'))."<div class='col-lg-10'>".form_input(array('name' => 'RECLAIM_PRODUCT', 'class' => 'form-control'))."</div></div>";
		$data_array['form_html']			.= "<div class='form-group'>".form_label($this->plugin_display_array[5],'',array('class' => 'form-label col-lg-2'))."<div class='col-lg-10'>".form_input(array('name' => 'RECLAIM_DATE', 'class' => 'form-control', 'readonly' => 'readonly', 'value' => date('Y').'-'.date('m').'-'.date('d')))."</div></div>";
		$data_array['form_html']			.= "<div class='form-group'>".form_label($this->plugin_display_array[6],'',array('class' => 'form-label col-lg-2'))."<div class='col-lg-10'>".form_textarea(array('name' => 'RECLAIM_DESCRIPTION', 'class' => 'form-control textarea'))."</div></div>";
		$data_array['form_html']			.= "<div class='form-group'>".form_label($this->plugin_display_array[8],'',array('class' => 'form-label col-lg-2'))."<div class='col-lg-10'>".form_dropdown('RECLAIM_RECEIVER', $staff)."</div></div>";
		$data_array['form_html']			.= "<div class='form-group'>".form_label($this->plugin_display_array[7],'',array('class' => 'form-label col-lg-2'))."<div class='col-lg-10'>".form_dropdown('RECLAIM_LOANER', array('SI' => 'SI', 'NO' => 'NO'), array('NO' => 'NO'), "disabled='disabled'")."</div></div>";
		
		return $data_array;
    }
	public function _html_plugin_update($result_data){
		
		//Listado del staff
		$staff = $this->plugin_reclamos->staff_list();
		
		//ID
		$data_array['data']					= new stdClass();
		$data_array['data']					= $result_data;
		$data_array['enable_action_btns']	= TRUE;
        
		//Formulario
		$data_array['form_html']			=	form_hidden('PROCESS_STAGE', $result_data->PROCESS_STAGE); 
		
		if($result_data->PROCESS_STAGE != "REPARACION" && $result_data->PROCESS_STAGE != "ENTREGA"): //Si no ha llegado al proceso de reparación
		$data_array['form_html']			.= "<div class='form-group'>".form_label($this->plugin_display_array[15],'',array('class' => 'form-label col-lg-2'))."<div class='col-lg-10'>".form_dropdown('PROCESS_WARRANTYAVAIL', array('NO' => 'NO', 'SI' => 'SI'), array($result_data->PROCESS_WARRANTYAVAIL => $result_data->PROCESS_WARRANTYAVAIL), 'class="form-control"')."</div></div>";
		$data_array['form_html']			.= "<div class='form-group'>".form_label($this->plugin_display_array[16],'',array('class' => 'form-label col-lg-2'))."<div class='col-lg-10'><div class='input-group'><span class='input-group-addon'>Q.</span>".form_input(array('name' => 'PROCESS_COST', 'class' => 'form-control', 'value' => $result_data->PROCESS_COST))."</div></div></div>";
		$data_array['form_html']			.= "<div class='form-group'>".form_label($this->plugin_display_array[17],'',array('class' => 'form-label col-lg-2'))."<div class='col-lg-10'>".form_input(array('name' => 'PROCESS_DELIVERY_DATE', 'class' => 'form-control', 'id' => 'datetimepicker', 'data-date-format' => 'YYYY-MM-DD', 'value' => $result_data->PROCESS_DELIVERY_DATE))."</div></div>";
		$data_array['form_html']			.= "<div class='form-group'>".form_label($this->plugin_display_array[18],'',array('class' => 'form-label col-lg-2'))."<div class='col-lg-10'>".form_textarea(array('name' => 'PROCESS_DESCRIPTION', 'class' => 'form-control textarea', 'value' => $result_data->PROCESS_DESCRIPTION))."</div></div>";
		
		elseif($result_data->PROCESS_STAGE != "ENTREGA"): //Si está en proceso de reparación
		$this->plugin_button_update 		= "Finalizar la orden";
		$data_array['form_html']			.= 	"<blockquote><p>
												Esta orden se encuentra en proceso de reparaci&oacute;n. Para hacer entrega del producto, imprimir el documento de entrega con el boton de imprimir y entregarlo al cliente para que firme de recibido.<br>Luego presionar \"Finalizar la orden\" para establecer la orden como entregada.
												</p></blockquote>";
		$data_array['form_html']			.=	'<a href="'.site_url('cms/plugin_proceso_reclamo/pdf/'.$result_data->ID).'" target="_blank" class="btn btn-default"><span class="glyphicon glyphicon-print"></span> Imprimir</a>';
		
		else: //Si ya fue entregada
			$data_array['enable_action_btns']	= FALSE;
			if($result_data->PROCESS_APPROVED == 'NO'):
			$data_array['form_html']			.= '<div class="alert alert-danger" role="alert">
													<strong>Orden Denegada</strong> Esta orden no fue aceptada por el cliente el día '.mysql_date_to_dmy($result_data->PROCESS_FINISHED).'
													</div>';
			else:
			$data_array['form_html']			.= '<div class="alert alert-info" role="alert">
													<strong>Orden Finalizada</strong> El producto de esta orden ya fue entregado el día '.mysql_date_to_dmy($result_data->PROCESS_FINISHED).'
													</div>';
			endif;
		endif;
		
		return $data_array;
	}
	
	//Funciones de los posts a enviar
	public function post_new_val(){
		$submit_posts 					= $this->input->post();
		
		return $this->_set_new_val($submit_posts);
	}
	public function post_update_val($data_id){
		$submit_posts 					= $this->input->post();
		$submit_posts['ID']				= $data_id;
		$submit_posts['PROCESS_PASSCODE'] = md5(uniqid("R-".str_pad($data_id, 5, "0", STR_PAD_LEFT), true));
		
		//Si el proceso 
		if($this->input->post('PROCESS_STAGE') == 'REPARACION'):
			$submit_posts['ID']					= $data_id;
			$submit_posts['PROCESS_STAGE']		= 'ENTREGA';
			$submit_posts['PROCESS_FINISHED']	= date("Y-m-d");
			
		else:
			if ($this->form_validation->run('RECLAIM_PROCESS') != FALSE):
				$submit_posts['PROCESS_STAGE']	= 'APROBACION';
				$this->fw_posts->send_approval_process($this->input->post('RECLAIM_CLIENT_EMAIL'), $submit_posts['PROCESS_PASSCODE']);
			else:
				$this->fw_alerts->add_new_alert(3004, 'ERROR');
			endif;
		endif;

		return $this->_set_update_val($submit_posts);
	}
	
	/**
	 * Funciones específicas del plugin
	 */
	 //GENERAR PDF
	 public function pdf(){
	 	$this->load->library('autoprint');		
		
	 	//Iniciar el PDF
	 	$pdf = new Autoprint('P', 'mm', array(76, 100));
		$pdf->Open();
		$pdf->AddPage();
		$pdf->Text(90, 50, 'Print me!');
		
		
		//$pdf->AutoPrint(true);
		$pdf->Output();
	 }
}