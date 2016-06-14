<?php
/**
 * Cron Job to inform late order tickets.
 */
class Cron extends CI_Controller {
	
	function __construct() {
		parent::__construct();
		$this->load->model('cms/cms_plugin_reclamos', 'plugin_reclamos');
		$this->load->library('FW_posts');
	}
	public function inform(){
		//Días para envío de correo según días para la fecha de entrega
		$days = array(8,5,3,1);
		
		
		$open_order = $this->plugin_reclamos->pending_reclaims(date('Y-m-d'), FALSE); //Obtiene los reclamos pendientes de entrega.
		//Obtener los días que lleva atrasada la entrega
		foreach($open_order as $order):
			//Obtener la diferencia entre el día actual y el día de entrega
			$datetime2 = date_create(date('Y-m-d'));
			$datetime1 = date_create($order->PROCESS_DELIVERY_DATE);
			$interval = date_diff($datetime1, $datetime2);
			//Mostrar los días atrasados desde que se debía haber realizado la entrega.
			$order->PROCESS_DELIVERY_DATE_DIF = '<span style="background-color: #d9534f;border-radius: 0.25em;color: #fff;display: inline;font-size: 75%;font-weight: 700;line-height: 1;padding: 0.2em 0.6em 0.3em;text-align: center;vertical-align: baseline;white-space: nowrap;">'.$interval->format('%a días').' ATRASADOS!</span>';
			$order_array[] = $order;
		endforeach;
		//Obtener los días faltantes para la entrega
		foreach($days as $day):
			$fecha = date_create(date('Y-m-d'));
			date_add($fecha, date_interval_create_from_date_string($day.' days'));
			$order_date = date_format($fecha, 'Y-m-d');		
			
			$open_order = $this->plugin_reclamos->pending_reclaims($order_date, TRUE);
			foreach($open_order as $order):
				$datetime1 = date_create(date('Y-m-d'));
				$datetime2 = date_create($order->PROCESS_DELIVERY_DATE);
				$interval = date_diff($datetime1, $datetime2);
				$order->PROCESS_DELIVERY_DATE_DIF = '<span style="background-color: #f0ad4e;border-radius: 0.25em;color: #fff;display: inline;font-size: 75%;font-weight: 700;line-height: 1;padding: 0.2em 0.6em 0.3em;text-align: center;vertical-align: baseline;white-space: nowrap;">'.$interval->format('%a días').' para la entrega.</span>';
				$order_array[] = $order;
			endforeach;
			
		endforeach;
		/*
		echo "<pre>";
		print_r($order_array);
		echo "</pre>";*/
		$this->fw_posts->delayed_orders($order_array);
	}
}
