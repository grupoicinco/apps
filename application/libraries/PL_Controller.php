<?php
/**
 * Controller for the plugins
 */
class PL_Controller extends MY_Controller {
	
	var $pagination_total_rows;
	var $plugin_single_id;
	var $upload_route;
	function __construct() {
		parent::__construct();
		
		//Load libraries
		$this->load->library('pagination'); //Paginación
		$this->load->library('form_validation'); //Validar formulario
		
		//Load the model
        $ci = get_instance();
        $this->current_plugin = get_class($ci);
        $this->load->model('cms/cms_plugin_model', 'plugins_model');
		
		$this->pagination_total_rows		= 200; //Número total de items a desplegar
		$this->uri_segment					= 5;
		
		//Set validation characteristics
		$this->form_validation->set_error_delimiters("<div class='alert alert-danger fade in'>", '<a class="close" data-dismiss="alert" href="#" aria-hidden="true">&times;</a></div>');
		
	}
	
	//MODEL controllers
	public function _plugin_display_query($query){
		/* --- Desde aquí se recibe el query y se manejan datos extra a enviar. ---*/
		$this->pagination_total_rows		= count($query);
		
		return $this->_html_plugin_display($query);
	}
    private function _plugin_create(){    
        return $this->_html_plugin_create();
    }
    public function _plugin_update($action_ID){
    			
    	//Obtener datos a actualizar
        $data['COLUMN_VAR'] = $action_ID;
        $result_obj = $this->plugins_model->get_single_row($data);
				
		//Caracteres ISO 8859-1 
		foreach($result_obj as $inputName => $inutValue):
			$result_obj->$inputName = ascii_to_entities(html_entity_decode($inutValue));
		endforeach;
			
        return $this->_html_plugin_update($result_obj);
    }
	
	//CRUD DISPLAY
	public function index(){
		$plugin_segments				= $this->uri->segment_array(); unset($plugin_segments[1]); unset($plugin_segments[2]); $plugin_segments = array_values($plugin_segments);
		$data_array 					= $this->_plugin_display($plugin_segments); //Obtener datos a desplegar
		$data_array['page_title']		= $this->plugin_page_title;//Título de la página
		$data_array['create_new_row']	= $this->plugin_button_create; //Botón para crear nuevo valor
		$data_array['current_plugin']	= strtolower($this->current_plugin); //Obtener el plugin actual
		$data_array['header']			= (!empty($data_array['header']))?$data_array['header']:array(); //Confirmar que se estén enviando datos del header
		$data_array['body']				= (!empty($data_array['body']))?$data_array['body']:''; //Confirmar envío de datos del body
		$data_array['pagination']		= $this->pagination->create_links(); //Mostrar o no mostrar paginación en el plugin
		$template_view					= (!empty($this->template_display))?$this->template_display:'plugin_display';
		
		$data_array['header'][0]		= $this->plugin_display_array[0];
		
		//Display de plugins del FW
		if($this->display_pagination == TRUE):
			$display_filter						= ($this->uri->segment(4) == '')?'display_all':$this->uri->segment(4);
			$config = array(
						'base_url'			=> (empty($this->base_url))?base_url('cms/'.strtolower($this->current_plugin).'/index/'.$display_filter.'/'):$this->base_url,
						'total_rows'		=> $this->pagination_total_rows,
						'per_page'			=> $this->pagination_per_page,
						'uri_segment'		=> $this->uri_segment
					);
			$this->pagination->initialize($config);
			$data_array['pagination']		= $this->pagination->create_links();
		endif;
		
		$this->load->templatecms('cms/'.$template_view, $data_array);
	}
    public function create_new_row(){
    	//Get template
		$template_view					= (!empty($this->template_create))?$this->template_create:'plugin_create';
		
		$plugin_html 					= $this->_plugin_create(); //Campos del formulario
		$plugin_html['extra_form']		= (isset($plugin_html['extra_form']))? $plugin_html['extra_form']:NULL;	
		
		//Si es el template default, se envían los campos default
		if($template_view == 'plugin_create'):
		//Apertura del formulario
		$view_data['form_html']			= form_open_multipart('cms/'.strtolower($this->current_plugin).'/post_new_val', array('class' => 'form-horizontal col-lg-9', 'role' => 'form'));
		$view_data['form_html']			.=  "<div class='row'><div class='col-lg-12 col-md-12 col-sm-12'>".validation_errors()."</div></div>";
		//Campos del formulario
		$view_data['form_html']			.= $plugin_html['form_html'];
		//Botones del formulario
		$view_data['form_html']			.= '<div class="form-actions">'.form_submit(array('value' => $this->plugin_button_create, 'class' => 'btn btn-primary', 'name' => 'POST_SUBMIT')).' '.anchor('cms/'.strtolower($this->current_plugin), $this->plugin_button_cancel, array('class'=>'btn btn-default')).'</div>';
		//Formulario extra a enviar
		$view_data['form_html']			.= $plugin_html['extra_form'];
		//Si no es el template default, se envían los parámetros establecidos en la función del plugin
		else:
		$view_data						= $plugin_html;
		endif;
		
		$view_data['page_title'] 		= $this->plugin_page_title;
		$view_data['page_subtitle']		= $this->plugin_page_create;
		
        $this->load->templatecms('cms/'.$template_view, $view_data);
    }
    public function update_table_row($action_ID){
    	//Get template
		$template_view					= (!empty($this->template_update))?$this->template_update:'plugin_create';
    	
		$plugin_html					= $this->_plugin_update($action_ID); //campos del formulario a desplegar
		$plugin_html['extra_form']		= (isset($plugin_html['extra_form']))? $plugin_html['extra_form']:NULL;	
		
		//Si es el template default, se envían los campos default
		if($template_view == 'plugin_create'):
		//Apertura del formulario
		$view_data['form_html']			=  form_open_multipart('cms/'.strtolower($this->current_plugin).'/post_update_val/'.$action_ID, array('class' => 'form-horizontal', 'role' => 'form'));
		$view_data['form_html']			.=  "<div class='row'><div class='col-lg-12 col-md-12 col-sm-12'>".validation_errors()."</div></div>";
		//Campos del formulario
		$view_data['form_html']			.= form_hidden('POST_ID', $action_ID);
		$view_data['form_html'] 		.= $plugin_html['form_html'];
		//Botones del formulario
		$view_data['form_html']			.= '<div class="form-actions">';
		$view_data['form_html']			.= form_submit(array('value' => $this->plugin_button_update, 'class' => 'btn btn-primary', 'name' => 'POST_SUBMIT')).' ';
		$view_data['form_html']			.= form_submit(array('value' => $this->plugin_button_delete, 'class' => 'btn btn-danger', 'name' => 'POST_SUBMIT')).' ';
		$view_data['form_html']			.= anchor('cms/'.strtolower($this->current_plugin), $this->plugin_button_cancel, array('class'=>'btn btn-default')).' ';
		$view_data['form_html']			.= '</div>';
		//Formulario extra a enviar
		$view_data['form_html']			.= $plugin_html['extra_form'];
		//Si no es el template default, se envían los parámetros establecidos en la función del plugin
		else:
		$view_data						= $plugin_html;
		endif;
		
		$view_data['page_title'] 		= $this->plugin_page_title;
		$view_data['page_subtitle']		= $this->plugin_page_update;
		
        $this->load->templatecms('cms/'.$template_view, $view_data);
    }
	
	//POST VALUES IN DATABASE
	    public function _set_new_val($form_posts){
        if($this->input->post()){
        	
            unset($form_posts['POST_SUBMIT']);
			unset($form_posts['_wysihtml5_mode']);
            unset($form_posts['galleryImgs']);
			
			//post values with accentuation
			foreach($form_posts as $name => $post):
				$form_posts[$name] = ascii_to_entities($post);
			endforeach;
        
            $this->plugins_model->insert($form_posts);
			$this->fw_alerts->add_new_alert(4013, 'SUCCESS');
        
            redirect('cms/'.strtolower($this->current_plugin));
        }
    }
    public function _set_update_val($form_posts){
        
        if($this->input->post()){
			
        	//Obtener el ID del post
        	$post_id = $form_posts['POST_ID'];
            unset($form_posts['POST_ID']); //Remover el post
			
			//post values with accentuations
			foreach($form_posts as $name => $post):
				$form_posts[$name] = (!is_numeric($post))?ascii_to_entities($post):$post;
			endforeach;
            switch ($form_posts['POST_SUBMIT']) {
				
                //Set to Update
                case $this->plugin_button_update:
                    unset($form_posts['POST_SUBMIT']);
					unset($form_posts['_wysihtml5_mode']);
		            unset($form_posts['galleryImgs']);
                    $this->plugins_model->update($form_posts, $post_id);
					$this->fw_alerts->add_new_alert(4010, 'SUCCESS');
                    redirect('cms/'.strtolower($this->current_plugin));
                    break;
                
                //Set to delete
                case $this->plugin_button_delete:
                    $this->plugins_model->delete($post_id);
					$this->fw_alerts->add_new_alert(4012, 'SUCCESS');
                    redirect('cms/'.strtolower($this->current_plugin));
                    break;
            }
        }
    }
	//Función para procesar las búsquedas
	public function search_filter_redirect(){
		if($this->input->post()){
			$search_val			= $this->input->post('SEARCH');
			redirect($this->config->site_url('cms/'.strtolower($this->current_plugin).'/index/'.convert_accented_characters($search_val)));
		}
	}
}
