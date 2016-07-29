<?php
/**
 * @author 	Guido A. Orellana
 * @name	Plugin reparaciones
 * @since	agosto 2014
 * 
 */
class Plugin_proceso_reclamo extends PL_Controller {
	
	function __construct(){
		parent::__construct();
		
		//Load the plugin data
		$this->plugin_action_table			= 'PLUGIN_RECLAIMS';
		$this->plugin_button_create			= "Crear Nuevo Registro de Reclamos";
		$this->plugin_button_cancel			= "Cancelar";
		$this->plugin_button_update			= "Guardar y Enviar Cambios";
		$this->plugin_button_denied			= "Denegar y Enviar";
		$this->plugin_button_delete			= "Eliminar";
		$this->plugin_page_title			= "Reclamos";
		$this->plugin_page_create			= "Crear Nuevo Registro de Reclamos";
		$this->plugin_page_read				= "Mostrar Registro de Reclamos";
		$this->plugin_page_update			= "Editar Registro de Reclamos";
		$this->plugin_page_delete			= "Eliminar";
		
		$this->template_display				= "plugin_proceso_reclamo"; //Si no se describe, se pone como default "plugin_display"
		$this->template_update				= "plugin_set_proceso_reclamo"; //Si no se describe, se pone como default "plugin_display"
		
		
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
		
		$this->plugin_display_array[14]		= "Etapa del proceso";
		$this->plugin_display_array[15]		= "¿Aplica Garantía?";
		$this->plugin_display_array[16]		= "Costo";
		$this->plugin_display_array[17]		= "Fecha de entrega";
		$this->plugin_display_array[18]		= "Descripción de la etapa";
		$this->plugin_display_array[19]		= "Etapa aprobada";
		
		$this->plugins_model->initialise($this->plugin_action_table);
		$this->load->model('cms/cms_plugin_reclamos', 'plugin_reclamos');
		
		
		//Extras to send
		$this->display_pagination			= TRUE; //Mostrar paginación en listado
		$this->pagination_per_page			= 10; //Numero de registros por página
		$this->pagination_total_rows		= $this->plugin_reclamos->total_rows("PROCESS_STAGE = '".(($this->uri->segment(5) == "")?'RECEPCION':$this->uri->segment(5))."'"); //Número total de items a desplegar
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
		$offset = (isset($filterArray[3]))?$filterArray[3]:0; //Obtener el primer valor a desplegar del listado
		$stage = (isset($filterArray[2]))?$filterArray[2]:'RECEPCION'; //OBTENER según el estado de pedidos 
		$search = (isset($filterArray[1]) && $filterArray[1] != 'display_all')?ltrim($filterArray[1], 0):NULL; //Busqueda especifica
		$this->pagination_total_rows = $this->plugin_reclamos->total_stage_reclaims((($this->uri->segment(5) == "")?'RECEPCION':$this->uri->segment(5)), $search); //Total de filas
		
		$result_array = array();
		$result_array = $this->plugin_reclamos->get_stage_reclaims($stage, $search, $this->pagination_per_page, $offset);
		
		
		return $this->_html_plugin_display($result_array);
	}
	
	//Función para enviar reclamo con los datos específicos
    public function plugin_update($id){
    	
		$result_array = $this->plugin_reclamos->get_reclaim($id);		
		return $this->_html_plugin_update($result_array);
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
		$data_array['header'][4]			= $this->plugin_display_array[14];
		
		//Body data
		$data_array['body'] = '';
		foreach($result_array as $field):
		$etapa								= $this->etapa($field->PROCESS_STAGE);
		$data_array['body']					.= '<tr>';
		$data_array['body']					.= '<td><a href="'.base_url('cms/'.strtolower($this->current_plugin).'/update_table_row/'.$field->ID).'">'.str_pad($field->ID, 5, 0, STR_PAD_LEFT).'</a></td>';
		$data_array['body']					.= '<td>'.$field->RECLAIM_CLIENT_NAME.'</td>';
		$data_array['body']					.= '<td>'.mysql_date_to_dmy($field->RECLAIM_DATE).'</td>';
		$data_array['body']					.= '<td><span class="label label-'.$etapa['color'].'">'.$etapa['label'].'<span></td>';
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
    
	/**
	 * Función para editar el contenido, desde aquí se especifican los campos de cierto contenido.
	 * El formulario se envía mediante objetos preestablecidos de codeigniter.
	 * El formulario se envía con un array con la clave form_html.
	 * Se puede encontrar una guía en: http://ellislab.com/codeigniter/user-guide/helpers/form_helper.html
	 * $result_data viene desde la librería PL_Controller donde para enviar un query específico se puede crear la función _plugin_update desde este controlador con el return de $this->_html_plugin_update.
	 */
	public function _html_plugin_update($result_data){
		
		//Listado del staff
		$staff = $this->plugin_reclamos->staff_list();
		
		//ID
		$data_array['data']					= new stdClass();
		$data_array['data']					= $result_data;
		$data_array['enable_action_btns']	= TRUE;
        
		//Formulario
		$data_array['form_html']			=	form_hidden('PROCESS_STAGE', $result_data->PROCESS_STAGE); 
		$data_array['submit_buttons']		= 	($result_data->PROCESS_STAGE != "FINALIZADO")?TRUE:FALSE; //Mostrar botones de actualización y envío de reclamos
		$data_array['denied_process']		= 	FALSE; //Habilitar botón para denegar proceso 
		$data_array['print_order']			= 	($result_data->PROCESS_STAGE != "RECEPCION")?TRUE:FALSE; //Habilitar botón para imprimir proceso
		
		if($result_data->PROCESS_STAGE != "REPARACION" && $result_data->PROCESS_STAGE != "FINALIZADO" && $result_data->PROCESS_STAGE != "ENTREGA"): //Si no ha llegado al proceso de reparación
		$data_array['denied_process']		= 	TRUE; //Habilitar botón para denegar proceso
		
		$data_array['form_html']			.= "<div class='form-group'>".form_label($this->plugin_display_array[15],'',array('class' => 'form-label col-lg-2'))."<div class='col-lg-10'>".form_dropdown('PROCESS_WARRANTYAVAIL', array('NO' => 'NO', 'SI' => 'SI'), array($result_data->PROCESS_WARRANTYAVAIL => $result_data->PROCESS_WARRANTYAVAIL), 'class="form-control"')."</div></div>";
		$data_array['form_html']			.= "<div class='form-group'>".form_label($this->plugin_display_array[16],'',array('class' => 'form-label col-lg-2'))."<div class='col-lg-10'><div class='input-group'><span class='input-group-addon'>Q.</span>".form_input(array('name' => 'PROCESS_COST', 'class' => 'form-control', 'value' => $result_data->PROCESS_COST))."</div></div></div>";
		$data_array['form_html']			.= "<div class='form-group'>".form_label($this->plugin_display_array[17],'',array('class' => 'form-label col-lg-2'))."<div class='col-lg-10'>".form_input(array('name' => 'PROCESS_DELIVERY_DATE', 'class' => 'form-control', 'id' => 'datetimepicker', 'data-date-format' => 'YYYY-MM-DD', 'value' => $result_data->PROCESS_DELIVERY_DATE))."</div></div>";
		$data_array['form_html']			.= "<div class='form-group'>".form_label($this->plugin_display_array[18],'',array('class' => 'form-label col-lg-2'))."<div class='col-lg-10'>".form_textarea(array('name' => 'PROCESS_DESCRIPTION', 'class' => 'form-control textarea', 'value' => $result_data->PROCESS_DESCRIPTION))."<p class='help-block'><span style='color:#F0AD4E;' class='glyphicon glyphicon-exclamation-sign'></span> Colocar la forma en que el proceso se llevar&aacute; a cabo, no escribir en forma de respuesta por correo electr&oacute;nico. En caso el reclamo no proceda con la pol&iacute;tica de garant&iacute;a, colocar la respuesta recibida por TUMI.</p></div></div>";
		
		elseif($result_data->PROCESS_STAGE != "ENTREGA"): //Si está en proceso de reparación
		$data_array['form_html']			.=($result_data->PROCESS_STAGE == "REPARACION")?"<blockquote>
													<p>
														Esta orden se encuentra en proceso de reparaci&oacute;n. 
														Al finalizar la reparaci&oacute;n f&iacute;sica, presionar el botón azul que dice \"Guardar y Enviar cambios\" para establecer la orden como finalizada y pendiente de entrega.
													</p>
												</blockquote>":
												"<blockquote><p>
												Esta orden ya fue reparada y finalizada el proceso, &uacute;nicamente est&aacute; pendiente la entrega del producto. <br />
												Para hacer entrega del reclamo seguir los siguientes pasos:
												<ol>
													<li>Imprimir el ticket de entrega con el boton celeste que dice \"Imprimir ticket de entrega\" seleccionando:
													<ul>
														<li>Si fue entrega de producto reparado o bien</li>
														<li>Si fue upgrade de producto.</li>
													</ul>
													<li>Entregar la copia al cliente para que firme de recibido.
													<li>Archivar la copia firmada</li>
													<li>Al haber obtenido la copia firmada por el cliente presionar el botón azul que dice \"Guardar y Enviar cambios\" para establecer la orden como entregada.</li>
												</ol>
												</p></blockquote>";
		else: //Si ya fue entregada
			$data_array['enable_action_btns']	= FALSE;
			if($result_data->PROCESS_APPROVED == 'NO'):
			$data_array['form_html']			.= '<div class="alert alert-danger row" role="alert">
														<div class="col-lg-9">
															<strong>Orden Denegada</strong> Esta orden no fue aceptada por el cliente el día '.mysql_date_to_dmy($result_data->PROCESS_FINISHED).'
														</div>
														<div class="col-lg-3"><a href="'.base_url('cms/'.strtolower($this->current_plugin).'/pdf_service_denied/'.$result_data->ID).'" target="_blank" class="btn btn-danger btn-block"><span class="glyphicon glyphicon-print"></span> Imprimir Entrega</a></div>
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
		$submit_posts 						= $this->input->post();
		$submit_posts['ID']					= $data_id;
		$submit_posts['PROCESS_PASSCODE'] 	= md5(uniqid("R-".str_pad($data_id, 5, "0", STR_PAD_LEFT), true));
		$submit_posts['PROCESS_FINISHED']	= date("Y-m-d");
		$submit_posts['POST_SUBMIT']		= $this->plugin_button_update;
		
		//Si el proceso 
		if($this->input->post('PROCESS_STAGE') == 'REPARACION'):
			$submit_posts['ID']					= $data_id;
			$submit_posts['PROCESS_STAGE']		= 'FINALIZADO';
			$submit_posts['PROCESS_FINISHED']	= date("Y-m-d");
			$submit_posts['POST_SUBMIT']		= $this->plugin_button_update;
			$submit_posts['PROCESS_PASSCODE']	= NULL;
		elseif($this->input->post('PROCESS_STAGE') == 'FINALIZADO'):
			$submit_posts['PROCESS_STAGE']		= 'ENTREGA';
			$submit_posts['PROCESS_PASSCODE'] 	= md5(uniqid("R-".str_pad($data_id, 5, "0", STR_PAD_LEFT), true).date('Ymd'));		
		else:
			if ($this->form_validation->run('RECLAIM_PROCESS') != FALSE):
				
				//Si el formulario a enviar procede con la garantía.
				if($this->input->post('POST_SUBMIT') == $this->plugin_button_update):
					$submit_posts['PROCESS_STAGE']	= 'APROBACION';
					//Enviar correo de aprobación
					$this->fw_posts->send_approval_process($this->input->post('RECLAIM_CLIENT_EMAIL'), $submit_posts['PROCESS_PASSCODE']);
				
				//Si el formulario a enviar no procede con la garantía.
				else:
					$submit_posts['PROCESS_STAGE']			= 'ENTREGA';
					$submit_posts['PROCESS_WARRANTYAVAIL']	= 'NO';
					$submit_posts['PROCESS_APPROVED']		= 'NO';
					$submit_posts['PROCESS_FINISHED']		= date('Y-m-d');
					$submit_posts['PROCESS_PASSCODE']		= NULL;

					$this->fw_posts->send_denied_process($this->input->post('RECLAIM_CLIENT_EMAIL'), $submit_posts['PROCESS_PASSCODE']);
				endif;
			else:
				$this->fw_alerts->add_new_alert(3004, 'ERROR');
			endif;
		endif;
		
		return $this->_set_update_val($submit_posts);
	}
	
	/**
	 * Funciones específicas del plugin
	 */
	 //VALIDAR ORDEN DE TRABAJO
	 public function vobo($uniqcode, $answer){
	 	
	 }
	/**
	 * Obtener la etapa del proceso
	 */
	 private function etapa($etapa = NULL){
	 	$etapa = (empty($etapa))?'RECEPCION':$etapa;
		
		$stage = array(
			'RECEPCION'		=> array(
								'label' => 'Recepci&oacute;n',
								'color' => 'danger'
								),
			'APROBACION'	=> array(
								'label' => 'Aprobaci&oacute;n',
								'color' => 'warning'
								),
			'REPARACION'	=> array(
								'label' => 'Reparaci&oacute;n',
								'color' => 'warning'
								),
			'FINALIZADO'	=> array(
								'label' => 'Finalizado',
								'color' => 'info'
								),
			'ENTREGA'		=> array(
								'label' => 'Entregado',
								'color' => 'success'
								)
		);
		
		return $stage[$etapa];
	 }
	 /**
	  * Impresión de cierre de proceso.
	  */
	  public function pdf_service($order){
	  	$order	= array('order' => $order);
	  	$this->load->library('FW_export', $order);
		
		//Actualizar datos de la orden
		$update_data	= array(
							'PROCESS_STAGE'							=> 'ENTREGA',
							'PROCESS_APPROVED'						=> 'SI',
							'PROCESS_PASSCODE'						=> md5(uniqid("R-".str_pad($order['order'], 5, "0", STR_PAD_LEFT), true).date('Ymd')),
							'PROCESS_FINISHED'						=> date('Y-m-d')
						);
		
		$update = $this->plugin_reclamos->update($update_data, $order['order']);
		if($update):
			return $pdf = $this->fw_export->pdf_service();
		else:
			echo "Error al tratar de actualizar.";
		endif;
	  }
	  public function pdf_service_denied($order){
	  	$order	= array('order' => $order);
	  	$this->load->library('FW_export', $order);
		
		//Actualizar datos de la orden
		$update_data	= array(
							'PROCESS_STAGE'							=> 'ENTREGA',
							'PROCESS_APPROVED'						=> 'NO',
							'PROCESS_PASSCODE'						=> NULL,
							'PROCESS_FINISHED'						=> date('Y-m-d')
						);
		
		$update = $this->plugin_reclamos->update($update_data, $order['order']);
		if($update):
			return $pdf = $this->fw_export->pdf_service_denied();
		else:
			echo "Error al tratar de actualizar.";
		endif;
	  }
	  public function pdf_upgrade($order){		
	  	//Datos de impresión
	  	$order	= array('order' => $order);
	  	$this->load->library('FW_export', $order);
		
		
		//Actualizar datos de la orden
		$update_data	= array(
							'PROCESS_STAGE'							=> 'ENTREGA',
							'PROCESS_APPROVED'						=> 'SI',
							'PROCESS_PASSCODE'						=> NULL,
							'PROCESS_FINISHED'						=> date('Y-m-d'),
							'PROCESS_UPGRADE_RECEIPT_SERIES'		=> $this->input->post('PROCESS_UPGRADE_RECEIPT_SERIES'),
							'PROCESS_UPGRADE_RECEIPT_NUMBER'		=> $this->input->post('PROCESS_UPGRADE_RECEIPT_NUMBER'),
							'PROCESS_UPGRADE_PRODUCT_CODE'			=> $this->input->post('PROCESS_UPGRADE_PRODUCT_CODE'),
							'PROCESS_UPGRADE_PRODUCT_DESCRIPTION'	=> $this->input->post('PROCESS_UPGRADE_PRODUCT_DESCRIPTION'),
							'PROCESS_UPGRADE_DISCOUNT'				=> $this->input->post('PROCESS_UPGRADE_DISCOUNT')
							);
		
		$update = $this->plugin_reclamos->update($update_data, $order['order']);
		if($update):
			$this->fw_posts->send_upgrade_info($update_data, $order['order']);
			return $pdf = $this->fw_export->pdf_upgrade($update_data);
		else:
			echo "Error al tratar de actualizar.";
		endif;
	  }
}