<?php
class Clubs_m extends MY_Model{

    function __construct()
    {
        // Call the Model contructor
        parent::__construct();
        $this->db_set();
    }

    function create($data)
    {
        $this->db->insert('clubs', $data);
        return $this->db->insert_id();
    }

    function create_record($data)
    {
        $this->db->insert('clubs_ratings', $data);
        return $this->db->insert_id();
    }

    function find($id)
    {
        return $this->db->where(array('id' => $id))->get('clubs')->row();
     }


    function exists($id)
    {
          return $this->db->where( array('id' => $id))->count_all_results('clubs') >0;
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

      //Find whether previously added
      function checkratings($student,$term,$year,$item,$club) {
        return $this->db
                    ->where(array('student' => $student))
                    ->where(array('term' => $term))
                    ->where(array('year' => $year))
                    ->where(array('item' => $item))
                    ->where(array('club' => $club))
                    ->get('clubs_ratings')
                    ->row();
     }

     //Check if comment was previously added
     function checkcomments($student,$term,$year,$club) {
        return $this->db
                    ->where(array('student' => $student))
                    ->where(array('term' => $term))
                    ->where(array('year' => $year))
                    ->where(array('club' => $club))
                    ->get('clubs_comments')
                    ->row();
     }

     function update_comments($id, $data)
     {
          return  $this->db->where('id', $id) ->update('clubs_comments', $data);
     }

     //Fetch Marks by Students
    function get_comments($students,$term,$year,$club) {
        // print_r($students);
        // die;

        return $this->db
                    ->where_in('student',$students)
                    ->where(array('term' => $term))
                    ->where(array('year' => $year))
                    ->where(array('club' => $club))
                    ->get('clubs_ratings')
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
    function get_comment($student,$term,$year,$club) {
        return $this->db
                    ->where(array('student' => $student))
                    ->where(array('term' => $term))
                    ->where(array('year' => $year))
                    ->where(array('club' => $club))
                    ->get('clubs_comments')
                    ->row();
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



     function create_comment($data)
    {
        $this->db->insert('clubs_comments', $data);
        return $this->db->insert_id();
    }

    function myclasses(){
      
        $this->db->where('class_teacher', $this->profile->user_id);
        $query = $this->db->get('classes');
        return $query->result(); 
    }


    function count()
    {
        
        return $this->db->count_all_results('clubs');
    }

    function update_attributes($id, $data)
    {
         return  $this->db->where('id', $id) ->update('clubs', $data);
    }

    function update_record($id, $data)
    {
         return  $this->db->where('id', $id) ->update('clubs_ratings', $data);
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
        return $this->db->delete('clubs', array('id' => $id));
     }

     /**
     * Setup DB Table Automatically
     * 
     */
     function db_set( )
     {
             $this->db->query(" 
	CREATE TABLE IF NOT EXISTS  clubs (
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
            $query = $this->db->get('clubs', $limit, $offset);

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