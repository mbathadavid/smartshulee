<?php

class Subject_categories_m extends MY_Model
{

        function __construct()
        {
                // Call the Model constructor
                parent::__construct();
                $this->db_set();
        }

        function create($data)
        {
                $this->db->insert('subject_categories', $data);
                return $this->db->insert_id();
        }

        function find($id)
        {
                return $this->db->where(array('id' => $id))->get('subject_categories')->row();
        }

        function exists($id)
        {
                return $this->db->where(array('id' => $id))->count_all_results('subject_categories') > 0;
        }

        function count()
        {

                return $this->db->count_all_results('subject_categories');
        }

        function update_attributes($id, $data)
        {
                return $this->db->where('id', $id)->update('subject_categories', $data);
        }

        function populate($table, $option_val, $option_text)
        {
                $query = $this->db->select('*')->order_by($option_text)->get($table);
                $dropdowns = $query->result();
                $options = array();
                foreach ($dropdowns as $dropdown)
                {
                        $options[$dropdown->$option_val] = $dropdown->$option_text;
                }
                return $options;
        }

        function delete($id)
        {
                return $this->db->delete('subject_categories', array('id' => $id));
        }

        /**
         * Setup DB Table Automatically
         * 
         */
        function db_set()
        {
                $this->db->query(" 
	CREATE TABLE IF NOT EXISTS  subject_categories (
	id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
	subject  INT(9) NOT NULL, 
	category  varchar(32)  DEFAULT '' NOT NULL, 
	created_by INT(11), 
	modified_by INT(11), 
	created_on INT(11) , 
	modified_on INT(11) 
	) ENGINE=InnoDB  DEFAULT CHARSET=utf8; ");
        }

        function paginate_all($limit, $page)
        {
                $offset = $limit * ( $page - 1);

                $this->db->order_by('id', 'asc');
                $query = $this->db->get('subject_categories', $limit, $offset);

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
