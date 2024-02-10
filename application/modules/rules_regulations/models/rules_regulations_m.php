<?php
class Rules_regulations_m extends MY_Model{

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
        $this->db_set();
    }

    function create($data)
    {
        $this->db->insert('rules_regulations', $data);
        return $this->db->insert_id();
    }

    function list_all()
    {
        return $this->db->order_by('id','asc')->get('rules_regulations')->result();
     }

	 function find($id)
    {
        return $this->db->where(array('id' => $id))->get('rules_regulations')->row();
     }


    function exists($id)
    {
          return $this->db->where( array('id' => $id))->count_all_results('rules_regulations') >0;
    }


    function count()
    {
        
        return $this->db->count_all_results('rules_regulations');
    }

    function update_attributes($id, $data)
    {
         return  $this->db->where('id', $id) ->update('rules_regulations', $data);
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
        return $this->db->delete('rules_regulations', array('id' => $id));
     }

     /**
     * Setup DB Table Automatically
     * 
     */
     function db_set( )
     {
             $this->db->query(" 
	CREATE TABLE IF NOT EXISTS  rules_regulations (
	id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
	title  varchar(256)  DEFAULT '' NOT NULL, 
	content  text  , 
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
            $query = $this->db->get('rules_regulations', $limit, $offset);

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