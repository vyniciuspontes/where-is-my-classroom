<?php
class Classroom_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getTurmas($id)
    {  
        $collection = $this->db->get_where('student_classroom', array('user_id' => $id));
        $i = 0;
        foreach ($collection->result() as $row) {
            $classroom = $this->db->get_where('classroom', array('id' => $row->classroom_id))->row();
            $subject_name = $this->db->get_where('subject', array('id' => $classroom->subject_id))->row()->name;
            $className[$i] = array(
                'Turma' => $subject_name,
                'Campus' => $classroom->campus,
                'Predio' => $classroom->building,
                'Sala' => $classroom->number
            );
            $i++;
        }
        //print_r($className);
        return $className;
    }

}
?>