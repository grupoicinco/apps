<?php
/**
 * @author 	Guido A. Orellana
 * @name	Plugin Empleados
 * @since	abril 2016
 * 
 */
class Plugin_staff extends PL_Controller {
	
	function __construct(){
		parent::__construct();
		
		//Load the plugin data
		$this->plugin_action_table			= 'PLUGIN_SALESMAN';
		$this->plugin_button_create			= "Guardar Datos";
		$this->plugin_button_cancel			= "Cancelar";
		$this->plugin_button_update			= "Guardar Datos";
		$this->plugin_button_delete			= "Eliminar";
		$this->plugin_page_title			= "Empleados";
		$this->plugin_page_create			= "Crear Nuevo Empleados";
		$this->plugin_page_read				= "Mostrar Empleados";
		$this->plugin_page_update			= "Editar Empleados";
		$this->plugin_page_delete			= "Eliminar";
		
		$this->template_create				= "plugin_staff_create"; //Si no se describe, se pone como default "plugin_display"
		$this->template_update				= "plugin_staff_create"; //Si no se describe, se pone como default "plugin_display"
		
		$this->plugin_display_array[0]		= "ID";
		$this->plugin_display_array[1]		= "C&oacutedigo SAC";
		$this->plugin_display_array[2]		= "Nombre";
		$this->plugin_display_array[3]		= "Apellido";
		$this->plugin_display_array[4]		= "Correo";
		$this->plugin_display_array[5]		= "&iquest;Habilitado?";
		$this->plugin_display_array[6]		= "Posici&oacute;n";
		$this->plugin_display_array[7]		= "Salario Fijo";
		$this->plugin_display_array[8]		= "Inicio de labores";
		$this->plugin_display_array[9]		= "Jornada";
		$this->plugin_display_array[10]		= "ISR";
		$this->plugin_display_array[11]		= "&iquest;Inscrito al IGSS?";
		
		$this->plugins_model->initialise($this->plugin_action_table);
		$this->load->model('cms/cms_plugin_staff', 'plugin_staff');
		
		//Extras to send
		$this->display_pagination			= TRUE; //Mostrar paginación en listado
		$this->pagination_per_page			= 10; //Numero de registros por página
		$this->pagination_total_rows		= $this->plugin_staff->total_rows(); //Número total de items a desplegar
		
		$this->display_filter				= FALSE; //Mostrar filtro de búsqueda 'SEARCH' o según listado 'LIST' o no mostrar FALSE
		
		//Obtener el profiler del plugin
		$this->output->enable_profiler(FALSE);
	}
	
	/**
	 * Funciones para editar Querys o Datos a enviar desde cada plugin
	 */
	//Función para desplegar listado, desde aquí se puede modificar el query
	public function _plugin_display($filterArray){
		
		$result_array = array();
		$result_array = $this->plugin_staff->list_rows();
		
		
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
		$data_array['header'][1]			= "SAC ID";
		$data_array['header'][2]			= $this->plugin_display_array[2];
		$data_array['header'][3]			= $this->plugin_display_array[3];
		$data_array['header'][4]			= $this->plugin_display_array[9];
		$data_array['header'][5]			= $this->plugin_display_array[5];
		
		//Body data
		$data_array['body'] = '';
		foreach($result_array as $field):
		$data_array['body']					.= '<tr>';
		$data_array['body']					.= '<td><a href="'.base_url('cms/'.strtolower($this->current_plugin).'/update_table_row/'.$field->ID).'">'.str_pad($field->SALESMAN_SAC_CODE, 5, 0, STR_PAD_LEFT).'</a></td>';
		$data_array['body']					.= '<td>'.$field->SALESMAN_NAME.'</td>';
		$data_array['body']					.= '<td>'.$field->SALESMAN_LASTNAME.'</td>';
		$data_array['body']					.= '<td>'.$field->SALESMAN_WORKHOURS.'</td>';
		$data_array['body']					.= '<td>'.$field->SALESMAN_ENABLED.'</td>';
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
		
		$position			= $this->position_list();
		$jornada			= $this->employee_workhours();
		
		//Formulario
		$data_array['form_html']			= form_open_multipart('cms/'.strtolower($this->current_plugin).'/post_new_val', array('class' => 'form-horizontal col-lg-9', 'role' => 'form'));
		$data_array['form_html']			.= "<div class='form-group'>".form_label($this->plugin_display_array[1],'',array('class' => 'form-label col-lg-2'))."<div class='col-lg-10'>".form_input(array('name' => 'SALESMAN_SAC_CODE', 'class' => 'form-control'))."<p class='help-block'>C&oacute;digo del empleado en el sistema SAC</p></div></div>";
		$data_array['form_html']			.= "<div class='form-group'>".form_label($this->plugin_display_array[2],'',array('class' => 'form-label col-lg-2'))."<div class='col-lg-10'>".form_input(array('name' => 'SALESMAN_NAME', 'class' => 'form-control'))."</div></div>";
		$data_array['form_html']			.= "<div class='form-group'>".form_label($this->plugin_display_array[3],'',array('class' => 'form-label col-lg-2'))."<div class='col-lg-10'>".form_input(array('name' => 'SALESMAN_LASTNAME', 'class' => 'form-control'))."</div></div>";
		$data_array['form_html']			.= "<div class='form-group'>".form_label($this->plugin_display_array[4],'',array('class' => 'form-label col-lg-2'))."<div class='col-lg-10'>".form_input(array('name' => 'SALESMAN_EMAIL', 'class' => 'form-control'))."<p class='help-block'>Correo Electr&oacute;nico al que se enviar&aacute; el comprobante del pago de planilla</p></div></div>";
		$data_array['form_html']			.= "<div class='form-group'>".form_label($this->plugin_display_array[8],'',array('class' => 'form-label col-lg-2'))."<div class='col-lg-10'>".form_input(array('name' => 'SALESMAN_COMMENCEMENT', 'class' => 'datetimepicker form-control', 'data-date-format' => 'YYYY-MM-DD'))."<p class='help-block'>Fecha en que el empleado inici&oacute; labores.</p></div></div>";
		$data_array['form_html']			.= "<div class='form-group'>".form_label($this->plugin_display_array[6],'',array('class' => 'form-label col-lg-2'))."<div class='col-lg-10'>".form_dropdown('SALESMAN_POSITION', $position, array(), 'class="form-control"')."</div></div>";
		$data_array['form_html']			.= "<div class='form-group'>".form_label($this->plugin_display_array[9],'',array('class' => 'form-label col-lg-2'))."<div class='col-lg-10'>".form_dropdown('SALESMAN_WORKHOURS', $jornada, array(), 'class="form-control"')."</div></div>";
		$data_array['form_html']			.= "<div class='form-group'>".form_label($this->plugin_display_array[7],'',array('class' => 'form-label col-lg-2'))."<div class='col-lg-10'><div class='input-group'><div class='input-group-addon'>Q.</div>".form_input(array('name' => 'SALESMAN_SALARY', 'class' => 'form-control', 'type' => 'number'))."</div><p class='help-block'>Salario mensual fijo, sin contar comisiones, del empleado.</p></div></div>";
		$data_array['form_html']			.= "<div class='form-group'>".form_label($this->plugin_display_array[10],'',array('class' => 'form-label col-lg-2'))."<div class='col-lg-10'><div class='input-group'><div class='input-group-addon'>Q.</div>".form_input(array('name' => 'SALESMAN_PROFITTAX', 'class' => 'form-control', 'type' => 'number', 'step' => '0.01'))."</div><p class='help-block'>Monto a descontar al empleado en Impuesto Sobre la Renta.</p></div></div>";
		$data_array['form_html']			.= "<div class='form-group'>".form_label($this->plugin_display_array[11],'',array('class' => 'form-label col-lg-2'))."<div class='col-lg-10'>".form_dropdown('SALESMAN_IGSSINSCRIPTION', array('SI' => 'SI', 'NO' => 'NO'), array(),'class="form-control"')."<p class='help-block'>&iquest;El empleado est&aacute; inscrito al IGSS y debitarle el monto de IGSS laboral?.</p></div></div>";
		$data_array['form_html']			.= "<div class='form-group'>".form_label($this->plugin_display_array[5],'',array('class' => 'form-label col-lg-2'))."<div class='col-lg-10'>".form_dropdown('SALESMAN_ENABLED', array('SI' => 'SI', 'NO' => 'NO'), array('SI' => 'SI'),'class="form-control"')."<p class='help-block'>Habilitar o Deshabilitar al empleado, en caso deje de laborar en la empresa.</p></div></div>";
		
		return $data_array;
    }
	public function _html_plugin_update($result_data){
		
		$position			= $this->position_list();
		$jornada			= $this->employee_workhours();
		
		//Formulario
		$data_array['form_html']			=  form_open_multipart('cms/'.strtolower($this->current_plugin).'/post_update_val/'.$result_data->ID, array('class' => 'form-horizontal', 'role' => 'form'));
		$data_array['form_html']			.= form_hidden('POST_ID', $result_data->ID);
		$data_array['form_html']			.= "<div class='form-group'>".form_label($this->plugin_display_array[1],'',array('class' => 'form-label col-lg-2'))."<div class='col-lg-10'>".form_input(array('name' => 'SALESMAN_SAC_CODE','value' => $result_data->SALESMAN_SAC_CODE, 'class' => 'form-control'))."<p class='help-block'>C&oacute;digo del empleado en el sistema SAC</p></div></div>";
		$data_array['form_html']			.= "<div class='form-group'>".form_label($this->plugin_display_array[2],'',array('class' => 'form-label col-lg-2'))."<div class='col-lg-10'>".form_input(array('name' => 'SALESMAN_NAME','value' => $result_data->SALESMAN_NAME, 'class' => 'form-control'))."</div></div>";
		$data_array['form_html']			.= "<div class='form-group'>".form_label($this->plugin_display_array[3],'',array('class' => 'form-label col-lg-2'))."<div class='col-lg-10'>".form_input(array('name' => 'SALESMAN_LASTNAME','value' => $result_data->SALESMAN_LASTNAME, 'class' => 'form-control'))."</div></div>";
		$data_array['form_html']			.= "<div class='form-group'>".form_label($this->plugin_display_array[4],'',array('class' => 'form-label col-lg-2'))."<div class='col-lg-10'>".form_input(array('name' => 'SALESMAN_EMAIL','value' => $result_data->SALESMAN_EMAIL, 'class' => 'form-control'))."<p class='help-block'>Correo Electr&oacute;nico al que se enviar&aacute; el comprobante del pago de planilla</p></div></div>";
		$data_array['form_html']			.= "<div class='form-group'>".form_label($this->plugin_display_array[8],'',array('class' => 'form-label col-lg-2'))."<div class='col-lg-10'>".form_input(array('name' => 'SALESMAN_COMMENCEMENT', 'value' => $result_data->SALESMAN_COMMENCEMENT, 'class' => 'datetimepicker form-control', 'data-date-format' => 'YYYY-MM-DD'))."<p class='help-block'>Fecha en que el empleado inici&oacute; labores.</p></div></div>";
		$data_array['form_html']			.= "<div class='form-group'>".form_label($this->plugin_display_array[6],'',array('class' => 'form-label col-lg-2'))."<div class='col-lg-10'>".form_dropdown('SALESMAN_POSITION', $position, array($result_data->SALESMAN_POSITION), 'class="form-control"')."</div></div>";
		$data_array['form_html']			.= "<div class='form-group'>".form_label($this->plugin_display_array[9],'',array('class' => 'form-label col-lg-2'))."<div class='col-lg-10'>".form_dropdown('SALESMAN_WORKHOURS', $jornada, array($result_data->SALESMAN_WORKHOURS), 'class="form-control"')."</div></div>";
		$data_array['form_html']			.= "<div class='form-group'>".form_label($this->plugin_display_array[7],'',array('class' => 'form-label col-lg-2'))."<div class='col-lg-10'><div class='input-group'><div class='input-group-addon'>Q.</div>".form_input(array('name' => 'SALESMAN_SALARY','value' => $result_data->SALESMAN_SALARY, 'class' => 'form-control', 'type' => 'number'))."</div><p class='help-block'>Salario mensual fijo, sin contar comisiones, del empleado.</p></div></div>";
		$data_array['form_html']			.= "<div class='form-group'>".form_label($this->plugin_display_array[10],'',array('class' => 'form-label col-lg-2'))."<div class='col-lg-10'><div class='input-group'><div class='input-group-addon'>Q.</div>".form_input(array('name' => 'SALESMAN_PROFITTAX','value' => $result_data->SALESMAN_PROFITTAX, 'class' => 'form-control', 'type' => 'text'))."</div><p class='help-block'>Monto a descontar al empleado en Impuesto Sobre la Renta.</p></div></div>";
		$data_array['form_html']			.= "<div class='form-group'>".form_label($this->plugin_display_array[11],'',array('class' => 'form-label col-lg-2'))."<div class='col-lg-10'>".form_dropdown('SALESMAN_IGSSINSCRIPTION', array('SI' => 'SI', 'NO' => 'NO'), array($result_data->SALESMAN_IGSSINSCRIPTION => $result_data->SALESMAN_IGSSINSCRIPTION),'class="form-control"')."<p class='help-block'>&iquest;El empleado est&aacute; inscrito al IGSS y debitarle el monto de IGSS laboral?.</p></div></div>";
		$data_array['form_html']			.= "<div class='form-group'>".form_label($this->plugin_display_array[5],'',array('class' => 'form-label col-lg-2'))."<div class='col-lg-10'>".form_dropdown('SALESMAN_ENABLED', array('SI' => 'SI', 'NO' => 'NO'), array($result_data->SALESMAN_ENABLED => $result_data->SALESMAN_ENABLED),'class="form-control" id="SALESMAN_ENABLED"')."<p class='help-block'>Habilitar o Deshabilitar al empleado, en caso deje de laborar en la empresa. <strong class='text-danger'>Nota:</strong> al habilitar un empleado deshabilitado, se añadirá como fecha de inicio de labores la actual.</p></div></div>";
		
		$infomodal							= $this->load->view('cms/javascript_staffdata.php', '', TRUE);
		$data_array['form_html']			.= $infomodal;
		return $data_array;
	}
	
	//Funciones de los posts a enviar
	public function post_new_val(){
		$submit_posts 							= $this->input->post();
		$submit_posts['SALESMAN_PROFITTAX']		= ($submit_posts['SALESMAN_PROFITTAX'] < 0)?$submit_posts['SALESMAN_PROFITTAX']:($submit_posts['SALESMAN_PROFITTAX'] * -1);
		
		return $this->_set_new_val($submit_posts);
	}
	public function post_update_val($data_id){
		$submit_posts 							= $this->input->post();
		$submit_posts['SALESMAN_PROFITTAX'] 	= ($submit_posts['SALESMAN_PROFITTAX'] < 0)?$submit_posts['SALESMAN_PROFITTAX']:($submit_posts['SALESMAN_PROFITTAX'] * -1);
		
		//Cambiar fecha de inicio de labores en caso haya sido dado de baja el empleado
		$data['COLUMN_VAR']						= $data_id;
		$data['COLUMN_SELECT']					= 'SALESMAN_ENABLED';
		$enabled								= $this->plugin_staff->get_single_row($data); //Obtener el estado del empleado.
		$submit_posts['SALESMAN_COMMENCEMENT'] 	= ($enabled->SALESMAN_ENABLED == 'NO' && $submit_posts['SALESMAN_ENABLED'] == 'SI')?date('Y-m-d'):$submit_posts['SALESMAN_COMMENCEMENT'];

		return $this->_set_update_val($submit_posts);
	}
	
	/**
	 * Funciones específicas del plugin
	 */
	
	/**
	 * Tipos de jornada laboral
	 */
	 private function employee_workhours(){
	 	$workhours	= array(
	 					'DIURNA' 	=> 'Diurna (06:00 - 18:00 hrs)',
	 					'MIXTA' 	=> 'Mixta (14:00 - 22:00 hrs)'
	 					);
		return $workhours;
	 }
	 private function position_list(){
	 	return array(
					'ASESOR' => 'Asesor',
					'ENCARGADO' => 'Encargado',
					'ADMINISTRACION' => 'Administraci&oacute;n',
					'MARKETING' => 'Marketing'
					);
	 }
}
