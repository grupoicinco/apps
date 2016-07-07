<?php
/**
 * Todos los posts de los diferentes formularios
 */
class FW_posts {
	
	var $current_website;
	var $company;
	var $tel;
	var $contact_email;
	var $top_image_route;
	var $FW;
	function __construct() {
		
		$this->FW						=& get_instance();
		$this->FW->load->library('email');
		
		//Datos generales del sitio
		$this->current_website			= $_SERVER['HTTP_HOST'];
		$this->company					= $this->FW->fw_resource->request('RESOURCE_COMPANY_NAME');
		$this->tel						= $this->FW->fw_resource->request('RESOURCE_COMPANY_PHONE');
		$this->contact_email			= $this->FW->fw_resource->request('RESOURCE_CONTACT_EMAIL');
		$this->email_header				= $this->FW->fw_resource->request('RESOURCE_EMAIL_IMAGE_ROUTE');
		
		$this->top_image_route			= base_url($this->email_header);
		
		//Validar formularios
		$this->FW->load->library('user_agent');
	}
	
	/**
	 * Funciones de cada post
	 */
	//Enviar formulario de asistencia
	public function assistance_request_post(){
		
		if($this->FW->input->post()):
			//Establecer parámetros
			$this->FW->email->from($this->FW->input->post('inputEmail'), $this->FW->input->post('inputName'));
			$this->FW->email->to('g.orellana.huelva@gmail.com'); 
			$this->FW->email->cc($this->FW->input->post('inputEmail'));
				
			$this->FW->email->subject('Correo solicitando asistencia');
			$html_body = array(
							array(
								'LABEL' 	=> 'Nombre',
								'POSTVAL'	=> $this->FW->input->post('inputName')
							),
							array(
								'LABEL' 	=> 'Email',
								'POSTVAL'	=> $this->FW->input->post('inputEmail')
							),
							array(
								'LABEL' 	=> 'Necesita Asistencia en:',
								'POSTVAL'	=> $this->FW->input->post('inputMessage')
							)
						);
			$html_message = $this->_html_body_template($this->current_website, $html_body, $this->company, $this->tel, $this->contact_email);
			$this->FW->email->message($html_message);
			$this->FW->email->send();
		endif;
	}
	//Enviar correo para aprobar reclamo
	public function send_approval_process($to, $passcode){
		if($this->FW->input->post()):
			//Establecer parámetros
			$this->FW->email->from('servicio@tumi.com.gt','Reclamos TUMI Guatemala');
			$this->FW->email->to($to);
				
			$this->FW->email->subject('Aprobación de orden R-'.str_pad($this->FW->input->post('POST_ID'), 5, "0", STR_PAD_LEFT));
			$html_body = array(
							array(
								'LABEL' 	=> NULL,
								'POSTVAL'	=> "Este correo es para solicitar su aprobaci&oacute;n para poder proceder con el reclamo de la orden Num. R-".str_pad($this->FW->input->post('POST_ID'), 5, "0", STR_PAD_LEFT).". Para enviarnos si desea continuar o denegar el proceso de reparación acceda al enlace que esta en este correo."
							),
							array(
								'LABEL' 	=> '¿Aplica Garantía?',
								'POSTVAL'	=> $this->FW->input->post('PROCESS_WARRANTYAVAIL')
							),
							array(
								'LABEL' 	=> 'Costo',
								'POSTVAL'	=> $this->FW->input->post('PROCESS_COST')
							),
							/*array(
								'LABEL' 	=> 'Fecha de entrega',
								'POSTVAL'	=> mysql_date_to_dmy($this->FW->input->post('PROCESS_DELIVERY_DATE'))
							),*/
							array(
								'LABEL' 	=> 'Proceso de reparación',
								'POSTVAL'	=> ascii_to_entities($this->FW->input->post('PROCESS_DESCRIPTION'))
							),
							array(
								'LABEL' 	=> 'Enlace',
								'POSTVAL'	=> '<a href="http://www.grupoi5.com/app/vobo/index/'.$this->FW->input->post('POST_ID').'/'.$passcode.'">http://www.grupoi5.com/app/vobo/index/'.$this->FW->input->post('POST_ID').'/'.$passcode.'</a>'
							)
						);
			$html_message = $this->_mailchimp_html_template($this->current_website, $html_body, $this->company, $this->tel, $this->contact_email);
			$this->FW->email->message($html_message);
			$this->FW->email->send();
		endif;
	}
	//Enviar correo denegando proceso de reclamo
	public function send_denied_process($to, $passcode){
		if($this->FW->input->post()):
			//Establecer parámetros
			$this->FW->email->from('servicio@tumi.com.gt','Reclamos TUMI Guatemala');
			$this->FW->email->to($to);
			$this->FW->email->cc(array('cayala@tumi.com.gt','oakland@tumi.com.gt'));
			$this->FW->email->attach('user_files/uploads/TUMI_warranty_es.pdf');
			
			$this->FW->email->subject('Orden R-'.str_pad($this->FW->input->post('POST_ID'), 5, "0", STR_PAD_LEFT).' no aplica garantía');
			$html_body = array(
							array(
								'LABEL' 	=> NULL,
								'POSTVAL'	=> "Queremos agradecer su preferencia por TUMI, marca l&iacute;der de art&iacute;culos para viajes, negocios y estilo de vida profesional.<br /><br />
								Desde 1975 TUMI se ha dedicado a ofrecer a nuestros clientes una experiencia excepcional como propietarios, con nuestra pol&iacute;tica de garant&iacute;as que para su conocimiento adjuntamos a este correo.<br />
								En base a esta pol&iacute;tica el reclamo no procede debido a la raz&oacute;n que se detalla a continuaci&oacute;n:"
							),
							array(
								'LABEL' 	=> NULL,
								'POSTVAL'	=> "<em>".ascii_to_entities($this->FW->input->post('PROCESS_DESCRIPTION'))."</em>"
							),
							array(
								'LABEL'		=> NULL,
								'POSTVAL'	=> "En TUMI tenemos un compromiso total de mantenerlo satisfecho con nuestros productos y servicios, una vez haya tenido el placer de poseer y utilizar nuestros productos, esperamos que se conviertan en fieles acompañantes de por vida.<br /><br />
								Para cualquier duda, puede comunicarse con nuestras tiendas autorizadas en Guatemala o bien al n&uacute;mero telef&oacute;nico 01-800-299-8864."
							)
						);
			$html_message = $this->_mailchimp_html_template($this->current_website, $html_body, $this->company, $this->tel, $this->contact_email);
			$this->FW->email->message($html_message);
			$this->FW->email->send();
		endif;
	}
	//Enviar correo de respuesta de cliente
	public function client_vobo_response($reclaimid){
		
		//Obtener datos del reclamo
		$this->FW->load->model('cms/cms_plugin_reclamos','reclamos_model');
		$reclaim = $this->FW->reclamos_model->get_reclaim($reclaimid);
		
		if($this->FW->input->post()):
			//Establecer parámetros
			$this->FW->email->from("servicio@tumi.com.gt","Reclamos TUMI Guatemala");
			$this->FW->email->to($this->contact_email); 
			$this->FW->email->cc($reclaim->RECLAIM_CLIENT_EMAIL);
				
			$this->FW->email->subject('Orden R-'.str_pad($reclaim->ID, 5, "0", STR_PAD_LEFT).' ha sido '.$this->FW->input->post('RESPONSE').' por '.$reclaim->RECLAIM_CLIENT_NAME);
			$html_body = array(
							array(
								'LABEL' 	=> NULL,
								'POSTVAL'	=> "La orden R-".str_pad($reclaim->ID, 5, "0", STR_PAD_LEFT).' ha sido '.$this->FW->input->post('RESPONSE').". Esta orden contiene los siguientes datos:"
							),
							array(
								'LABEL' 	=> 'Fecha de la orden',
								'POSTVAL'	=> mysql_date_to_dmy($reclaim->RECLAIM_DATE)
							),
							array(
								'LABEL' 	=> 'TUMI Product (SKU#)',
								'POSTVAL'	=> $reclaim->RECLAIM_PRODUCT
							),
							array(
								'LABEL' 	=> 'Recibi&oacute;',
								'POSTVAL'	=> $reclaim->SALESMAN_SAC_CODE.' - '.$reclaim->SALESMAN_NAME.' '.$reclaim->SALESMAN_LASTNAME
							),
							array(
								'LABEL' 	=> NULL,
								'POSTVAL'	=> "El d&iacute;a de hoy con fecha ".date('d/m/Y').'. '.ascii_to_entities($reclaim->RECLAIM_CLIENT_NAME).' confirm&oacute; que la orden R-'.str_pad($reclaim->ID, 5, "0", STR_PAD_LEFT).' ha sido '.$this->FW->input->post('RESPONSE')." y comenta lo siguiente:"
							),
							array(
								'LABEL' 	=> 'Comentario',
								'POSTVAL'	=> ascii_to_entities($this->FW->input->post('VOBO_COMMENT'))
							)
						);
			$html_message = $this->_mailchimp_html_template($this->current_website, $html_body, $this->company, $this->tel, $this->contact_email);
			$this->FW->email->message($html_message);
			return $this->FW->email->send();
		else:
			return FALSE;
		endif;
	}
	//Enviar correo de recordatorio de ordenes atrasadas.
	public function delayed_orders($reclaims){
		
		if(!empty($reclaims)):
			//Establecer parámetros
			$this->FW->email->from('servicio@tumi.com.gt');
			$this->FW->email->to('cayala@tumi.com.gt'); 
			$this->FW->email->cc(array('guido.orellana@grupoi5.com','oakland@tumi.com.gt'));
				
			$this->FW->email->subject('Reclamos atrasados para entrega.');
			
			$html_body = array();
			foreach($reclaims as $reclaim):
			$html_body[] =	array(
								'LABEL' 	=> 'Código:',
								'POSTVAL'	=> '<a href="'.base_url('cms/plugin_proceso_reclamo/update_table_row/'.$reclaim->ID).'" target="_blank">R-'.str_pad($reclaim->ID, 5, "0", STR_PAD_LEFT)."</a> ".$reclaim->PROCESS_DELIVERY_DATE_DIF
							);
			$html_body[] =	array(
								'LABEL' 	=> 'Fecha de entrega',
								'POSTVAL'	=> mysql_date_to_dmy($reclaim->PROCESS_DELIVERY_DATE)
							);
			$html_body[] =	array(
								'LABEL' 	=> 'Nombre del cliente:',
								'POSTVAL'	=> $reclaim->RECLAIM_CLIENT_NAME
							);
			$html_body[] =	array(
								'LABEL' 	=> 'Recibi&oacute;',
								'POSTVAL'	=> $reclaim->SALESMAN_SAC_CODE.' - '.$reclaim->SALESMAN_NAME.' '.$reclaim->SALESMAN_LASTNAME
							);
			$html_body[] =	array(
								'LABEL' 	=> 'Producto:',
								'POSTVAL'	=> $reclaim->RECLAIM_PRODUCT
							);
			$html_body[] =	array(
								'LABEL' 	=> NULL,
								'POSTVAL'	=> '<hr />'
							);
			endforeach;
			
			$html_message = $this->_mailchimp_html_template($this->current_website, $html_body, $this->company, $this->tel, $this->contact_email);
			$this->FW->email->message($html_message);
			return $this->FW->email->send();
		else:
			return FALSE;
		endif;
	}
	/**
	 * Función para enviar correo de planillas
	 * @var $payroll_data - Objeto con los datos de la planilla a enviar.
	 */
	 public function payroll_confirmation($payroll_data){
	 	
		if(!empty($payroll_data)):
			//Establecer parámetros
			$this->FW->email->from($this->FW->fw_resource->request('RESOURCE_PAYROLL_MANAGEREMAIL'),"Nómina ICINCO");
			$this->FW->email->to($payroll_data->SALESMAN_EMAIL); 
			$this->FW->email->cc($this->FW->fw_resource->request('RESOURCE_PAYROLL_MANAGEREMAIL'));
			$this->FW->email->attach($_SERVER['DOCUMENT_ROOT'].('/app/user_files/uploads/planillas/planilla'.$payroll_data->ID.'.pdf'));
			
			$initialdate 	= mysql_date_to_dmy($payroll_data->PAYROLL_INITIALDATE);
			$enddate	 	= mysql_date_to_dmy($payroll_data->PAYROLL_ENDDATE);
			$total_payed	= $payroll_data->PAYROLL_TOTALACCRUED + $payroll_data->PAYROLL_TOTALDISCOUNTS;
			
			$this->FW->email->subject('Planilla de '.iconv('UTF-8', 'windows-1252', strip_tags(html_entity_decode($payroll_data->SALESMAN_NAME." ".$payroll_data->SALESMAN_LASTNAME))).' correspondiente del '.$initialdate.' al '.$enddate);
			$html_body = array(
							array(
								'LABEL' 	=> NULL,
								'POSTVAL'	=> "Adjunto encontrar&aacute; la boleta correspondiente a la planilla del $initialdate al $enddate con la informaci&oacute;n detallando el total neto pagado. Es necesario se imprima, se firme colocando nombre, fecha y firma y se corten ambas copias."
							),
							array(
								'LABEL' 	=> 'Fecha de emisi&oacute;n',
								'POSTVAL'	=> mysql_date_to_dmy($payroll_data->PAYROLL_ISSUEDATE)
							),
							array(
								'LABEL' 	=> 'Total Devengado',
								'POSTVAL'	=> "Q. ".number_format($payroll_data->PAYROLL_TOTALACCRUED,2,".",",")
							),
							array(
								'LABEL' 	=> 'Total Descontado',
								'POSTVAL'	=> "Q. ".number_format($payroll_data->PAYROLL_TOTALDISCOUNTS,2,".",",")
							),
							array(
								'LABEL' 	=> 'Neto pagado',
								'POSTVAL'	=> "Q. ".number_format($total_payed,2,".",",")
							)
						);
			$html_message = $this->_icinco_html_template($this->current_website, $html_body, $this->company, $this->tel, $this->contact_email);
			$this->FW->email->message($html_message);
			return $this->FW->email->send();
		else:
			return FALSE;
		endif;
	 }
	/**
	 * Función para enviar correo de planillas individuales
	 * @var $payroll_data - Objeto con los datos de la planilla a enviar.
	 */
	 public function payroll_settlement($payroll_data){
	 	
		if(!empty($payroll_data)):
			//Establecer parámetros
			$this->FW->email->from("contabilidad@grupoi5.com","Nómina ICINCO");
			$this->FW->email->to($payroll_data->SALESMAN_EMAIL);
			$this->FW->email->cc($this->FW->fw_resource->request('RESOURCE_PAYROLL_MANAGEREMAIL'));
			$this->FW->email->attach($_SERVER['DOCUMENT_ROOT'].('/app/user_files/uploads/planillas/finiquito'.$payroll_data->ID.'.pdf'));
			
			$initialdate 	= mysql_date_to_dmy($payroll_data->PAYROLL_INITIALDATE);
			$enddate	 	= mysql_date_to_dmy($payroll_data->PAYROLL_ENDDATE);
			
			$this->FW->email->subject('Finiquito correspondiente del '.$initialdate.' al '.$enddate);
			$html_body = array(
							array(
								'LABEL' 	=> NULL,
								'POSTVAL'	=> "Adjunto encontrar&aacute; la boleta correspondiente al finiquito del $initialdate al $enddate con la informaci&oacute;n detallando el total neto a pagar. Es necesario se imprima, se firme colocando nombre, fecha y firma."
							),
							array(
								'LABEL' 	=> 'Fecha de emisi&oacute;n',
								'POSTVAL'	=> mysql_date_to_dmy($payroll_data->PAYROLL_ISSUEDATE)
							),
							array(
								'LABEL' 	=> 'Neto a pagar',
								'POSTVAL'	=> "Q. ".number_format(($payroll_data->PAYROLL_TOTALACCRUED),2,".",",")
							)
						);
			$html_message = $this->_icinco_html_template($this->current_website, $html_body, $this->company, $this->tel, $this->contact_email);
			$this->FW->email->message($html_message);
			return $this->FW->email->send();
		else:
			return FALSE;
		endif;
	 }
	/**
	 * Función para enviar planilla general total de mes
	 * @var $payroll_date - Mes de la planilla a enviar
	 * @var $payroll_file - Nombre del archivo de la planilla general.
	 */
	 public function payroll_total($payroll_date, $payroll_file){
	 	
		if(!empty($payroll_date)):
			//Establecer parámetros
			$this->FW->email->from("contabilidad@grupoi5.com","Nómina ICINCO");
			$this->FW->email->to($this->FW->fw_resource->request('RESOURCE_PAYROLL_MANAGEREMAIL'));
			$this->FW->email->cc($this->FW->fw_resource->request('RESOURCE_PAYROLL_SUBMANAGEREMAIL'));
			$this->FW->email->attach($_SERVER['DOCUMENT_ROOT'].('/app/user_files/uploads/planillas/planillageneral'.$payroll_file.'.pdf'));
			
			$this->FW->email->subject('Planilla total de '.$payroll_date);
			$html_body = array(
							array(
								'LABEL' 	=> NULL,
								'POSTVAL'	=> "Adjunto se encuentra la planilla general de todos los empleados con datos del mes y acumulados y reserva de bono 14 y aguinaldo."
							),
							array(
								'LABEL' 	=> 'Fecha de planilla',
								'POSTVAL'	=> $payroll_date
							)
						);
			$html_message = $this->_icinco_html_template($this->current_website, $html_body, $this->company, $this->tel, $this->contact_email);
			$this->FW->email->message($html_message);
			return $this->FW->email->send();
		else:
			return FALSE;
		endif;
	 }

	/**
	 * Template del HTML a enviar por correo
	 */
	private function _html_body_template($current_website, $body_array, $company, $tel, $contact_email){
		$html_code = '<html>
						<head><title>'.$current_website.'</title></head>
						<body style="font-family:Arial, Helvetica; sans-serif;">
							<p><span style="font-size:18px;color:#000;font-weight:bold;">Correo Electr&oacute;nico - '.$company.'</span><p>
							<table width="550" style="background-color:#EEE;width:526px;font-size:12px;">
							<tr>
								<td>
									<table width="550" style="border-color:#dbdbdb;border-style:solid;border-width:1px;width:526px;font-size:12px; font-family:Arial, Helvetica; sans-serif;">
										<tr style="line-height:20px;"><td colspan="2" style="font-size:14px;"><img src="'.$this->top_image_route.'" width="550" height="60" style="padding:0px;margin:0px;" /></td></tr>';
		foreach($body_array as $field):
		$html_code .= (!empty($field['LABEL']))?'
											<tr>
												<td style="padding-left:10px;padding-top:10px;padding-bottom:10px; width:120"><b>'.$field['LABEL'].'</b></td>
												<td style="padding-left:10px;padding-top:10px;padding-bottom:10px">'.$field['POSTVAL'].'</td>
											</tr>':
											'<tr>
												<td style="padding-left:10px;padding-top:10px;padding-bottom:10px" colspan="2">'.$field['POSTVAL'].'</td>
											</tr>';
		endforeach;
		$html_code .= '				</table>
								</td>
							</tr>
							</table>
							<p style="font-size:11px;color:#505050;">La informaci&oacute;n contenida en este mensaje es privada y confidencial. Si la ha recibido por error, por favor proceda a notificar al remitente y eliminarla de su sistema.</p>
							<p style="font-size:12px;color:#505050;">Atentamente,</p>
							<p style="font-size:12px;color:#505050;">'.$company.'</p>
							<p style="font-size:11px;color:#505050;">Tel. '.$tel.'</p>
						</body>
					</html>';
		return $html_code;
	}

	private function _mailchimp_html_template($current_website, $body_array, $company, $tel, $contact_email){
		$html_code		= '<div style="margin:0;padding:0;background-color:#ffffff;min-height:100%!important;width:100%!important" marginheight="0" marginwidth="0">
		<center>	
			<table width="100%" height="100%" cellspacing="0" cellpadding="0" border="0" align="center" style="border-collapse:collapse;margin:0;padding:0;background-color:#ffffff;height:100%!important;width:100%!important">
				<tbody>
					<tr>
						<td valign="top" align="center">
							<table width="600" cellspacing="0" cellpadding="0" border="0" style="border-collapse:collapse;background-color:#ffffff;border:1px solid #DEDEDE;">
								<tbody>
									<tr>
										<td valign="top" style="padding:0px">
											<table width="600" cellspacing="0" cellpadding="0" border="0" style="border-collapse:collapse;background-color:#ffffff;border-top:0;border-bottom:0; margin:10px;">
												<tbody>
													<tr>
														<td valign="top" style="padding:0px">
															<table width="600" cellspacing="0" cellpadding="0" border="0" style="border-collapse:collapse;background-color:#ffffff;border-top:0;border-bottom:0">
																<tr>
																	<td valign="top" style="text-align: left;">
																		<img style="max-width:600px;padding-bottom:0;display:inline!important;vertical-align:bottom;border:0;outline:none;text-decoration:none" src="http://www.grupoi5.com/app/library/images/TUMI-transparencia.png" />
																	</td>
																	<td valign="middle" style="text-align: right; color:rgb(128,128,128); font-family: \'Proxima Nova\', Helvetica; font-weight: bold; font-size: 11px;line-height:125%;">
																		<p>
																			Cayal&aacute; | Oakland<br />
																			<strong>T.</strong>+502 2493-8136 | +502 5211-7337 <br />
																			<strong>E.</strong> cayala@tumi.com.gt | oakland@tumi.com.gt
																		</p>
																	</td>
																</tr>
															</table>
														</td>
													</tr>
													<tr>
														<td valign="top" style="padding: 0px;" height="20">
															&nbsp;
														</td>
													</tr>
													<tr>
														<td valign="top" style="padding: 0px;">
															<table width="600" cellspacing="0" cellpadding="0" border="0" style="border-collapse:collapse;background-color:#ffffff;border-top:0;border-bottom:0">
																<tr>
																	<td valign="top" style="text-align: left;">
																		<img style="max-width:600px;padding-bottom:0;display:inline!important;vertical-align:bottom;border:0;outline:none;text-decoration:none" src="'.$this->top_image_route.'" />
																	</td>
																</tr>
															</table>
														</td>
													</tr>
													<tr>
														<td valign="top" height="20" style="padding:0;">
														&nbsp;
														</td>
													</tr>
													<tr>
														<td valign="top" style="padding:0px;">
															<table width="600" cellspacing="0" cellpadding="0" border="0" style="border-collapse:collapse;background-color:#ffffff;border-top:0;border-bottom:0">
																<tbody>';
																		foreach($body_array as $field):
																		$html_code .= (!empty($field['LABEL']))?'
																											<tr>
																												<td style="padding-left:10px;padding-top:10px;padding-bottom:10px; width:120"><b>'.$field['LABEL'].'</b></td>
																												<td style="padding-left:10px;padding-top:10px;padding-bottom:10px">'.$field['POSTVAL'].'</td>
																											</tr>':
																											'<tr>
																												<td style="padding-left:10px;padding-top:10px;padding-bottom:10px" colspan="2">'.$field['POSTVAL'].'</td>
																											</tr>';
																		endforeach;
													$html_code		.= '</tbody>
															</table>
														</td>
													</tr>
												</tbody>
											</table>
										</td>
									</tr>
									<tr>
										<td valign="top" style="padding: 0px;">
											<table width="100%" cellspacing="0" cellpadding="0" border="0" style="border-collapse:collapse;background-color:rgb(151,150,152);border-top:0;border-bottom:0">
												<tr>
													<td valign="middle" style="padding: 0px; text-align: right; padding-right: 10px;" height="40">
														<a href="http://www.facebook.com/TumiTravel" target="_blank" style="border: none"><img style="max-width:600px;padding-bottom:0;display:inline!important;vertical-align:bottom;border:0;outline:none;text-decoration:none" src="http://www.grupoi5.com/app/library/images/fb.gif" height="25" /></a>
														<a href="http://www.twitter.com/TumiTravel" target="_blank" style="border: none"><img style="max-width:600px;padding-bottom:0;display:inline!important;vertical-align:bottom;border:0;outline:none;text-decoration:none" src="http://www.grupoi5.com/app/library/images/twitter.gif" height="25" /></a>
														<a href="http://www.instagram.com/TumiTravel" target="_blank" style="border: none"><img style="max-width:600px;padding-bottom:0;display:inline!important;vertical-align:bottom;border:0;outline:none;text-decoration:none" src="http://www.grupoi5.com/app/library/images/instagram.gif" height="25" /></a>
														<a href="http://www.youtube.com/TumiTravel" target="_blank" style="border: none"><img style="max-width:600px;padding-bottom:0;display:inline!important;vertical-align:bottom;border:0;outline:none;text-decoration:none" src="http://www.grupoi5.com/app/library/images/youtube.gif" height="25" /></a>
														
													</td>
												</tr>
											</table>
										</td>
									</tr>
								</tbody>
							</table>
						</td>
					</tr>
				</tbody>
			</table>
		</center></div>';
			return $html_code;
	}
	
	private function _icinco_html_template($current_website, $body_array, $company, $tel, $contact_email){
		$html_code		= '<div style="margin:0;padding:0;background-color:#ffffff;min-height:100%!important;width:100%!important" marginheight="0" marginwidth="0">
		<center>	
			<table width="100%" height="100%" cellspacing="0" cellpadding="0" border="0" align="center" style="border-collapse:collapse;margin:0;padding:0;background-color:#ffffff;height:100%!important;width:100%!important">
				<tbody>
					<tr>
						<td valign="top" align="center">
							<table width="600" cellspacing="0" cellpadding="0" border="0" style="border-collapse:collapse;background-color:#ffffff;border:1px solid #DEDEDE;">
								<tbody>
									<tr>
										<td valign="top" style="padding:0px">
											<table width="600" cellspacing="0" cellpadding="0" border="0" style="border-collapse:collapse;background-color:#ffffff;border-top:0;border-bottom:0; margin:10px;">
												<tbody>
													<tr>
														<td valign="top" style="padding:0px">
															<table width="600" cellspacing="0" cellpadding="0" border="0" style="border-collapse:collapse;background-color:#ffffff;border-top:0;border-bottom:0">
																<tr>
																	<td valign="top" style="text-align: left;">
																		<img style="max-width:300px;padding-bottom:0;display:inline!important;vertical-align:bottom;border:0;outline:none;text-decoration:none" width="300" src="http://www.grupoi5.com/app/library/images/logo-iCinco-final.png" />
																	</td>
																	<td valign="middle" style="text-align: right; color:rgb(128,128,128); font-family: \'Proxima Nova\', Helvetica; font-weight: bold; font-size: 11px;line-height:125%;">
																		<p>
																			Paseo Cayal&aacute; 10-05 zona 16. Lacal I-109
																			<strong>T.</strong>+502 2493-8135<br />
																			<strong>E.</strong> contabilidad@grupoi5.com
																		</p>
																	</td>
																</tr>
															</table>
														</td>
													</tr>
													<tr>
														<td valign="top" style="padding: 0px;" height="20">
															&nbsp;
														</td>
													</tr>
													<tr>
														<td valign="top" height="20" style="padding:0;">
														&nbsp;
														</td>
													</tr>
													<tr>
														<td valign="top" style="padding:0px;">
															<table width="600" cellspacing="0" cellpadding="0" border="0" style="border-collapse:collapse;background-color:#ffffff;border-top:0;border-bottom:0">
																<tbody>';
																		foreach($body_array as $field):
																		$html_code .= (!empty($field['LABEL']))?'
																											<tr>
																												<td style="padding-left:10px;padding-top:10px;padding-bottom:10px; width:120"><b>'.$field['LABEL'].'</b></td>
																												<td style="padding-left:10px;padding-top:10px;padding-bottom:10px">'.$field['POSTVAL'].'</td>
																											</tr>':
																											'<tr>
																												<td style="padding-left:10px;padding-top:10px;padding-bottom:10px" colspan="2">'.$field['POSTVAL'].'</td>
																											</tr>';
																		endforeach;
													$html_code		.= '</tbody>
															</table>
														</td>
													</tr>
												</tbody>
											</table>
										</td>
									</tr>
								</tbody>
							</table>
						</td>
					</tr>
				</tbody>
			</table>
		</center></div>';
			return $html_code;
	}
}
