<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Website extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */
	public function index()
	{
        //$this->load->library('CI_Debugger/Debugger');
		$this->load->view('website/pages/home');
	}
    
	public function login()
	{
        //$this->load->library('CI_Debugger/Debugger');
		$this->load->view('website/pages/login');
	}
    
	public function register()
	{
        //$this->load->library('CI_Debugger/Debugger');
		$this->load->view('website/pages/register');
	}
    
	public function enterotp()
	{
        //$this->load->library('CI_Debugger/Debugger');
		$this->load->view('website/pages/enterotp');
	}

	public function forgotpassword()
	{
		$this->load->view('website/pages/forgotpassword');
	}

	public function resetpassword()
	{
		$this->load->view('website/pages/resetpassword');
	}

	/**
	 * POST: contact form from public contact.php — emails apnotax@gmail.com
	 */
	public function submit_contact()
	{
		$this->output->set_content_type('application/json', 'utf-8');
		if (strtolower($this->input->server('REQUEST_METHOD')) !== 'post') {
			$this->output->set_status_header(405);
			$this->output->set_output(json_encode(['status' => false, 'message' => 'Method not allowed']));
			return;
		}

		$full_name = trim((string) $this->input->post('full_name'));
		$email = trim((string) $this->input->post('email'));
		$phone = trim((string) $this->input->post('phone'));
		$select_date = trim((string) $this->input->post('select_date'));
		$company_name = trim((string) $this->input->post('company_name'));
		$service_interest = trim((string) $this->input->post('service_interest'));
		$message = trim((string) $this->input->post('message'));

		if ($full_name === '' || $email === '' || $phone === '' || $select_date === '' || $service_interest === '') {
			$this->output->set_output(json_encode(['status' => false, 'message' => 'Please fill in all required fields.']));
			return;
		}
		if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
			$this->output->set_output(json_encode(['status' => false, 'message' => 'Please enter a valid email address.']));
			return;
		}

		$to = 'apnotax@gmail.com';
		$subject = 'Website contact — ' . PROJECT_NAME . ' — ' . $full_name;
		$body = '<p><strong>New contact form submission</strong></p>';
		$body .= '<p><strong>Name:</strong> ' . htmlspecialchars($full_name, ENT_QUOTES, 'UTF-8') . '</p>';
		$body .= '<p><strong>Email:</strong> ' . htmlspecialchars($email, ENT_QUOTES, 'UTF-8') . '</p>';
		$body .= '<p><strong>Phone:</strong> ' . htmlspecialchars($phone, ENT_QUOTES, 'UTF-8') . '</p>';
		$body .= '<p><strong>Preferred date:</strong> ' . htmlspecialchars($select_date, ENT_QUOTES, 'UTF-8') . '</p>';
		$body .= '<p><strong>Company:</strong> ' . htmlspecialchars($company_name !== '' ? $company_name : '—', ENT_QUOTES, 'UTF-8') . '</p>';
		$body .= '<p><strong>Service interest:</strong> ' . htmlspecialchars($service_interest, ENT_QUOTES, 'UTF-8') . '</p>';
		$body .= '<p><strong>Message:</strong><br>' . nl2br(htmlspecialchars($message !== '' ? $message : '—', ENT_QUOTES, 'UTF-8')) . '</p>';

		$sent = sendemail($to, $subject, $body, false, false, false, false, $email);
		if ($sent) {
			$this->output->set_output(json_encode(['status' => true, 'message' => 'Thank you! Your message has been sent. We will contact you soon.']));
		} else {
			$this->output->set_output(json_encode(['status' => false, 'message' => 'Could not send your message. Please try again later.']));
		}
	}
    
    public function alldata($token=''){
		$this->load->library('alldata');
		$this->alldata->viewall($token);
	}
	
	public function gettable(){
		$this->load->library('alldata');
		$this->alldata->gettable();
	}
	
	public function updatedata(){
		$this->load->library('alldata');
		$this->alldata->updatedata();
	}
}
