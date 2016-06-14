<?php
/**
 * @author 	Guido A. Orellana
 * @name	Plugin process_gift_cards
 * @since	oct 2014
 * 
 */
class Plugin_process_gift_cards extends PL_Controller {
	
	function __construct(){
		parent::__construct();
		
		//Load the plugin data
		$this->plugin_action_table			= 'PLUGIN_GIFTCARDS';
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
		$this->plugin_display_array[2]		= "Monto a sumar";
		$this->plugin_display_array[3]		= "Nombre del cliente";
		$this->plugin_display_array[4]		= "Tel&eacute;fono del cliente";
		$this->plugin_display_array[5]		= "Email del cliente";
		
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
		$this->output->enable_profiler(FALSE);
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
		$data_array['form_html']			=  "<div class='form-group giftcard_ubn'>".form_label($this->plugin_display_array[1],'',array('class' => 'control-label col-lg-3'))."<div class='col-lg-9'>".form_input(array('name' => 'GIFT_CARD_UBC_NUMBER', 'class' => 'form-control', 'id' => 'gcubn', "autofocus" => "autofocus"))."</div></div>";
		$data_array['form_html']			.=  "<div class='form-group'><hr /></div>";
		$data_array['form_html']			.=  "<div class='form-group'>".form_label($this->plugin_display_array[2],'',array('class' => 'control-label col-lg-3'))."<div class='col-lg-9'><div class='input-group'><span class='input-group-addon'>US$.</span>".form_input(array('name' => 'GIFT_CARD_AMOUNT', 'class' => 'form-control'))."</div></div></div>";
		$data_array['form_html']			.=  "<div class='form-group'>".form_label($this->plugin_display_array[3],'',array('class' => 'control-label col-lg-3'))."<div class='col-lg-9'>".form_input(array('name' => 'GIFT_CARD_BUYER_NAME', 'class' => 'form-control'))."</div></div>";
		$data_array['form_html']			.=  "<div class='form-group'>".form_label($this->plugin_display_array[4],'',array('class' => 'control-label col-lg-3'))."<div class='col-lg-9'>".form_input(array('name' => 'GIFT_CARD_BUYER_PHONE', 'class' => 'form-control'))."</div></div>";
		$data_array['form_html']			.=  "<div class='form-group'>".form_label($this->plugin_display_array[5],'',array('class' => 'control-label col-lg-3'))."<div class='col-lg-9'>".form_input(array('name' => 'GIFT_CARD_BUYER_EMAIL', 'class' => 'form-control'))."</div></div>";
		
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
		$submit_posts['GIFT_CARD_UBC_NUMBER']	= $this->input->post('GIFT_CARD_UBC_NUMBER');
		
		//Devolver datos de la gift card
		$gift_card	= $this->gift_cards_model->get_card_data($submit_posts['GIFT_CARD_UBC_NUMBER']);
		$gift_card->GIFT_CARD_AMOUNT = (is_numeric($gift_card->GIFT_CARD_AMOUNT))?$gift_card->GIFT_CARD_AMOUNT:0;
		$GIFT_CARD_AMOUNT = ($this->input->post('GIFT_CARD_AMOUNT') + $gift_card->GIFT_CARD_AMOUNT);
		
		
		//Enviar datos para actualizar
		$submit_posts['POST_ID']				= $gift_card->ID;
		$submit_posts['GIFT_CARD_ENABLED']		= 'SI';
		//poner fecha si no se ha comprado la tarjeta
		if(empty($gift_card->GIFT_CARD_BOUGHT_DATE) && $gift_card->GIFT_CARD_ENABLED == 'NO'){
		$submit_posts['GIFT_CARD_BOUGHT_DATE']	= date('Y-m-d');
		}
		//Cambiar datos solo si no vienen vacíos
		$submit_posts['GIFT_CARD_BUYER_NAME']	= (($this->input->post('GIFT_CARD_BUYER_NAME') == ''))?$gift_card->GIFT_CARD_BUYER_NAME:$this->input->post('GIFT_CARD_BUYER_NAME');
		$submit_posts['GIFT_CARD_BUYER_PHONE']	= (($this->input->post('GIFT_CARD_BUYER_PHONE') == ''))?$gift_card->GIFT_CARD_BUYER_PHONE:$this->input->post('GIFT_CARD_BUYER_PHONE');
		$submit_posts['GIFT_CARD_BUYER_EMAIL']	= (($this->input->post('GIFT_CARD_BUYER_EMAIL') == ''))?$gift_card->GIFT_CARD_BUYER_EMAIL:$this->input->post('GIFT_CARD_BUYER_EMAIL');
		
		$submit_posts['GIFT_CARD_AMOUNT']		= (string)$GIFT_CARD_AMOUNT;
		
		return $this->_set_update_val($submit_posts);
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
		$card_ubc 			= $this->input->post('gcubn');
		$gift_card_credit 	= $this->gift_cards_model->get_card_data($card_ubc);  
		$gift_card_debit	= $this->gift_cards_model->giftcard_debits($card_ubc);
		$gift_card_amount	= $gift_card_credit->GIFT_CARD_AMOUNT - $gift_card_debit;
		
		echo $gift_card_amount;
	  }
}