<?php defined('BASEPATH') or exit('No direct script access allowed');

class Admin extends Admin_Controller
{
    function __construct()
    {
        parent::__construct();
        /*$this->template->set_layout('default');
			$this->template->set_partial('sidebar','partials/sidebar.php')
                    -> set_partial('top', 'partials/top.php');*/
        if (!$this->ion_auth->logged_in()) {
            redirect('admin/login');
        }
        $this->load->model('igcse_m');
        $this->load->model('exams/exams_m');
        
    }

    public function index()
    {
        $config = $this->set_paginate_options(); //Initialize the pagination class
        $this->pagination->initialize($config);
        $page = ($this->uri->segment(4)) ? $this->uri->segment(4) : 1;
        $data['igcse'] = $this->igcse_m->paginate_all($config['per_page'], $page);

        //create pagination links
        $data['links'] = $this->pagination->create_links();
        $data['classes'] = $this->exams_m->list_classes();

        //page number  variable
        $data['page'] = $page;
        $data['per'] = $config['per_page'];

        //load view
        $this->template->title(' Igcse ')->build('admin/list', $data);
    }

    function create($page = NULL)
    {
        //create control variables
        $data['updType'] = 'create';
        $form_data_aux  = array();
        $data['page'] = ($this->uri->segment(4))  ? $this->uri->segment(4) : $page;

        //Rules for validation
        $this->form_validation->set_rules($this->validation());
        $range = range(date('Y') - 50, date('Y'));
        $data['yrs'] = array_combine($range, $range);

        //validate the fields of form
        if ($this->form_validation->run()) {         //Validation OK!
            // echo "<pre>";
            //     print_r($this->input->post());
            // echo "</pre>";
            // die;

            $user = $this->ion_auth->get_user();
            $form_data = array(
                'title' => $this->input->post('title'),
                'term' => $this->input->post('term'),
                'year' => $this->input->post('year'),
                'cats_weight' => $this->input->post('cats_weight'),
                'main_weight' => $this->input->post('main_weight'),
                'description' => $this->input->post('description'),
                'created_by' => $user->id,
                'created_on' => time()
            );

            $ok =  $this->igcse_m->create($form_data);

            if ($ok) {
                $this->session->set_flashdata('message', array('type' => 'success', 'text' => lang('web_create_success')));
            } else {
                $this->session->set_flashdata('message', array('type' => 'error', 'text' => lang('web_create_failed')));
            }

            redirect('admin/igcse/');
        } else {
            $get = new StdClass();
            foreach ($this->validation() as $field) {
                $get->$field['field']  = set_value($field['field']);
            }

            $data['result'] = $get;
            //load the view and the layout
            $this->template->title('Add Igcse ')->build('admin/create', $data);
        }
    }

    function edit($id = FALSE, $page = 0)
    {

        //get the $id and sanitize
        $id = ($id != 0) ? filter_var($id, FILTER_VALIDATE_INT) : NULL;

        $page = ($page != 0) ? filter_var($page, FILTER_VALIDATE_INT) : NULL;

        //redirect if no $id
        if (!$id) {
            $this->session->set_flashdata('message', array('type' => 'warning', 'text' => lang('web_object_not_exist')));
            redirect('admin/igcse/');
        }
        if (!$this->igcse_m->exists($id)) {
            $this->session->set_flashdata('message', array('type' => 'warning', 'text' => lang('web_object_not_exist')));
            redirect('admin/igcse');
        }
        //search the item to show in edit form
        $get =  $this->igcse_m->find($id);
        //variables for check the upload
        $form_data_aux = array();
        $files_to_delete  = array();
        //Rules for validation
        $this->form_validation->set_rules($this->validation());

        //create control variables
        $data['updType'] = 'edit';
        $data['page'] = $page;

        if ($this->form_validation->run())  //validation has been passed
        {
            $user = $this->ion_auth->get_user();
            // build array for the model
            $form_data = array(
                'title' => $this->input->post('title'),
                'term' => $this->input->post('term'),
                'year' => $this->input->post('year'),
                'cats_weight' => $this->input->post('cats_weight'),
                'main_weight' => $this->input->post('main_weight'),
                'description' => $this->input->post('description'),
                'modified_by' => $user->id,
                'modified_on' => time()
            );

            //add the aux form data to the form data array to save
            $form_data = array_merge($form_data_aux, $form_data);

            //find the item to update

            $done = $this->igcse_m->update_attributes($id, $form_data);

            if ($done) {
                $this->session->set_flashdata('message', array('type' => 'success', 'text' => lang('web_edit_success')));
                redirect("admin/igcse/");
            } else {
                $this->session->set_flashdata('message', array('type' => 'error', 'text' => $done->errors->full_messages()));
                redirect("admin/igcse/");
            }
        } else {
            foreach (array_keys($this->validation()) as $field) {
                if (isset($_POST[$field])) {
                    $get->$field = $this->form_validation->$field;
                }
            }
        }
        $range = range(date('Y') - 50, date('Y'));
        $data['yrs'] = array_combine($range, $range);
        $data['result'] = $get;
        //load the view and the layout
        $this->template->title('Edit Igcse ')->build('admin/create', $data);
    }

    //Function to reciord exams
    function exams($id) {
        $thread = $this->igcse_m->find($id);

        if ($this->input->post()) {
            $user = $this->ion_auth->get_user();

            $form = [
                'tid' => $thread->id,
                'title' => $this->input->post('title'),
                'term' => $thread->term,
                'year' => $thread->year,
                'type' => $this->input->post('type'),
                'start_date' => strtotime($this->input->post('start_date')),
                'end_date' => strtotime($this->input->post('end_date')),
                'recording_end' => strtotime($this->input->post('recording_end_date')),
                'description' => $this->input->post('description'),
                'created_by' => $this->user->id,
                'created_on' => time()
            ];

            $ok = $this->igcse_m->create_exam($form);

            if ($ok)
            {
                $details = implode(' , ', $this->input->post());
                $user = $this->ion_auth->get_user();
                $log = array(
                    'module' => $this->router->fetch_module(),
                    'item_id' => $ok,
                    'transaction_type' => $this->router->fetch_method(),
                    'description' => base_url('admin') . '/' . $this->router->fetch_module() . '/' . $this->router->fetch_method() . '/' . $ok,
                    'details' => $details,
                    'created_by' => $user->id,
                    'created_on' => time()
                );

                $this->ion_auth->create_log($log);

                $this->session->set_flashdata('message', array('type' => 'success', 'text' => lang('web_create_success')));
            }
            else
            {
                $this->session->set_flashdata('message', array('type' => 'error', 'text' => lang('web_create_failed')));
            }

            redirect('admin/igcse/exams/'.$id);
            
        }

        $range = range(date('Y') - 50, date('Y'));
        $data['yrs'] = array_combine($range, $range);
        $data['thread'] = $thread;
        $data['exams'] = $this->igcse_m->get_thread_exams($id);
        $data['classes'] = $this->exams_m->list_classes();
        $data['id'] = $id;

        $this->template->title('Igcse Exam Threads')->build('admin/exams', $data);
    }

    //Compute Marks
    public function compute($id) {
        $thread = $this->igcse_m->find($id);
        $exams = $this->igcse_m->get_thread_exams($id);

        if ($this->input->post()) {
            echo "<pre>";
                print_r($this->input->post());
            echo "</pre>";
            die;
        }

        $data['thread'] = $thread;
        $data['exams'] = $exams;
        $data['id'] = $id;

        $this->template->title('Compute Marks')->build('admin/compute', $data);
    }

    public function record($thid,$exid,$id){
        $students = [];
        $sb = 0;
        //push class name to next view
        $class_name = $this->exams_m->populate('class_groups', 'id', 'name');
        $exam = $this->exams_m->find1($thid);
        $tar = $this->exams_m->get_stream($id);
        $class_id = $tar->class;
        $stream = $tar->stream;
        $heading = 'Exam Marks For: <span style="color:blue">' . $class_name[$class_id] . '</span>';
        $exam_type = $this->exams_m->get_exams_by_tid($thid);
    
        
        $subjects = $this->exams_m->get_subjects($id, $exam->term);

        // echo "<pre>";
        // print_r($tid);
        // echo "</pre>";
        // die;

        $sel = 0;
        if ($this->input->get('sb')) {
            $sb = $this->input->get('sb');
            $data['selected'] = isset($subjects[$sb]) ? $subjects[$sb] : [];
            $row = $this->exams_m->fetch_subject($sb);
            $rrname = $row ? ' - ' . $row->name : '';
            $heading = 'Exam Marks For: <span style="color:blue">' . $class_name[$class_id] . $rrname . '</span>';

            if ($row->is_optional == 2) {
                $sel = 1;
            }

            $data['checkmarks'] = $this->igcse_m->check_marks($thid,$exid,$sb);
            $students = $this->exams_m->get_students($class_id, $stream);
        }

        $data['list_subjects'] = $this->exams_m->list_subjects();
        $data['subjects'] = $subjects;
        $data['class_name'] = $heading;
        $data['assign'] = $sel;
        $data['count_subjects'] = $this->exams_m->count_subjects($class_id, $exam->term);
        $data['full_subjects'] = $this->exams_m->get_full_subjects();
        $data['thid'] = $thid;
        $data['exid'] = $exid;
        $data['sb'] = $sb;

        //create control variables
        $data['updType'] = 'create';
        $data['page'] = '';
        $data['exams'] = $this->exams_m->list_exams();
        $data['grading'] = $this->exams_m->get_grading_system();
        //Rules for validation
        $this->form_validation->set_rules($this->rec_validation());

        //validate the fields of form
        if ($this->form_validation->run()) {
            // echo "<pre>";
            //     print_r($this->input->post());
            // echo "</pre>";
            // die;

            if ($this->input->get('sb')) {
                $user = $this->ion_auth->get_user();
                $inc = [];
                $mkpost = $this->input->post();
                if (isset($mkpost['done'])) {
                    $inc = $mkpost['done'];
                }
                $sb = $this->input->get('sb');
                $gd_id = $this->input->post('grading');
                $marks = $this->input->post('marks');
                $units = $this->input->post('units');
                $k = 0;
                $kk = 0;
                
                // $this->exams_m->set_grading($exid, $id, $sb, $gd_id, $user->id);
                $perf_list = $this->_prep_marks($sb, $exid, $marks, $units);

                foreach ($perf_list as $dat) {
                    $dat = (object) $dat;

                    $mm = (object) $dat->marks;
                    $mkcon = $mm->marks ? $mm->marks : 0;

                    $fvalues = [
                        'tid' => $thid,
                        'class' => $id,
                        'class_group' => $class_id,
                        'exams_id' => $dat->exams_id,
                        'student' => $dat->student,
                        'marks' => $mkcon,
                        'type' =>  $exam_type->id,
                        'out_of' =>  $dat->outof,
                        'subject' => $mm->subject,
                        'created_by' => $dat->created_by,
                        'created_on' => time()
                    ];

                    //Check if marks Exists to Update
                    $ckmarks = $this->igcse_m->check_student_marks($thid,$exid,$sb,$dat->student);

                    if ($ckmarks) {
                        $k++;
                        $done = $this->igcse_m->update_marks_attributes($ckmarks->id,['marks' => $mkcon,'modified_on' => time(),'modified_by' => $user->id]);
                    } else {
                        $kk++;
                        $ok = $this->exams_m->insert_marks1($fvalues);
                    }
                    

                     }

                // if ($ok) {
                    // $this->acl->audit($ok, implode(' , ', $svalues));
                    $mess = $kk.' Marks Inserted Successfully. '.$k.'Marks Updated Successfully';

                    $this->session->set_flashdata('message', array('type' => 'success', 'text' => $mess));
                // } else {
                //     $this->session->set_flashdata('message', array('type' => 'error', 'text' => lang('web_create_failed')));
                // }
            } else {
                $this->session->set_flashdata('message', array('type' => 'error', 'text' => 'Subject Not Specified'));
            }
            redirect('admin/igcse/exams/'.$thid);
        } else {
            $get = new StdClass();
            foreach ($this->rec_validation() as $field) {
                $get->{$field['field']} = set_value($field['field']);
            }

            $data['sb'] = $sb;
            $data['result'] = $get;
            $data['class_id'] = $id;
            $data['exam_id'] = $exid;
            $data['students'] = $students;
            $data['igcse_exam'] = $this->igcse_m->find_igcse_exam($exid);

            $this->template->title('Record Exam Marks')->build('admin/records', $data);
        }

    }
    function _prep_marks($subject, $exm_mgmt_id, $marks = [], $units = [])
    {
        $perf_list = [];
        $sub_marks = [];
        $user = $this->ion_auth->get_user();
        $outof = $this->input->post('outof');


        // print_r($marks);
        // die;
        if ($units && !empty($units)) {
            foreach ($units as $stid => $unmarks) {
                foreach ($unmarks as $uid => $mk) {
                    $sunits[] = array(
                        'parent' => $subject,
                        'unit' => $uid,
                        'marks' => $mk
                    );
                }
            }
        }

        foreach ($marks as $std => $score) {
            $sunits = [];
            $sub_marks = array(
                'subject' => $subject,
                'marks' => $score
            );
            if ($units && isset($units[$std])) {
                $mine = $units[$std];
                foreach ($mine as $uid => $mk) {
                    $sunits[] = array(
                        'parent' => $subject,
                        'unit' => $uid,
                        'marks' => $mk
                    );
                }
            }
            $perf_list[] = array(
                'exams_id' => $exm_mgmt_id,
                'student' => $std,
                'marks' => $sub_marks,
                'units' => $sunits,
                'outof' => $outof,
                'created_by' => $user->id,
                'created_on' => time()
            );
        }
        return $perf_list;
    }
    private function rec_validation()
    {

        $config = array(
            array(
                'field' => 'record_date',
                'label' => 'Record Date',
                'rules' => 'xss_clean'
            ),
            array(
                'field' => 'exam_type',
                'label' => 'The Exam',
                'rules' => 'trim|xss_clean'
            ),
            array(
                'field' => 'subject[]',
                'label' => 'Subject',
                'rules' => 'xss_clean'
            ),
            array(
                'field' => 'student[]',
                'label' => 'student',
                'rules' => 'xss_clean'
            ),
            array(
                'field' => 'total[]',
                'label' => 'Total',
                'rules' => 'xss_clean'
            ),
            array(
                'field' => 'marks[]',
                'label' => 'Marks',
                'rules' => 'xss_clean'
            ),
            array(
                'field' => 'grading',
                'label' => 'Grading',
                'rules' => 'required'
            ),
            array(
                'field' => 'remarks[]',
                'label' => 'Remarks',
                'rules' => 'xss_clean'
            ),
        );
        $this->form_validation->set_error_delimiters("<br/><span class='error'>", '</span>');
        return $config;
    }

    function delete($id = NULL, $page = 1)
    {
        //filter & Sanitize $id
        $id = ($id != 0) ? filter_var($id, FILTER_VALIDATE_INT) : NULL;

        //redirect if its not correct
        if (!$id) {
            $this->session->set_flashdata('message', array('type' => 'warning', 'text' => lang('web_object_not_exist')));

            redirect('admin/igcse');
        }

        //search the item to delete
        if (!$this->igcse_m->exists($id)) {
            $this->session->set_flashdata('message', array('type' => 'warning', 'text' => lang('web_object_not_exist')));

            redirect('admin/igcse');
        }

        //delete the item
        if ($this->igcse_m->delete($id) == TRUE) {
            $this->session->set_flashdata('message', array('type' => 'sucess', 'text' => lang('web_delete_success')));
        } else {
            $this->session->set_flashdata('message', array('type' => 'error', 'text' => lang('web_delete_failed')));
        }

        redirect("admin/igcse/");
    }

    private function validation()
    {
        $config = array(
            array(
                'field' => 'title',
                'label' => 'Title',
                'rules' => 'required|trim|xss_clean|min_length[0]|max_length[60]',
            ),
            array(
                'field' => 'term',
                'label' => 'Term',
                'rules' => 'required|trim|xss_clean|min_length[0]|max_length[60]',
            ),
            array(
                'field' => 'year',
                'label' => 'Year',
                'rules' => 'required|trim|xss_clean|min_length[0]|max_length[60]',
            ),
            array(
                'field' => 'cats_weight',
                'label' => 'CATs Weight',
                'rules' => 'required|trim|xss_clean|min_length[0]|max_length[60]',
            ),
            array(
                'field' => 'main_weight',
                'label' => 'Main Exam Weight',
                'rules' => 'required|trim|xss_clean|min_length[0]|max_length[60]',
            ),
        );
        $this->form_validation->set_error_delimiters("<br /><span class='error'>", '</span>');
        return $config;
    }


    private function set_paginate_options()
    {
        $config = array();
        $config['base_url'] = site_url() . 'admin/igcse/index/';
        $config['use_page_numbers'] = TRUE;
        $config['per_page'] = 10;
        $config['total_rows'] = $this->igcse_m->count();
        $config['uri_segment'] = 4;

        $config['first_link'] = lang('web_first');
        $config['first_tag_open'] = "<li>";
        $config['first_tag_close'] = '</li>';
        $config['last_link'] = lang('web_last');
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
