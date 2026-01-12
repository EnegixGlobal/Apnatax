<?php 
	if(!defined('BASEPATH')) exit('No direct script access allowed');
	if(!function_exists('checkcookie')) {
  		function checkcookie() {
    		$ci=& get_instance();
			if($ci->input->cookie('cookie_data')===NULL){
				$cookie_str=random_string('alnum', 32);
				$cookie_data=array("name"=>"cookie_data","value"=>$cookie_str,"expire"=>time()+60*60*24*30);
				$ci->input->set_cookie($cookie_data);
			}
			//else{ echo $ci->input->cookie('cookie_data'); }
		}
	}
?>