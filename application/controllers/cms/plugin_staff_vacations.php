<?php
/**
 * @author 	Guido A. Orellana
 * @name	Plugin Per&iacute;odo de vacaciones
 * @since	abril 2016
 * 
 */
class Plugin_staff_vacations extends PL_Controller {
	
	function __construct(){
		parent::__construct();
		
		//Load the plugin data
		$this->plugin_action_table			= 'PLUGIN_SALESMAN_VACATIONS';
		$this->plugin_button_create			= "Crear Nuevo per&iacute;odo de vacaciones";
		$this->plugin_button_cancel			= "Cancelar";
		$this->plugin_button_update			= "Guardar Datos";
		$this->plugin_button_delete			= "Eliminar";
		$this->plugin_page_title			= "Per&iacute;odo de vacaciones";
		$this->plugin_page_create			= "Crear Nuevo Per&iacute;odo de vacaciones";
		$this->plugin_page_read				= "Mostrar Per&iacute;odo de vacaciones";
		$this->plugin_page_update			= "Editar Per&iacute;odo de vacaciones";
		$this->plugin_page_delete			= "Eliminar";
		
				
		$this->plugin_display_array[0]		= "ID";
		$this->plugin_display_array[1]		= "Empleado";
		$this->plugin_display_array[2]		= "D&iacute;a de descanso";
		$this->plugin_display_array[3]		= "Inicio";
		$this->plugin_display_array[4]		= "Finalizac&oacute;n";
		
		$this->plugins_model->initialise($this->plugin_action_table);
		$this->load->model('cms/cms_plugin_staff', 'plugin_staff');
		$this->load->model('cms/cms_plugin_vacations', 'plugin_staff_vacations');
		
		//Extras to send
		$this->display_pagination			= TRUE; //Mostrar paginaci�n en listado
		$this->pagination_per_page			= 10; //Numero de registros por p�gina
		$this->pagination_total_rows		= $this->plugin_staff->total_rows(); //N�mero total de items a desplegar
		
		$this->display_filter				= FALSE; //Mostrar filtro de b�squeda 'SEARCH' o seg�n listado 'LIST' o no mostrar FALSE
		
		//Obtener el profiler del plugin
		$this->output->enable_profiler(TRUE);
	}
	
	/**
	 * Funciones para editar Querys o Datos a enviar desde cada plugin
	 */
	//Funci�n para desplegar listado, desde aqu� se puede modificar el query
	public function _plugin_display($filterArray){
		
		$result_array = array();
		$result_array = $this->plugin_staff_vacations->list_vacations(NULL, NULL, '`PSV`.`VACATION_ENDDATE` DESC');
		
		
		return $this->_html_plugin_display($result_array);
	}
	
	/**
	 * Funci�n para desplegar listado completo de datos guardados, enviar los t�tulos en array con clave header y el cuerpo en un array con clave body.
	 * Para editar fila es a la funci�n 'update_table_row'
	 * 
	 * @param	$result_array 		array 		Array con la listado devuelto por query de la DB
	 * @return	$data_array 		array 		Arreglo con la informaci�n del header y body
	 */
	public function _html_plugin_display($result_array){
		
		//Header data
		$data_array['header'][1]			= $this->plugin_display_array[1];
		$data_array['header'][2]			= $this->plugin_display_array[3];
		$data_array['header'][3]			= $this->plugin_display_array[4];
		
		//Body data
		$data_array['body'] = '';
		foreach($result_array as $field):
		$data_array['body']					.= '<tr>';
		$data_array['body']					.= '<td><a href="'.base_url('cms/'.strtolower($this->current_plugin).'/update_table_row/'.$field->ID).'">'.$field->SALESMAN_NAME.' '.$field->SALESMAN_LASTNAME.'</a></td>';
		$data_array['body']					.= '<td>'.$field->VACATION_INITIALDATE.'</td>';
		$data_array['body']					.= '<td>'.$field->VACATION_ENDDATE.'</td>';
		$data_array['body']					.= '</tr>';
		endforeach;
		
		return $data_array;
	}
	
	/*
	 * Funci�n para crear nuevo contenido, desde aqu� se especifican los campos a enviar en el formulario.
	 * El formulario se env�a mediante objectos preestablecidos de codeigniter. 
	 * El formulario se env�a con un array con la clave form_html.
	 * Se puede encontrar una gu�a en: http://ellislab.com/codeigniter/user-guide/helpers/form_helper.html
	 */
	public function _html_plugin_create(){
		
		$employees					= $this->get_staff_list(); //Obtener los empleados activos.
		$diassemana					= $this->diassemana();
		
		//Formulario
		$data_array['form_html']	=  "<div class='form-group'>".form_label($this->plugin_display_array[1],'',array('class' => 'form-label col-lg-2'))."<div class='col-lg-10'>".form_dropdown('VACATIONS_EMPLOYEE', $employees,'', 'class="form-control"')."</div></div>";
		$data_array['form_html']	.= "<div class='form-group'>".form_label($this->plugin_display_array[2],'',array('class' => 'form-label col-lg-2'))."<div class='col-lg-10'>".form_dropdown('VACATION_RESTDAY', $diassemana,'', 'class="form-control"')."<p class='help-block'>D&iacute;a de la semana que el empleado tiene su descanso regular.</p></div></div>";
		$data_array['form_html']	.=  "<div class='form-group'>".form_label($this->plugin_display_array[3],'',array('class' => 'form-label col-lg-2'))."<div class='col-lg-10'>".form_input(array('name' => 'VACATION_INITIALDATE', 'class' => 'datetimepicker form-control', 'data-date-format' => 'YYYY-MM-DD'))."<p class='help-block'>Fecha que inici&oacute; vacaciones.</p></div></div>";
		$data_array['form_html']	.=  "<div class='form-group'>".form_label($this->plugin_display_array[4],'',array('class' => 'form-label col-lg-2'))."<div class='col-lg-10'>".form_input(array('name' => 'VACATION_ENDDATE', 'class' => 'datetimepicker form-control', 'data-date-format' => 'YYYY-MM-DD'))."<p class='help-block'>Fecha que finaliz&oacute; vacaciones.</p></div></div>";
		
		$data_array['extra_form']	= $this->datetimepicker(); //Llamar el script para escoger fechas.
		
		return $data_array;
    }
	public function _html_plugin_update($result_data){
		
		$employees					= $this->get_staff_list(); //Obtener los empleados activos.
		$diassemana					= $this->diassemana();
		
		//Formulario
		$data_array['form_html']	=  "<div class='form-group'>".form_label($this->plugin_display_array[1],'',array('class' => 'form-label col-lg-2'))."<div class='col-lg-10'>".form_dropdown('VACATIONS_EMPLOYEE', $employees, $result_data->VACATIONS_EMPLOYEE, 'class="form-control"')."</div></div>";
		$data_array['form_html']	.= "<div class='form-group'>".form_label($this->plugin_display_array[2],'',array('class' => 'form-label col-lg-2'))."<div class='col-lg-10'>".form_dropdown('VACATION_RESTDAY', $diassemana, $result_data->VACATION_RESTDAY, 'class="form-control"')."<p class='help-block'>D&iacute;a de la semana que el empleado tiene su descanso regular.</p></div></div>";
		$data_array['form_html']	.=  "<div class='form-group'>".form_label($this->plugin_display_array[3],'',array('class' => 'form-label col-lg-2'))."<div class='col-lg-10'>".form_input(array('name' => 'VACATION_INITIALDATE', 'class' => 'datetimepicker form-control', 'data-date-format' => 'YYYY-MM-DD', 'value' =>  $result_data->VACATION_INITIALDATE))."<p class='help-block'>Fecha que inici&oacute; vacaciones.</p></div></div>";
		$data_array['form_html']	.=  "<div class='form-group'>".form_label($this->plugin_display_array[4],'',array('class' => 'form-label col-lg-2'))."<div class='col-lg-10'>".form_input(array('name' => 'VACATION_ENDDATE', 'class' => 'datetimepicker form-control', 'data-date-format' => 'YYYY-MM-DD',  'value' =>  $result_data->VACATION_ENDDATE))."<p class='help-block'>Fecha que finaliz&oacute; vacaciones.</p></div></div>";
		
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
	 * Funciones espec�ficas del plugin
	 */
	private function get_staff_list(){
		$staff_list = $this->plugin_staff->list_rows('', 'SALESMAN_ENABLED = "SI"');
		
		foreach($staff_list as $id => $employee):
			$employees[$employee->ID] = $employee->SALESMAN_NAME.' '.$employee->SALESMAN_LASTNAME;
		endforeach;
		
		return $employees;
	}
	private function datetimepicker(){
		
		$jquery	= "<script type='text/javascript'>
					$(function () {
						$('.datetimepicker').datetimepicker({
							pickTime: false
						});
					});
				</script>";
		
		return $jquery;
		
	}
	private function diassemana(){
		$diassemana = array(
						'Monday' 	=> 'Lunes',
						'Tuesday' 	=> 'Martes',
						'Wednesday'	=> 'Mi&eacute;rcoles',
						'Thursday' 	=> 'Jueves',
						'Friday' 	=> 'Viernes',
						'Saturday' 	=> 'S&aacute;bado',
						'Sunday' 	=> 'Domingo'
		);
		
		return $diassemana;
	}
}