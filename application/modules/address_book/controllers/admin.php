<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends Admin_Controller
{
function __construct()
{
parent::__construct();
			/*$this->template->set_layout('default');
			$this->template->set_partial('sidebar','partials/sidebar.php')
                    -> set_partial('top', 'partials/top.php');*/ 
			if (!$this->ion_auth->logged_in())
	{
	redirect('admin/login');
	}
			/* if (!$this->ion_auth->is_in_group($this->user->id, 1) && !$this->ion_auth->is_in_group($this->user->id, 7))
        {
             $this->session->set_flashdata('message', array('type' => 'success', 'text' => '<b style="color:red">Sorry you do not have permission to access this Module!!</b>'));
			redirect('admin');
        }*/
			
			$this->load->model('address_book_m');
	}


	public function index()
	{
	   $config = $this->set_paginate_options(); 	//Initialize the pagination class
		$this->pagination->initialize($config);

		$page = ($this->uri->segment(4)) ? $this->uri->segment(4) : 1;

 		$data['address_book'] = $this->address_book_m->paginate_all($config['per_page'], $page);

            //create pagination links
            $data['links'] = $this->pagination->create_links();

	//page number  variable
	 $data['page'] = $page;
	  $data['title']='Address Book';
                $data['per'] = $config['per_page'];

            //load view
            $this->template->title(' Address Book ' )->build('admin/list', $data);
	}
	
	public function others()
	{
	  

 		$data['address_book'] = $this->address_book_m->others(); 
          $data['title']='Others Address Book';
            $this->template->title('Others Address Book ' )->build('admin/address_book', $data);
	}
	public function suppliers()
	{
	  

 		$data['address_book'] = $this->address_book_m->suppliers(); 
   $data['title']='Suppliers Address Book';
            $this->template->title(' Suppliers Address Book ' )->build('admin/address_book', $data);
	}
	
public function customers()
	{
	   

 		$data['address_book'] = $this->address_book_m->customer(); 

			//page number  variable
		
            $data['title']='Customers Address Book';
            //load view
            $this->template->title(' Customers Address Book ' )->build('admin/address_book', $data);
	}

	function create($page = NULL)
	{
            //create control variables
            $data['updType'] = 'create';
            $form_data_aux  = array();
            $data['page'] = ( $this->uri->segment(4) )  ? $this->uri->segment(4) : $page;
 
        //Rules for validation
        $this->form_validation->set_rules($this->validation());

            //validate the fields of form
            if ($this->form_validation->run() )
            {         //Validation OK!
          $user = $this -> ion_auth -> get_user();
        $form_data = array(
				'address_book' => $this->input->post('address_book'), 
				'title' => $this->input->post('title'), 
				'category' => $this->input->post('category'), 
				'company_name' => $this->input->post('company_name'), 
				'website' => $this->input->post('website'), 
				'address' => $this->input->post('address'), 
				'email' => $this->input->post('email'), 
				'phone' => $this->input->post('phone'), 
				'landline' => $this->input->post('landline'), 
				'city' => $this->input->post('city'), 
				'country' => $this->input->post('country'), 
				'description' => $this->input->post('description'), 
				 'created_by' => $user -> id ,   
				 'created_on' => time()
			);

            $ok=  $this->address_book_m->create($form_data);

            if ( $ok ) 
            {
				
				   $details = implode(' , ',$this->input->post());
					             $user = $this->ion_auth->get_user();
									$log = array(
										'module' =>  $this->router->fetch_module(), 
										'item_id' => $ok, 
										'transaction_type' => $this->router->fetch_method(), 
										'description' => base_url('admin').'/'.$this->router->fetch_module().'/'.$this->router->fetch_method().'/'.$ok, 
										'details' => $details,   
										'created_by' => $user -> id ,   
										'created_on' => time()
									);

								  $this->ion_auth->create_log($log);
								  
                    $this->session->set_flashdata('message', array( 'type' => 'success', 'text' => lang('web_create_success') ));
            }
            else
            {
                    $this->session->set_flashdata('message', array( 'type' => 'error', 'text' => lang('web_create_failed') ));
            }

			redirect('admin/address_book/');

	  	}else
                {
                $get = new StdClass();
                foreach ($this -> validation() as $field)
                {   
                         $get->{$field['field']}  = set_value($field['field']);
                 }
		 
                 $data['result'] = $get; 
                 $data['category'] = $this->address_book_m->get_category(); 
		 //load the view and the layout
		 $this->template->title('Add Address Book ' )->build('admin/create', $data);
		}		
	}

	function edit($id = FALSE, $page = 0)
	{ 
          
            //get the $id and sanitize
            $id = ( $id != 0 ) ? filter_var($id, FILTER_VALIDATE_INT) : NULL;

            $page = ( $page != 0 ) ? filter_var($page, FILTER_VALIDATE_INT) : NULL;

            //redirect if no $id
            if (!$id){
                    $this->session->set_flashdata('message', array( 'type' => 'warning', 'text' => lang('web_object_not_exist') ) );
                    redirect('admin/address_book/');
            }
         if(!$this->address_book_m-> exists($id) )
             {
             $this->session->set_flashdata('message', array( 'type' => 'warning', 'text' => lang('web_object_not_exist') ) );
            redirect('admin/address_book');
              }
        //search the item to show in edit form
        $get =  $this->address_book_m->find($id); 
            //variables for check the upload
            $form_data_aux = array();
            $files_to_delete  = array(); 
            //Rules for validation
            $this->form_validation->set_rules($this->validation());

            //create control variables
            $data['updType'] = 'edit';
            $data['page'] = $page;

            if ($this->form_validation->run() )  //validation has been passed
             {
			$user = $this -> ion_auth -> get_user();
            // build array for the model
            $form_data = array( 
							'address_book' => $this->input->post('address_book'), 
							'title' => $this->input->post('title'), 
							'category' => $this->input->post('category'), 
							'company_name' => $this->input->post('company_name'), 
							'website' => $this->input->post('website'), 
							'address' => $this->input->post('address'), 
							'email' => $this->input->post('email'), 
							'phone' => $this->input->post('phone'), 
							'landline' => $this->input->post('landline'), 
							'city' => $this->input->post('city'), 
							'country' => $this->input->post('country'), 
							'description' => $this->input->post('description'), 
				 'modified_by' => $user -> id ,   
				 'modified_on' => time() );

        //add the aux form data to the form data array to save
        $form_data = array_merge($form_data_aux, $form_data);

        //find the item to update
        
            $done = $this->address_book_m->update_attributes($id, $form_data);

        
        if ( $done) 
                {
					
					  $details = implode(' , ',$this->input->post());
					  $user = $this->ion_auth->get_user();
						$log = array(
							'module' =>  $this->router->fetch_module(), 
							'item_id' => $done, 
							'transaction_type' => $this->router->fetch_method(), 
							'description' => base_url('admin').'/'.$this->router->fetch_module().'/'.$this->router->fetch_method().'/'.$done, 
							'details' => $details,   
							'created_by' => $user -> id ,   
							'created_on' => time()
						);

					  $this->ion_auth->create_log($log);
								  
								  
				$this->session->set_flashdata('message', array( 'type' => 'success', 'text' => lang('web_edit_success') ));
				redirect("admin/address_book/");
			}

			else
			{
				$this->session->set_flashdata('message', array( 'type' => 'error', 'text' => $done->errors->full_messages() ) );
				redirect("admin/address_book/");
			}
	  	}
             else
             {
                 foreach (array_keys($this -> validation()) as $field)
                {
                        if (isset($_POST[$field]))
                        {  
                                $get -> $field = $this -> form_validation -> $field;
                        }
                }
		}
               $data['result'] = $get;
			    $data['category'] = $this->address_book_m->get_category(); 
             //load the view and the layout
             $this->template->title('Edit Address Book ' )->build('admin/create', $data);
	}


	function delete($id = NULL, $page = 1)
	{
		//filter & Sanitize $id
		$id = ($id != 0) ? filter_var($id, FILTER_VALIDATE_INT) : NULL;

		//redirect if its not correct
		if (!$id){
			$this->session->set_flashdata('message', array( 'type' => 'warning', 'text' => lang('web_object_not_exist') ) );

			redirect('admin/address_book');
		}

		//search the item to delete
		if ( !$this->address_book_m->exists($id) )
		{
			$this->session->set_flashdata('message', array( 'type' => 'warning', 'text' => lang('web_object_not_exist') ) );

			redirect('admin/address_book');
		}
 
		//delete the item
	 if ( $this->address_book_m->delete($id) == TRUE) 
		{
			 // $details = implode(' , ',$this->input->post());
			 $user = $this->ion_auth->get_user();
				$log = array(
					'module' =>  $this->router->fetch_module(), 
					'item_id' => $id, 
					'transaction_type' => $this->router->fetch_method(), 
					'description' => base_url('admin').'/'.$this->router->fetch_module().'/'.$this->router->fetch_method().'/'.$id, 
					//'details' => $details,   
					'created_by' => $user -> id ,   
					'created_on' => time()
				);

			  $this->ion_auth->create_log($log);
			
			$this->session->set_flashdata('message', array( 'type' => 'sucess', 'text' => lang('web_delete_success') ));
		}
		else
		{
			$this->session->set_flashdata('message', array( 'type' => 'error', 'text' => lang('web_delete_failed') ) );
		}

		redirect("admin/address_book/");
	}
  
    private function validation()
    {
$config = array(
                 array(
		 'field' =>'address_book',
                 'label' => 'Address Book',
                 'rules' =>'required|xss_clean'),

                 array(
		 'field' =>'title',
                'label' => 'Title',
                'rules' => 'required|trim|xss_clean|min_length[0]|max_length[60]'),

                 array(
		 'field' =>'category',
                 'label' => 'Category',
                 'rules' =>'xss_clean'),

                 array(
		 'field' =>'company_name',
                'label' => 'Company Name',
                'rules' => 'trim|xss_clean|min_length[0]|max_length[60]'),

                 array(
		 'field' =>'website',
                'label' => 'Website',
                'rules' => 'trim|xss_clean|min_length[0]|max_length[60]'),

                 array(
		 'field' =>'address',
                'label' => 'Address',
                'rules' => 'trim|xss_clean|min_length[0]|max_length[60]'),

                 array(
		 'field' =>'email',
                'label' => 'Email',
                'rules' => 'trim|xss_clean|min_length[0]|max_length[60]'),

                 array(
		 'field' =>'phone',
                'label' => 'Phone',
                'rules' => 'required|trim|xss_clean|min_length[0]|max_length[60]'),

                 array(
		 'field' =>'landline',
                'label' => 'Landline',
                'rules' => 'trim|xss_clean|min_length[0]|max_length[60]'),

                 array(
		 'field' =>'city',
                'label' => 'City',
                'rules' => 'trim|xss_clean|min_length[0]|max_length[60]'),

                 array(
		 'field' =>'country',
                 'label' => 'Country',
                 'rules' =>'xss_clean'),

                array(
		 'field' =>'description',
                'label' => 'Description',
                'rules' => 'trim|xss_clean|min_length[0]|max_length[500]'),
		);
		$this->form_validation->set_error_delimiters("<br /><span class='error'>", '</span>');
return $config; 
	}
        

	private function set_paginate_options()
	{
		$config = array();
		$config['base_url'] = site_url() . 'admin/address_book/index/';
		$config['use_page_numbers'] = TRUE;
	       $config['per_page'] = 100;
            $config['total_rows'] = $this->address_book_m->count();
            $config['uri_segment'] = 4 ;

            $config['first_link'] = lang('web_first');
            $config['first_tag_open'] = "<li>";
            $config['first_tag_close'] = '</li>';
            $config['last_link'] = lang('web_last') ;
            $config['last_tag_open'] = "<li>";
            $config['last_tag_close'] = '</li>';
            $config['next_link'] = FALSE;
            $config['next_tag_open'] = "<li>";
            $config['next_tag_close'] = '</li>';
            $config['prev_link'] = FALSE;
            $config['prev_tag_open'] = "<li>";
            $config['prev_tag_close'] = '</li>';
            $config['cur_tag_open'] = '<li class="active">  <a href="#">';
            $config['cur_tag_close'] = '</a></li>';
            $config['num_tag_open'] = "<li>";
            $config['num_tag_close'] = '</li>';
            $config['full_tag_open'] = '<div class="pagination pagination-centered"><ul>';
            $config['full_tag_close'] = '</ul></div>';
            $choice = $config["total_rows"] / $config["per_page"];
            //$config["num_links"] = round($choice);

            return $config;
	} 
}