<?php
class Classroom_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }
    public function getTurmas()
    {
        $collection = $this->db->get('classroom');
        $i = 0;
        foreach ($collection->result() as $row) {
            $subject_name = $this->db->get_where('subject', array('id' => $row->subject_id))->row()->name;
            $className[$i] = array(
                'Turma' => $subject_name,
                'Campus' => $row->campus,
                'Predio' => $row->building,
                'Sala' => $row->number,
            );
            $i++;
        }
        //print_r($className);
        return $className;
    }

    public function getTurmasById($id)
    {
        //$collection = $this->db->get_where('student_classroom', array('user_id' => $id));
        //var_dump($collection->result());
        $this->db->select('*');
        $this->db->from('classroom');
        $this->db->join('student_classroom', 'student_classroom.classroom_id = classroom.id');
        $this->db->join('subject', 'subject.id = classroom.subject_id');
        $this->db->where('student_classroom.user_id', $id);
        $collection = $this->db->get();
        $i = 0;
        if (!empty($collection->result())) {
            $className[] = '';
            foreach ($collection->result() as $row) {
                $className[$i] = array(
                    'Id' => $row->id,
                    'Turma' => $row->name,
                    'Campus' => $row->campus,
                    'Predio' => $row->building,
                    'Sala' => $row->number,
                );
                $i++;
            }
            //print_r($className);
            return $className;
        }
        return '';
    }

    public function getTurmasByName($name)
    {
        $this->db->select('*');
        $this->db->from('classroom');
        $this->db->join('subject', 'subject.id = classroom.subject_id');
        $this->db->like('name', $name, 'both');
        $name = $this->db->get();
        $i = 0;
        foreach ($name->result() as $row) {
            //$classroom = $this->db->get_where('classroom', array('subject_id' => $row->id))->row();
            $className[$i] = array(
                'Id' => $row->id,
                'Turma' => $row->name,
                'Campus' => $row->campus,
                'Predio' => $row->building,
                'Sala' => $row->number,
            );
            $i++;
        }
        // print_r($className);
        // echo '<br>';
        return $className;
    }

}
