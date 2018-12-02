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
        $data['user_firstname'] = $this->session->userdata('user_firstname');
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
    public function modalTeacher()
    {
        foreach ($_POST as $key => $value) {
            $data[$key] = $this->input->post($key);
        }
        $name = $data['name'];
        $teacherImage = $_FILES['teacherImage'];
        $config = array(
            'upload_path' => './teacherimage',
            'allowed_types' => 'gif|jpg|png|jpeg',
            'file_name' => $name . $_FILES['teacherImage']['name'],
            'max_width' => '10000',
            'max_height' => '10000',
            'max_size' => '5000',
        );
        $this->upload->initialize($config);
        if ($this->upload->do_upload('teacherImage')) {
            $path = 'teacherimage/' . str_ireplace(' ', '_', $config['file_name']);
            $data['path'] = $path;
            $data['boolean'] = true;
        } else {
            $data['boolean'] = false;
        };

        if ($data["action"] == 'edit') {
            $this->Classroom_model->updateTeacher($data);
        } else if ($data["action"] == 'delete') {
            $this->Classroom_model->deleteTeacher($data);
        } else if ($data["action"] == 'create') {
            $this->Classroom_model->createTeacher($data);
        }
        redirect('admin/admin/teachers');
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
    public function modalSubject()
    {
        foreach ($_POST as $key => $value) {
            $data[$key] = $this->input->post($key);
        }
        if ($data["action"] == 'edit') {
            $this->Classroom_model->updateSubject($data);
        } else if ($data["action"] == 'delete') {
            $this->Classroom_model->deleteSubject($data);
        } else if ($data["action"] == 'create') {
            $this->Classroom_model->createSubject($data);
        }
        redirect('admin/admin/subjects');
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
    public function modalPeriod()
    {
        foreach ($_POST as $key => $value) {
            $data[$key] = $this->input->post($key);
        }
        if ($data["action"] == 'edit') {
            $this->Classroom_model->updatePeriod($data);
        } else if ($data["action"] == 'delete') {
            $this->Classroom_model->deletePeriod($data);
        } else if ($data["action"] == 'create') {
            $this->Classroom_model->createPeriod($data);
        }
        redirect('admin/admin/periods');
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

    public function editTurma()
    {
        //$data = $this->input->post("teacher-select");
        foreach ($_POST as $key => $value) {
            $data[$key] = $this->input->post($key);
        }
        $this->Classroom_model->updateClassroom($data);
        redirect('admin/admin');
    }
    public function createTurma(){
        echo 'ola';
        foreach ($_POST as $key => $value) {
            $data[$key] = $this->input->post($key);
        }
        var_dump($data);
    }

}
