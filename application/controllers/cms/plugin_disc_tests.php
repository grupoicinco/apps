<?php
/**
 * @author 	Guido A. Orellana
 * @name	Plugin Pruebas DISC
 * @since	septiembre 2016
 * 
 */
class Plugin_disc_tests extends PL_Controller {
	
	function __construct(){
		parent::__construct();
		
		//Load the plugin data
		$this->plugin_action_table			= 'PLUGIN_DISC_TESTS';
		$this->plugin_button_create			= NULL;
		$this->plugin_button_cancel			= "Cancelar";
		$this->plugin_button_update			= "Guardar Datos";
		$this->plugin_button_delete			= "Eliminar";
		$this->plugin_page_title			= "Pruebas DISC";
		$this->plugin_page_create			= "Crear Nueva Prueba";
		$this->plugin_page_read				= "Mostrar Pruebas";
		$this->plugin_page_update			= "Editar Pruebas";
		$this->plugin_page_delete			= "Eliminar";
		
//		$this->template_display				= "plugin_display_disc"; //Si no se describe, se pone como default "plugin_display"
				
		$this->plugin_display_array[0]		= "ID";
		$this->plugin_display_array[1]		= "Nombre";
		$this->plugin_display_array[2]		= "Apellido";
		$this->plugin_display_array[3]		= "Realizada";
		
		$this->plugins_model->initialise($this->plugin_action_table);
		$this->load->model('plugins/rrhh_model', 'rrhh_model');
		
		//Extras to send
		$this->display_pagination			= TRUE; //Mostrar paginación en listado
		$this->pagination_per_page			= 10; //Numero de registros por página
		$this->pagination_total_rows		= $this->rrhh_model->total_rows(); //Número total de items a desplegar
		
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
		$result_array = $this->rrhh_model->list_disc_tests();
		
		
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
			
		$field->TEST_MASANSWERS				= (!empty($field->TEST_MASANSWERS))?json_decode($field->TEST_MASANSWERS, TRUE):array();
		$field->TEST_MENOSANSWERS			= (!empty($field->TEST_MENOSANSWERS))?json_decode($field->TEST_MENOSANSWERS, TRUE):array();
		$pdfreport							= (count($field->TEST_MASANSWERS) > 23 && count($field->TEST_MENOSANSWERS) > 23)?'<a target="_blank" href="'.base_url('recursos_humanos/disc_result/'.$field->TEST_PASSCODE.'/'.$field->ID.'/').'">SI</a>':'NO';
		
		$data_array['body']					.= '<tr>';
		$data_array['body']					.= '<td>'.$field->NAME.'</td>';
		$data_array['body']					.= '<td>'.$field->LASTNAME.'</td>';
		$data_array['body']					.= '<td>'.$pdfreport.'</td>';
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
		return FALSE;
    }
	public function _html_plugin_update(){
		return FALSE;
	}
	
	//Funciones de los posts a enviar
	public function post_new_val(){
		return FALSE;
	}
	public function post_update_val(){
		return FALSE;
	}
	
	/**
	 * Funciones específicas del plugin
	 */
}
