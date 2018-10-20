<?php
//defined('BASEPATH') or exit('No direct script access allowed');

class Home extends CI_Controller
{

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

	public function __construct()
	{
		parent::__construct();
		$this->load->Model('Login_model');
	}
	public function index()
	{
		$this->load->view('home.phtml');
	}

	public function login()
	{
		$this->load->library('form_validation');
		$validLogin = false;
		if ($this->form_validation->set_rules('login', 'Login', 'trim|required|valid_email'));
		if ($this->form_validation->set_rules('senha', 'Senha', 'required'));

		if (!$this->form_validation->run()) {
			$this->load->view('home.phtml');
		} else {
			$login = $this->input->post('login');
			$senha = $this->input->post('senha');
			$query = $this->Login_model->getLogin($login);
			$row = $query->row();
			if (isset($row)) {
				if ($row->email == $login && $row->password == $senha) {
					$data['first_name'] = $row->first_name;
					if ($row->is_admin == 1) {
						$this->load->view('home_admin.phtml', $data);
					} else {
						$this->load->view('home_user.phtml', $data);
					}
				} else {
					$data['error'] = 'Login ou Senha Inválidos';
					$this->load->view('home.phtml', $data);
				}
			} else {
				$data['error'] = 'Login ou Senha Inválidos';
				$this->load->view('home.phtml', $data);
			}
		}

	}

}
