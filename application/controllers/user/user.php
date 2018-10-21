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
        $data['table'] = $this->montaTabela($data['user_id']);
        $this->load->view('user/home.phtml', $data);
    }

    private function montaTabela($id)
    {
        $this->load->library('table');
        $this->table->set_heading('Turma', 'Campus', 'Predio', 'Sala');
        $turmas = $this->Classroom_model->getTurmas($id);
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

    public function addClassroom()
    {
        $data['user_id'] = $this->session->userdata('user_id');
        $this->load->view('user/add.phtml', $data);
    }
}