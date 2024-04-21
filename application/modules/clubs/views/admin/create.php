<?php 
$teachers = $this->teachers_m->list();
$trs = [];

foreach ($teachers as $key => $tr) {
  $trs[$tr->id] = ucwords($tr->first_name.' '.$tr->last_name);
}


?>
<div class="col-md-8">
  <div class="head">
    <div class="icon"><span class="icosg-target1"></span></div>
    <h2> Clubs </h2>
    <div class="right">
      <?php echo anchor('admin/clubs/create', '<i class="glyphicon glyphicon-plus">
                </i> ' . lang('web_add_t', array(':name' => 'Clubs')), 'class="btn btn-primary"'); ?>
      <?php echo anchor('admin/clubs', '<i class="glyphicon glyphicon-list">
                </i> ' . lang('web_list_all', array(':name' => 'Clubs')), 'class="btn btn-primary"'); ?>

    </div>
  </div>


  <div class="block-fluid">

    <?php
    $attributes = array('class' => 'form-horizontal', 'id' => '');
    echo   form_open_multipart(current_url(), $attributes);
    ?>
    <div class='form-group'>
      <div class="col-md-3" for='name'>Name </div>
      <div class="col-md-6">
        <?php echo form_input('name', isset($result->name) ? $result->name : '', 'id="name_"  class="form-control" '); ?>
        <?php echo form_error('name'); ?>
      </div>
    </div>

    <div class='form-group'>
      <div class="col-md-3" for='teacher'>Teacher in Charge </div>
      <div class="col-md-6">
      <?php 
        echo form_dropdown('teacher', array('' => 'Select Teacher') + $trs, isset($result->teacher) ? $trs[$result->teacher] : '', ' class="select" placeholder="Select Year" ');
        echo form_error('teacher'); ?>
      </div>
    </div>

    <div class='form-group'>
      <div class="col-md-3"></div>
      <div class="col-md-6">


        <?php echo form_submit('submit', ($updType == 'edit') ? 'Update' : 'Save', (($updType == 'create') ? "id='submit' class='btn btn-primary''" : "id='submit' class='btn btn-primary'")); ?>
        <?php echo anchor('admin/clubs', 'Cancel', 'class="btn  btn-default"'); ?>
      </div>
    </div>

    <?php echo form_close(); ?>
    <div class="clearfix"></div>
  </div>
</div>