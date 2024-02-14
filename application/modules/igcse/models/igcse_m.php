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
     function get_computed_marks() {
        return $this->db->get('igcse_computed_marks')->result();
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