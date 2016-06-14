<?php
/**
 * @author 	Guido A. Orellana
 * @name	Plugin Comisiones
 * @since	abril 2016
 * 
 */
class Plugin_staff extends PL_Controller {
	
	function __construct(){
		parent::__construct();
		
		//Load the plugin data
		$this->plugin_action_table			= 'PLUGIN_SALESMAN_COMMISSIONS';
		$this->plugin_button_create			= "Crear Nueva Comisi&oacute;n";
		$this->plugin_button_cancel			= "Cancelar";
		$this->plugin_button_update			= "Guardar Cambios";
		$this->plugin_button_delete			= "Eliminar";
		$this->plugin_page_title			= "Comisiones";
		$this->plugin_page_create			= "Crear Nueva Comisi&oacute;ns";
		$this->plugin_page_read				= "Mostrar Comisiones";
		$this->plugin_page_update			= "Editar Comisiones";
		$this->plugin_page_delete			= "Eliminar";
		
		//$this->template_display				= "plugin_staff"; //Si no se describe, se pone como default "plugin_display"
		
		$this->plugin_display_array[0]		= "ID";
		$this->plugin_display_array[1]		= "% meta";
		$this->plugin_display_array[2]		= "Comisi&oacute;n";
		$this->plugin_display_array[3]		= "Posici&oacute;n para la comisi&oacute;n";
		
		$this->plugins_model->initialise($this->plugin_action_table);
		$this->load->model('cms/cms_plugin_commissions', 'plugin_commission');
		
		//Extras to send
		$this->display_pagination			= FALSE; //Mostrar paginación en listado
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
		$data_array['header'][1]			= $this->plugin_display_array[1];
		$data_array['header'][2]			= $this->plugin_display_array[2];
		$data_array['header'][3]			= $this->plugin_display_array[3];
		
		//Body data
		$data_array['body'] = '';
		foreach($result_array as $field):
		$data_array['body']					.= '<tr>';
		$data_array['body']					.= '<td><a href="'.base_url('cms/'.strtolower($this->current_plugin).'/update_table_row/'.$field->ID).'">'.($field->COMMISSION_GOAL*100).'%</a></td>';
		$data_array['body']					.= '<td>'.($field->COMMISSION_VALUE * 100).'%</td>';
		$data_array['body']					.= '<td>'.$field->COMMISSION_POSITION.'</td>';
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
		
		//Formulario
		$data_array['form_html']			=  "<div class='form-group'>".form_label($this->plugin_display_array[1],'',array('class' => 'form-label col-lg-2'))."<div class='col-lg-10'>".form_input(array('name' => 'SALESMAN_SAC_CODE', 'class' => 'form-control'))."<p class='help-block'>C&oacute;digo del empleado en el sistema SAC</p></div></div>";
		$data_array['form_html']			.= "<div class='form-group'>".form_label($this->plugin_display_array[2],'',array('class' => 'form-label col-lg-2'))."<div class='col-lg-10'>".form_input(array('name' => 'SALESMAN_NAME', 'class' => 'form-control'))."</div></div>";
		$data_array['form_html']			.= "<div class='form-group'>".form_label($this->plugin_display_array[3],'',array('class' => 'form-label col-lg-2'))."<div class='col-lg-10'>".form_input(array('name' => 'SALESMAN_LASTNAME', 'class' => 'form-control'))."</div></div>";
		$data_array['form_html']			.= "<div class='form-group'>".form_label($this->plugin_display_array[4],'',array('class' => 'form-label col-lg-2'))."<div class='col-lg-10'>".form_input(array('name' => 'SALESMAN_EMAIL', 'class' => 'form-control'))."<p class='help-block'>Correo Electr&oacute;nico al que se enviar&aacute; el comprobante del pago de planilla</p></div></div>";
		$data_array['form_html']			.= "<div class='form-group'>".form_label($this->plugin_display_array[6],'',array('class' => 'form-label col-lg-2'))."<div class='col-lg-10'>".form_dropdown('SALESMAN_POSITION', array('ASESOR' => 'Asesor de Ventas', 'ENCARGADO' => 'Encargado de ventas', 'ADMINISTRACION' => 'Administrativo'), array('SI' => 'SI'))."</div></div>";
		$data_array['form_html']			.= "<div class='form-group'>".form_label($this->plugin_display_array[7],'',array('class' => 'form-label col-lg-2'))."<div class='col-lg-10'><div class='input-group'><div class='input-group-addon'>Q.</div>".form_input(array('name' => 'SALESMAN_SALARY', 'class' => 'form-control', 'type' => 'number'))."</div><p class='help-block'>Salario mensual fijo, sin contar comisiones, del empleado.</p></div></div>";
		$data_array['form_html']			.= "<div class='form-group'>".form_label($this->plugin_display_array[5],'',array('class' => 'form-label col-lg-2'))."<div class='col-lg-10'>".form_dropdown('SALESMAN_ENABLED', array('SI' => 'SI', 'NO' => 'NO'), array('SI' => 'SI'))."<p class='help-block'>Habilitar o Deshabilitar al empleado, en caso deje de laborar en la empresa.</p></div></div>";
		
		return $data_array;
    }
	public function _html_plugin_update($result_data){
		
		//Formulario
		$data_array['form_html']			=  "<div class='form-group'>".form_label($this->plugin_display_array[1],'',array('class' => 'form-label col-lg-2'))."<div class='col-lg-10'>".form_input(array('name' => 'SALESMAN_SAC_CODE','value' => $result_data->SALESMAN_SAC_CODE, 'class' => 'form-control'))."<p class='help-block'>C&oacute;digo del empleado en el sistema SAC</p></div></div>";
		$data_array['form_html']			.= "<div class='form-group'>".form_label($this->plugin_display_array[2],'',array('class' => 'form-label col-lg-2'))."<div class='col-lg-10'>".form_input(array('name' => 'SALESMAN_NAME','value' => $result_data->SALESMAN_NAME, 'class' => 'form-control'))."</div></div>";
		$data_array['form_html']			.= "<div class='form-group'>".form_label($this->plugin_display_array[3],'',array('class' => 'form-label col-lg-2'))."<div class='col-lg-10'>".form_input(array('name' => 'SALESMAN_LASTNAME','value' => $result_data->SALESMAN_LASTNAME, 'class' => 'form-control'))."</div></div>";
		$data_array['form_html']			.= "<div class='form-group'>".form_label($this->plugin_display_array[4],'',array('class' => 'form-label col-lg-2'))."<div class='col-lg-10'>".form_input(array('name' => 'SALESMAN_EMAIL','value' => $result_data->SALESMAN_EMAIL, 'class' => 'form-control'))."<p class='help-block'>Correo Electr&oacute;nico al que se enviar&aacute; el comprobante del pago de planilla</p></div></div>";
		$data_array['form_html']			.= "<div class='form-group'>".form_label($this->plugin_display_array[6],'',array('class' => 'form-label col-lg-2'))."<div class='col-lg-10'>".form_dropdown('SALESMAN_POSITION', array('ASESOR' => 'Asesor de Ventas', 'ENCARGADO' => 'Encargado de ventas', 'ADMINISTRACION' => 'Administrativo'), array($result_data->SALESMAN_POSITION))."</div></div>";
		$data_array['form_html']			.= "<div class='form-group'>".form_label($this->plugin_display_array[7],'',array('class' => 'form-label col-lg-2'))."<div class='col-lg-10'><div class='input-group'><div class='input-group-addon'>Q.</div>".form_input(array('name' => 'SALESMAN_SALARY','value' => $result_data->SALESMAN_SALARY, 'class' => 'form-control', 'type' => 'number'))."</div><p class='help-block'>Salario mensual fijo, sin contar comisiones, del empleado.</p></div></div>";
		$data_array['form_html']			.= "<div class='form-group'>".form_label($this->plugin_display_array[5],'',array('class' => 'form-label col-lg-2'))."<div class='col-lg-10'>".form_dropdown('SALESMAN_ENABLED', array('SI' => 'SI', 'NO' => 'NO'), array($result_data->SALESMAN_ENABLED => $result_data->SALESMAN_ENABLED))."<p class='help-block'>Habilitar o Deshabilitar al empleado, en caso deje de laborar en la empresa.</p></div></div>";
		
		return $data_array;
	}
	
	//Funciones de los posts a enviar
	public function post_new_val(){
		$submit_posts 					= $this->input->post();
		
		return $this->_set_new_val($submit_posts);
	}
	public function post_update_val($data_id){
		$submit_posts 					= $this->input->post();
		
		return $this->_set_update_val($submit_posts);
	}
	
	/**
	 * Funciones específicas del plugin
	 */
	
}
