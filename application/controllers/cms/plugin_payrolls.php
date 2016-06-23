<?php
/**
 * @author 	Guido A. Orellana
 * @name	Plugin Planillas
 * @since	abril 2016
 * 
 */
class Plugin_payrolls extends PL_Controller {
	
	function __construct(){
		parent::__construct();
		
		//Load the plugin data
		$this->plugin_action_table			= 'PLUGIN_SALESMAN_PAYROLL';
		$this->plugin_button_create			= "Crear Nueva Planilla";
		$this->plugin_button_cancel			= "Cancelar";
		$this->plugin_button_update			= "Guardar Cambios";
		$this->plugin_button_delete			= "Eliminar";
		$this->plugin_page_title			= "Planillas";
		$this->plugin_page_create			= "Crear Nueva Planillas";
		$this->plugin_page_read				= "Mostrar Planillas";
		$this->plugin_page_update			= "Editar Planillas";
		$this->plugin_page_delete			= "Eliminar";
		
		$this->template_create				= "plugin_set_payrolls";
		$this->template_update				= "plugin_update_payrolls";
		
		$this->plugin_display_array[0]		= "ID";
		$this->plugin_display_array[1]		= "Empleado";
		$this->plugin_display_array[2]		= "Fecha de inicio";
		$this->plugin_display_array[3]		= "Fecha final";
		$this->plugin_display_array[4]		= "Venta Individual";
		$this->plugin_display_array[5]		= "Horas extra";
		$this->plugin_display_array[6]		= "Meta Individual";
		$this->plugin_display_array[7]		= "Meta Tiendas";
		$this->plugin_display_array[8]		= "Venta Tiendas";
		$this->plugin_display_array[9]		= "Horas días festivos";
		$this->plugin_display_array[10]		= "Bono Adicional";
		$this->plugin_display_array[11]		= "Descuento Adicional";
		$this->plugin_display_array[12]		= "&iquest;Liquidar?";
		
		$this->plugins_model->initialise($this->plugin_action_table);
		$this->load->model('cms/cms_plugin_payrolls', 'plugin_payrolls');
		
		//Extras to send
		$this->display_pagination			= TRUE; //Mostrar paginación en listado
		$this->pagination_per_page			= 15; //Numero de registros por página
		$this->pagination_total_rows		= $this->plugin_payrolls->total_rows(); //Número total de items a desplegar
		
		$this->display_filter				= FALSE; //Mostrar filtro de búsqueda 'SEARCH' o según listado 'LIST' o no mostrar FALSE
		
		//Obtener el profiler del plugin
		$this->output->enable_profiler(FALSE);
		
		//Modelos extra a obtener
		$this->load->model('cms/cms_plugin_staff', 'plugin_staff');
		$this->load->library('FW_export');
	}
	
	/**
	 * Funciones para editar Querys o Datos a enviar desde cada plugin
	 */
	//Función para desplegar listado, desde aquí se puede modificar el query
	public function _plugin_display($filterArray){
		$offset = (!isset($filterArray[2]))?0:$filterArray[2];
		
		$result_array = array();
		$result_array = $this->plugin_payrolls->payroll_list(NULL,$this->pagination_per_page, $offset);
		
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
		
		
		//Ayuda
		$date = date_components();
				
		//Header data
		$data_array['header'][1]			= "Mes y año de planilla";
		$data_array['header'][2]			= $this->plugin_display_array[1];
		$data_array['header'][3]			= "Neto pagado";
		
		//Body data
		$data_array['body'] 				= '';
		foreach($result_array as $i => $field):
		$sentemail							= ($field->PAYROLL_EMAILSENT == 'SI')?"":'<span class="active-nonsent">&#9679;</span> ';
		$employees[$field->PAYROLL_EMPLOYEE] = array('SALESMAN_NAME' => $field->SALESMAN_NAME.' '.$field->SALESMAN_LASTNAME, 'SALESMAN_POSITION' => $field->SALESMAN_POSITION, 'SALESMAN_WORKHOURS' => $field->SALESMAN_WORKHOURS);
		$data_array['body']					.= '<tr>';
		$data_array['body']					.= '<td><a href="'.base_url('cms/'.strtolower($this->current_plugin).'/update_table_row/'.$field->ID).'">'.$date['meses'][date_format(date_create($field->PAYROLL_ENDDATE), 'm')].', '.date_format(date_create($field->PAYROLL_ENDDATE),'Y').'</a></td>';
		$data_array['body']					.= '<td>'.$sentemail.$field->SALESMAN_NAME.' '.$field->SALESMAN_LASTNAME.'</td>';
		$data_array['body']					.= '<td>'.$field->PAYROLL_SALARYPAID.' <a href="#" data-toggle="modal" data-target="#myModal" data-payrollid="'.$field->ID.'"><span class="glyphicon glyphicon-play-circle pull-right" data-toggle="tooltip" data-placement="top" title="Previsualizar planilla"></span></a></td>';
		$data_array['body']					.= '</tr>';
		endforeach;
	
		$payrolldata						= array('employees'=>$employees);
		$data_array['body']					.= $this->load->view('cms/javascript_payrolldata',$payrolldata, TRUE);
		$data_array['body']					.= '<script type="text/javascript">
												$(function () {
													$("[data-toggle=\"tooltip\"]").tooltip()
												})
												</script>';
		
		return $data_array;
	}
	
	/*
	 * Función para crear nuevo contenido, desde aquí se especifican los campos a enviar en el formulario.
	 * El formulario se envía mediante objectos preestablecidos de codeigniter. 
	 * El formulario se envía con un array con la clave form_html.
	 * Se puede encontrar una guía en: http://ellislab.com/codeigniter/user-guide/helpers/form_helper.html
	 */
	public function _html_plugin_create(){
		
		$employees					= $this->get_staff_list();
		$employees					= array('' => 'Seleccionar empleado') + $employees; //Añadir campo en blanco
		$liquidacion				= array('NO' => 'No', 'SI' => 'Si - Sin indemnizaci&oacute;n', 'INDEMNIZAR' => 'Si - Con indemnizaci&oacute;n');
		
		$data_array['form_html']	= form_open_multipart('cms/'.strtolower($this->current_plugin).'/post_new_val', array('class' => 'form-horizontal col-lg-9', 'role' => 'form'));
		$data_array['form_html']	.=  "<div class='row'><div class='col-lg-12 col-md-12 col-sm-12'>".validation_errors()."</div></div>";
		//Formulario
		$data_array['form_html']	.= "<div class='form-group'>".form_label($this->plugin_display_array[1],'',array('class' => 'form-label col-lg-2'))."<div class='col-lg-10'>".form_dropdown('PAYROLL_EMPLOYEE', $employees,'', 'class="form-control" id="PAYROLL_EMPLOYEE"')."</div></div>";
		$data_array['form_html']	.= "<div class='form-group'>".form_label($this->plugin_display_array[2],'',array('class' => 'form-label col-lg-2'))."<div class='col-lg-10'>".form_input(array('name' => 'PAYROLL_INITIALDATE', 'id' => 'PAYROLL_INITIALDATE', 'class' => 'form-control', 'data-date-format' => 'YYYY-MM-DD', 'readonly' => 'readonly'))."<p class='help-block'>Fecha inicial para contabilizar dias laborados en pago planilla.</p></div></div>";
		$data_array['form_html']	.= "<div class='form-group'>".form_label($this->plugin_display_array[3],'',array('class' => 'form-label col-lg-2'))."<div class='col-lg-10'>".form_input(array('name' => 'PAYROLL_ENDDATE', 'class' => 'datetimepicker form-control', 'data-date-format' => 'YYYY-MM-DD'))."<p class='help-block'>Fecha final para contabilizar en pago de planilla.</p></div></div>";
		$data_array['form_html']	.= "<div class='form-group'>".form_label($this->plugin_display_array[12],'',array('class' => 'form-label col-lg-2'))."<div class='col-lg-10'>".form_dropdown('PAYROLL_SETTLEMENT', $liquidacion,'', 'class="form-control" id="PAYROLL_SETTLEMENT"')."<p class='help-block'>Seleccionar si es la planilla de liquidaci&oacute;n de empleado. En caso haya que liquidar, seleccionar si se pagar&aacute; indemnizaci&oacute;n o no.</p></div></div>";
		$data_array['form_html']	.= "<div class='form-group'>".form_label($this->plugin_display_array[6],'',array('class' => 'form-label col-lg-2'))."<div class='col-lg-10'><div class='input-group'><div class='input-group-addon'>Q.</div>".form_input(array('name' => 'PAYROLL_SALESGOAL', 'class' => 'form-control', 'type' => 'text'))."</div><p class='help-block'>Meta individual para asesores. Ventas sin IVA.</p></div></div>";
		$data_array['form_html']	.= "<div class='form-group'>".form_label($this->plugin_display_array[4],'',array('class' => 'form-label col-lg-2'))."<div class='col-lg-10'><div class='input-group'><div class='input-group-addon'>Q.</div>".form_input(array('name' => 'PAYROLL_SALES', 'class' => 'form-control', 'type' => 'text'))."</div><p class='help-block'>Ventas alcanzadas individualmente para asesores. Ventas sin IVA.</p></div></div>";
		$data_array['form_html']	.= "<div class='form-group'>".form_label($this->plugin_display_array[5],'',array('class' => 'form-label col-lg-2'))."<div class='col-lg-10'>".form_input(array('name' => 'PAYROLL_EXTRAHOURS', 'class' => 'form-control'))."<p class='help-block'>&Uacute;nicamente monto de horas extra trabajadas, sin contar horas por días festivos.</p></div></div>";
		$data_array['form_html']	.= "<div class='form-group'>".form_label($this->plugin_display_array[9],'',array('class' => 'form-label col-lg-2'))."<div class='col-lg-10'>".form_input(array('name' => 'PAYROLL_FESTIVEHOURS', 'class' => 'form-control'))."<p class='help-block'>&Uacute;nicamente monto de horas de días festivos trabajadas.</p></div></div>";
		$data_array['form_html']	.= "<div class='form-group'>".form_label($this->plugin_display_array[10],'',array('class' => 'form-label col-lg-2'))."<div class='col-lg-10'><div class='row'><div class='col-lg-4'><div class='input-group'><span class='input-group-addon'>Q.</span>".form_input(array('name' => 'PAYROLL_EXTRAINCOME', 'class' => 'form-control', 'type' => 'text'))."</div></div><div class='col-lg-8'>".form_input(array('name' => 'PAYROLL_EXTRAINCOMEDESCRIPTION', 'placeholder' => 'Descripción. (Bono por venta de maletas Árrive)',  'class' => 'form-control', 'type' => 'text'))."</div></div><p class='help-block'>Monto y descripci&oacute;n de bono extra a agregar en planilla. E.g. (Bonos promocionales para impulsar ventas de cierta línea.)</p></div></div>";
		$data_array['form_html']	.= "<div class='form-group'>".form_label($this->plugin_display_array[11],'',array('class' => 'form-label col-lg-2'))."<div class='col-lg-10'><div class='row'><div class='col-lg-4'><div class='input-group'><span class='input-group-addon'>Q.</span>".form_input(array('name' => 'PAYROLL_EXTRADISCOUNT', 'class' => 'form-control', 'type' => 'text'))."</div></div><div class='col-lg-8'>".form_input(array('name' => 'PAYROLL_EXTRADISCOUNTDESCRIPTION', 'placeholder' => 'Descripción. (Por producto 022060D2 desaparecido.)',  'class' => 'form-control', 'type' => 'text'))."</div></div><p class='help-block'>Monto y descripci&oacute;n de descuento extra a agregar en planilla. E.g. (Descuentos por producto desaparecido.)</p></div></div>";
		$data_array['form_html']	.= "<h4>Datos encargado y administraci&oacute;n</h4>";
		$data_array['form_html']	.= "<hr />";
		$data_array['form_html']	.= "<div class='form-group'>".form_label($this->plugin_display_array[7],'',array('class' => 'form-label col-lg-2'))."<div class='col-lg-10'><div class='input-group'><div class='input-group-addon'>Q.</div>".form_input(array('name' => 'PAYROLL_STORESALESGOAL', 'class' => 'form-control', 'type' => 'text'))."</div><p class='help-block'>Meta de tiendas para encargado. Ventas sin IVA.</p></div></div>";
		$data_array['form_html']	.= "<div class='form-group'>".form_label($this->plugin_display_array[8],'',array('class' => 'form-label col-lg-2'))."<div class='col-lg-10'><div class='input-group'><div class='input-group-addon'>Q.</div>".form_input(array('name' => 'PAYROLL_STORESALES', 'class' => 'form-control', 'type' => 'text'))."</div><p class='help-block'>Ventas alcanzadas de tiendas para encargado. Ventas sin IVA.</p></div></div>";
		//Botones del formulario
		$data_array['form_html']	.= '<div class="form-actions">'.form_submit(array('value' => $this->plugin_button_create, 'class' => 'btn btn-primary', 'name' => 'POST_SUBMIT')).' '.anchor('cms/'.strtolower($this->current_plugin), $this->plugin_button_cancel, array('class'=>'btn btn-default')).'</div>';
				
		return $data_array;
    }
	public function _html_plugin_update($result_data){
		
		$employees					= $this->get_staff_list();
		$liquidacion				= array('NO' => 'No', 'SI' => 'Si - Sin indemnizaci&oacute;n', 'INDEMNIZAR' => 'Si - Con indemnizaci&oacute;n');
		
		$data_array['form_html']	=  form_open_multipart('cms/'.strtolower($this->current_plugin).'/post_update_val/'.$result_data->ID, array('class' => 'form-horizontal', 'role' => 'form'));
		$data_array['form_html']	.= form_hidden('POST_ID', $result_data->ID);
		$data_array['form_html']	.=  "<div class='row'><div class='col-lg-12 col-md-12 col-sm-12'>".validation_errors()."</div></div>";
		
		//Formulario
		$data_array['form_html']	.= "<div class='form-group'>".form_label($this->plugin_display_array[1],'',array('class' => 'form-label col-lg-2'))."<div class='col-lg-10'>".form_dropdown('PAYROLL_EMPLOYEE', $employees, $result_data->PAYROLL_EMPLOYEE, 'class="form-control"')."</div></div>";
		$data_array['form_html']	.=  "<div class='form-group'>".form_label($this->plugin_display_array[2],'',array('class' => 'form-label col-lg-2'))."<div class='col-lg-10'>".form_input(array('name' => 'PAYROLL_INITIALDATE', 'value' => $result_data->PAYROLL_INITIALDATE, 'class' => 'form-control', 'data-date-format' => 'YYYY-MM-DD',  'readonly' => 'readonly'))."<p class='help-block'>Fecha inicial para contabilizar dias laborados en pago planilla.</p></div></div>";
		$data_array['form_html']	.=  "<div class='form-group'>".form_label($this->plugin_display_array[3],'',array('class' => 'form-label col-lg-2'))."<div class='col-lg-10'>".form_input(array('name' => 'PAYROLL_ENDDATE', 'value' => $result_data->PAYROLL_ENDDATE, 'class' => 'datetimepicker form-control', 'data-date-format' => 'YYYY-MM-DD'))."<p class='help-block'>Fecha final para contabilizar en pago de planilla.</p></div></div>";
		$data_array['form_html']	.= "<div class='form-group'>".form_label($this->plugin_display_array[12],'',array('class' => 'form-label col-lg-2'))."<div class='col-lg-10'>".form_dropdown('PAYROLL_SETTLEMENT', $liquidacion,$result_data->PAYROLL_SETTLEMENT, 'class="form-control" id="PAYROLL_SETTLEMENT"')."<p class='help-block'>Seleccionar si es la planilla de liquidaci&oacute;n de empleado. En caso haya que liquidar, seleccionar si se pagar&aacute; indemnizaci&oacute;n o no.</p></div></div>";
		$data_array['form_html']	.= "<div class='form-group'>".form_label($this->plugin_display_array[6],'',array('class' => 'form-label col-lg-2'))."<div class='col-lg-10'><div class='input-group'><div class='input-group-addon'>Q.</div>".form_input(array('name' => 'PAYROLL_SALESGOAL', 'value' => $result_data->PAYROLL_SALESGOAL, 'class' => 'form-control', 'type' => 'text'))."</div><p class='help-block'>Meta individual para asesores. Ventas sin IVA.</p></div></div>";
		$data_array['form_html']	.= "<div class='form-group'>".form_label($this->plugin_display_array[4],'',array('class' => 'form-label col-lg-2'))."<div class='col-lg-10'><div class='input-group'><div class='input-group-addon'>Q.</div>".form_input(array('name' => 'PAYROLL_SALES', 'value' => $result_data->PAYROLL_SALES, 'class' => 'form-control', 'type' => 'text'))."</div><p class='help-block'>Ventas alcanzadas individualmente para asesores. Ventas sin IVA.</p></div></div>";
		$data_array['form_html']	.=  "<div class='form-group'>".form_label($this->plugin_display_array[5],'',array('class' => 'form-label col-lg-2'))."<div class='col-lg-10'>".form_input(array('name' => 'PAYROLL_EXTRAHOURS', 'value'  => $result_data->PAYROLL_EXTRAHOURS, 'class' => 'form-control'))."<p class='help-block'>&Uacute;nicamente monto de horas extra trabajadas, sin contar horas por días festivos.</p></div></div>";
		$data_array['form_html']	.=  "<div class='form-group'>".form_label($this->plugin_display_array[9],'',array('class' => 'form-label col-lg-2'))."<div class='col-lg-10'>".form_input(array('name' => 'PAYROLL_FESTIVEHOURS', 'value' => $result_data->PAYROLL_FESTIVEHOURS, 'class' => 'form-control'))."<p class='help-block'>&Uacute;nicamente monto de horas de días festivos trabajadas.</p></div></div>";
		$data_array['form_html']	.=  "<div class='form-group'>".form_label($this->plugin_display_array[10],'',array('class' => 'form-label col-lg-2'))."<div class='col-lg-10'><div class='row'><div class='col-lg-4'><div class='input-group'><span class='input-group-addon'>Q.</span>".form_input(array('name' => 'PAYROLL_EXTRAINCOME', 'value' =>$result_data->PAYROLL_EXTRAINCOME, 'class' => 'form-control', 'type' => 'text'))."</div></div><div class='col-lg-8'>".form_input(array('name' => 'PAYROLL_EXTRAINCOMEDESCRIPTION','value' =>$result_data->PAYROLL_EXTRAINCOMEDESCRIPTION, 'placeholder' => 'Descripción. (Bono por venta de maletas Árrive)',  'class' => 'form-control', 'type' => 'text'))."</div></div><p class='help-block'>Monto y descripci&oacute;n de bono extra a agregar en planilla. E.g. (Bonos promocionales para impulsar ventas de cierta línea.)</p></div></div>";
		$data_array['form_html']	.=  "<div class='form-group'>".form_label($this->plugin_display_array[11],'',array('class' => 'form-label col-lg-2'))."<div class='col-lg-10'><div class='row'><div class='col-lg-4'><div class='input-group'><span class='input-group-addon'>Q.</span>".form_input(array('name' => 'PAYROLL_EXTRADISCOUNT','value' =>$result_data->PAYROLL_EXTRADISCOUNT, 'class' => 'form-control', 'type' => 'text'))."</div></div><div class='col-lg-8'>".form_input(array('name' => 'PAYROLL_EXTRADISCOUNTDESCRIPTION','value' =>$result_data->PAYROLL_EXTRADISCOUNTDESCRIPTION, 'placeholder' => 'Descripción. (Por producto 022060D2 desaparecido.)',  'class' => 'form-control', 'type' => 'text'))."</div></div><p class='help-block'>Monto y descripci&oacute;n de descuento extra a agregar en planilla. E.g. (Descuentos por producto desaparecido.)</p></div></div>";
		$data_array['form_html']	.= "<h4>Datos encargado y administraci&oacute;n</h4>";
		$data_array['form_html']	.= "<hr />";
		$data_array['form_html']	.= "<div class='form-group'>".form_label($this->plugin_display_array[7],'',array('class' => 'form-label col-lg-2'))."<div class='col-lg-10'><div class='input-group'><div class='input-group-addon'>Q.</div>".form_input(array('name' => 'PAYROLL_STORESALESGOAL', 'value' => $result_data->PAYROLL_STORESALESGOAL, 'class' => 'form-control', 'type' => 'text'))."</div><p class='help-block'>Meta de tiendas para encargado. Ventas sin IVA.</p></div></div>";
		$data_array['form_html']	.= "<div class='form-group'>".form_label($this->plugin_display_array[8],'',array('class' => 'form-label col-lg-2'))."<div class='col-lg-10'><div class='input-group'><div class='input-group-addon'>Q.</div>".form_input(array('name' => 'PAYROLL_STORESALES', 'value' => $result_data->PAYROLL_STORESALES, 'class' => 'form-control', 'type' => 'text'))."</div><p class='help-block'>Ventas alcanzadas de tiendas para encargado. Ventas sin IVA.</p></div></div>";
		//Botones
		$data_array['form_html']	.= '<div class="form-actions">';
		$data_array['form_html']	.= form_submit(array('value' => $this->plugin_button_update, 'class' => 'btn btn-primary', 'name' => 'POST_SUBMIT')).' ';
		$data_array['form_html']	.= form_submit(array('value' => $this->plugin_button_delete, 'class' => 'btn btn-danger', 'name' => 'POST_SUBMIT')).' ';
		$data_array['form_html']	.= anchor('cms/'.strtolower($this->current_plugin), $this->plugin_button_cancel, array('class'=>'btn btn-default')).' ';
		$data_array['form_html']	.= anchor('cms/'.strtolower($this->current_plugin).'/email_payroll/'.$result_data->ID, "<span class='glyphicon glyphicon-envelope'></span> Enviar Planilla", array('class'=>'btn btn-default pull-right')).' ';
		$data_array['form_html']	.= '</div>';
		
		return $data_array;
	}
	
	//Funciones de los posts a enviar
	public function post_new_val(){
		$form_posts 					= $this->input->post();
		$employee_earned 				= $this->calculo_total_devengado($form_posts['PAYROLL_EMPLOYEE'], $form_posts['PAYROLL_INITIALDATE'], $form_posts['PAYROLL_ENDDATE'], $form_posts['PAYROLL_EXTRAHOURS'], $form_posts['PAYROLL_SALESGOAL'], $form_posts['PAYROLL_SALES'], $form_posts['PAYROLL_STORESALESGOAL'], $form_posts['PAYROLL_STORESALES'], $form_posts['PAYROLL_FESTIVEHOURS'], $form_posts['PAYROLL_SETTLEMENT'], $form_posts['PAYROLL_EXTRAINCOME'], $form_posts['PAYROLL_EXTRADISCOUNT']);
		$form_posts['PAYROLL_EMAILSENT']= 'NO';
		
		//Datos a enviar para nueva planilla
		$submit_posts					= $this->form_posts($form_posts, $employee_earned); //Datos a enviar a la base de datos.
		
		return $this->_set_new_val($submit_posts);
	}
	public function post_update_val($data_id){
		$form_posts 					= $this->input->post();
		$employee_earned 				= $this->calculo_total_devengado($form_posts['PAYROLL_EMPLOYEE'], $form_posts['PAYROLL_INITIALDATE'], $form_posts['PAYROLL_ENDDATE'], $form_posts['PAYROLL_EXTRAHOURS'], $form_posts['PAYROLL_SALESGOAL'], $form_posts['PAYROLL_SALES'], $form_posts['PAYROLL_STORESALESGOAL'], $form_posts['PAYROLL_STORESALES'], $form_posts['PAYROLL_FESTIVEHOURS'], $form_posts['PAYROLL_SETTLEMENT'], $form_posts['PAYROLL_EXTRAINCOME'], $form_posts['PAYROLL_EXTRADISCOUNT']);
		$form_posts['PAYROLL_EMAILSENT']= 'NO';
		
		//Datos a enviar para nueva planilla
		$submit_posts					= $this->form_posts($form_posts, $employee_earned, TRUE); //Datos a enviar a la base de datos.
		
		return $this->_set_update_val($submit_posts);
	}
	/*
	 * Funciones específicas del plugin
	 */
	 
	 /**
	  * Función para obtener los datos del formulario
	  * 
	  * @var $form_posts - Inputs enviados por el formulario
	  * @var $employee_earned - Array de montos de total de ingresos para sumatoria final.
	  * @var $update - Binario de false o true para establecer si es actualización o nuevo registro.
	  */
	 private function form_posts($form_posts, $employee_earned, $update = FALSE){
		
		if($update == TRUE)
		$submit_posts['POST_ID']					= $form_posts['POST_ID'];
		
		$submit_posts['POST_SUBMIT']				= $form_posts['POST_SUBMIT'];
		$submit_posts['PAYROLL_EMPLOYEE']			= $form_posts['PAYROLL_EMPLOYEE'];
		$submit_posts['PAYROLL_INITIALDATE']		= $form_posts['PAYROLL_INITIALDATE'];
		$submit_posts['PAYROLL_ENDDATE']			= $form_posts['PAYROLL_ENDDATE'];
		$submit_posts['PAYROLL_SALESGOAL']			= $form_posts['PAYROLL_SALESGOAL'];
		$submit_posts['PAYROLL_SALES']				= $form_posts['PAYROLL_SALES'];
		$submit_posts['PAYROLL_STORESALESGOAL']		= $form_posts['PAYROLL_STORESALESGOAL'];
		$submit_posts['PAYROLL_STORESALES']			= $form_posts['PAYROLL_STORESALES'];
		$submit_posts['PAYROLL_COMMISSION']			= number_format($employee_earned['earned_salary'][1], 2, '.', '');
		$submit_posts['PAYROLL_EXTRAHOURSSALARY']	= number_format($employee_earned['earned_salary'][2], 2, '.', '');
		$submit_posts['PAYROLL_EXTRAHOURS']			= $form_posts['PAYROLL_EXTRAHOURS'];
		$submit_posts['PAYROLL_FESTIVEHOURSSALARY']	= number_format($employee_earned['earned_salary'][3], 2, '.', '');
		$submit_posts['PAYROLL_FESTIVEHOURS']		= $form_posts['PAYROLL_FESTIVEHOURS'];
		$submit_posts['PAYROLL_ISSUEDATE']			= date('Y-m-d');
		$submit_posts['PAYROLL_SALARYPAID']			= number_format($employee_earned['earned_salary'][0], 2, '.', '');
		$submit_posts['PAYROLL_14BONUS']			= number_format($employee_earned['bono14'], 2, '.', '');
		$submit_posts['PAYROLL_AGUINALDO']			= number_format($employee_earned['aguinaldo'], 2, '.', '');
		$submit_posts['PAYROLL_VACATIONS']			= number_format($employee_earned['vacations'], 2, '.', '');
		$submit_posts['PAYROLL_IGSS']				= number_format($employee_earned['discount_salary'][1], 2, '.', '');
		$submit_posts['PAYROLL_ISR']				= number_format($employee_earned['discount_salary'][2], 2, '.', '');
		$submit_posts['PAYROLL_TOTALACCRUED']		= number_format(array_sum($employee_earned['earned_salary']), 2, '.', '');
		$submit_posts['PAYROLL_EXTRAINCOME']		= number_format($employee_earned['earned_salary'][4], 2, '.', '');
		$submit_posts['PAYROLL_EXTRAINCOMEDESCRIPTION']	= $form_posts['PAYROLL_EXTRAINCOMEDESCRIPTION'];
		$submit_posts['PAYROLL_EXTRADISCOUNT']		= number_format($employee_earned['discount_salary'][0], 2, '.', '');
		$submit_posts['PAYROLL_EXTRADISCOUNTDESCRIPTION'] = $form_posts['PAYROLL_EXTRADISCOUNTDESCRIPTION'];
		$submit_posts['PAYROLL_ESTABLISHEDBONUS'] 	= number_format($employee_earned['earned_salary'][6], 2, '.', '');
		$submit_posts['PAYROLL_TOTALDISCOUNTS']		= number_format(array_sum($employee_earned['discount_salary']), 2, '.', '');
		$submit_posts['PAYROLL_SETTLEMENT']			= $form_posts['PAYROLL_SETTLEMENT'];
		//Actualizar datos de empleado
		$salesman_enabled							= ($form_posts['PAYROLL_SETTLEMENT'] == 'NO')?'SI':'NO'; //Si se selecciona liquidar al empleado, se inhabilitará en la tabla de empleados.
		$staff_data['SALESMAN_ENABLED']				= $salesman_enabled;
		$this->plugin_staff->update($staff_data, $form_posts['PAYROLL_EMPLOYEE']);
		
		return $submit_posts;
	 }
	private function get_staff_list(){
		$staff_list = $this->plugin_staff->list_rows('', '');
		
		foreach($staff_list as $id => $employee):
			$employees[$employee->ID] = $employee->SALESMAN_NAME.' '.$employee->SALESMAN_LASTNAME;
		endforeach;
		
		return $employees;
	}
	
	private function calculo_total_devengado($employeeid, $initialdate, $enddate, $extrahours, $sales_goal, $salesreached, $store_salesgoal, $store_salesreached, $festivehours, $liquidar = 'NO', $extra_income, $extra_discount){
		
		$employee['COLUMN_VAR'] 	= $employeeid;
		$employeedata				= $this->plugin_staff->get_single_row($employee);
		$employeedata->PAYROLL_ENDDATE = $enddate; //Enviar la fecha final de planilla para el cálculo de total devengado
		$return['salary']			= $employeedata->SALESMAN_SALARY;
		$daily_salary				= $employeedata->SALESMAN_SALARY / 30; //Calcular el salario diario
		$this->load->model('cms/cms_plugin_commissions', 'plugin_commissions');
		
		//Salario devengado
		$initialworkdate			= ($employeedata->SALESMAN_COMMENCEMENT > $initialdate)?$employeedata->SALESMAN_COMMENCEMENT:$initialdate;
		$datetime1 					= new DateTime($initialworkdate);
		$datetime2 					= new DateTime($enddate);
		$interval 					= $datetime1->diff($datetime2);
		$return['days_worked']		= $interval->format('%a');//Calcular el total de días laborados
		$return['earned_salary'][0]	= ($liquidar != 'NO' OR $employeedata->SALESMAN_COMMENCEMENT > $initialdate)?$daily_salary * intval($return['days_worked']):$employeedata->SALESMAN_SALARY; //Calcular el salario devengado.
		
		//Comisiones
		$commissions 				= $this->plugin_commissions->list_rows('', 'SALESMAN_POSITION = "'.$employeedata->SALESMAN_POSITION.'"');
		$store_salesgoal			= ($store_salesgoal < 1)?$store_salesreached:$store_salesgoal; //Colocar la meta lo vendido en caso el valor de meta sea cero o menor.
		$goalreached				= ($employeedata->SALESMAN_POSITION == 'ASESOR')?($salesreached / $sales_goal):($store_salesreached / $store_salesgoal); //Colocar la meta sobre venta personal o venta de tiendas.
		foreach($commissions as $i => $commission):
			if($goalreached >= $commission->COMMISSION_GOAL): //Si el valor de meta alcanzado es mayor al objetivo de comisión
				$commission_value	= $commission->COMMISSION_VALUE; //Establecer el valor de comisión.
				$commission_bonus	= $commission->COMMISSION_BONUS; //Establecer el bono decreto extra generado.
			endif;
		endforeach;
		$sales						= ($employeedata->SALESMAN_POSITION == 'ASESOR')?$salesreached:$store_salesreached;
		$commission_amount			= $commission_value * $sales;
		$store_management_commision	= ($employeedata->SALESMAN_POSITION == 'ENCARGADO')?($commission_amount + ($salesreached * $this->fw_resource->request('RESOURCE_STOREMANAGER_COMMISSION'))):$commission_amount; //Comisión administrativa, ya sea del encargado de tienda o administración general.
		$return['commissions']		= ($employeedata->SALESMAN_POSITION == 'ASESOR')?$commission_amount:$store_management_commision; //Colocar comisión de encargado o administrativa.
		$return['earned_salary'][1]	= number_format($return['commissions'],2, '.','');
		
		//Horas extra
		$workhours					= ($employeedata->SALESMAN_WORKHOURS == 'DIURNA')?8:7; //horas trabajadas semanalmente.
		$hoursalary					= ($employeedata->SALESMAN_SALARY / 30)/$workhours; //Salario por hora.
		$return['earned_salary'][2]	= number_format(($extrahours * $hoursalary * 1.5), 2, '.', ''); //Calculo de pago de horas extras.
		
		//Bonos extra
		$extra_income				= (is_numeric($extra_income))?$extra_income:0;
		$return['earned_salary'][4]	= number_format($extra_income,2,'.','');
		
		//Días festivos
		$return['earned_salary'][3]	= ($hoursalary * $festivehours * 0.5); //Pago del salario ordinario por hora, mas el 50% extra por hora de día festivo.
		
		//Bono Decreto
		$bonodecreto				= ($liquidar != 'NO' OR $employeedata->SALESMAN_COMMENCEMENT > $initialdate)?((intval($this->fw_resource->request('RESOURCE_ESTABLISHED_BONUS')) / 30) * intval($return['days_worked'])):$this->fw_resource->request('RESOURCE_ESTABLISHED_BONUS');
		$return['earned_salary'][6]	= number_format($bonodecreto + $commission_bonus,2,'.','');
		//Bono 14
		$salariodevengadobono		= ($return['earned_salary'][0] + $return['earned_salary'][1]); //Salario a utilizar si no se tienen datos anteriores.
		$bono14						= $this->bono14($employeedata, $salariodevengadobono);
		$return['bono14']			= $bono14['bonus14tosave'];
		
		//Aguinaldo
		$aguinaldo					= $this->aguinaldo($employeedata, $salariodevengadobono);
		$return['aguinaldo']		= $aguinaldo['christmasbonustosave'];
		
		//Vacaciones
		$vacaciones					= $this->vacations($employeedata, $salariodevengadobono);
		$return['vacations']		= $vacaciones['total'];
		
		//Descuentos
		//IGSS
		$return['discount_salary'][1] = ($employeedata->SALESMAN_IGSSINSCRIPTION == 'SI')?((array_sum($return['earned_salary']) - $return['earned_salary'][6]) * $this->fw_resource->request('RESOURCE_IGSS_LABOR_QUOTA')) * -1:0.00;
		//ISR
		$return['discount_salary'][2] = $employeedata->SALESMAN_PROFITTAX;
		//Descuento adicional
		$extra_discount				= (is_numeric($extra_discount))?$extra_discount:0;
		$extra_discount				= ($extra_discount < 0)?$extra_discount:$extra_discount * -1;
		$return['discount_salary'][0]	= number_format($extra_discount,2,'.','');
		
		
		return $return;
	}
	
	
	/**
	 * Obtener la información para bonificaciones
	 * @var $employeedata - Información del empleado obtenida de base de datos tabla PLUGIN_SALESMAN según ID del empleado
	 * @var $monthtopay - Mes en el que se paga la bonificación (7 en caso del bono 14 y 12 en caso del aguinaldo).
	 * @var $salariodevengadobono - Salario + comisiones del mes, se utiliza para cuando no se tiene el historial de salarios anteriores.
	 */
	private function bonus($employeedata, $monthtopay, $salariodevengadobono){
		//Obtener el salario diario
		$ordinary_salary 			= $employeedata->SALESMAN_SALARY;
		$daily_salary				= $ordinary_salary / 30;
		
		//Obtener fecha de pago bono14
		$monthtopay					= str_pad($monthtopay, 2, 0, STR_PAD_LEFT);
		$payedbonusyear				= (date('m') > $monthtopay)?date('Y'):(date('Y')-1);
		$lastpaymentmade			= $payedbonusyear.'-'.$monthtopay.'-01'; //Fecha del último pago de bono 14 realizado.
		$bonusdate					= (date('Y-').$monthtopay.'-01'); //Fecha de pago de bono14
		$nextpayment				= (date('m') > $monthtopay)?((date('Y-') + 1).$monthtopay.'-01'):(date('Y-').$monthtopay.'-01');
		
		//Fecha de pago
		$data['commencementdate']	= ($employeedata->SALESMAN_COMMENCEMENT > $lastpaymentmade)? $employeedata->SALESMAN_COMMENCEMENT: $lastpaymentmade; //Obtener fecha de inicio de labores o último pago de bono 14, dependiendo el caso.
		$firsdate 					= new DateTime($data['commencementdate']); //Fecha desde que inició el cálculo de bono 14
		$seconddate 				= new DateTime($employeedata->PAYROLL_ENDDATE); //Fecha que finaliza el pago de planilla.
		$interval 					= $firsdate->diff($seconddate);
		$dayssalarypaid				= $interval->format('%a');//Calcular el total de días pendiente de pago
		$data['dayssalarypaid']		= ($dayssalarypaid > 365)?365:$dayssalarypaid; //No contar los días extra de julio.
		
		//Obtener comisiones de último pago o tiempo trabajado
		$payrollenddate 			= strtotime ( '-1 day' , strtotime ( $employeedata->PAYROLL_ENDDATE ) ) ;
		$previousSavings			= date ( 'Y-m-d' , $payrollenddate);
		$employeedata->PAYROLL_EMPLOYEE = (!isset($employeedata->PAYROLL_EMPLOYEE))?$employeedata->ID:$employeedata->PAYROLL_EMPLOYEE; //En caso no se envíe según información de planilla, sino únicamente de empleado.
		$employeePayrolls			= $this->plugin_payrolls->list_rows('PAYROLL_COMMISSION, PAYROLL_SALARYPAID', 'PAYROLL_EMPLOYEE = "'.$employeedata->PAYROLL_EMPLOYEE.'" AND PAYROLL_ENDDATE BETWEEN "'.$lastpaymentmade.'" AND "'.$previousSavings.'"');
		$yearCommissions			= array();
		$yearSalary					= array();
		foreach($employeePayrolls as $commissions):
			$yearCommissions[]		= $commissions->PAYROLL_COMMISSION; //Guardar las comisiones en un array
			$yearSalary[]			= $commissions->PAYROLL_SALARYPAID;
		endforeach;
		$monthlyavecommission		= array_sum($yearCommissions); //Comisiones promedio de seis meses
		$monthlyavesalary			= (array_sum($yearSalary) + $salariodevengadobono); //Salario total en un mes promedio.Sumando el salario del mes en curso mas los anteriores. 
		$data['totalreceivedmonthly']= (($monthlyavesalary + $monthlyavecommission) / $data['dayssalarypaid']) * 30; //Total recibido en un mes promedio
		
		return $data;
	}
	private function bono14($employeedata, $salariodevengadobono){
		
		$bonusinfo					= $this->bonus($employeedata, 7, $salariodevengadobono);
		
		//Obtener el bono14
		$payrollenddate 			= strtotime ( '-1 day' , strtotime ( $employeedata->PAYROLL_ENDDATE ) ) ;
		$previousSavings			= date ( 'Y-m-d' , $payrollenddate); //Obtener la fecha anterior al cierre de planilla
		$employee14bonus			= $this->plugin_payrolls->list_rows('PAYROLL_14BONUS', 'PAYROLL_EMPLOYEE = "'.$employeedata->ID.'" AND PAYROLL_ENDDATE BETWEEN "'.$bonusinfo['commencementdate'].'" AND "'.$previousSavings.'"'); 
		$bonosahorrados				= array();
		foreach($employee14bonus as $bono):
			$bonosahorrados[]		= $bono->PAYROLL_14BONUS;
		endforeach;
		$savedbonus					= array_sum($bonosahorrados); //Obtener los bonos14 resguardados mes a mes
		$total14bonus				= (($bonusinfo['totalreceivedmonthly'] / 30) * $bonusinfo['dayssalarypaid']) * 0.083333;
		$bonus14tosave				= $total14bonus - $savedbonus;
		
		$return['total14bonus']		= $total14bonus;
		$return['bonus14tosave']	= $bonus14tosave;
		$return['commencementdate']	= $bonusinfo['commencementdate'];
		$return['salariopromedio']	= $bonusinfo['totalreceivedmonthly'];
		$return['diaspendientes']	= $bonusinfo['dayssalarypaid'];
		
		return $return;
		
	}
	
	private function aguinaldo($employeedata, $salariodevengadobono){
		
		//Obtener información del aguinaldo
		$bonusinfo					= $this->bonus($employeedata, 12, $salariodevengadobono);
				
		//Obtener el aguinaldo
		$payrollenddate 			= strtotime ( '-1 day' , strtotime ( $employeedata->PAYROLL_ENDDATE ) ) ;
		$previousSavings			= date ( 'Y-m-d' , $payrollenddate); //Obtener la fecha anterior al cierre de planilla
		$employeechristmasbonus		= $this->plugin_payrolls->list_rows('PAYROLL_AGUINALDO', 'PAYROLL_EMPLOYEE = "'.$employeedata->ID.'" AND PAYROLL_ENDDATE BETWEEN "'.$bonusinfo['commencementdate'].'" AND "'.$previousSavings.'"'); 
		$bonosahorrados				= array();
		foreach($employeechristmasbonus as $bono):
			$bonosahorrados[]		= $bono->PAYROLL_AGUINALDO;
		endforeach;
		$savedbonus					= array_sum($bonosahorrados); //Obtener los aguinaldo resguardados mes a mes
		$totalchristmasbonus		= ($bonusinfo['totalreceivedmonthly'] * $bonusinfo['dayssalarypaid']) / 365;
		$christmasbonustosave		= $totalchristmasbonus - $savedbonus;
		
		$return['totalchristmasbonus'] = $totalchristmasbonus;
		$return['christmasbonustosave'] = $christmasbonustosave;
		$return['commencementdate']	= $bonusinfo['commencementdate'];
		$return['salariopromedio']	= $bonusinfo['totalreceivedmonthly'];
		$return['diaspendientes']	= $bonusinfo['dayssalarypaid'];
		
		return $return;
		
	}
	/**
	 * Funcion que devuelve los dias de descanso que hay entre 2 fechas
	 * @var $fechaInicio - Fecha inicial para retornar días de descanso.
	 * @var $fechaFin - Fecha final a contar los días de descanso.
	 * @var $diaDescanso - Día de la semana de descanso.
	 **/
	private function countRestDays($fechaInicio,$fechaFin, $diaDescanso = 'Monday'){
		$dias=array();
		$fecha1=date($fechaInicio);
		$fecha2=date($fechaFin);
		$fechaTime=strtotime("-1 day",strtotime($fecha1));//Les resto un dia para que el next sunday pueda evaluarlo en caso de que sea un domingo
		$fecha=date("Y-m-d",$fechaTime);
		while($fecha <= $fecha2){
			$proximo_domingo=strtotime("next ".$diaDescanso,$fechaTime);
			$fechaDomingo=date("Y-m-d",$proximo_domingo);
			if($fechaDomingo <= $fechaFin){
				$dias[]=$fechaDomingo;
			}else{
				break;
			}
			$fechaTime=$proximo_domingo;
			$fecha=date("Y-m-d",$proximo_domingo);
		}
	 
	 return count($dias);
	}
	/**
	 * Función que genera valor a pagar en concepto de vacaciones
	 * @var $employeedata - datos del empleado 
	 */
	private function vacations($employeedata, $salariodevengadobono){
			
			
		$commencementday			= date('d', strtotime($employeedata->SALESMAN_COMMENCEMENT)); //Obtener el día en que el empleado inició labores
		$monthtopay					= date('m', strtotime($employeedata->SALESMAN_COMMENCEMENT)); //Obtener el mes en que el empleado inició labores
		$commencementyear			= date('Y', strtotime($employeedata->SALESMAN_COMMENCEMENT)); //Obtener el año en que el empleado inició labores
		
		$bonusinfo					= $this->bonus($employeedata, $monthtopay, $salariodevengadobono);
		
		//Obtener salarios del empleado según plazos de vacaciones de un año desde que inició de labores en la empresa.
		$payrollenddate 			= strtotime ( '-1 day' , strtotime ( $employeedata->PAYROLL_ENDDATE ) ) ;
		$previousSavings			= date ( 'Y-m-d' , $payrollenddate);
		$date1						= new DateTime($employeedata->SALESMAN_COMMENCEMENT);
		$date2						= new DateTime($employeedata->PAYROLL_ENDDATE); //Contar el día último de finalización de la planilla
		$interval					= $date1->diff($date2);
		$dayssincecommencement		= $interval->format('%a'); //Días transcurridos a la fecha desde el inicio de labores en la empresa del empleado.
		$Yearssincecommencement		= ceil($dayssincecommencement / 365);
		$ksortyears					= $commencementyear + $Yearssincecommencement;
		$avgPayroll					= array();
		$monthtotalpayroll			= array();
		for($i = 0; $i < $Yearssincecommencement; $i++):
			$endyear				= $ksortyears - $i; //Año final para contar vacaciones
			$initialYear			= $endyear - 1; //Año para iniciar conteo de vacaciones
			$employeeyearenddate	= $endyear.'-'.$monthtopay.'-'.$commencementday; //Finalización de cada año, desde que el empleado inició labores.
			$vacationenddate		= ($employeeyearenddate > $previousSavings)?$previousSavings:$employeeyearenddate; //Colocar fecha final de conteo de salarios de planillas la última al cierre de la planilla.
			$fechauno				= new DateTime($initialYear.'-'.$monthtopay.'-'.$commencementday);
			$fechados				= new DateTime($vacationenddate);
			$yearinterval			= $fechauno->diff($fechados);
			$yeardays				= $yearinterval->format('%a'); //Dias laborados en el año obtenido de salarios.
			$employeedata->PAYROLL_EMPLOYEE = (!isset($employeedata->PAYROLL_EMPLOYEE))?$employeedata->ID:$employeedata->PAYROLL_EMPLOYEE; //En caso no se envíe según información de planilla, sino únicamente de empleado.
			$employeePayrolls		= $this->plugin_payrolls->employee_year_payrolls($employeedata->PAYROLL_EMPLOYEE, $initialYear.'-'.$monthtopay.'-'.$commencementday, $vacationenddate);
			$yearly_earned[$i]		= ($employeePayrolls->PAYROLL_YEAR_PAYMENTS_EARNED > 0)?($employeePayrolls->PAYROLL_YEAR_PAYMENTS_EARNED):0;
			$daily_earned[$i]		= $yearly_earned[$i] / $yeardays; //Salario diario devengado en el año laborado.
			/*
			$monthtotalpayroll[$i]	= array();
			foreach($employeePayrolls as $payroll):
				$monthtotalpayroll[$i][]= ($payroll->PAYROLL_COMMISSION + $payroll->PAYROLL_SALARYPAID); //Obtener el total ganado en cada año laborado
			endforeach;
			$currentpayrollsalary	= ($i == ($Yearssincecommencement - 1))?$salariodevengadobono:0; //Colocar el monto del salario y comisiones al cierre de la planilla para sumarlo al último año propuesto.
			 
			$avgPayroll[$i]			= (array_sum($monthtotalpayroll[$i]) > 0)?(((array_sum($monthtotalpayroll[$i]) + $currentpayrollsalary) / $dayssincecommencement)*30):$salariodevengadobono; //Obtener el promedio ganado según cada año laborado
			*/
		endfor;
		$daily_earned				= array_reverse($daily_earned);
		
		//Obtener días tomados de vacaciones
		$this->load->model('cms/cms_plugin_vacations', 'plugin_vacations');
		$employeeid					= (isset($employeedata->PAYROLL_EMPLOYEE))?$employeedata->PAYROLL_EMPLOYEE:$employeedata->ID;
		$vacationsTaken				= $this->plugin_vacations->list_rows('',"VACATIONS_EMPLOYEE = '".$employeeid."' AND VACATION_INITIALDATE BETWEEN '".$employeedata->SALESMAN_COMMENCEMENT."' AND '".date('Y-m-d')."'"); //Obtener las vacaciones tomadas por el empleado.
		$vacationdays				= array();
		foreach($vacationsTaken as $vacations):
			$restDays				= $this->countRestDays($vacations->VACATION_INITIALDATE, $vacations->VACATION_ENDDATE, $vacations->VACATION_RESTDAY); //Obtener la suma de los días de descanso en el período de vacaciones tomadas.
			$date1 					= new DateTime($vacations->VACATION_INITIALDATE); //Fecha desde que inició vacaciones.
			$date2	 				= new DateTime($vacations->VACATION_ENDDATE); //Fecha desde que finalizó vacaciones.
			$interval 				= $date1->diff($date2);
			$totaldaystaken			= $interval->format('%a');//Calcular el total de días descansados en vacaciones contando días de descanso
			$vacationdays[]			= ($totaldaystaken - $restDays); //Calcular el total de días descansados en vacaciones sin contar días de descanso
		endforeach;
		$totalVacationsTaken		= array_sum($vacationdays); //Sumar el total de días descansados desde que inició labores
		$periodsTaken				= floor($totalVacationsTaken / 15); //Obtener los períodos cumplidos con vacaciones tomadas.
		$vacationsTakenArray		= ($totalVacationsTaken > 0)?array_fill(0, $totalVacationsTaken, 1):array(); //Obtener todos los días de vacaciones tomadas en un array.
		$vacationsTakenArray		= array_chunk($vacationsTakenArray, 15); //Agrupar en 15 los días tomados de vacaciones.
		$daysFromVacationsTaken		= array();
		foreach($vacationsTakenArray as $i => $vacationsDaysTaken):
			$daysFromVacationsTaken[$i]= array_sum($vacationsDaysTaken); //Sumar los días de cada período de vacaciones tomadas.
		endforeach;
		
		//Obtener días pendientes de tomar vacaciones
		$vacationsearned			= floor(($dayssincecommencement / 365) * 15); //Días de vacaciones ganadas por el empleado 
		$lasthoursearned			= (($dayssincecommencement / 365) * 15) - $vacationsearned; //Horas en decimales por dia restantes de vacaciones.
		$vacationsPending			= ($vacationsearned - $totalVacationsTaken); //Dias pendientes de vacaciones
		$vacationsPending			= ($vacationsPending > 0)?$vacationsPending:0; //Si los días de vacaciones son mayores a 0, colocarle cero.
				
		//Obtener el monto de vacaciones pendientes
		$leftperiods				= ($Yearssincecommencement - $periodsTaken); //Periodos pendientes de tomar vacaciones.
		$lastcommencementyear		= (intval(date('md')) > intval($monthtopay.$commencementday))?(date('Y') - 1):date('Y'); //Obtener último año de fecha de inicio de labores
		$lastcommencementdate		= new DateTime($lastcommencementyear.'-'.$monthtopay.'-'.$commencementday); //Última fecha anual cumplida desde inicio de labores.
		$today						= new DateTime(date('Y-m-d'));
		$lastperiodearnedvacations	= $lastcommencementdate->diff($today)->format('%a'); //Dias pendientes de vacaciones, al último período ganado.
		$totalearnedvacationsarray	= ($vacationsearned > 0)?array_fill(0, $vacationsearned, 1):array(); //Array con total de vacaciones ganadas.
		$totalearnedvacationsarray	= array_chunk($totalearnedvacationsarray, 15);
		foreach($totalearnedvacationsarray as $i => $earnedvacationsdays):
			$totalearnedvacationsarray[$i] = array_sum($earnedvacationsdays); //Sumar periodos de vacaciones ganadas.
		endforeach;
		end($totalearnedvacationsarray);
		$totalearnedvacationslastkey= key($totalearnedvacationsarray); 
		$totalearnedvacationsarray[$totalearnedvacationslastkey] = $totalearnedvacationsarray[$totalearnedvacationslastkey] + $lasthoursearned;//Agregar decimales a último valor de array
		foreach ($totalearnedvacationsarray as $i => $totalearnedvacationsperiod): //Obtener los dias pendientes por cada período de vacaciones anuales ganadas.
			$daysFromVacationsTaken[$i] = (isset($daysFromVacationsTaken[$i]))?$daysFromVacationsTaken[$i]:0; //Agregar ceros en los períodos de vacaciones en donde no se han tomado.
			$pendingDays				= ($totalearnedvacationsperiod - $daysFromVacationsTaken[$i]); //Dias pendientes de vacaciones
			$pendingVacationDays[$i]	= ($pendingDays < 0)?0:$pendingDays; //Almacenar por período las vacaciones pendientes de tomar, en caso se hayan tomado mas vacaciones de las ganadas, colocar cero.
		endforeach;
		
		$earnedVacationDays			= ($vacationsPending > 0)?array_fill(0, $vacationsPending, 1):array(); //Obtener todos los días de vacaciones pendientes en un array.
		$earnedVacationDays			= array_chunk($earnedVacationDays, 15); //Agrupar en 15 los días pendientes de vacaciones.
		$daysFromVacationPeriod		= array();
		foreach($earnedVacationDays as $i => $vacationDaysPending):
			$daysFromVacationPeriod[$i]= array_sum($vacationDaysPending); //Sumar los días de cada período pendiente de tomar vacaciones.
		endforeach;
		
		$pendingPayroll				= array();
		foreach($pendingVacationDays as $i => $period):
			$pendingPayroll[$i]		= number_format(($daily_earned[$i] * $pendingVacationDays[$i]), 2, '.', ''); //Obtener el salario promedio del período pendiente a pagar.
		endforeach;
		
		$return['dpendientes']		= $pendingVacationDays;
		$return['salaries']			= $pendingPayroll;
		$return['total']			= array_sum($pendingPayroll);
		
		/*
		echo "<pre>";
		print_r($return);
		echo "</pre>";*/
		return $return;
	}
	/**
	 * Obtener dato de indemnización del empleado
	 * @var $employee - ID del empleado
	 */
	 private function indemnizacion($employee, $enddate){
				 	
			$initialdate				= strtotime('-6 month', strtotime($enddate)); //Fecha inicial de cálculo de comisión
			$initialdate				= date('Y-m-d',$initialdate);//Fecha para cálculo de comisiones
			$payrolls					= $this->plugin_payrolls->payroll_list("PSPR.PAYROLL_EMPLOYEE = '$employee' AND PSPR.PAYROLL_ENDDATE BETWEEN '$initialdate' AND '$enddate'");
			
			//Obtener total de tiempo laborado
			$date1						= new DateTime($payrolls[0]->SALESMAN_COMMENCEMENT);
			$date2						= new DateTime($enddate); //Contar el día último de finalización de la planilla
			$interval					= $date1->diff($date2);
			$dayssincecommen			= $interval->format('%a'); //Días transcurridos a la fecha desde el inicio de labores en la empresa del empleado.
			$return['yearssincecommen']	= floor($dayssincecommen / 365); //Años en la empresa.
			$return['dayswithoutyears']	= ($dayssincecommen - ($return['yearssincecommen'] * 365)); //Dias después de los años laborados.
			$return['dayssincecommen']	= ($dayssincecommen > 180)? 180:$dayssincecommen; //Para cálculo de 
			
			//Total de sueldos devengados
			foreach($payrolls as $i => $payroll):
				$salariespaid[]			= $payroll->PAYROLL_SALARYPAID; //Obtener todos los salarios en un array.
				$commissions[]			= $payroll->PAYROLL_COMMISSION; //Obtener todos las comisiones en un array.
			endforeach;
			$return['salaries']			= array_sum($salariespaid); //Sumar todos los sarios
			$return['commissions']		= array_sum($commissions); //Sumar todos las comisiones
			$return['bonos14']			= 0; //Establecer como cero los bono 14, en caso no hayan cumplido los seis meses. colocar el bono 14 del finiquito
			$return['aguinaldos']		= 0; //Establecer como cero los aguinaldo, en caso no hayan cumplido los seis meses. colocar el aguinaldo del finiquito
			$numpayrolls				= count($payrolls);
			if($numpayrolls > 6):
			//Agregar valor de bono 14, si y solo si ;), tiene mas de seis meses de laborar en la empresa.
			$devengados					= ($return['salaries'] + $return['commissions']);
			$return['bonos14']			= $devengados * 0.83333333333333; //Obtener el proporcional a bono 14.
			$return['aguinaldos']		= $devengados * 0.83333333333333; //Obtener el proporcional a bono 14.
			endif;
			
			return $return;			
	 }
	/**
	* Obtener último día de un mes.
	 * @var $elAnio - Año del mes a obtener último día.
	 * @var $elMes - Mes a obtener último día.
	*/
	private function getUltimoDiaMes($elAnio,$elMes) {
		return date("d",(mktime(0,0,0,$elMes+1,1,$elAnio)-1));
	}
	/**
	 * Función para obtener el último mes pagada de planilla
	 * @var $employeeid - ID del empleado
	 */
	 public function last_payroll_date($employeeid){
	 	
		$data['COLUMN_VAR'] = $employeeid;
	 	$lastpaiddate 		= $this->plugin_payrolls->last_paiddate($employeeid); //Obtener la última fecha de pago
		$employeedata		= $this->plugin_staff->get_single_row($data); //Obtener información del empleado
		$previousmonth		= date('m', strtotime($employeedata->SALESMAN_COMMENCEMENT)); //Obtener el mes en que el empleado inició labores
		$previousmonth		= str_pad($previousmonth - 1, 2, "0", STR_PAD_LEFT);//Obtener el mes anterior al ingreso
		$previousday		= $this->getUltimoDiaMes(date('Y'), $previousmonth);
		
		$initialdate	= (empty($lastpaiddate->PAYROLL_ENDDATE))?(date('Y').'-'.$previousmonth.'-'.$previousday):$lastpaiddate->PAYROLL_ENDDATE;
		
	 	echo $initialdate;
	 }
	/**
	 * Función para exportar la planilla a PDF
	 * @var $payrollid - ID de la planilla a obtener información en el pdf.
	 */
	 public function payroll_pdf($payrollid = NULL){
	  	$this->load->library('FW_export', $payrollid);
		
		return $pdf = $this->fw_export->pdfpayroll($payrollid);
	 }
	/**
	 * Función para enviar datos de la planilla por correo
	 */
	public function email_payroll($payrollid = NULL){
		
		$payrolldata		= $this->plugin_payrolls->get_payroll($payrollid); //Obtener datos de la planilla.
		$this->fw_export->pdfpayroll($payrollid);
		
		if($payrolldata->PAYROLL_SETTLEMENT == 'NO'): 
			//Si no es liquidación del empleado	
			if(file_exists($_SERVER['DOCUMENT_ROOT'].('/app/user_files/uploads/planillas/planilla'.$payrollid.'.pdf'))): //Confirmar si existe el archivo de planilla.
				$this->fw_posts->payroll_confirmation($payrolldata); //Enviar el correo electrónico.
				$this->plugin_payrolls->update(array('PAYROLL_EMAILSENT' => 'SI'), $payrollid); //Actualizar a enviado el correo electrónico.
				$this->fw_alerts->add_new_alert(3001, 'SUCCESS');
			else:
				$this->fw_alerts->add_new_alert(3002, 'ERROR');
			endif;
		else: 
			//Si es liquidación de empleado
			$employee['COLUMN_VAR'] 	= $payrolldata->PAYROLL_EMPLOYEE;
			$employeedata				= $this->plugin_staff->get_single_row($employee);
			$payrollsalary				= ($payrolldata->PAYROLL_SALARYPAID + $payrolldata->PAYROLL_COMMISSION); //Obtener el monto de la planilla actual para cálculo de bonos.
			$vacations					= $this->vacations($payrolldata, $payrollsalary);
			$bono14						= $this->bono14($payrolldata, $payrollsalary);
			$aguinaldo					= $this->aguinaldo($payrolldata, $payrollsalary);
			$indemnizacion				= FALSE;
			
			//Obtener tiempo trabajado
			$initialdate				= new DateTime($employeedata->SALESMAN_COMMENCEMENT);
			$enddate					= new DateTime($payrolldata->PAYROLL_ENDDATE);
			$interval					= $initialdate->diff($enddate);
			$dayssincecommencement		= ($interval->format('%a') + 1); //Días transcurridos a la fecha desde el inicio de labores en la empresa del empleado. Se le asigna un dia mas para contar el de la fecha que inició.
			
			if($payrolldata->PAYROLL_SETTLEMENT == 'INDEMNIZAR' && $dayssincecommencement > 60):
				$indemnizacion				= $this->indemnizacion($payrolldata->PAYROLL_EMPLOYEE, $payrolldata->PAYROLL_ENDDATE);
				//Enviar datos de cálculo de indemnización.
				$indemnizacion['bonos14']	= ($indemnizacion['bonos14'] > 0)?$indemnizacion['bonos14']:$bono14['total14bonus']; //Colocar el bono 14 proporcional.
				$indemnizacion['aguinaldos']= ($indemnizacion['aguinaldos'] > 0)?$indemnizacion['aguinaldos']:$aguinaldo['totalchristmasbonus']; //Colocar el bono 14 proporcional.
				$indemnizacion['totalganado'] = $indemnizacion['salaries']+$indemnizacion['bonos14']+$indemnizacion['commissions']+$indemnizacion['aguinaldos'];
				$indemnizacion['promediomes'] = ($indemnizacion['totalganado'] / $indemnizacion['dayssincecommen']) * 30;
				$indemnizacion['promediodia'] = ($indemnizacion['promediomes'] / 365);
				$indemnizacion['indemizaryear'] = ($indemnizacion['promediomes'] * $indemnizacion['yearssincecommen']);
				$indemnizacion['indemizarday'] = ($indemnizacion['promediodia'] * $indemnizacion['dayswithoutyears']);
				$indemnizacion['totalindemnizar'] = ($indemnizacion['indemizaryear'] + $indemnizacion['indemizarday']);
			endif;
			$totalindemnizar			= ($indemnizacion != FALSE)?$indemnizacion['totalindemnizar']:0;
			$liquidaciontotal			= ($payrolldata->PAYROLL_TOTALACCRUED + $vacations['total'] + $bono14['total14bonus'] + $aguinaldo['totalchristmasbonus'] + $totalindemnizar);
			
			$this->fw_export->pdfsettlement($payrollid, $vacations, $bono14, $aguinaldo, $indemnizacion, $liquidaciontotal);
			
			if(file_exists($_SERVER['DOCUMENT_ROOT'].('/app/user_files/uploads/planillas/finiquito'.$payrollid.'.pdf'))): //Confirmar si existe el archivo de planilla.
				$payrolldata->PAYROLL_INITIALDATE 	= $employeedata->SALESMAN_COMMENCEMENT;
				$payrolldata->PAYROLL_TOTALACCRUED	= $liquidaciontotal;
				$this->fw_posts->payroll_settlement($payrolldata); //Enviar el correo electrónico.
				$this->plugin_payrolls->update(array('PAYROLL_EMAILSENT' => 'SI'), $payrollid); //Actualizar a enviado el correo electrónico.
				$this->fw_alerts->add_new_alert(3001, 'SUCCESS');
			else:
				$this->fw_alerts->add_new_alert(3002, 'ERROR');
			endif;
		endif;
		
		redirect('cms/'.strtolower($this->current_plugin));
	}
}
