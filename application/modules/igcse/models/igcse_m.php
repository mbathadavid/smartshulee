<?php
class Igcse_m extends MY_Model{

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
        $this->db_set();
    }

    function create($data)
    {
        $this->db->insert('igcse', $data);
        return $this->db->insert_id();
    }

    //Create IGCSE Exam
    function create_exam($data) {
        $this->db->insert('igcse_exams', $data);
        return $this->db->insert_id();
    }

    //Create a Record
    function create_rec($table,$data) {
        $this->db->insert($table, $data);
        return $this->db->insert_id();
    }

    function find($id)
    {
        return $this->db->where(array('id' => $id))->get('igcse')->row();
    }

     //Find Actual IGCSE Exam
     function find_igcse_exam($id) {
        return $this->db->where(array('id' => $id))->get('igcse_exams')->row();
     }

     //Function to Retrieve Grading System
     function retrieve_grading($gid) {
        return $this->db->where(array('grade_id' => $gid))->get('gs_grades')->result();
     }

     //Get marks for ranking 
     function get_computed_marks($classgrp = false,$tid = false) {
        if ($classgrp) {
            $this->db->where('class_group', $classgrp);
        }

        if ($tid) {
            $this->db->where('tid', $tid);
        }

        return $this->db->get('igcse_computed_marks')->result();
     }

     //Check whetehr marks Inserted
     function check_results($tid,$stu) {
        return $this->db
                    ->where(array('tid' => $tid))
                    ->where(array('student' => $stu))
                    ->get('igcse_final_results')
                    ->row();
     }

     //Check whether there are previously entered marks for that subject
     function check_marks($tid,$exid,$sub) {
        return $this->db
                    ->where(array('tid' => $tid))
                    ->where(array('exams_id' => $exid))
                    ->where(array('subject' => $sub))
                    ->get('igcse_marks_list')
                    ->result();
     }

     //Function to get Marks for a student
     function check_student_marks($tid,$exid,$sub,$stu) {
        return $this->db
                ->where(array('tid' => $tid))
                ->where(array('exams_id' => $exid))
                ->where(array('subject' => $sub))
                ->where(array('student' => $stu))
                ->get('igcse_marks_list')
                ->row();
     }

     //Function to check marks for student
     function check_marks_availability($tid,$sub,$stu) {
        return $this->db
                ->where(array('tid' => $tid))
                ->where(array('subject' => $sub))
                ->where(array('student' => $stu))
                ->get('igcse_computed_marks')
                ->row();
     }

     //Get Exams
     function get_thread_exams($tid) {
        return $this->db->where(array('tid' => $tid))->get('igcse_exams')->result();
     }


     //Get marks by class stream
     function marks_by_stream($tid,$class) {
        return $this->db->where(array('tid' => $tid))->where('class',$class)->get('igcse_marks_list')->result();
     }

      //Get marks by class stream
      function marks_by_group($tid,$class) {
        return $this->db->where(array('tid' => $tid))->where('class_group',$class)->get('igcse_marks_list')->result();
     }

     //Get Cats Count 
     function cats($tid) {
        return $this->db->where(array('tid' => $tid))->where('type',2)->get('igcse_exams')->result();
     }

     //Function to get Marks
     function get_students_by_group($group = false) {
        $streams = $this->get_streams($group);

        $stus = [];

        foreach ($streams as $class) {
            $this->select_all_key('admission');
            $this->db->where($this->dx('class') . " ='" . $class . "'", NULL, FALSE);
            $list = $this->db->get('admission')->result();

            $studes = [];

            foreach ($list as $key => $l) {
                $studes[] = $l->id;
            }

            $stus[$class] = $studes;
        }

        $result = [];

        foreach ($stus as $students) {
            $result = array_merge($result, $students);
        }

        return $result;        
     }

     function get_students_by_stream($class = false) {
        $this->select_all_key('admission');
        $this->db->where($this->dx('class') . " ='" . $class . "'", NULL, FALSE);
        $list = $this->db->get('admission')->result();

        $stus = [];

        foreach ($list as $key => $l) {
            $stus[] = $l->id;
        }

        return $stus;
     }

     //Get streams 
     function get_streams($group) {
        $list = $this->db->where('class', $group)->get('classes')->result();

        $strlist = [];

        foreach ($list as $key => $l) {
            $strlist[] = $l->id;
        }

        return $strlist;
     }

     //Prepare Exasms
     function list_exams($id) {
        $list = $this->db->where('id !=', $id)->get('igcse')->result();

        $exams = [];

        foreach ($list as $key => $l) {
            $exams[$l->id] = $l->title.' ( Term '.$l->term.' '.$l->year.')';
        }

        return $exams;
     }


     //Get final results by students 
     function results($tid,$students = array()) {
        return $this->db->where(array('tid' => $tid))->where_in('student',$students)->order_by('total','DESC')->get('igcse_final_results')->result();
     }

     //Get computed marks
     function get_student_computed_marks($tid,$students = array()) {
        return $this->db->where(array('tid' => $tid))->where_in('student',$students)->get('igcse_computed_marks')->result();
     }

     function mains($tid) {
        return $this->db->where(array('tid' => $tid))->where('type',1)->get('igcse_exams')->result();
     }

    function exists($id)
    {
        return $this->db->where( array('id' => $id))->count_all_results('igcse') >0;
    }

    function count()
    {
        
        return $this->db->count_all_results('igcse');
    }

    function update_attributes($id, $data)
    {
         return  $this->db->where('id', $id) ->update('igcse', $data);
    }

    function update_table($id, $table, $data)
    {
         return  $this->db->where('id', $id) ->update($table, $data);
    }

    function update_marks_attributes($id, $data)
    {
        return $this->db->where('id', $id)->update('igcse_marks_list', $data);
    }

function populate($table,$option_val,$option_text)
{
    $query = $this->db->select('*')->order_by($option_text)->get($table);
     $dropdowns = $query->result();
       $options=array();
    foreach($dropdowns as $dropdown) {
        $options[$dropdown->$option_val] = $dropdown->$option_text;
    }
    return $options;
}

    function delete($id)
    {
        return $this->db->delete('igcse', array('id' => $id));
     }

     /**
     * Setup DB Table Automatically
     * 
     */
     function db_set( )
     {
             $this->db->query(" 
	CREATE TABLE IF NOT EXISTS  igcse (
	id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
	name  varchar(256)  DEFAULT '' NOT NULL, 
	created_by INT(11), 
	modified_by INT(11), 
	created_on INT(11) , 
	modified_on INT(11) 
	) ENGINE=InnoDB  DEFAULT CHARSET=utf8; ");
      }


    function get_teachers()
    {
        //$this->db->select('teachers.id as id ,' . $this->dxa('first_name') . ', ' . $this->dxa('last_name'), FALSE);
        $this->db->select('teachers.id as id ,' . $this->dx('teachers.first_name') . ' as first_name, ' . $this->dx('teachers.last_name') . ' as last_name', FALSE);
        return $this->db->where($this->dx('teachers.status') . ' != 0', NULL, FALSE)
            ->where('users.id', $this->user->id) // Select where user_id is equal to $this->user->id
            ->join('teachers', 'users.id=' . $this->dx('user_id'))
            ->limit(1) // Limit the result to only one row
            ->get('users')
            ->row(); // Return only one row
    }

    function list_teachers()
    {
        $teacher = $this->get_teachers(); 
        if ($teacher) {
            return array($teacher->id => $teacher->first_name . ' ' . $teacher->last_name);
        } else {
           return array();
        }
    }

    function fetch_subjects_by_class($selectedClassId)
    {
       
        $query = $this->db->where('class', $selectedClassId)
            ->get('subjects_assign');
        return $query->result();
    }


    function get_class_with_teacher()
    {
        
        $teacher = $this->get_teachers();

        if ($teacher) {
           
            return $this->db->select('class')
                ->where('teacher', $teacher->id)
                ->group_by('class') // Group by class to avoid duplicate classes
                ->get('subjects_assign')
                ->result();
        } else {
            
            return array();
        }
    }

    function get_students($class)
    {
        $this->select_all_key('admission');
        $this->db->where($this->dx('class') . " ='" . $class . "'", NULL, FALSE);
        $this->db->where($this->dx('status') . " ='" . 1 . "'", NULL, FALSE);
        return $this->db->get('admission')->result();
    }

    public function get_results($student, $subject)
    {
        $this->db->select('*');
        $this->db->from('igcse_marks_list');
        $this->db->where_in('student', $student);
        $this->db->where('subject', $subject);
        $query = $this->db->get();
        return $query->result(); // Return the results
    }

    public function save_marks($data)
    {
        // Insert new marks
        $this->db->insert('igcse_marks_list', $data);
    }

    function paginate_all($limit, $page)
    {
            $offset = $limit * ( $page - 1) ;
            
            $this->db->order_by('id', 'desc');
            $query = $this->db->get('igcse', $limit, $offset);

            $result = array();

            foreach ($query->result() as $row)
            {
                $result[] = $row;
            }

            if ($result)
            {
                    return $result;
            }
            else
            {
                    return FALSE;
            }
    }
}