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
        $data['user_firstname'] = $this->session->userdata('user_firstname');
        $data['table'] = $this->Classroom_model->getTurmasByUserId($data['user_id'], null);
        var_dump(empty($data['table']));
        $this->session->set_userdata(array('table'=>$data['table']));
        $this->load->view('user/home.phtml', $data);
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

    
    public function insertTurma(){
        $userId = $this->session->userdata('user_id');
        $classroomId = $this->input->post('id');
        $this->Classroom_model->insertStudentclassroom($userId, $classroomId);

        $data['user_id'] = $this->session->userdata('user_id');
        $data['table'] = $this->Classroom_model->getTurmasByUserId($data['user_id'], null);
        $this->session->set_userdata(array('table'=>$data['table']));

        $classroomName = $this->Classroom_model->getClassroomName($classroomId)->name;
        $data = array('msg'=>'Turma ' .$classroomName. ' adicionada a lista.' );
        echo json_encode($data);
    }
    public function removeTurma(){
        $userId = $this->session->userdata('user_id');
        $classroomId = $this->input->post('id');
        $this->Classroom_model->removeStudentclassroom($userId, $classroomId);

        $data['user_id'] = $this->session->userdata('user_id');
        $data['table'] = $this->Classroom_model->getTurmasByUserId($data['user_id'], null);
        $this->session->set_userdata(array('table'=>$data['table']));

        $classroomName = $this->Classroom_model->getClassroomName($classroomId)->name;
        $data = array('msg'=>'Turma ' .$classroomName. ' removida da lista.' );
        echo json_encode($data);
    }
}
