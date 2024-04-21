
<?php

if (!defined('BASEPATH'))
  exit('No direct script access allowed');

class Trs extends Trs_Controller
{

  /**
   * Class constructor
   */
  function __construct()
  {
    parent::__construct();
    if ($this->ion_auth->logged_in()) {
      if (!$this->is_teacher) {
        redirect('admin');
      }
    } else {
      redirect('login');
    }
    $this->load->model('trs_m');
    // $this->load->model('igcse_m');
    $this->load->model('clubs_m');
    $this->load->model('evideos/evideos_m');
    $this->load->model('teachers/teachers_m');
    $this->load->model('messages/messages_m');
    $this->load->model('lesson_plan/lesson_plan_m');
    $this->load->model('assignments/assignments_m');
    $this->load->model('past_papers/past_papers_m');
    $this->load->model('class_attendance/class_attendance_m');
    $this->load->library('Dates');
    $this->template->set_layout('default');
  }



  /**
   * Record Exam Marks
   */
  public function record()
  {

    $range = range(date('Y') - 50, date('Y'));
    $data['yrs'] = array_combine($range, $range);
    $students = [];

    if ($this->input->post()) {
        $post = (object) $this->input->post();
        $students = $this->clubs_m->get_students_by_stream($post->class);
     
    }

    // Check whether logged in poersin is class teacher

    $check = $this->clubs_m->myclasses();
    // $trs = $this->igcse_m->get_teacher_byuserid();

    $myclasses = [];
    if ($check) {

      foreach ($check as $ck) {
        $myclasses[$ck->id] = $this->streams[$ck->id];
      }
    }


    $data['students'] = $students;
    $data['myclasses'] = $myclasses;
    
    $this->template->title('Boarding Sheet')->build('trs/record', $data);
  }

  //Submit Records
  function submit_ratings() {
    $post = (object) $this->input->post();
    $user = $this->ion_auth->get_user();

    // echo "<pre>";
    //     print_r($post);
    // echo "</pre>";
    // die;

    $rating = $post->rating;

    $k = 0;
    $kk = 0;
    $kkk = 0;
    foreach ($rating as $st => $rt) {
      $form_data = array(
        'student' => $st,
        'rating' => $rt,
        'term' => $this->input->post('term'),
        'year' => $this->input->post('year'),
        'item' => $this->input->post('item'),
        'club' => $this->input->post('club'),
        'class' => $this->input->post('class'),
      );

      $check = $this->clubs_m->checkratings($st,$this->input->post('term'),$this->input->post('year'),$this->input->post('item'),$this->input->post('club'));

      if ($check) {
        $form_data['modified_by'] = $user->id;
        $form_data['modified_on'] = time();

        $done = $this->clubs_m->update_record($check->id,$form_data);
          if ($done) {
            $kkk++;
          } else {
            $kk++;
          }
        
      } else {
        $form_data['created_by'] = $user->id;
        $form_data['created_on'] = time();
        $ok = $this->clubs_m->create_record($form_data);

        if ($ok) {
          $k++;
        } else {
          $kk++;
        }
    }
      
    }

    $mess = $k.' Records Created. '.$kkk.' Records Updated. '.$kk.' Failed';
    $this->session->set_flashdata('message', array( 'type' => 'success', 'text' => $mess));
    redirect('clubs/trs/record');
  }

  //General Comments
  function comments() {
    $range = range(date('Y') - 50, date('Y'));
    $data['yrs'] = array_combine($range, $range);
    $students = [];

    if ($this->input->post()) {
        $post = (object) $this->input->post();
        $students = $this->clubs_m->get_students_by_stream($post->class);
     
    }

    // Check whether logged in poersin is class teacher

    $check = $this->clubs_m->myclasses();
    // $trs = $this->igcse_m->get_teacher_byuserid();

    $myclasses = [];
    if ($check) {

      foreach ($check as $ck) {
        $myclasses[$ck->id] = $this->streams[$ck->id];
      }
    }


    $data['students'] = $students;
    $data['myclasses'] = $myclasses;
    
    $this->template->title('General Comments')->build('trs/comments', $data);
  }


  //Submit Ratings
  function submit_comments() {
      $post = (object) $this->input->post();
      $user = $this->ion_auth->get_user();

      $comments = $post->comment;

    //   echo "<pre>";
    //     print_r($post);
    //   echo "</pre>";
    //   die;

      $k = 0;
      $kk = 0;
      $kkk = 0;
      foreach ($comments as $st => $com) {
        $form_data = array(
            'student' => $st,
            'term' => $post->term,
            'class' => $post->class,
            'year' => $post->year,
            'club' => $post->club,
            'comment' => $com,
            'overview' => $post->overview[$st]
        );

        $check = $this->clubs_m->checkcomments($st,$post->term,$post->year,$post->club);

        if ($check) {
          $form_data['modified_by'] = $user->id;
          $form_data['modified_on'] = time();
  
          $done = $this->clubs_m->update_comments($check->id,$form_data);
            if ($done) {
              $kkk++;
            } else {
              $kk++;
            }
          
        } else {
          $form_data['created_by'] = $user->id;
          $form_data['created_on'] = time();
          $ok = $this->clubs_m->create_comment($form_data);
  
          if ($ok) {
            $k++;
          } else {
            $kk++;
          }
      }

      // echo "<pre>";
      //   print_r($form_data);
      // echo "</pre>";
  }

  // die;
    $mess = $k.' Records Created. '.$kkk.' Records Updated. '.$kk.' Failed';
    $this->session->set_flashdata('message', array( 'type' => 'success', 'text' => $mess));
    redirect('clubs/trs/comments');
}


  private function _att_validation()
  {
    $config = array(
      array(
        'field' => 'attendance_date',
        'label' => 'Attendance Date',
        'rules' => 'required|xss_clean'
      ),
      array(
        'field' => 'title',
        'label' => 'Title',
        'rules' => 'xss_clean'
      ),
      array(
        'field' => 'student',
        'label' => 'Student',
        'rules' => 'xss_clean'
      ),
      array(
        'field' => 'status',
        'label' => 'Status',
        'rules' => 'xss_clean'
      ),
      array(
        'field' => 'remarks',
        'label' => 'Remarks',
        'rules' => 'xss_clean'
      ),
    );
    $this->form_validation->set_error_delimiters("<br/><span class='error'>", '</span>');
    return $config;
  }

  /**
   * Pagination Options
   * 
   * @return array
   */
  private function _exam_paginate_options()
  {
    $config = array();
    $config['base_url'] = site_url() . 'trs/record/index';
    $config['use_page_numbers'] = TRUE;
    $config['per_page'] = 15;
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
    $config['full_tag_open'] = '<ul class="pagination pagination-split">';
    $config['full_tag_close'] = '</ul></div>';

    return $config;
  }

  /**
   * Record Exams Validation
   * 
   * @return array
   */
  private function _rec_validation()
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

  
}
