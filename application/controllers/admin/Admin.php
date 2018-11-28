<?php
//defined('BASEPATH') or exit('No direct script access allowed');

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
        //$this->load->view('admin/classroom_create_edit.phtml');

        $data['user_id'] = $this->session->userdata('user_id');
        $data['table'] = $this->Classroom_model->getTurmas();

        $this->load->view('admin/home.phtml', $data);
    }

    public function teachers()
    {
        $data['user_id'] = $this->session->userdata('user_id');
        $data['table'] = $this->Classroom_model->getTeacherByName('');
        $this->load->view('admin/teachers.phtml', $data);
    }
    public function searchTeachers()
    {
        $data['user_id'] = $this->session->userdata('user_id');
        $name = $this->input->post('search');
        $data['table'] = $this->Classroom_model->getTeacherByName($name);
        $this->load->view('admin/teachers.phtml', $data);
    }

    public function subjects()
    {
        $data['user_id'] = $this->session->userdata('user_id');
        $data['table'] = $this->Classroom_model->getSubjectByName('');
        $this->load->view('admin/subjects.phtml', $data);
    }
    public function searchTurma()
    {
        $data['user_id'] = $this->session->userdata('user_id');
        $name = $this->input->post('search');
        $data['table'] = $this->Classroom_model->getSubjectByName($name);
        $this->load->view('admin/subjects.phtml', $data);
    }

    public function periods()
    {
        $data['user_id'] = $this->session->userdata('user_id');
        $data['table'] = $this->Classroom_model->getPeriodByName('');
        $this->load->view('admin/periods.phtml', $data);
    }
    public function searchPeriods()
    {
        $data['user_id'] = $this->session->userdata('user_id');
        $name = $this->input->post('search');
        $data['table'] = $this->Classroom_model->getPeriodByName($name);
        $this->load->view('admin/periods.phtml', $data);
    }

    public function classroom($id, $action)
    {
        if (!strcmp($action, 'editar')) {
            $data['turma'] = $this->Classroom_model->getTurmaById($id);
        }
        $data['action'] = $action;
        $data['user_id'] = $this->session->userdata('user_id');
        $data['teacherDropdown'] = $this->Classroom_model->getTeacherAsDropdown();
        $data['subjectDropdown'] = $this->Classroom_model->getSubjectAsDropdown();
        $data['periodDropdown'] = $this->Classroom_model->getPeriodAsDropdown();

        $this->load->view('admin/classroom_create_edit.phtml', $data);
    }

    public function logout()
    {
        $this->session->sess_destroy();
        redirect(base_url());
    }

    // private function montaTabelaBy($aux)
    // {
    //     //echo $aux; echo '<br>';
    //     if ((int) $aux) {
    //         $turmas = $this->Classroom_model->getTurmasByUserId($aux);
    //     } else {
    //         $turmas = $this->Classroom_model->getTurmasByName($aux);
    //         //print_r($turmas);
    //     }
    //     //echo "teste "; var_dump(isset($turmas));
    //     if (!empty($turmas)) {
    //         $this->load->library('table');
    //         $this->table->set_heading('Turma', 'Campus', 'Predio', 'Sala');
    //         foreach ($turmas as $turma) {
    //             $table_row = null;
    //             $table_row[] = $turma["Turma"];
    //             $table_row[] = $turma["Campus"];
    //             $table_row[] = $turma["Predio"];
    //             $table_row[] = $turma["Sala"];
    //             if ($this->session->userdata('logged')) {
    //                 $table_row[] = anchor('user/user/setTurma/' . $turma["Id"], 'Adicionar');
    //                 $table_row[] = anchor('user/user/removeTurma/' . $turma["Id"], 'Remover');
    //             }

    //             $this->table->add_row($table_row);
    //         }
    //         return $this->table->generate();
    //     }
    //     return '';
    // }

    // public function addTurma()
    // {
    //     $data['user_id'] = $this->session->userdata('user_id');
    //     $data['table'] = $this->session->userdata('table');
    //     $this->load->view('user/home.phtml', $data);
    // }

    // public function setTurma($id)
    // {
    //     $data = array(
    //         'user_id' => $this->session->userdata('user_id'),
    //         'classroom_id' => $id,
    //     );
    //     $this->db->insert('student_classroom', $data);
    //     redirect(site_url('user/user'));
    // }
    // public function removeTurma($id)
    // {
    //     $data = array(
    //         'user_id' => $this->session->userdata('user_id'),
    //         'classroom_id' => $id,
    //     );
    //     $this->db->delete('student_classroom', $data);
    //     redirect(site_url('user/user'));
    // }
    public function editTurma()
    {
        //$data = $this->input->post("teacher-select");
        foreach ($_POST as $key => $value) {
            $data[$key] = $this->input->post($key);
        }
        $this->Classroom_model->updateClassroom($data);
        redirect('admin/admin');
    }
    public function modalSubject()
    {
        foreach ($_POST as $key => $value) {
            $data[$key] = $this->input->post($key);
        }
        if ($data["action"] == 'edit') {
            $this->Classroom_model->updateSubject($data);
        } else if ($data["action"] == 'delete'){
            $this->Classroom_model->deleteSubject($data);
        } else if ($data["action"] == 'create') {
            $this->Classroom_model->createSubject($data);
        }
        redirect('admin/admin/subjects');
    }
    public function modalPeriod(){
        foreach ($_POST as $key => $value) {
            $data[$key] = $this->input->post($key);
        }
        var_dump($data);
        if ($data["action"] == 'edit') {
            $this->Classroom_model->updatePeriod($data);
        } else if ($data["action"] == 'delete'){
            $this->Classroom_model->deletePeriod($data);
        } else if ($data["action"] == 'create') {
            $this->Classroom_model->createPeriod($data);
        }
        redirect('admin/admin/periods');
    }
}
