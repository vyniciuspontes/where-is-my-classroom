<?php
defined('BASEPATH') or exit('No direct script access allowed');

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
		$this->load->Model('Classroom_model');
	}
	public function index()
	{
		if ($this->session->userdata('logged')){
			$data['user_id'] = $this->session->userdata('user_id');
			$data['table_user'] = $this->Classroom_model->getTurmasByUserId($data['user_id'], null);
		}
		//$data['table'] = $this->montaTabela();
		$data['table'] = $this->Classroom_model->getTurmas();
		$this->load->view('home.phtml', $data);
	}
	public function login(){
		$this->load->view('login.phtml');
	}

	public function loginValidate()
	{
		$this->load->library('form_validation');
		$validLogin = false;
		$this->form_validation->set_rules('login', 'Login', 'trim|required|valid_email');
		$this->form_validation->set_rules('senha', 'Senha', 'required');

		if (!$this->form_validation->run()) {
			$this->index();
			//redirect('./');
			//$this->load->view('home.phtml');
		} else {
			$login = $this->input->post('login');
			$senha = $this->input->post('senha');
			$query = $this->db->get_where('user', array('email' => $login, 'password' => $senha));
			$row = $query->row();
			if (isset($row)) {
				$data = array('user_id' => $row->id, 'user_firstname' => $row->first_name, 'is_admin' => $row->is_admin,'logged' => true);
				$this->session->set_userdata($data);
				if ($row->is_admin == 1) {
					redirect('admin/admin');
				} else {
					redirect('user/user');
				}
			} else {
				$data['error'] = 'Login ou Senha Inválidos';
				$this->load->view('home.phtml', $data);
			}
		}
	}
	private function montaTabela()
    {
        $this->load->library('table');
        $this->table->set_heading('Turma', 'Campus', 'Predio', 'Sala');
        $turmas = $this->Classroom_model->getTurmas();
        foreach ($turmas as $turma) {
            $table_row = null;
            $table_row[] = $turma["Turma"];
            $table_row[] = $turma["Campus"];
            $table_row[] = $turma["Predio"];
            $table_row[] = $turma["Sala"];
            $this->table->add_row($table_row);
        }
        return $this->table->generate();
	}
	public function searchTurma()
    {
		if ($this->session->userdata('logged')){
			$data['user_id'] = $this->session->userdata('user_id');
			$data['table_user'] = $this->Classroom_model->getTurmasByUserId($data['user_id'], null);
		}
        $name = $this->input->post('search');
        $data['table'] = $this->Classroom_model->getTurmasByName($name);
        $this->load->view('home.phtml', $data);
    }

}
