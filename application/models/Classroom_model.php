<?php
class Classroom_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }
    public function joinQuery($param)
    {
        $this->db->select('
        classroom.id as cid,
        teacher.name as tname,
        subject.name as sname,
        classroom.number as number,
        classroom.campus as campus,
        classroom.building as building,
        GROUP_CONCAT(distinct week_day.name order by week_day.id ASC SEPARATOR "," ) as wdays,
        classroom_week_day.start_time,
        classroom_week_day.end_time,
        maps_info,
        ', false);
        $this->db->from('classroom');
        //$this->db->join('student_classroom', 'student_classroom.classroom_id = classroom.id');
        $this->db->join('subject', 'subject.id = classroom.subject_id');
        $this->db->join('teacher', 'teacher.id = classroom.teacher_id');
        $this->db->join('classroom_week_day', 'classroom_week_day.classroom_id = classroom.id');
        $this->db->join('week_day', 'week_day.id = classroom_week_day.week_day_id');
        $this->db->group_by(array("teacher.id", "subject.id"));
        if (!$param) {
            $query = $this->db->get();
            // echo $this->db->last_query() . '<br>';
            // print_r($query->result());
            return $query;
        } else if ((int)$param) {
            $this->db->where('student_classroom.user_id', $param);
        } else {
            $this->db->like('subject.name', $param, 'both');
            $this->db->or_like('teacher.name', $param, 'both');


            //$this->db->like('teacher.name', $param, 'both');
            //print_r($turmas);
        }
        return $this->db->get();

    }

    private function getTurmasDetails(){
      return $this->db->select('
        classroom.id as cid,
        teacher.name as tname,
        subject.name as sname,
        classroom.number as number,
        classroom.campus as campus,
        classroom.building as building,
        GROUP_CONCAT(distinct week_day.name order by week_day.id ASC SEPARATOR "," ) as wdays,
        classroom_week_day.start_time,
        classroom_week_day.end_time,
        maps_info,
        ', false);
    }

    public function getDetalhesTurmasUsuario($userId, $queryString) {

      $this->db = $this->getTurmasDetails();
      $this->db->from('classroom');
      $this->db->join('subject', 'subject.id = classroom.subject_id');
      $this->db->join('teacher', 'teacher.id = classroom.teacher_id');
      $this->db->join('classroom_week_day', 'classroom_week_day.classroom_id = classroom.id');
      $this->db->join('week_day', 'week_day.id = classroom_week_day.week_day_id');
      $this->db->join('student_classroom', 'student_classroom.classroom_id = classroom.id');
      $this->db->where('student_classroom.user_id', $userId);

      if($queryString) {
          $this->db->like('subject.name', $queryString, 'both');
          $this->db->or_like('teacher.name', $queryString, 'both');
      }

      $this->db->group_by(array("teacher.id", "subject.id"));

      return $this->db->get();
    }

    public function getTurmas()
    {
        $collection = $this->joinQuery(0);
        //echo $this->db->last_query();
        //var_dump($collection->result());
        $i = 0;
        foreach ($collection->result() as $row) {
            $className[$i] = array(
                'id' => $row->cid,
                'turma' => $row->sname,
                'professor' => $row->tname,
                'horario_ini' => $row->start_time,
                'horario_fim' => $row->end_time,
                'dia' => $row->wdays,
                'campus' => $row->campus,
                'predio' => $row->building,
                'sala' => $row->number,
            );
            $i++;
        }
        //print_r($className);
        return $className;
    }

    public function getTurmasByUserId($id, $queryString)
    {
        //$collection = $this->db->get_where('student_classroom', array('user_id' => $id));
        //var_dump($collection->result());
        $collection = $this->getDetalhesTurmasUsuario($id, $queryString);
        $i = 0;
        if (!empty($collection->result())) {
            $className[] = '';
            foreach ($collection->result() as $row) {
              $className[$i] = array(
                  'id' => $row->cid,
                  'turma' => $row->sname,
                  'professor' => $row->tname,
                  'horario_ini' => $row->start_time,
                  'horario_fim' => $row->end_time,
                  'dia' => $row->wdays,
                  'campus' => $row->campus,
                  'predio' => $row->building,
                  'sala' => $row->number,
              );
                $i++;
            }
            //print_r($className);
            return $className;
        }
        return [];
    }

    public function getTurmasByName($name)
    {
        $name = $this->joinQuery($name);

        $i = 0;
        //var_dump($name->result());
        $className[] = '';
        foreach ($name->result() as $row) {
            //$classroom = $this->db->get_where('classroom', array('subject_id' => $row->id))->row();
            $className[$i] = array(
                'turma' => $row->sname,
                'professor' => $row->tname,
                'horario_ini' => $row->start_time,
                'horario_fim' => $row->end_time,
                'dia' => $row->wdays,
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
