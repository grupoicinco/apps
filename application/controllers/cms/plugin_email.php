<?php
/**
 * @author 	Guido A. Orellana
 * @name	Plugin_contenidos
 * @since	abril 2013
 * 
 */
class Plugin_email extends PL_Controller {
	
	function __construct(){
		parent::__construct();
		
		//Load the plugin data
		$this->plugin_action_table			= 'PLUGIN_EMAIL';
		$this->plugin_button_create			= "Crear Nuevo Email";
		$this->plugin_button_cancel			= "Cancelar";
		$this->plugin_button_update			= "Guardar Cambios";
		$this->plugin_button_delete			= "Eliminar";
		$this->plugin_page_title			= "Emails";
		$this->plugin_page_create			= "Crear Nuevo Email";
		$this->plugin_page_read				= "Mostrar Email";
		$this->plugin_page_update			= "Editar Email";
		$this->plugin_page_delete			= "Eliminar";
		
		$this->template_display				= "plugin_email"; //Si no se describe, se pone como default "plugin_display"
		$this->template_create				= NULL;
		$this->template_update				= NULL;
		
		$this->plugin_image_route			= "/user_files/uploads/images/";
		$this->plugin_display_array[0]		= "ID";
		$this->plugin_display_array[1]		= "Imagen del encabezado";
		$this->plugin_display_array[2]		= "Nombre de la empresa";
		$this->plugin_display_array[3]		= "Tel&eacute;fono de la empresa";
		$this->plugin_display_array[4]		= "Email de la empresa";
		
		$this->plugins_model->initialise($this->plugin_action_table);
		
		//Extras to send
		$this->display_pagination			= FALSE; //Mostrar paginación en listado
		$this->pagination_per_page			= 0; //Numero de registros por página
		$this->pagination_total_rows		= 0; //Número total de items a desplegar
		
		$this->display_filter				= FALSE; //Mostrar filtro de búsqueda 'SEARCH' o según listado 'LIST' o no mostrar FALSE
		
		//Obtener el profiler del plugin
		$this->output->enable_profiler(FALSE);
	}
	
	/**
	 * Funciones para editar Querys o Datos a enviar desde cada plugin
	 */
	//Función para desplegar listado, desde aquí se puede modificar el query
	public function _plugin_display($filterArray){
		return $this->_html_plugin_display();
	}
	
	/**
	 * Función para desplegar listado completo de datos guardados, enviar los títulos en array con clave header y el cuerpo en un array con clave body.
	 * Para editar fila es a la función 'update_table_row'
	 * 
	 * @param	$result_array 		array 		Array con la listado devuelto por query de la DB
	 * @return	$data_array 		array 		Arreglo con la información del [header] y [body]
	 * 											Envía el key [filteroptions] con las opciones de un filtro en listado y [currentFilter] con el resultado actual.
	 */
	public function _html_plugin_display(){
		
		//Formulario con datos generales de los correos
		$data_array['email_resource'] 	= form_open_multipart('cms/'.strtolower($this->current_plugin).'/post_update_resource/', array('class' => 'form-horizontal col-lg-8'));
		
		$data_array['email_resource']	.= "<div class='form-group'>".form_label($this->plugin_display_array[1],'',array('class' => 'control-label col-lg-4'))."<div class='col-lg-8'>".'<div class="row"><div class="col-lg-5">'.form_upload(array('name' => 'EMAIL_HEADER')).'</div><div class="col-lg-7" ><span class="thumbnail"><img class="media-object" src="'.base_url($this->fw_resource->request('RESOURCE_EMAIL_IMAGE_ROUTE')).'" alt=""></span></div></div>'."</div></div>";
		$data_array['email_resource']	.= "<div class='form-group'>".form_label($this->plugin_display_array[2],'',array('class' => 'control-label col-lg-4'))."<div class='col-lg-8'>".form_input(array('name' => 'RESOURCE_COMPANY_NAME', 'value' => $this->fw_resource->request('RESOURCE_COMPANY_NAME'), 'class' => 'form-control'))."</div></div>";
		$data_array['email_resource']	.= "<div class='form-group'>".form_label($this->plugin_display_array[3],'',array('class' => 'control-label col-lg-4'))."<div class='col-lg-8'>".form_input(array('name' => 'RESOURCE_COMPANY_PHONE', 'value' => $this->fw_resource->request('RESOURCE_COMPANY_PHONE'), 'class' => 'form-control'))."</div></div>";
		$data_array['email_resource']	.= "<div class='form-group'>".form_label($this->plugin_display_array[4],'',array('class' => 'control-label col-lg-4'))."<div class='col-lg-8'>".form_input(array('name' => 'RESOURCE_CONTACT_EMAIL', 'value' => $this->fw_resource->request('RESOURCE_CONTACT_EMAIL'), 'class' => 'form-control'))."</div></div>";
		
		$data_array['email_resource']	.= '<div class="form-actions">';
		$data_array['email_resource']	.= form_submit(array('value' => $this->plugin_button_update, 'class' => 'btn btn-primary', 'name' => 'POST_SUBMIT')).' ';
		$data_array['email_resource']	.= anchor('cms/'.strtolower($this->current_plugin), $this->plugin_button_cancel, array('class'=>'btn btn-default')).' ';
		$data_array['email_resource']	.= '</div>';
		//Header data
		$data_array['header'][1]			= $this->plugin_display_array[1];
		
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
        
		//Datos fijos
		$data_array['page_title'] 			= $this->plugin_page_title;
		$data_array['page_subtitle']		= $this->plugin_button_create;
		
		//Datos a enviar
		$options_values_array 				= $this->display_cateogries_array();
		
		//Formulario
		$data_array['form_html']			=  form_open('cms/'.strtolower($this->current_plugin).'/post_new_val', array('class' => 'form-horizontal'));
		
		$data_array['form_html']			.= "<div class='form-group'>".form_label($this->plugin_display_array[1],'',array('class' => 'control-label col-lg-2'))."<div class='col-lg-10'>".form_input(array('name' => 'CATEGORY_NAME', 'class' => 'form-control'))."</div></div>";
		$data_array['form_html']			.= "<div class='form-group'>".form_label($this->plugin_display_array[2],'',array('class' => 'control-label col-lg-2'))."<div class='col-lg-10'>".form_dropdown('CATEGORY_PARENT', $options_values_array)."</div></div>";
		
		$data_array['form_html']			.= '<div class="form-actions">'.form_submit(array('value' => $this->plugin_button_create, 'class' => 'btn btn-primary', 'name' => 'POST_SUBMIT')).' '.anchor('cms/'.strtolower($this->current_plugin), $this->plugin_button_cancel, array('class'=>'btn')).'</div>';
		
		return $data_array;
    }
	public function _html_plugin_update($result_data){
		
		//Datos fijos
		$data_array['page_title'] 			= $this->plugin_page_title;
		$data_array['page_subtitle']		= $this->plugin_page_update;
		
		//Datos a enviar
		$options_values_array 				= $this->display_cateogries_array();
		
		
		//Formulario
		$data_array['form_html']			=  form_open('cms/'.strtolower($this->current_plugin).'/post_update_val/'.$result_data->ID, array('class' => 'form-horizontal'));
		
		$data_array['form_html']			.= "<div class='form-group'>".form_label($this->plugin_display_array[1],'',array('class' => 'control-label col-lg-2'))."<div class='col-lg-10'>".form_input(array('name' => 'CATEGORY_NAME', 'value' => $result_data->CATEGORY_NAME, 'class' => 'form-control'))."</div></div>";
		$data_array['form_html']			.= "<div class='form-group'>".form_label($this->plugin_display_array[2],'',array('class' => 'control-label col-lg-2'))."<div class='col-lg-10'>".form_dropdown('CATEGORY_PARENT', $options_values_array, $result_data->CATEGORY_PARENT)."</div></div>";
		
		$data_array['form_html']			.= '<div class="form-actions">';
		$data_array['form_html']			.= form_submit(array('value' => $this->plugin_button_update, 'class' => 'btn btn-primary', 'name' => 'POST_SUBMIT')).' ';
		$data_array['form_html']			.= form_submit(array('value' => $this->plugin_button_delete, 'class' => 'btn btn-danger', 'name' => 'POST_SUBMIT')).' ';
		$data_array['form_html']			.= anchor('cms/'.strtolower($this->current_plugin), $this->plugin_button_cancel, array('class'=>'btn')).' ';
		$data_array['form_html']			.= '</div>';
		
		return $data_array;
	}
	
	//Funciones de los posts a enviar
	public function post_new_val(){
		$submit_posts 				= $this->input->post();
		
		return $this->_set_new_val($submit_posts);
	}
	public function post_update_val($data_id){
		$submit_posts 				= $this->input->post();
		$submit_posts['ID']			= $data_id;
		
		return $this->_set_update_val($submit_posts);
	}
	
	/**
	 * Funciones específicas del plugin
	 */
	public function post_update_resource(){
		if(!$this->input->post()):
			$this->fw_alerts->add_new_alert(9990, 'ERROR');
		else:
			//Si se carga una imagen
			if(!empty($_FILES["EMAIL_HEADER"]["name"])):
				$upload_config['upload_path'] 		= '.'.$this->plugin_image_route;
				$upload_config['overwrite']			= TRUE;
				$upload_config['allowed_types'] 	= 'gif|jpg|png';
				$upload_config['max_width']  		= '550';
				$upload_config['max_height'] 		= '60';
				$this->upload->initialize($upload_config);
				
				if (!$this->upload->do_upload('EMAIL_HEADER')):
					$this->fw_alerts->add_new_alert(4002, 'ERROR');
				else:
					$uploaded_data = $this->upload->data();
					$update_data['RESOURCE_EMAIL_IMAGE_ROUTE'] = $this->plugin_image_route.$uploaded_data['file_name'];
					$this->fw_alerts->add_new_alert(4001, 'SUCCESS');
				endif;
			endif;
			
			//Enviar los datos a actualizar
			$update_data['RESOURCE_COMPANY_NAME']		= $this->input->post('RESOURCE_COMPANY_NAME');
			$update_data['RESOURCE_COMPANY_PHONE']		= $this->input->post('RESOURCE_COMPANY_PHONE');
			$update_data['RESOURCE_CONTACT_EMAIL']		= $this->input->post('RESOURCE_CONTACT_EMAIL');
			$alert_key = $this->plugins_model->update_email_data($update_data); //Actualizar la información de los correos
			$this->fw_alerts->add_new_alert($alert_key); //Mostrar el resultado de la actualización
			
			redirect('cms/plugin_email');
		endif;
	}
}
