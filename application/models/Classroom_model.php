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
                'Sala' => $row->number
            );
            $i++;
        }
        //print_r($className);
        return $className;
    }

    public function getTurmasById($id)
    {
        $collection = $this->db->get_where('student_classroom', array('user_id' => $id));
        $i = 0;
        $className[] = '';
        if (!empty($collection)) {
            foreach ($collection->result() as $row) {
                $classroom = $this->db->get_where('classroom', array('id' => $row->classroom_id))->row();
                $subject_name = $this->db->get_where('subject', array('id' => $classroom->subject_id))->row()->name;
                $className[$i] = array(
                    'Id' => $classroom->id,
                    'Turma' => $subject_name,
                    'Campus' => $classroom->campus,
                    'Predio' => $classroom->building,
                    'Sala' => $classroom->number
                );
                $i++;
            }
        }
        //print_r($className);
        return $className;
    }

    public function getTurmasByName($name)
    {
        $this->db->like('name', $name, 'both');
        $name = $this->db->get('subject');
        $i = 0;
        foreach ($name->result() as $row) {
            $classroom = $this->db->get_where('classroom', array('subject_id' => $row->id))->row();
            $className[$i] = array(
                'Id' => $classroom->id,
                'Turma' => $row->name,
                'Campus' => $classroom->campus,
                'Predio' => $classroom->building,
                'Sala' => $classroom->number
            );
            $i++;
        }
        // print_r($className);
        // echo '<br>';
        return $className;
    }

}
?>