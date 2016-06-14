<?php
/**
 * @author 	Guido A. Orellana
 * @name	Plugin Comisiones
 * @since	abril 2016
 * 
 */
class Plugin_commissions extends PL_Controller {
	
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
		$this->plugin_display_array[4]		= "Bono decreto extra";
		
		$this->plugins_model->initialise($this->plugin_action_table);
		$this->load->model('cms/cms_plugin_commissions', 'plugin_commissions');
		
		//Extras to send
		$this->display_pagination			= FALSE; //Mostrar paginación en listado
		$this->pagination_per_page			= 10; //Numero de registros por página
		$this->pagination_total_rows		= $this->plugin_commissions->total_rows(); //Número total de items a desplegar
		
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
		$result_array = $this->plugin_commissions->list_rows('','',NULL,NULL,'SALESMAN_POSITION, COMMISSION_GOAL');
		
		
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
		$data_array['header'][4]			= $this->plugin_display_array[4];
		$data_array['header'][3]			= $this->plugin_display_array[3];
		
		//Body data
		$data_array['body'] = '';
		foreach($result_array as $field):
		$data_array['body']					.= '<tr>';
		$data_array['body']					.= '<td><a href="'.base_url('cms/'.strtolower($this->current_plugin).'/update_table_row/'.$field->ID).'">'.($field->COMMISSION_GOAL*100).'%</a></td>';
		$data_array['body']					.= '<td>'.($field->COMMISSION_VALUE * 100).'%</td>';
		$data_array['body']					.= '<td>'.($field->COMMISSION_BONUS).'</td>';
		$data_array['body']					.= '<td>'.$field->SALESMAN_POSITION.'</td>';
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
		$data_array['form_html']			=  "<div class='form-group'>".form_label($this->plugin_display_array[1],'',array('class' => 'form-label col-lg-2'))."<div class='col-lg-10'>".form_input(array('name' => 'COMMISSION_GOAL', 'type' => 'number', 'step' => '0.00001', 'min' => '0', 'class' => 'form-control'))."<p class='help-block'>Meta a alcanzar en decimales, no en porcentaje.</p></div></div>";
		$data_array['form_html']			.=  "<div class='form-group'>".form_label($this->plugin_display_array[2],'',array('class' => 'form-label col-lg-2'))."<div class='col-lg-10'>".form_input(array('name' => 'COMMISSION_VALUE', 'type' => 'number','step' => '0.00001', 'min' => '0', 'class' => 'form-control'))."<p class='help-block'>Comisi&oacute;n en decimales, no en porcentaje.</p></div></div>";
		$data_array['form_html']			.=  "<div class='form-group'>".form_label($this->plugin_display_array[4],'',array('class' => 'form-label col-lg-2'))."<div class='col-lg-10'><div class='input-group'><span class='input-group-addon'>Q.</span>".form_input(array('name' => 'COMMISSION_BONUS', 'type' => 'number','step' => '0.00001', 'min' => '0', 'class' => 'form-control'))."</div><p class='help-block'>Bono extra en quetzales, al alcanzar % de ventas.</p></div></div>";
		$data_array['form_html']			.= "<div class='form-group'>".form_label($this->plugin_display_array[3],'',array('class' => 'form-label col-lg-2'))."<div class='col-lg-10'>".form_dropdown('SALESMAN_POSITION', array('ASESOR' => 'Asesor de Ventas', 'ENCARGADO' => 'Encargado de ventas', 'ADMINISTRACION' => 'Administraci&oacute;n', 'MARKETING' => 'Marketing'),'', 'class="form-control"')."</div></div>";
		
		return $data_array;
    }
	public function _html_plugin_update($result_data){
		
		//Formulario
		$data_array['form_html']			=  "<div class='form-group'>".form_label($this->plugin_display_array[1],'',array('class' => 'form-label col-lg-2'))."<div class='col-lg-10'>".form_input(array('name' => 'COMMISSION_GOAL', 'value' => $result_data->COMMISSION_GOAL,'type' => 'number', 'step' => '0.00001', 'min' => '0', 'class' => 'form-control'))."<p class='help-block'>Meta a alcanzar en decimales, no en porcentaje.</p></div></div>";
		$data_array['form_html']			.=  "<div class='form-group'>".form_label($this->plugin_display_array[2],'',array('class' => 'form-label col-lg-2'))."<div class='col-lg-10'>".form_input(array('name' => 'COMMISSION_VALUE', 'value' => $result_data->COMMISSION_VALUE, 'type' => 'number','step' => '0.00001', 'min' => '0', 'class' => 'form-control'))."<p class='help-block'>Comisi&oacute;n en decimales, no en porcentaje.</p></div></div>";
		$data_array['form_html']			.=  "<div class='form-group'>".form_label($this->plugin_display_array[4],'',array('class' => 'form-label col-lg-2'))."<div class='col-lg-10'><div class='input-group'><span class='input-group-addon'>Q.</span>".form_input(array('name' => 'COMMISSION_BONUS', 'value' => $result_data->COMMISSION_BONUS, 'type' => 'number','step' => '0.00001', 'min' => '0', 'class' => 'form-control'))."</div><p class='help-block'>Bono extra en quetzales, al alcanzar % de ventas.</p></div></div>";
		$data_array['form_html']			.= "<div class='form-group'>".form_label($this->plugin_display_array[3],'',array('class' => 'form-label col-lg-2'))."<div class='col-lg-10'>".form_dropdown('SALESMAN_POSITION', array('ASESOR' => 'Asesor de Ventas', 'ENCARGADO' => 'Encargado de ventas', 'ADMINISTRACION' => 'Administraci&oacute;n', 'MARKETING' => 'Marketing'), array($result_data->SALESMAN_POSITION), 'class="form-control"')."</div></div>";
		
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
