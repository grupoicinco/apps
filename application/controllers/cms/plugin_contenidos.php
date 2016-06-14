<?php
/**
 * Plugin para administrar los contenidos editables del sitio
 * @author 	Guido A. Orellana
 * @name	Plugin_contenidos
 * @since	abril 2013
 * 
 */
class Plugin_contenidos extends PL_Controller {
	
	function __construct(){
		parent::__construct();
		
		//Load the plugin data
		$this->plugin_action_table			= 'FRAMEWORK_POSTS';
		$this->plugin_button_create			= "Crear Nuevo Contenido";
		$this->plugin_button_cancel			= "Cancelar";
		$this->plugin_button_update			= "Guardar Cambios";
		$this->plugin_button_delete			= "Eliminar";
		$this->plugin_page_title			= "Contenidos";
		$this->plugin_page_create			= "Crear Nuevo Contenido";
		$this->plugin_page_read				= "Mostrar Contenido";
		$this->plugin_page_update			= "Editar Contenido";
		$this->plugin_page_delete			= "Eliminar";
		
		$this->template_display				= "posts_display";
		
		$this->plugin_display_array[0]		= "ID";
		$this->plugin_display_array[1]		= "T&iacute;tulo";
		$this->plugin_display_array[2]		= "Contenidos";
		$this->plugin_display_array[3]		= "Clave";
		$this->plugin_display_array[4]		= "P&aacute;gina";
		
		$this->display_pagination			= FALSE;
		
		$this->plugins_model->initialise($this->plugin_action_table);
	}
	
	/**
	 * Función para desplegar listado completo de datos guardados, enviar los títulos en array con clave header y el cuerpo en un array dentro de otro con clave body
	 * 
	 * @param	$result_array 		array 		Array con la listado devuelto por query de la DB
	 * @return	$data_array 		array 		Arreglo con la información del header y body
	 */
	public function _html_plugin_display($result_array){
		
		//Datos fijos
		$data_array['page_title'] 			= $this->plugin_page_title;
		$data_array['header'][0]			= $this->plugin_display_array[0];
		
		//Header data
		$data_array['header'][1]			= $this->plugin_display_array[1];
		$data_array['header'][2]			= $this->plugin_display_array[2];
		$data_array['header'][3]			= $this->plugin_display_array[3];
		
		//Body data
		if(!empty($result_array)):
		foreach($result_array as $field):
		
		//Añade las columnas de los datos a desplegar
		$data_array['body'][0][]			= $field->ID;
		$data_array['body'][1][]			= $field->POST_TITLE;
		$data_array['body'][2][]			= $field->POST_DETAIL;
		$data_array['body'][3][]			= $field->POST_KEY;
		$data_array['body'][4][]			= $field->POST_PAGE;
		
		endforeach;
		
		foreach($data_array['body'][4] as $post_page):
		$data_array['pages'][]				= $post_page;
		endforeach;
		$data_array['pages']				= array_unique($data_array['pages']);
		$data_array['pages']				= $this->_post_page_title($data_array['pages']);
		endif;
		
		return $data_array;
	}
	
	/*
	 * Función para crear nuevo contenido, desde aquí se especifican los campos a enviar en el formulario.
	 * El formulario se envía mediante objectos preestablecidos de codeigniter. 
	 * El formulario se envía con un array con la clave form_html.
	 * Para enviar un formulario extra se agrega en el array la clave extra_form.
	 * Se puede encontrar una guía en: http://ellislab.com/codeigniter/user-guide/helpers/form_helper.html
	 */
	public function _html_plugin_create(){
        
		//Datos fijos
		$options_values_array 				= $this->_get_framework_pages();
		
		//Formulario
		$data_array['form_html']			= "<div class='form-group'>".form_label($this->plugin_display_array[1],'',array('class' => 'control-label col-lg-2'))."<div class='col-lg-10'>".form_input(array('name' => 'POST_TITLE', 'class' => 'form-control'))."</div></div>";
		$data_array['form_html']			.= "<div class='form-group'>".form_label($this->plugin_display_array[3],'',array('class' => 'control-label col-lg-2'))."<div class='col-lg-10'>".form_input(array('name' => 'POST_KEY', 'class' => 'form-control'))."</div></div>";
		$data_array['form_html']			.= "<div class='form-group'>".form_label($this->plugin_display_array[4],'',array('class' => 'control-label col-lg-2'))."<div class='col-lg-10'>".form_dropdown('POST_PAGE', $options_values_array, NULL, 'class="form-control"')."</div></div>";
		$data_array['form_html']			.= "<div class='form-group'>".form_label($this->plugin_display_array[2],'',array('class' => 'control-label col-lg-2'))."<div class='col-lg-10'>".form_textarea(array('name' => 'POST_DETAIL', 'class' => 'form-control textarea'))."<span class='help-block'>*No pegar texto directamente desde word</span></div></div>";
		
		
		return $data_array;
    }
	public function _html_plugin_update($result_data){
		
		//Datos fijos
		$options_values_array 				= $this->_get_framework_pages();
		
		
		//Formulario
		$data_array['form_html']			= "<div class='form-group'>".form_label($this->plugin_display_array[1],'',array('class' => 'control-label col-lg-2'))."<div class='col-lg-10'>".form_input(array('name' => 'POST_TITLE', 'value' => $result_data->POST_TITLE, 'class' => 'form-control'))."</div></div>";
		$data_array['form_html']			.= "<div class='form-group'>".form_label($this->plugin_display_array[3],'',array('class' => 'control-label col-lg-2'))."<div class='col-lg-10'>".form_input(array('name' => 'POST_KEY', 'value' => $result_data->POST_KEY, 'class' => 'form-control'))."</div></div>";
		$data_array['form_html']			.= "<div class='form-group'>".form_label($this->plugin_display_array[4],'',array('class' => 'control-label col-lg-2'))."<div class='col-lg-10'>".form_dropdown('POST_PAGE', $options_values_array, $result_data->POST_PAGE, 'class="form-control"')."</div></div>";
		$data_array['form_html']			.= "<div class='form-group'>".form_label($this->plugin_display_array[2],'',array('class' => 'control-label col-lg-2'))."<div class='col-lg-10'>".form_textarea(array('name' => 'POST_DETAIL', 'value' => $result_data->POST_DETAIL, 'class' => 'form-control textarea'))."<span class='help-block'>*No pegar texto directamente desde word</span></div></div>";
		
		
		return $data_array;
	}
	
	/**
	 * Funciones para editar Querys o Datos a enviar desde cada plugin
	 */
	//Función para desplegar listado, desde aquí se puede modificar el query
	public function _plugin_display(){
		$result_array = array();
		$result_array = $this->plugins_model->display_result();
		return $this->_html_plugin_display($result_array);
	}
	//Funciones de los posts a enviar
	public function post_new_val(){
		$submit_posts 				= $this->input->post();
		$submit_posts['POST_KEY']	= strtolower(str_replace(' ', '_', convert_accented_characters($this->input->post('POST_KEY')))); //Enviar sin tildes ni espaciados
		
		return $this->_set_new_val($submit_posts);
	}
	public function post_update_val($data_id){
		$submit_posts 				= $this->input->post();
		$submit_posts['POST_KEY']	= strtolower(str_replace(' ', '_', convert_accented_characters($this->input->post('POST_KEY')))); //Enviar sin tildes ni espaciados
		
		return $this->_set_update_val($submit_posts);
	}
	
	/**
	 * Funciones específicas del plugin
	 */
	private function _post_page_title($post_pages){
		foreach($post_pages as $i => $page):
			$requireArray['COLUMN_VAR']		= $page;
			$requireArray['TABLE']			= 'FRAMEWORK_PAGES';
			$result_array 					= $this->plugins_model->get_single_row($requireArray);
			$page_result[$i]['ID'] 			= $page;
			$page_result[$i]['PAGE']		= $result_array->LABEL;
		endforeach;
		
		return $page_result;
	}
	private function _get_framework_pages(){
		$result_array = $this->plugins_model->list_rows('ID, LABEL', '', NULL, NULL, '', 'FRAMEWORK_PAGES');
		$option_values_array = array();
		foreach($result_array as $page):
			$option_values_array[$page->ID] = $page->LABEL;
		endforeach;
		
		return $option_values_array;
	}
}
