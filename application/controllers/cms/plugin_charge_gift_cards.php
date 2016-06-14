<?php
/**
 * @author 	Guido A. Orellana
 * @name	Plugin process_gift_cards
 * @since	oct 2014
 * 
 */
class Plugin_charge_gift_cards extends PL_Controller {
	
	function __construct(){
		parent::__construct();
		
		//Load the plugin data
		$this->plugin_action_table			= 'PLUGIN_GIFTCARDS_INVOICES';
		$this->plugin_button_create			= "Crear Nuevo Registro de Gift Card";
		$this->plugin_button_cancel			= "Cancelar";
		$this->plugin_button_update			= "Guardar y Enviar Cambios";
		$this->plugin_button_delete			= "Eliminar";
		$this->plugin_page_title			= "Gift Cards";
		$this->plugin_page_create			= "Crear Nuevo Registro de Gift Card";
		$this->plugin_page_read				= "Mostrar Registro de Gift Card";
		$this->plugin_page_update			= "Editar Registro de Gift Card";
		$this->plugin_page_delete			= "Eliminar";
		
		$this->template_display				= "plugin_set_gift_cards"; //Si no se describe, se pone como default "plugin_display"
		
		$this->plugin_display_array[0]		= "ID";
		$this->plugin_display_array[1]		= "Número de gift card";
		$this->plugin_display_array[2]		= "Serie de la factura";
		$this->plugin_display_array[3]		= "Número de Factura";
		$this->plugin_display_array[4]		= "Monto a debitar";
		
		$this->plugins_model->initialise($this->plugin_action_table);
		$this->load->model('cms/cms_plugin_gift_cards', 'gift_cards_model');
		
		//Extras to send
		$this->display_pagination			= FALSE; //Mostrar paginación en listado
		$this->pagination_per_page			= 10; //Numero de registros por página
		$this->pagination_total_rows		= FALSE; //Número total de items a desplegar
		$this->uri_segment					= 6;
		$this->base_url						= base_url('cms/'.strtolower($this->current_plugin).'/index/'.(($this->uri->segment(4) == "")?'display_all':$this->uri->segment(4)).'/'.(($this->uri->segment(5) == "")?'RECEPCION':$this->uri->segment(5)).'/');
		
		$this->display_filter				= FALSE; //Mostrar filtro de búsqueda 'SEARCH' o según listado 'LIST' o no mostrar FALSE
		
		$this->enable_action_btns 			= TRUE; //Mostrar los botones del formulario
		
		//Obtener el profiler del plugin
		$this->output->enable_profiler(TRUE);
	}
	
	/**
	 * Funciones para editar Querys o Datos a enviar desde cada plugin
	 */
	//Función para desplegar listado, desde aquí se puede modificar el query
	public function _plugin_display($filterArray){
		//Variables a enviar
		
		return $this->_html_plugin_create();
	}
	
	/**
	 * Función para desplegar listado completo de datos guardados, enviar los títulos en array con clave header y el cuerpo en un array con clave body.
	 * Para editar fila es a la función 'update_table_row'
	 * 
	 * @param	$result_array 		array 		Array con la listado devuelto por query de la DB
	 * @return	$data_array 		array 		Arreglo con la información del header y body
	 */
	public function _html_plugin_display($result_array){
		
		return NULL;
	}
	
	/*
	 * Función para crear nuevo contenido, desde aquí se especifican los campos a enviar en el formulario.
	 * El formulario se envía mediante objectos preestablecidos de codeigniter. 
	 * El formulario se envía con un array con la clave form_html.
	 * Se puede encontrar una guía en: http://ellislab.com/codeigniter/user-guide/helpers/form_helper.html
	 */
	public function _html_plugin_create(){
		
        
		//Formulario
		$data_array['form_html']			=  "<div class='form-group giftcard_ubn'>".form_label($this->plugin_display_array[1],'',array('class' => 'control-label col-lg-3'))."<div class='col-lg-9'>".form_input(array('name' => 'INVOICE_GIFTCARD', 'class' => 'form-control', 'id' => 'gcubn', "autofocus" => "autofocus"))."</div></div>";
		$data_array['form_html']			.=  "<div class='form-group'><hr /></div>";
		$data_array['form_html']			.=  "<div class='form-group'>".form_label($this->plugin_display_array[2],'',array('class' => 'control-label col-lg-3'))."<div class='col-lg-9'>".form_dropdown('INVOICE_SERIAL', array('A1' => 'A1 - Factura impresa en ticket', 'A0' => 'A0 - Factura para llenar a mano'), 'A1', 'class="form-control"')."</div></div>";
		$data_array['form_html']			.=  "<div class='form-group'>".form_label($this->plugin_display_array[3],'',array('class' => 'control-label col-lg-3'))."<div class='col-lg-9'>".form_input(array('name' => 'INVOICE_NUMBER', 'class' => 'form-control'))."</div></div>";
		$data_array['form_html']			.=  "<div class='form-group'>".form_label($this->plugin_display_array[4],'',array('class' => 'control-label col-lg-3'))."<div class='col-lg-9'><div class='input-group'><span class='input-group-addon'>US$.</span>".form_input(array('name' => 'INVOICE_AMOUNT', 'class' => 'form-control'))."</div></div></div>";
		
		return $data_array;
    }
	public function _html_plugin_update($result_data){
		
		return NULL;
	}
	
	//Funciones de los posts a enviar
	public function post_new_val(){
		$submit_posts 					= $this->input->post();
		
		return $this->_set_new_val($submit_posts);
	}
	public function post_update_val(){
		$submit_posts 							= $this->input->post();
		
		//Devolver datos de la gift card
		$gift_card	= $this->gift_cards_model->get_card_data($this->input->post('INVOICE_GIFTCARD'));
		$submit_posts['INVOICE_GIFTCARD']		= $gift_card->ID;
		
		//Enviar datos para agregar
		if(!empty($submit_posts['INVOICE_SERIAL']) && !empty($submit_posts['INVOICE_NUMBER']) && !empty($submit_posts['INVOICE_AMOUNT']) && (($gift_card->GIFT_CARD_AMOUNT - $this->gift_cards_model->giftcard_debits($this->input->post('INVOICE_GIFTCARD'))) >= $submit_posts['INVOICE_AMOUNT'])):
			return $this->_set_new_val($submit_posts);
		else:
			$this->fw_alerts->add_new_alert(4011, 'ERROR');
        
            redirect('cms/'.strtolower($this->current_plugin));
		endif;
	}
	
	/**
	 * Funciones específicas del plugin
	 */
	 public function validate_gcubn(){
	 	$gcubn = $this->input->post('gcubn');
	 	echo $this->gift_cards_model->validate_gcubn($gcubn);
	 }
	 /**
	  * Get Gift Card balance
	  */
	  public function get_card_balance()
	  {
		$card_ubc 	= $this->input->post('gcubn');
		$gift_card 	= $this->gift_cards_model->get_card_data($card_ubc);  
		
		echo $gift_card->GIFT_CARD_AMOUNT;
	  }
	 
}