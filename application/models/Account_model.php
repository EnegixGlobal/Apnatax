<?php
class Account_model extends CI_Model{
	
	function __construct(){
		parent::__construct(); 
		$this->db->db_debug = false;
	}
    
    public function register($data){
        $username=$data['username'];
        $mobile=$data['mobile'];
        $role=$data['role'];
        $where="(username='$username' or mobile='$mobile'";
        $where.=") and role='$role'";
        $query=$this->db->get_where("users",$where);
        $status=true;
        if($query->num_rows()>0){
            $array=$query->unbuffered_row('array');
            /*$msg=array();
            if($array['mobile']==$mobile){
                $msg[]='Mobile No';
            }
            if(!empty($msg) && count($msg)<2){
                $msg=implode(", ",$msg)." not Available!";
                return array("status"=>false,"message"=>$msg,'mobile'=>'Registered');
            }
            else{
                return array("status"=>true,"message"=>"User added Successfully!","user_id"=>$array['id']);
            }*/
            if($array['status']==0){
                return array("status"=>true,"message"=>"User added Successfully!","user_id"=>$array['id'],'old'=>true);
            }
            else{
                return array("status"=>false,"message"=>"Mobile no. Already Registered!");
            }
        }
        if($status){
            $vp=$password=$data['password'];
            $salt=random_string('alnum', 16);
            $password=$password.SITE_SALT.$salt;
            $password=password_hash($password,PASSWORD_DEFAULT);
            $data['salt']=$salt;
            $data['password']=$password;
            $data['vp']=$vp;
            $data['created_on']=$data['updated_on']=date('Y-m-d H:i:s');
            $result=$this->db->insert("users",$data);
            if($result){
                $user_id=$this->db->insert_id();
                return array("status"=>true,"message"=>"User added Successfully!","user_id"=>$user_id,'old'=>false);
            }
            else{
                $err=$this->db->error();
                return array("status"=>false,"message"=>$err['message']);
            }
        }
    }
    
    public function login($data){
        $username=$data['username'];
        $where=array("username"=>$username);
        if(isset($data['role'])){
            $where['role']=$data['role'];
        }
        $flag=true;
        $msg="Wrong Username or Password!";
        $query=$this->db->get_where("users",$where);
        if($query->num_rows()==0){
            $flag=false;
        }
        else{
            $user=$query->unbuffered_row('array');
            if($user['status']==0){
                $flag=false;
                $msg="Account not Verified!";
            }
            elseif($user['status']==2){
                $flag=false;
                $msg="Invalid Login Details!";
            }
            else{
                $salt=$user['salt'];
                $password=$user['password'];
                if(!password_verify($data['password'].SITE_SALT.$salt,$password)){
                    $flag=false;
                }
            }
        }
        if($flag===true){
            return array("status"=>true,"message"=>"Successfully Logged In!",'user'=>$user);
        }
        else{
            return array("status"=>false,"message"=>$msg);
        }
    }
    
    public function createotp($where){
		$query = $this->db->get_where('users',$where);
		if($query->num_rows()>0){
			$result=$query->unbuffered_row('array');
            if($result['status']==2){ return array("status"=>false,"message"=>"Invalid Details!"); }
            else{
                $otp=random_string('numeric',6);
                $encotp=$otp.SITE_SALT.$result['salt'];
                $encotp=password_hash($encotp,PASSWORD_DEFAULT);
                $data['otp']=$encotp;
                $data['vp']=$otp;
                $data['updated_on']=date('Y-m-d H:i:s');
                $this->db->where($where);
                if($this->db->update('users',$data)){
                    if($result['status']==1){ $type="login"; }
                    else{ $type="activate"; }
                    $array=array("otp"=>$otp, "type"=>$type, "id"=>$result['id'], "name"=>$result['name'], "email"=>$result['email'], "mobile"=>$result['mobile']);
                    return array("status"=>true,'result'=>$array);
                }
                else{
                    $err=$this->db->error();
                    return array("status"=>false,"message"=>$err['message']);
                }
            }
		}
		else{
			return array("status"=>false,"message"=>"User not Available!");
		}
    }
    
	public function verifyotp($otp,$where){
		$query = $this->db->get_where('users',$where);
		$result=$query->unbuffered_row('array');
        $flag=false;
		if(!empty($result)){
			if(time()-strtotime($result['updated_on'])<1800){
                if(password_verify($otp.SITE_SALT.$result['salt'],$result['otp'])){
					$this->db->where($where);
					$this->db->update('users',array("status"=>1,'otp'=>''));
					$flag=true;
				}
			}
			else{
				$flag='expired';
			}
		}
        if($flag===true){
            return array("status"=>true,"result"=>$result);
        }
        elseif($flag===false){
            return array("status"=>false,"message"=>'Invalid OTP!');
        }
        else{
            return array("status"=>false,"message"=>'OTP Expired!');
        }
	}
    
	public function addtoken($data,$type="single"){
        if($type!="multiple"){
			$this->db->order_by("updated_on desc");
			$getold=$this->db->get_where("tokens",array("user_id"=>$data['user_id'],"status"=>1));
			$count=$getold->num_rows();
			if($count>0){
				switch($type){
					case "single" : $this->db->update("tokens",array("status"=>0),array("user_id"=>$data['user_id']));
					break;
					case "restricted" : $checkdevice=$this->db->get_where("tokens",array("user_id"=>$data['user_id'],"device_id"=>$data['device_id']))->num_rows();
										if($checkdevice==0){
											return "User Already Logged in from Different Device";
										}
										else{
											$this->db->update("tokens",array("status"=>0),array("user_id"=>$data['user_id']));
										}
					break;
				}
				if(is_numeric($type) && $count>=$type){
					$toremove=$count-$type+1;
					$query="UPDATE ".TP."tokens set status='0' where user_id='$data[user_id]' and status='1' order by updated_on limit $toremove";
					$this->db->query($query);
				}
			}
		}
		$data['added_on']=$data['updated_on']=date("Y-m-d H:i:s");
		if($this->db->insert("tokens",$data)){
			return true;
		}
		else{
			$err=$this->db->error();
			return $err['message'];
		}
	}
	
	public function verify_token($token){
		$gettoken=$this->db->get_where('tokens',"token='$token' and status='1'");
		if($gettoken->num_rows()==1){
			$user_id=$gettoken->unbuffered_row()->user_id;
			$array=$this->db->get_where("users",array("id"=>$user_id))->unbuffered_row('array');
			if(is_array($array)){
				$updated_on=date('Y-m-d H:i:s');
				$this->db->update("tokens",array("updated_on"=>$updated_on),array("token"=>$token,"status"=>1));
				return $array;
			}
			else{
				return false;
			}
		}
		else{
			return false;
		}
	}
    
    public function getuser($where){
        $this->db->select("*");
        $this->db->where($where);
        $query=$this->db->get('users');
        if($query->num_rows()==1){
            $result=$query->unbuffered_row('array');
            return array("status"=>true,"user"=>$result);
        }
        else{
            return array("status"=>false,"message"=>"User not available!");
        }
    }
    
    public function updateuser($data,$where){
        $user=$this->db->get_where("users",$where)->unbuffered_row('array');
        //if($this->db->get_where("users",array("username"=>$data['username'],"id!="=>$user['id']))->num_rows()==0){
            if($this->db->update("users",$data,$where)){
                return array("status"=>true,"message"=>"Profile Updated Successfully!");
            }
            else{
                $error=$this->db->error();
                return array("status"=>false,"message"=>$error['message']);
            }
        /*}
        else{
            return array("status"=>false,"message"=>"Email-Id Registered to different Account!");
        }*/
    }
    
    public function updateuserstatus($data,$where){
        if($this->db->update("users",$data,$where)){
            return array("status"=>true,"message"=>"User Updated Successfully!");
        }
        else{
            $error=$this->db->error();
            return array("status"=>false,"message"=>$error['message']);
        }
    }
    
    public function updatepassword($data,$where){
        $vp=$password=$data['password'];
        $salt=random_string('alnum', 16);
        $password=$password.SITE_SALT.$salt;
        $password=password_hash($password,PASSWORD_DEFAULT);
        $data['salt']=$salt;
        $data['password']=$password;
        $data['vp']=$vp;
        $data['updated_on']=date('Y-m-d H:i:s');
        
        if($this->db->update("users",$data,$where)){
            return array("status"=>true,"message"=>"Password Updated Successfully!");
        }
        else{
            $error=$this->db->error();
            return array("status"=>false,"message"=>$error['message']);
        }
    }
    
    public function updatephoto($data,$where){
        $src=$this->db->get_where("users",$where)->unbuffered_row()->photo;
        if (is_readable('.'.$src) && is_file('.'.$src)) {
            unlink('.'.$src);
            //echo "The file has been deleted";
        }
        if($this->db->update("users",$data,$where)){
            return array("status"=>true,"message"=>"Photo Updated Successfully!");
        }
        else{
            $error=$this->db->error();
            return array("status"=>false,"message"=>$error['message']);
        }
    }
    
    public function addrole($data){
        if($this->db->get_where('roles',array('name'=>$data['name']))->num_rows()==0){
            if($this->db->insert("roles",$data)){
                return array("status"=>true,"message"=>"Role Added Successfully!");
            }
            else{
                $error=$this->db->error();
                return array("status"=>false,"message"=>$error['message']);
            }
        }
        else{
            return array("status"=>false,"message"=>"Role Already Added!");
        }
    }
    
    public function getroles($where=array(),$type="all"){
        //$columns="*,  case when image='' then '' else concat('".file_url()."',image) end as image";
        //$this->db->select($columns);
        $this->db->where($where);
        $query=$this->db->get("roles");
        if($type=='all'){
            $array=$query->result_array();
        }
        else{
            $array=$query->unbuffered_row('array');
        }
        return $array;
    }
    
    public function updaterole($data){
        $id=$data['id'];
        unset($data['id']);
        $where=array("id"=>$id);
        $getrole=$this->db->get_where('roles',array('name'=>$data['name'],"id!="=>$id));
        if($getrole->num_rows()==0){
            $role=$this->db->get_where('roles',array("id"=>$id))->unbuffered_row('array');
            if($this->db->update("roles",$data,$where)){
                if(isset($data['slug']) && $role['slug']!=$data['slug']){
                    $this->db->update('users',['role'=>$data['slug']],['role'=>$role['slug']]);
                }
                return array("status"=>true,"message"=>"Role Updated Successfully!");
            }
            else{
                $error=$this->db->error();
                return array("status"=>false,"message"=>$error['message']);
            }
        }
        else{
            return array("status"=>false,"message"=>"Role Already Added!");
        }
    }
    
	public function adduser($data){
        //if($this->db->get_where('users',array('e_id'=>$data['e_id']))->num_rows()==0){
            $password=$data['password'];
            $data['vp']=$password;
            $salt=random_string('alnum', 16);
            $encpassword=$password.SITE_SALT.$salt;
            $encpassword=password_hash($encpassword,PASSWORD_DEFAULT);
            $data['salt']=$salt;
            $data['password']=$encpassword;
            $data['created_on']=date('Y-m-d H:i:s');
            $data['updated_on']=date('Y-m-d H:i:s');
            $data['status']=1;
            if($this->db->insert("users",$data)){
                $user_id=$this->db->insert_id();
                $result=array("status"=>true,"user_id"=>$user_id,"message"=>"User Added Successfully!");
                return $result;
            }
            else{
                $error= $this->db->error();
                $error['status']=false;
                return $error;
            }
		/*}
		else{
			$error['status']=false;
            $error['message']="Employee Already added as User!";
			return $error;
		}*/
	}
	
    public function getusers($where=array(),$type="all"){
        $columns="t1.*,  ifnull(t2.name,'Admin') as role_name,  t3.name as manager_name";
        $this->db->select($columns);
        $this->db->where($where);
        $this->db->from("users t1");
        $this->db->join("roles t2","t2.slug=t1.role",'left');
        $this->db->join("users t3","t3.id=t1.parent_id",'left');
        $query=$this->db->get();
        if($type=='all'){
            $array=$query->result_array();
        }
        else{
            $array=$query->unbuffered_row('array');
        }
        return $array;
    }
    

    public function updatecrmuser($data){
        $id=$data['id'];
        unset($data['id']);
        
        $where=array("id"=>$id);
        if($this->db->get_where('users',array('username'=>$data['username'],"id!="=>$id))->num_rows()==0){
            if(!empty($data['password'])){
                $password=$data['password'];
                $data['vp']=$password;
                $salt=random_string('alnum', 16);
                $encpassword=$password.SITE_SALT.$salt;
                $encpassword=password_hash($encpassword,PASSWORD_DEFAULT);
                $data['salt']=$salt;
                $data['password']=$encpassword;
            }
            $data['updated_on']=date('Y-m-d H:i:s');
            if($this->db->update("users",$data,$where)){
                return array("status"=>true,"message"=>"User Updated Successfully!");
            }
            else{
                $error=$this->db->error();
                return array("status"=>false,"message"=>$error['message']);
            }
        }
        else{
            return array("status"=>false,"message"=>"User Already Added!");
        }
    }
    
    public function savekyc($data){
        if($this->db->get_where('kyc',['user_id'=>$data['user_id']])->num_rows()==0){
            $data['added_on']=$data['updated_on']=date('Y-m-d H:i:s');
            $result=$this->db->insert('kyc',$data);
            $msg="KYC Saved Successfully!";
        }
        else{
            $data['updated_on']=date('Y-m-d H:i:s');
            $result=$this->db->update('kyc',$data,['user_id'=>$data['user_id']]);
            $msg="KYC Updated Successfully!";
        }
        if($result){
            return array("status"=>true,"message"=>$msg);
        }
        else{
            $error=$this->db->error();
            return array("status"=>false,"message"=>$error['message']);
        }
    }
    
    public function getkyc($where){
        $columns="t1.pan,case when t1.pan_image='' then '' else concat('".file_url()."',t1.pan_image) end as pan_image,";
        $columns.="t1.aadhar,case when t1.aadhar_image='' then '' else concat('".file_url()."',t1.aadhar_image) end as aadhar_image,";
        $columns.="case when t1.aadhar_back='' then '' else concat('".file_url()."',t1.aadhar_back) end as aadhar_back,t1.status";
        $this->db->select($columns);
        $this->db->where($where);
        $this->db->from('kyc t1');
        $query=$this->db->get();
        $array=$query->unbuffered_row('array');
        return $array;
    }
    
    public function saveaddress($data){
        if($this->db->get_where('customers',['user_id'=>$data['user_id']])->num_rows()==0){
            $data['added_on']=$data['updated_on']=date('Y-m-d H:i:s');
            $result=$this->db->insert('customers',$data);
            $msg="Address Saved Successfully!";
        }
        else{
            $data['updated_on']=date('Y-m-d H:i:s');
            $result=$this->db->update('customers',$data,['user_id'=>$data['user_id']]);
            $msg="Address Updated Successfully!";
        }
        if($result){
            return array("status"=>true,"message"=>$msg);
        }
        else{
            $error=$this->db->error();
            return array("status"=>false,"message"=>$error['message']);
        }
    }
    
    public function getaddress($where){
        $columns="t1.address,t2.name as state_name,t3.name as district_name,t1.pincode";
        $this->db->select($columns);
        $this->db->where($where);
        $this->db->from('customers t1');
        $this->db->join('area t2','t1.parent_id=t2.id','left');
        $this->db->join('area t3','t1.area_id=t3.id','left');
        $this->db->join('users t4','t1.added_by=t4.id','left');
        $query=$this->db->get();
        $array=$query->unbuffered_row('array');
        return $array;
    }
    
    
}
