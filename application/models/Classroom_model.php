<?php
class Classroom_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }
    public function getTurmas()
    {
        $this->db->select('*, subject.name as sname, teacher.name as tname, week_day.name as wname');
        $this->db->from('classroom');
        $this->db->join('student_classroom', 'student_classroom.classroom_id = classroom.id');
        $this->db->join('subject', 'subject.id = classroom.subject_id');
        $this->db->join('teacher', 'teacher.id = classroom.teacher_id');
        $this->db->join('classroom_week_day', 'classroom_week_day.classroom_id = classroom.id');
        $this->db->join('week_day', 'week_day.id = classroom_week_day.week_day_id');
        $collection = $this->db->get();
        //echo $this->db->last_query();
        //var_dump($collection->result());
        $i = 0;
        foreach ($collection->result() as $row) {
            $className[$i] = array(
                'turma' => $row->sname,
                'professor' => $row->tname,
                'horario_ini' => $row->start_time,
                'horario_fim' => $row->end_time,
                'dia' => $row->wname,
                'campus' => $row->campus,
                'predio' => $row->building,
                'sala' => $row->number,
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
                    'id' => $row->id,
                    'turma' => $row->name,
                    'campus' => $row->campus,
                    'predio' => $row->building,
                    'sala' => $row->number,
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
                'id' => $row->id,
                'turma' => $row->name,
                'campus' => $row->campus,
                'predio' => $row->building,
                'sala' => $row->number,
            );
            $i++;
        }
        // print_r($className);
        // echo '<br>';
        return $className;
    }

}
