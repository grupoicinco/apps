<?php
class Cms_plugin_gift_cards extends MY_Model {

    public function __construct()
    {
        parent::__construct();
		$this->load->library('upload');
		$this->set_table('PLUGIN_GIFTCARDS');
		
    }
    public function initialise($current_table)
    {
        $this->_table = $current_table;
    }
    
    public function display_result(){
        $query = $this->db->get($this->_table);
        
        return $query->result();
    }
	//Obtener los datos de la gift card
	public function get_card_data($card_ubc){
		$query = $this->db->from('PLUGIN_GIFTCARDS')
				->where('GIFT_CARD_UBC_NUMBER', $card_ubc)->get();
		
		return $query->row();
	}
	//Obtener si la tarjeta existe
	public function validate_gcubn($gcubn){
		$query = $this->db->from('PLUGIN_GIFTCARDS')
				->where('GIFT_CARD_UBC_NUMBER', $gcubn)->count_all_results();
		
		return $query;
	}
	//Obtener las facturas debitadas de la gift card
	public function giftcard_debits($gcubn)
	{
		$gc		= $this->get_card_data($gcubn);
		$query 	= $this->db->select('SUM(INVOICE_AMOUNT) AS TOTAL_INVOICE_AMOUNT', FALSE)
				->from('PLUGIN_GIFTCARDS_INVOICES')
				->where('INVOICE_GIFTCARD', $gc->ID)->get()->row();
		return $query->TOTAL_INVOICE_AMOUNT;
	}
}