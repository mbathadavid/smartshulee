<?php
class Boarding_m extends MY_Model{

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
        $this->db_set();
    }

    function create($data)
    {
        $this->db->insert('boarding', $data);
        return $this->db->insert_id();
    }

    function create_comment($data)
    {
        $this->db->insert('boarding_comments', $data);
        return $this->db->insert_id();
    }

    function find($id)
    {
        return $this->db->where(array('id' => $id))->get('boarding')->row();
     }

     //Get all students in a class
     function students_by_class($id)
    {
        $this->select_all_key('admission');
        
        $list = $this->db
                    ->where($this->dx('class') . " ='" . $id . "'", NULL, FALSE)
                    ->get('admission')
                    ->result();

        $stus = [];

        foreach ($list as $key => $l) {
            $stus[] = $l->id;
        }

        return $stus;
    }

    //Fetch Marks by Students
    function get_comments($students,$term,$year) {
        // print_r($students);
        // die;

        return $this->db
                    ->where_in('student',$students)
                    ->where(array('term' => $term))
                    ->where(array('year' => $year))
                    ->get('boarding')
                    ->result();
    }

    //Function to get class teacher
    function get_class_teacher($id) {
        return $this->db
                    ->where_in('id',$id)
                    ->get('classes')
                    ->result();
    }

    function get($id)
    {
        $this->select_all_key('teachers');
        return $this->db->where($this->dx('user_id') . ' = ' . $id, NULL, FALSE)->get('teachers')->row();
    }

    //Get General comment
    function get_comment($student,$term,$year) {
        return $this->db
                    ->where(array('student' => $student))
                    ->where(array('term' => $term))
                    ->where(array('year' => $year))
                    ->get('boarding_comments')
                    ->row();
    }

     //Find whether previously added
     function checkratings($student,$term,$year,$item) {
        return $this->db
                    ->where(array('student' => $student))
                    ->where(array('term' => $term))
                    ->where(array('year' => $year))
                    ->where(array('item' => $item))
                    ->get('boarding')
                    ->row();
     }

     //Check if comment was previously added
     function checkcomments($student,$term,$year) {
        return $this->db
                    ->where(array('student' => $student))
                    ->where(array('term' => $term))
                    ->where(array('year' => $year))
                    ->get('boarding_comments')
                    ->row();
     }

     function myclasses(){
      
        $this->db->where('class_teacher', $this->profile->user_id);
        $query = $this->db->get('classes');
        return $query->result(); 
    }

    function get_students_by_stream($class = false) {
        $this->select_all_key('admission');
        $this->db->where($this->dx('class') . " ='" . $class . "'", NULL, FALSE);
        $list = $this->db->get('admission')->result();

        return $list;

        // $stus = [];

        // foreach ($list as $key => $l) {
        //     $stus[] = $l->id;
        // }

        // return $stus;
     }

    function exists($id)
    {
          return $this->db->where( array('id' => $id))->count_all_results('boarding') >0;
    }


    function count()
    {
        
        return $this->db->count_all_results('boarding');
    }

    function update_attributes($id, $data)
    {
         return  $this->db->where('id', $id) ->update('boarding', $data);
    }

    function update_comments($id, $data)
    {
         return  $this->db->where('id', $id) ->update('boarding_comments', $data);
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
        return $this->db->delete('boarding', array('id' => $id));
     }

     /**
     * Setup DB Table Automatically
     * 
     */
     function db_set( )
     {
             $this->db->query(" 
	CREATE TABLE IF NOT EXISTS  boarding (
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
            $query = $this->db->get('boarding', $limit, $offset);

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