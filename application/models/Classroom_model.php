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
        classroom.address as address,
        period.name as period,
        GROUP_CONCAT(distinct week_day.name order by week_day.id ASC SEPARATOR "," ) as wdays,
        classroom_week_day.start_time,
        classroom_week_day.end_time,
        maps_info,
        ', false);
        $this->db->from('classroom');
        //$this->db->join('student_classroom', 'student_classroom.classroom_id = classroom.id');
        $this->db->join('subject', 'subject.id = classroom.subject_id');
        $this->db->join('teacher', 'teacher.id = classroom.teacher_id');
        $this->db->join('period', 'period.id = classroom.period_id');
        $this->db->join('classroom_week_day', 'classroom_week_day.classroom_id = classroom.id');
        $this->db->join('week_day', 'week_day.id = classroom_week_day.week_day_id');
        $this->db->group_by(array("teacher.id", "subject.id", "classroom_week_day.start_time"));
        if (!$param) {
            $query = $this->db->get();
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

    private function getTurmasDetails()
    {
        return $this->db->select('
        classroom.id as cid,
        teacher.name as tname,
        subject.name as sname,
        classroom.number as number,
        classroom.campus as campus,
        classroom.building as building,
        classroom.address as address,
        period.name as period,
        GROUP_CONCAT(distinct week_day.name order by week_day.id ASC SEPARATOR "," ) as wdays,
        classroom_week_day.start_time,
        classroom_week_day.end_time,
        maps_info,
        ', false);
    }

    public function getDetalhesTurmasUsuario($userId, $queryString)
    {

        $this->db = $this->getTurmasDetails();
        $this->db->from('classroom');
        $this->db->join('subject', 'subject.id = classroom.subject_id');
        $this->db->join('teacher', 'teacher.id = classroom.teacher_id');
        $this->db->join('period', 'period.id = classroom.period_id');
        $this->db->join('classroom_week_day', 'classroom_week_day.classroom_id = classroom.id');
        $this->db->join('week_day', 'week_day.id = classroom_week_day.week_day_id');
        $this->db->join('student_classroom', 'student_classroom.classroom_id = classroom.id');
        $this->db->where('student_classroom.user_id', $userId);

        if ($queryString) {
            $this->db->like('subject.name', $queryString, 'both');
            $this->db->or_like('teacher.name', $queryString, 'both');
        }

        $this->db->group_by(array("teacher.id", "subject.id"));

        return $this->db->get();
    }

    public function getTableArray($collection)
    {
        $i = 0;
        foreach ($collection->result() as $row) {
            $tableArray[$i] = array(
                'id' => $row->cid,
                'turma' => $row->sname,
                'professor' => $row->tname,
                'horario_ini' => $row->start_time,
                'horario_fim' => $row->end_time,
                'dia' => $row->wdays,
                'campus' => $row->campus,
                'predio' => $row->building,
                'sala' => $row->number,
                'endereco' => $row->address,
                'periodo' => $row->period,
            );
            $i++;
        }
        return $tableArray;
    }

    public function getTurmas()
    {
        $collection = $this->joinQuery(0);
        $className[] = '';
        if (!empty($collection->result())) {
            $className = $this->getTableArray($collection);
        }
        //var_dump($className);
        return $className;
    }

    public function getTurmasByUserId($id, $queryString)
    {
        $collection = $this->getDetalhesTurmasUsuario($id, $queryString);
        $className[] = null;
        if (!empty($collection->result())) {
            $className = $this->getTableArray($collection);
        }
        return $className;
    }

    public function getTurmasByName($name)
    {
        $collection = $this->joinQuery($name);

        $i = 0;
        //var_dump($name->result());
        $className[] = '';
        if (!empty($collection->result())) {
            $className = $this->getTableArray($collection);
        }
        // print_r($className);
        // echo '<br>';
        return $className;
    }
    public function getTurmaById($id)
    {
        $this->db = $this->getTurmasDetails();
        $this->db->from('classroom');
        $this->db->join('subject', 'subject.id = classroom.subject_id');
        $this->db->join('teacher', 'teacher.id = classroom.teacher_id');
        $this->db->join('period', 'period.id = classroom.period_id');
        $this->db->join('classroom_week_day', 'classroom_week_day.classroom_id = classroom.id');
        $this->db->join('week_day', 'week_day.id = classroom_week_day.week_day_id');
        $this->db->where('classroom.id', $id);
        $collection = $this->db->get();
        //echo $this->db->last_query();
        $className[] = '';
        if (!empty($collection->result())) {
            $className = $this->getTableArray($collection);
        }
        return $className;
    }

    public function getOptionsAsDropdown($options, $optionName)
    {
        $dropdown = '<option value="">Selecione ' . $optionName . '</option>';
        if (count($options) > 0) {
            foreach ($options as $option) {
                $dropdown .= '<option value="' . $option->id . '">' . $option->name . '</option>';
            }
        }
        return $dropdown;
    }

    public function getTeacherAsDropdown()
    {
        $this->db->select('id, name');
        $options = $this->db->get('teacher')->result();
        return $this->getOptionsAsDropdown($options, 'o Professor');
    }
    public function getSubjectAsDropdown()
    {
        $this->db->select('id, name');
        $options = $this->db->get('subject')->result();
        return $this->getOptionsAsDropdown($options, 'a Matéria');
    }
    public function getPeriodAsDropdown()
    {
        $this->db->select('id, name');
        $options = $this->db->get('period')->result();
        return $this->getOptionsAsDropdown($options, 'o Período');
    }
    
    //CRUD TURMA
    public function updateClassroom($data)
    {
        $values = array(
            'teacher_id' => $data["teacher_id"],
            'subject_id' => $data["subject_id"],
            'campus' => $data["campus"],
            'building' => $data["building"],
            'number' => $data["number"],
            'address' => $data["address"],
            'period_id' => $data["period_id"],
        );
        $this->db->where('id', $data["id"]);
        $this->db->update('classroom', $values);

        $this->db->delete('classroom_week_day', array('classroom_id' => $data["id"]));
        var_dump($data);
        $count = count($data['week_day']);
        for ($i = 0; $i < $count; $i++) {
            $time = array(
                'classroom_id' => $data['id'],
                'week_day_id' => $data['week_day'][$i],
                'start_time' => $data['start_time'],
                'end_time' => $data['end_time']
            );
            $this->db->insert('classroom_week_day', $time);
        }

    }
    public function createClassRoom($data)
    {
        $values = array(
            'teacher_id' => $data['teacher_id'],
            'subject_id' => $data['subject_id'],
            'period_id' => $data['period_id'],
            'campus' => $data['campus'],
            'building' => $data['building'],
            'address' => $data['address'],
            'number' => $data['number']
        );
        $this->db->insert('classroom', $values);
        $last_id = $this->db->insert_id();
        $count = count($data['week_day']);
        for ($i = 0; $i < $count; $i++) {
            $time = array(
                'classroom_id' => $last_id,
                'week_day_id' => $data['week_day'][$i],
                'start_time' => $data['start_time'],
                'end_time' => $data['end_time']
            );
            $this->db->insert('classroom_week_day', $time);
        }
    }
    public function deleteClassroom($id){
        $this->db->delete('classroom_week_day', array('classroom_id' => $id));
        $this->db->delete('student_classroom', array('classroom_id' => $id));
        $this->db->delete('classroom', array('id' => $id));
    }

    //CRUD SUBJECT
    public function getSubjectByName($data)
    {
        $this->db->select('*');
        $this->db->from('subject');
        $this->db->like('name', $data, 'both');
        return $this->db->get()->result();
    }
    public function updateSubject($data)
    {
        $values = array(
            'name' => $data["name"],
            'code' => $data["code"],
        );
        $this->db->where('id', $data["id"]);
        $this->db->update('subject', $values);
    }
    public function deleteSubject($data)
    {
        $this->db->delete('subject', array('id' => $data["id"]));
    }
    public function createSubject($data)
    {
        $values = array(
            'name' => $data["name"],
            'code' => $data["code"],
        );
        $this->db->insert('subject', $values);
    }
    public function getClassroomName($id){
        $this->db->select('name');
        $this->db->from('subject');
        $this->db->join('classroom', 'classroom.subject_id = subject.id');
        $this->db->where('classroom.id',$id );
        return $this->db->get()->row();
    }

    //CRUD TEACHER
    public function getTeacherByName($data)
    {
        $this->db->select('*');
        $this->db->from('teacher');
        $this->db->like('name', $data, 'both');
        return $this->db->get()->result();
    }
    public function updateTeacher($data)
    {
        $values["name"] = $data["name"];
        if ($data["boolean"] == true) {
            $values["img_url"] = $data["path"];
        }
        $this->db->where('id', $data["id"]);
        $this->db->update('teacher', $values);
    }
    public function deleteTeacher($data)
    {
        $this->db->delete('teacher', array('id' => $data["id"]));
    }
    public function createTeacher($data)
    {
        $values['name'] = $data["name"];
        if ($data["boolean"] == true) {
            $values["img_url"] = $data["path"];
        }
        $this->db->insert('teacher', $values);
    }
    
    //CRUD PERIOD
    public function getPeriodByName($data)
    {
        $this->db->select('*');
        $this->db->from('period');
        $this->db->like('name', $data, 'both');
        return $this->db->get()->result();
    }
    public function updatePeriod($data)
    {
        $values = array(
            'name' => $data["name"],
        );
        $this->db->where('id', $data["id"]);
        $this->db->update('period', $values);
    }
    public function deletePeriod($data)
    {
        $this->db->delete('period', array('id' => $data["id"]));
    }
    public function createPeriod($data)
    {
        $values = array(
            'name' => $data["name"],
        );
        $this->db->insert('period', $values);
    }

    public function insertStudentclassroom($userId, $classroomId){
        $data = array(
            'user_id' => $userId,
            'classroom_id' => $classroomId,
        );
        $this->db->insert('student_classroom', $data);

    }
    public function removeStudentclassroom($userId, $classroomId){
        $data = array(
            'user_id' => $userId,
            'classroom_id' => $classroomId,
        );
        $this->db->delete('student_classroom', $data);

    }

}
