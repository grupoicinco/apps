<?php
/**
 * 
 */
class Recursos_humanos extends CI_Controller {
	
	function __construct() {
		parent::__construct();
		$this->load->model('plugins/rrhh_model', 'rrhh_model');		
		//Obtener el profiler del plugin
		$this->output->enable_profiler(TRUE);
	}
	
	public function index(){
			
	}
	public function formulario_disc($validation = NULL, $id = NULL){
		$this->load->library('form_validation');

		//Obtener datos del usuario
		if(!empty($validation) && !empty($id)):
			$userdata			= $this->rrhh_model->plugin_disc_tests($validation, $id);
			$data['questions'] 	= $this->rrhh_model->disc_form();
			$data['userdata']	= $userdata;
			$data['passcode']	= $validation;
			$data['testid']		= $id;	
			
			//Validar el formulario
			//Div con errores
			$this->form_validation->set_error_delimiters('<p class="text-danger">', '</p>');
			foreach($data['questions'] as $i => $groups):
			$this->form_validation->set_rules('MASDISC['.$i.']', 'Mas se identifica de la pregunta '.$i, 'required');
			$this->form_validation->set_rules('MENOSDISC['.$i.']', 'Menos se identifica de la pregunta '.$i, 'required');
			endforeach;
			
			if(empty($userdata)):
				$data['disabled']		= 'disabled="disabled"';
				$data['error_message']	= '<div class="alert alert-danger" role="alert"><p><strong>Error:</strong><br /> No tienes permitido enviar el formulario debido a alguna de las siguientes razones:</p><ul><li>Tu usuario no ha sido validado por administraci&oacute;n para llevar a cabo la prueba.</li><li>El c&oacute;digo ha expirado.</li><li>El enlace no es v&aacute;lido.</li></ul><p>Escribe a reclutamiento@grupoi5.com para mas informaci&oacute;n.</p></div>';
			else:
				$data['disabled']		= '';
				$data['error_message']	= '';
			endif;
			if($this->form_validation->run() == FALSE):
				$this->load->template('formulario_disc', $data);
			else:
				$updatedata['TEST_MASANSWERS'] 	= json_encode($this->input->post('MASDISC'));
				$updatedata['TEST_MENOSANSWERS'] 	= json_encode($this->input->post('MENOSDISC'));
				$expiration_date 			= strtotime ( '+1 year' , strtotime (date('Y-m-d')) ) ;
				$expiration_date 			= date ( 'Y-m-d' , $expiration_date);
				$updatedata['TEST_EXPDATE']		= $expiration_date;
				
				$this->rrhh_model->update($updatedata, $id, NULL, 'PLUGIN_DISC_TESTS');
				
				$this->load->template('formulario_disc_response');
			endif;
		endif;
	}
	
	/**
	 * Obtener información según pruebas disc
	 */
	 public function disc_result($passcode, $id){
	 	//Obtener la información de la prueba
	 	$disc_data					= $this->rrhh_model->plugin_disc_tests($passcode, $id);
		//Convertir respuestas en array
		$mas						= json_decode($disc_data->TEST_MASANSWERS, TRUE);
		$menos						= json_decode($disc_data->TEST_MENOSANSWERS, TRUE);
		
		//Obtener número de valores según si son t,c,e,z
		$typemas					= array_count_values($mas);
		$typemenos					= array_count_values($menos);
		
		//Obtener valores del segmento
		$segment					= array(
										"DECISION"	=>	$typemas["z"] - $typemenos["z"],
										"INFLUENCE"	=>	$typemas["c"] - $typemenos["c"],
										"SERENITY"	=>	$typemas["t"] - $typemenos["t"],
										"COMPLIANCE"=>	$typemas["e"] - $typemenos["e"]
										);
		//Obtener valores de la pauta
		foreach($segment as $type => $value):
			$val					= $this->rrhh_model->pauta_data($type, $value);
			$pauta[]				= $val->PAUTADATA_VALUE;
		endforeach;
		
		//Obtener el valor total de pauta
		$pauta_number				= ($pauta[0]*1000+$pauta[1]*100+$pauta[2]*10+$pauta[3]);
		
		//Obtener la personalidad
		$personality				= $this->rrhh_model->pauta_personality($pauta_number);
		$pauta_personality			= $personality->PERSONALITY_DESCRIPTION;
		$pauta_code					= $personality->PERSONALITY_CODE;
		
		//Obtener las descripciones
	  	$personalities				= array('EMOTIONS', 'GOAL', 'JUDGE', 'INFLUENCE', 'VALUE', 'ABUSE', 'PRESSION', 'FEAR', 'EFFICIENT');
		$descriptions				= $this->rrhh_model->disc_descriptions($pauta_code, $personalities);
		
		echo "Mas:<pre>";
		print_r($typemas);
		echo "</pre><br />";
		echo "Menos:<pre>";
		print_r($typemenos);
		echo "</pre><br />";
		echo "Segmento:<pre>";
		print_r($segment);
		echo "</pre><br />";
		echo "Pauta:<pre>";
		print_r($pauta);
		echo "</pre><br />";
		echo $pauta_number;
		echo "<br />";
		echo $pauta_personality."($pauta_code)";
		echo "<br />";
		echo "Descripciones:<pre>";
		print_r($descriptions);
		echo "</pre><br />";
	 }
}
