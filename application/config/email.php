<?php
		//Cargar la librería de email
		$config['protocol'] = 'sendmail';
		$config['mailpath'] = '/usr/sbin/sendmail';
		$config['charset'] 	= 'iso-8859-1';
		$config['wordwrap']	= TRUE;
		$config['mailtype']	= 'html';