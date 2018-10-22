<?php
defined('BASEPATH') or exit('No direct script access allowed');

class User extends CI_Controller
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
        $data['table'] = $this->montaTabelaBy($data['user_id']);
        var_dump($data['table']);
        $this->load->view('user/home.phtml', $data);
    }
    public function logout()
    {
        $this->session->sess_destroy();
        redirect(base_url());
    }

    private function montaTabelaBy($aux)
    {
        $this->load->library('table');
        $this->table->set_heading('Turma', 'Campus', 'Predio', 'Sala');
        if ((int)$aux) {
            $turmas = $this->Classroom_model->getTurmasById($aux);
        } else {
            $turmas = $this->Classroom_model->getTurmasByName($aux);
        }
        if (!empty($turmas)) {
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
        }
        return $this->table->generate();
    }

    public function addTurma()
    {
        $data['user_id'] = $this->session->userdata('user_id');
        $data['table'] = $this->session->userdata('table');
        $this->load->view('user/add.phtml', $data);
    }

    public function searchTurma()
    {
        $name = $this->input->post('classroomname');
        $table = $this->montaTabelaBy($name);
        $data = array('table' => $table);
        $this->session->set_userdata($data);
        $this->addTurma();
    }

    public function setTurma($id)
    {
        $data = array(
            'user_id' => $this->session->userdata('user_id'),
            'classroom_id' => $id
        );
        $this->db->insert('student_classroom', $data);
        redirect(site_url('user/user'));
    }
    public function removeTurma($id)
    {
        $data = array(
            'user_id' => $this->session->userdata('user_id'),
            'classroom_id' => $id
        );
        $this->db->delete('student_classroom', $data);
        redirect(site_url('user/user'));
    }
}