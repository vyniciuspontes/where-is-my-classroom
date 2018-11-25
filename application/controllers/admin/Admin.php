<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Admin extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->Model('Classroom_model');

        if (!$this->session->userdata('logged')) {
            redirect();
        }
    }
    public function index()
    {
        $data['user_id'] = $this->session->userdata('user_id');
        $data['table'] = $this->Classroom_model->getTurmas();

        $this->load->view('admin/home.phtml', $data);
    }

    public function teachers(){
      $data['user_id'] = $this->session->userdata('user_id');
      $this->load->view('admin/teachers.phtml', $data);
    }

    public function subjects(){
      $data['user_id'] = $this->session->userdata('user_id');
      $this->load->view('admin/subjects.phtml', $data);
    }

    public function periods(){
      $data['user_id'] = $this->session->userdata('user_id');
      $this->load->view('admin/periods.phtml', $data);
    }


    public function logout()
    {
        $this->session->sess_destroy();
        redirect(base_url());
    }

    private function montaTabelaBy($aux)
    {
        //echo $aux; echo '<br>';
        if ((int) $aux) {
            $turmas = $this->Classroom_model->getTurmasByUserId($aux);
        } else {
            $turmas = $this->Classroom_model->getTurmasByName($aux);
            //print_r($turmas);
        }
        //echo "teste "; var_dump(isset($turmas));
        if (!empty($turmas)) {
            $this->load->library('table');
            $this->table->set_heading('Turma', 'Campus', 'Predio', 'Sala');
            foreach ($turmas as $turma) {
                $table_row = null;
                $table_row[] = $turma["Turma"];
                $table_row[] = $turma["Campus"];
                $table_row[] = $turma["Predio"];
                $table_row[] = $turma["Sala"];
                if ($this->session->userdata('logged')) {
                    $table_row[] = anchor('user/user/setTurma/' . $turma["Id"], 'Adicionar');
                    $table_row[] = anchor('user/user/removeTurma/' . $turma["Id"], 'Remover');
                }

                $this->table->add_row($table_row);
            }
            return $this->table->generate();
        }
        return '';
    }

    public function addTurma()
    {
        $data['user_id'] = $this->session->userdata('user_id');
        $data['table'] = $this->session->userdata('table');
        $this->load->view('user/home.phtml', $data);
    }

    public function searchTurma()
    {
        $data['user_id'] = $this->session->userdata('user_id');
        $name = $this->input->post('search');
        $table = $this->Classroom_model->getTurmasByUserId($data['user_id'], $name);
        $data = array('table' => $table);
        $this->session->set_userdata($data);
        $this->addTurma();
    }

    public function setTurma($id)
    {
        $data = array(
            'user_id' => $this->session->userdata('user_id'),
            'classroom_id' => $id,
        );
        $this->db->insert('student_classroom', $data);
        redirect(site_url('user/user'));
    }
    public function removeTurma($id)
    {
        $data = array(
            'user_id' => $this->session->userdata('user_id'),
            'classroom_id' => $id,
        );
        $this->db->delete('student_classroom', $data);
        redirect(site_url('user/user'));
    }
}
