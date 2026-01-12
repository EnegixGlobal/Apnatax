<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Template {
    var $ci;
	private $styles=array("link"=>array(),"file"=>array());
	private $top_script=array("link"=>array(),"file"=>array());
	private $bottom_script=array("link"=>array(),"file"=>array());

    function __construct() {
       $this->ci =& get_instance();
    }

    function load($folder, $view, $data=array("title"=>"Page")) {
		$location=$folder.'/';
		if(!empty($data['styles'])){ 
			$styles=$data['styles'];
			foreach($styles as $key=>$style){
				if(is_array($style)){
					foreach($style as $single_style){
						if(array_search($single_style,$this->styles[$key])===false)
							$this->styles[$key][]=$single_style;
					}
				}
				else{
					if(array_search($style,$this->styles[$key])===false)
						$this->styles[$key][]=$style;
				}
			}
		}

        if(!empty($data['top_script'])){ 
			$top_script=$data['top_script'];
			foreach($top_script as $key=>$script){
				if(is_array($script)){
					foreach($script as $single_script){
						if(array_search($single_script,$this->top_script[$key])===false)
							$this->top_script[$key][]=$single_script;
					}
				}
				else{
					if(array_search($script,$this->top_script[$key])===false)
						$this->top_script[$key][]=$script;
				}
			}
		}

        if(!empty($data['bottom_script'])){ 
			$bottom_script=$data['bottom_script'];
			foreach($bottom_script as $key=>$script){
				if(is_array($script)){
					foreach($script as $single_script){
						if(!isset($this->bottom_script[$key]) || 
                           array_search($single_script,$this->bottom_script[$key])===false)
							$this->bottom_script[$key][]=$single_script;
					}
				}
				else{
					if(!isset($this->bottom_script[$key]) || 
                           array_search($script,$this->bottom_script[$key])===false)
						$this->bottom_script[$key][]=$script;
				}
			}
		}
        if(isset($data['datatable']) && $data['datatable']===true){
            $this->loaddatatable();
        }
        if(isset($data['datatableexport']) && $data['datatableexport']===true){
            $this->loaddatatableexport();
        }
        if(isset($data['select2']) && $data['select2']===true){
            $this->loadselect2();
        }
        if(isset($data['switchery']) && $data['switchery']===true){
            $this->loadswitchery();
        }
        if(isset($data['datepicker']) && $data['datepicker']===true){
            $this->loaddatepicker();
        }
        if(isset($data['timepicker']) && $data['timepicker']===true){
            $this->loadtimepicker();
        }
        if(isset($data['ckeditor']) && $data['ckeditor']===true){
            $this->loadckeditor();
        }
		if(isset($data['highchart']) && $data['highchart']===true){
			$this->loadhighchart();
        }
		if(isset($data['variablepiechart']) && $data['variablepiechart']===true){
			$this->loadvariablepiechart();
		}
		if(isset($data['funnelchart']) && $data['funnelchart']===true){
			$this->loadfunnelchart();
		}
		if(isset($data['highchart']) && $data['highchart']===true){
			$this->loadhighchartextras();
        }
        if(isset($data['rangepicker']) && $data['rangepicker']===true){
            $this->loadrangepicker();
        }
        if(isset($data['fileuploader']) && $data['fileuploader']===true){
            $this->loadfileuploader();
        }
        if(isset($data['alertify']) && $data['alertify']===true){
            $this->loadalertify();
        }
        /*if(NTYPE=='sweetalert' || (isset($data['ntype']) && $data['ntype']==='sweetalert')){
            $this->loadsweetalert();
        }
        if(NTYPE=='toastr' || (isset($data['ntype']) && $data['ntype']==='toastr')){
            $this->loadtoastr();
        }*/
        $data['styles']=$this->styles;
        $data['top_script']=$this->top_script;
        $data['bottom_script']=$this->bottom_script;
        $getsidebar=getsidebar();
        //print_pre($getsidebar,true);
        if($getsidebar['status']===false){
            $link=!empty($_SERVER['HTTP_REFERER'])?$_SERVER['HTTP_REFERER']:'/';
            redirect($link);
        }
        if(isset($getsidebar['sidebar'])){
            $data['sidebar']=$getsidebar['sidebar'];
        }
        else{
            $data['sidebar']=array();
        }
        $this->ci->load->view('includes/top-section',$data);
        $this->ci->load->view('includes/header');
        $this->ci->load->view('includes/sidebar');
        $this->ci->load->view($location.$view);
        $this->ci->load->view('includes/footer');
        $this->ci->load->view('includes/bottom-section');
    }

    function loaddatatable(){
        $this->styles['link'][]="https://cdn.datatables.net/v/bs4/dt-1.10.20/datatables.min.css";
        $this->top_script['link'][]="https://cdn.datatables.net/v/bs4/dt-1.10.20/datatables.min.js";
    }

	function loaddatatableexport(){
		$this->top_script['link'][]="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js";
		$this->top_script['link'][]="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js";
		$this->top_script['link'][]="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js";
		$this->top_script['link'][]="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js";
		$this->top_script['link'][]="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js";
		$this->top_script['link'][]="https://cdn.datatables.net/buttons/2.2.2/js/buttons.print.min.js";
    }
	
    function loadselect2(){
        $this->styles['file'][]="includes/plugins/select2/select2.min.css";
        $this->bottom_script['file'][]="includes/plugins/select2/select2.full.min.js";
    }

    function loadalertify(){
        $this->styles['file'][]='includes/plugins/alertifyjs/build/css/alertify.min.css';
        $this->styles['file'][]='includes/plugins/alertifyjs/build/css/themes/default.min.css';
        $this->bottom_script['file'][]='includes/plugins/alertifyjs/build/alertify.min.js';
    }

    function rangepicker(){
        $this->styles['file'][]="plugins/daterangepicker/daterangepicker.css";
        $this->bottom_script['link'][]="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.11.2/moment.min.js";
        $this->bottom_script['file'][]="plugins/daterangepicker/daterangepicker.js";
    }

    function loadtimepicker(){
        $this->styles['file'][]="includes/plugins/timepicker/bootstrap-timepicker.min.css";
        $this->bottom_script['file'][]="includes/plugins/timepicker/bootstrap-timepicker.min.js";
    }
    function loaddatepicker(){
        $this->styles['file'][]="includes/plugins/datepicker/datepicker3.css";
        $this->bottom_script['file'][]="includes/plugins/datepicker/bootstrap-datepicker.js";
    }

    function loadckeditor(){
        $this->top_script['link'][]="//cdn.ckeditor.com/4.13.0/standard/ckeditor.js";
    }

    function loadswitchery(){
        $this->styles['file'][]="includes/css/switchery.css";
        $this->top_script['file'][]="includes/js/switchery.js";
        $this->top_script['file'][]="includes/js/swithces.js";
    }

    function loadsweetalert(){
        $this->styles['file'][]="includes/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css";
        $this->bottom_script['file'][]="includes/plugins/sweetalert2/sweetalert2.min.js";
    }

    function loadtoastr(){
        $this->styles['file'][]="includes/plugins/toastr/toastr.min.css";
        $this->bottom_script['file'][]="includes/plugins/toastr/toastr.min.js";
    }
    
	function loadhighchart(){
		$this->styles['file'][]="includes/custom/highchart.css";
		$this->top_script['link'][]="https://code.highcharts.com/highcharts.js";
	}
	
	function loadvariablepiechart(){
		$this->top_script['link'][]="https://code.highcharts.com/modules/variable-pie.js";
	}
	
	function loadfunnelchart(){
		$this->top_script['link'][]="https://code.highcharts.com/modules/funnel.js";
	}
	
	function loadhighchartextras(){
		$this->top_script['link'][]="https://code.highcharts.com/modules/exporting.js";
		$this->top_script['link'][]="https://code.highcharts.com/modules/export-data.js";
		$this->top_script['link'][]="https://code.highcharts.com/modules/accessibility.js";
	}
	
    function loadfileuploader(){
        $this->bottom_script['file'][]="includes/plugins/fancyuploder/jquery.ui.widget.js";
        $this->bottom_script['file'][]="includes/plugins/fancyuploder/jquery.fileupload.js";
        $this->bottom_script['file'][]="includes/plugins/fancyuploder/jquery.iframe-transport.js";
        $this->bottom_script['file'][]="includes/plugins/fancyuploder/jquery.fancy-fileupload.js";
        //$this->bottom_script['file'][]="includes/plugins/fancyuploder/fancy-uploader.js";
    }
    
    function loadplugins($data=array()){
        if(isset($data['datatable']) && $data['datatable']===true){
            $this->loaddatatable();
        }
        if(isset($data['select2']) && $data['select2']===true){
            $this->loadselect2();
        }
        if(isset($data['switchery']) && $data['switchery']===true){
            $this->loadswitchery();
        }
        if(isset($data['datepicker']) && $data['datepicker']===true){
            $this->loaddatepicker();
        }
        if(isset($data['timepicker']) && $data['timepicker']===true){
            $this->loadtimepicker();
        }
        if(isset($data['ckeditor']) && $data['ckeditor']===true){
            $this->loadckeditor();
        }
        if(isset($data['rangepicker']) && $data['rangepicker']===true){
            $this->loadrangepicker();
        }
        $result=array();
        $result['styles']=$this->styles;
        $result['top_script']=$this->top_script;
        $result['bottom_script']=$this->bottom_script;
        return $result;
    }
}
