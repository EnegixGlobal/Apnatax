<?php 
	if(!defined('BASEPATH')) exit('No direct script access allowed');
	if(!function_exists('sendemail')) {
  		function sendemail($email,$subject,$message,$fieldname=false,$upload_path=false,$allowed_types=false,$file_name=false,$reply_to=false) {
    		// Getting CI class instance.
    		$CI = get_instance();
			if(!$CI->load->is_loaded('email')){
				$CI->load->library('email');
			} 
			if(!function_exists('upload')){
				$CI->load->helper('upload');
			} 
			$from="";
			$httpHost = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '';
			$isLocal = in_array($httpHost, array('localhost','127.0.0.1'));

			$CI->config->load('email_smtp', TRUE);
			$cfgVal = function($key, $default = '') use ($CI) {
				$c = $CI->config->item($key, 'email_smtp');
				return ($c !== null && $c !== '') ? $c : $default;
			};
			$envOrCfg = function($envKey, $cfgKey, $default = '') use ($cfgVal) {
				$v = getenv($envKey);
				if ($v !== false && $v !== '') {
					return $v;
				}
				return $cfgVal($cfgKey, $default);
			};

			$smtpHost = $envOrCfg('SMTP_HOST', 'smtp_host', '');
			$smtpPort = $envOrCfg('SMTP_PORT', 'smtp_port', '');
			$smtpUser = $envOrCfg('SMTP_USER', 'smtp_user', '');
			$smtpPass = $envOrCfg('SMTP_PASS', 'smtp_pass', '');
			$smtpCrypto = $envOrCfg('SMTP_CRYPTO', 'smtp_crypto', '');
			$mailFromEmail = $envOrCfg('MAIL_FROM_EMAIL', 'mail_from_email', '');
			$protocolEnv = $envOrCfg('MAIL_PROTOCOL', 'mail_protocol', '');
			$mailFromName = getenv('MAIL_FROM_NAME');
			if ($mailFromName === false || $mailFromName === '') {
				$mailFromName = $cfgVal('mail_from_name', '');
			}
			if ($mailFromName === '') {
				$mailFromName = SITE_SALT;
			}

			if($CI->input->get('test')=='test'){
                $config['mailpath']= "/usr/bin/sendmail";
                $config['protocol'] = 'smtp';
                $config['smtp_host'] = 'smtp.gmail.com';
                $config['_smtp_auth'] = TRUE;
                $config['smtp_port'] = '587';
                $config['smtp_crypto'] = 'tls';
                $config['smtp_user'] = '';
                $config['smtp_pass'] = '';
                $config['charset'] = 'iso-8859-1';
                $config['smtp_timeout'] = 15;
				$from = $mailFromEmail !== '' ? $mailFromEmail : ($config['smtp_user'] ?: 'no-reply@example.com');
			}
			elseif($isLocal){
				$config['protocol'] = 'smtp';
				$config['smtp_host'] = $smtpHost !== '' ? $smtpHost : 'smtp.gmail.com';
				$config['smtp_port'] = $smtpPort !== '' ? $smtpPort : '587';
				$config['smtp_timeout'] = $envOrCfg('SMTP_TIMEOUT', 'smtp_timeout', '30');
				$config['smtp_crypto'] = $smtpCrypto !== '' ? $smtpCrypto : 'tls';
				$config['smtp_user'] = $smtpUser;
				$config['smtp_pass'] = $smtpPass;
				$config['_smtp_auth'] = !empty($smtpUser) && !empty($smtpPass);
				$from = $mailFromEmail !== '' ? $mailFromEmail : ($smtpUser !== '' ? $smtpUser : 'no-reply@localhost');
			}
			else{
				// Production: use SMTP env if provided; otherwise fall back to PHP mail().
				if(!empty($smtpHost) || $protocolEnv === 'smtp'){
					$config['protocol'] = $protocolEnv !== '' ? $protocolEnv : 'smtp';
					$config['smtp_host'] = $smtpHost;
					$config['smtp_port'] = $smtpPort !== '' ? $smtpPort : '587';
					$config['smtp_timeout'] = $envOrCfg('SMTP_TIMEOUT', 'smtp_timeout', '30');
					$config['smtp_user'] = $smtpUser;
					$config['smtp_pass'] = $smtpPass;
					$config['smtp_crypto'] = $smtpCrypto !== '' ? $smtpCrypto : 'tls';
					$config['_smtp_auth'] = !empty($smtpUser) || !empty($smtpPass);
				}else{
					$config['protocol'] = 'mail';
				}

				$from = $mailFromEmail !== '' ? $mailFromEmail : ($smtpUser !== '' ? $smtpUser : 'no-reply@example.com');
			}
			
			$config['newline']="\r\n";
			$config['wordwrap'] = TRUE;
			//$config['charset'] = 'iso-8859-1';
            $config['charset'] = 'utf-8';
			$config['mailtype'] = "html";
			if($CI->input->get('test')=='test'){
                print_pre($config);
            }
            
            //$CI->load->library('email',$config);
            //getmethods();
            //$CI->email->set_newline("\r\n");
            //$CI->email->set_wordwrap(TRUE); // Enable word wrapping
            //$CI->email->set_mailtype('html'); // Set mailtype to HTML
			//print_pre($config,true);
			$CI->email->initialize($config);
			$CI->email->from($from,$mailFromName);
			if ($reply_to !== false && is_string($reply_to) && filter_var($reply_to, FILTER_VALIDATE_EMAIL)) {
				$CI->email->reply_to($reply_to);
			}
            $CI->email->set_newline("\r\n");
            $CI->email->set_header('Return-Path', $from);
			$CI->email->to($email);
			$CI->email->subject($subject);
			$CI->email->message($message);
            
            // Add the List-Unsubscribe header
            $CI->email->set_header('List-Unsubscribe', '<mailto:?subject=Unsubscribe>, <https://crm.studionineconstructions.com/unsubscribe>');

			
			if($fieldname!==false && $upload_path!==false && $allowed_types!==false){
				if($file_name===false){
					$file_name=$fieldname.'-attachment';
				}
				if(is_array($_FILES[$fieldname]['name'])){
					$count=count($_FILES[$fieldname]['name']);
					for($i=0; $i<$count; $i++) {
						if(is_uploaded_file($_FILES[$fieldname]['tmp_name'][$i])){
							$_FILES['multi']['name']     = $_FILES[$fieldname]['name'][$i];
							$_FILES['multi']['type']     = $_FILES[$fieldname]['type'][$i];
							$_FILES['multi']['tmp_name'] = $_FILES[$fieldname]['tmp_name'][$i];
							$_FILES['multi']['error']     = $_FILES[$fieldname]['error'][$i];
							$_FILES['multi']['size']     = $_FILES[$fieldname]['size'][$i];
								
							$attachment=upload_file('multi',$upload_path,$allowed_types,$file_name);
							$CI->email->attach(file_url($attachment));
							$attachment='.'.$attachment;
							if(file_exists($attachment)){
								unlink($attachment);
							}
						}
					}
				}
				else{
					$attachment=upload_file($fieldname,$upload_path,$allowed_types,$file_name);
					$CI->email->attach(file_url($attachment));
					$attachment='.'.$attachment;
					if(file_exists($attachment)){
						unlink($attachment);
					}
				}
			}
			if($CI->email->send()){
                if($CI->input->get('test')=='test'){
                    print_pre($CI->email);
                }
				return true;
			}
			else{
                if($CI->input->get('test')=='test'){
                    print_pre($CI->email);
                }
				log_message('error', 'sendemail failed for: '.$email.' — '.$CI->email->print_debugger(array('headers')));
				return false;
			}
		}  
	}

?>
