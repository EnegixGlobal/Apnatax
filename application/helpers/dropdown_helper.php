<?php 
	if(!defined('BASEPATH')) exit('No direct script access allowed');
    if(!function_exists('state_dropdown')){
        function state_dropdown(){
            $CI = get_instance();
            $options=array(''=>'Select State');
            $states=$CI->common->getstates();
            if(!empty($states)){
                foreach($states as $state){
                    $options[$state['id']]=$state['name'];
                }
            }
            return $options;
        }
    }

    if(!function_exists('district_dropdown')){
        function district_dropdown($state_id=NULL){
            $CI = get_instance();
            $options=array(''=>'Select District');
            if(!empty($state_id)){
                $districts=$CI->common->getdistricts($state_id);
                if(!empty($districts)){
                    foreach($districts as $district){
                        $options[$district['id']]=$district['name'];
                    }
                }
            }
            return $options;
        }
    }

    if(!function_exists('role_dropdown')){
        function role_dropdown(){
            $CI = get_instance();
            $options=array(''=>'Select Role');
            $roles=$CI->account->getroles();
            if(!empty($roles)){
                foreach($roles as $role){
                    $options[$role['slug']]=$role['name'];
                }
            }
            return $options;
        }
    }

    if(!function_exists('employee_dropdown')){
        function employee_dropdown($where=array()){
            $CI = get_instance();
            $options=array(''=>'Select Employee');
            $employees=$CI->employee->getemployees($where);
            if(!empty($employees)){
                foreach($employees as $employee){
                    $options[$employee['id']]=$employee['name'];
                }
            }
            return $options;
        }
    }

    if(!function_exists('customer_dropdown')){
        function customer_dropdown($where=array(),$id=false){
            $CI = get_instance();
            $options=array(''=>'Select Customer');
            $customers=$CI->customer->getcustomers($where);
            if(!empty($customers)){
                foreach($customers as $customer){
                    if($id){ $options[$customer['id']]=$customer['name']; }
                    else{ $options[$customer['user_id']]=$customer['name']; }
                }
            }
            return $options;
        }
    }

    if(!function_exists('year_dropdown')){
        function year_dropdown($start='2023'){
            $years=getyearly($start);
            $options=array(''=>'Select Year');
            if(!empty($years)){
                foreach($years as $year){
                    $options[$year['id']]=$year['value'];
                }
            }
            return $options;
        }
    }

    if(!function_exists('month_dropdown')){
        function month_dropdown($year=NULL){
            $options=array(''=>'Select Month');
            if($year!==NULL){
                $months=getmonths($year);
                if(!empty($months)){
                    foreach($months as $month){
                        $options[$month['id']]=$month['value'];
                    }
                }
            }
            return $options;
        }
    }

    if(!function_exists('quarter_dropdown')){
        function quarter_dropdown($year=NULL){
            $options=array(''=>'Select Quarter');
            if($year!==NULL){
                $quarters=getquarterly($year);
                if(!empty($quarters)){
                    foreach($quarters as $quarter){
                        $options[$quarter['id']]=$quarter['value'];
                    }
                }
            }
            return $options;
        }
    }

    if(!function_exists('category_dropdown')){
        function category_dropdown($where,$text='Category'){
            $CI = get_instance();
            $options=array(''=>'Select '.$text);
            $categories=$CI->master->getcategory($where);
            if(!empty($categories)){
                foreach($categories as $category){
                    $options[$category['id']]=$category['name'];
                }
            }
            return $options;
        }
    }

    if(!function_exists('company_dropdown')){
        function company_dropdown($where){
            $CI = get_instance();
            $options=array(''=>'Select Company');
            $companies=$CI->master->getcompanies($where);
            if(!empty($companies)){
                foreach($companies as $company){
                    $options[$company['id']]=$company['name'];
                }
            }
            return $options;
        }
    }

    if(!function_exists('brand_dropdown')){
        function brand_dropdown($where){
            $CI = get_instance();
            $options=array(''=>'Select Brand');
            $brands=$CI->master->getbrands($where);
            if(!empty($brands)){
                foreach($brands as $brand){
                    $options[$brand['id']]=$brand['name'];
                }
            }
            return $options;
        }
    }

    if(!function_exists('gstrate_dropdown')){
        function gstrate_dropdown($where){
            $CI = get_instance();
            $options=array(''=>'Select GST Rate');
            $gstrates=$CI->master->getgstrates($where);
            if(!empty($gstrates)){
                foreach($gstrates as $rate){
                    $options[$rate['gst_rate']]=$rate['gst_rate'].'%';
                }
            }
            return $options;
        }
    }

    if(!function_exists('unit_dropdown')){
        function unit_dropdown($where){
            $CI = get_instance();
            $options=array(''=>'Select Unit');
            $units=$CI->master->getunits($where);
            if(!empty($units)){
                foreach($units as $unit){
                    $options[$unit['id']]=$unit['name'];
                }
            }
            return $options;
        }
    }

    if(!function_exists('account_dropdown')){
        function account_dropdown($where,$includeCash=false){
            $CI = get_instance();
            $options=array(''=>'Select Account');
            if($includeCash){
                $options[0]="Cash";
            }
            $accounts=$CI->master->getaccounts($where);
            if(!empty($accounts)){
                foreach($accounts as $account){
                    $options[$account['id']]=$account['display_name'];
                }
            }
            return $options;
        }
    }

    if(!function_exists('product_dropdown')){
        function product_dropdown($where,$barcode=false){
            $CI = get_instance();
            $options=array(''=>'Select Product');
            $products=$CI->product->getproducts($where);
            if(!empty($products)){
                foreach($products as $product){
                    $options[$product['id']]=(($barcode===true)?$product['barcode'].' - ':'').$product['name'];
                }
            }
            return $options;
        }
    }


    if(!function_exists('servicetype_dropdown')){
        function servicetype_dropdown($where,$barcode=false){
            $CI = get_instance();
            $options=array(''=>'Select Service Type');
            $servicetypes=$CI->master->getservicetypes($where);
            if(!empty($servicetypes)){
                foreach($servicetypes as $single){
                    $options[$single['id']]=$single['name'];
                }
            }
            return $options;
        }
    }

    if(!function_exists('firm_dropdown')){
        function firm_dropdown(){
            $CI = get_instance();
            $user=getuser();
            $where=array("t1.user_id"=>$user['id'],'t1.status'=>1,'t1.request!='=>1);
            $firms=$CI->customer->getfirms($where);
            $options=array(''=>'Select Firm');
            if(!empty($firms)){
                foreach($firms as $single){
                    $options[$single['id']]=$single['name'];
                }
            }
            return $options;
        }
    }

    if(!function_exists('service_dropdown')){
        function service_dropdown($where=array()){
            $CI = get_instance();
            $options=array(''=>'Select Service');
            $services=$CI->master->getservices($where);
            if(!empty($services)){
                foreach($services as $single){
                    $options[$single['id']]=$single['name'];
                }
            }
            return $options;
        }
    }


