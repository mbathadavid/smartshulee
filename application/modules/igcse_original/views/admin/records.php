<?php


if ($this->input->get('sb')) {
  $sel = $this->input->get('sb');
} elseif ($this->session->userdata('sub')) {
  $sel = $this->session->userdata('sub');
} else {
  $sel = 0;
}


// echo "<pre>";
// print_r($checkmarks);
// echo "</pre>";

?>
<div class="col-md-3">
  <div class="widget">
    <div class="head dark">
      <div class="icon"><span class="icosg-newtab"></span></div>
      <h2>Select a Subject</h2>
    </div>
    <div class="block-fluid">
      <ul class="list tickets">
        <?php
        $i = 0;
        foreach ($subjects as $sj => $dets) {
          $ds = (object) $dets;
          $i++;
          $cll = $sel == $sj ? 'sel' : '';
        ?>
          <li class="<?php echo $cll; ?> clearfix">
            <div class="title">
              <a href="<?php echo current_url() . '?sb=' . $sj; ?>"><?php echo $ds->full; ?></a>
              <p>&nbsp;</p>
            </div>
          </li>
        <?php } ?>
      </ul>
    </div>
  </div>
</div>
<div class="col-md-9">
  <div class="head">
    <div class="icon"><span class="icosg-target1"></span></div>
    <h2><?php echo $igcse_exam->title . ' ( Term ' . $igcse_exam->term . ' - ' . $igcse_exam->year . ')' ?> Marks Management</h2>
    <div class="right">
      <?php
       echo anchor('admin/igcse/exams/'. $this->uri->segment(4), '<i class="glyphicon glyphicon-list">
                </i> List All', 'class="btn btn-primary"'); ?>    </div>
  </div>
  <div class="block-fluid">
        <!-- Marks Found alert -->
        <?php 
          if (isset($checkmarks) && count($checkmarks) > 0) { 
            $outofs = [];
            $gids = [];

            foreach ($checkmarks as $checkmark) {
              $outofs[] = $checkmark->out_of;
              $gids[] = $checkmark->gid;
            }

            $outof = array_unique($outofs);
            $gid = array_unique($gids);
            
        ?>
        <div class="alert alert-info alert-dismissible">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
            <strong>Alert!</strong> Some Previously Added Marks Were Found for <b><?php echo count($checkmarks) ?></b> students. You can only edit these Marks.
        </div>
        <?php } ?>
        <!-- Marks Fiound Alert End -->

    <?php
    $attributes = array('class' => 'form-horizontal', 'id' => '');
    echo form_open_multipart(current_url() . '?sb=' . $sb, $attributes);
    ?>
    <div class='form-group'>
      <div class="col-md-3">Grading System <span class='required'>*</span></div>
      <div class="col-md-9">
        <?php
        echo form_dropdown('grading', array('' => '') + $grading, isset($gid) ? !empty($gid) ? $gid[0] : '' : '', ' class="select" data-placeholder="Select Grading System" ');
        echo form_error('grading');
        ?>
      </div>
    </div>
    <div class='form-group'>
      <div class="col-md-3">Out_Of <span class='required'>*</span></div>
      <div class="col-md-9">
        <?php echo form_input('outof', isset($outof) ? $outof[0] : '', ' id="outof" class="ol" style="width:60%" placeholder="Marks out of" '); ?>
      </div>
    </div>
    <h3 style="text-align:center; text-decoration:underline"><?php echo $class_name; ?></h3>
    <div class="form-group">
      <div class="row">
        <div class="col-md-3">&nbsp;</div>

      </div>
    </div>
    <?php
    if (count($students)) {
    ?>
      <table class="table-striped table-bordered " id="scores">
        <!-- BEGIN -->
        <thead>
          <tr>
            <th width="3%">#</th>
            <th>Student</th>
            <?php
            $sel = (object) $selected;
            if (isset($sel->units)) {
              foreach ($sel->units as $utk => $utt) {
            ?>
                <th>
                  <abbr title="<?php echo $utt; ?>">
                    <?php echo $utt; ?>
                  </abbr>
                </th>
            <?php
              }
            }
            ?>
            <th>Total Marks</th>
            <?php
            if ($assign) {
            ?>
              <th><input type="checkbox" class="checkall" /></th>
            <?php } ?>
          </tr>
        </thead>
        <tbody>
          <?php
          //convert to array for sorting
          $list = json_decode(json_encode($students), true);
          //sort 
          aasort($list, 'first_name');
          //return to object
          $students = json_decode(json_encode($list));

          $i = 1;
          $tot_class = '';
          foreach ($students as $post) :
            $std = $this->worker->get_student($post->id)
          ?>
            <tr>
              <td>
                <span id="reference" name="reference" class="heading-reference"><?php echo $i . '. '; ?></span>
              </td>
              <td> <?php echo $std->first_name . ' ' . $std->last_name; ?> </td>
              <?php
              if (isset($sel->units)) {
                $outs = $sel->outs;
                $tot_class = 'totd_' . $sb;
                foreach ($sel->units as $utk => $utt) {
                  // echo $utk;
              ?>
                  <td>
                    <?php
                    $uval = '';
                    $cap = isset($outs[$utk]) ? $outs[$utk] : 0;
                    if ($this->input->post('units')) {
                      $usetval = $this->input->post('units');
                      $uval = $usetval[$post->id][$utk];
                      // $uval = 9;
                    }

                    $checksubunitmarks = $this->igcse_m->find_submarks($thid,$sb,$exid,$std->id,$utk);

                    if ($checksubunitmarks) {
                      $uval = $checksubunitmarks->marks;
                    }                  

                    // echo $uval;
                    $unm = 'units[' . $post->id . '][' . $utk . ']';
                    echo form_input($unm, $uval, ' title="' . $cap . '" placeholder="Sub Unit Marks" class="umarks mkd_' . $sb . '" ');
                    echo form_error('units');
                    ?>
                  </td>
              <?php
                }
              }
              ?>
              <td>
                <?php
                $val = '';
                if (count($checkmarks) > 0) {
                  $ckmarks = $this->igcse_m->check_student_marks($thid,$exid,$sb,$std->id);

                  if ($ckmarks) {
                    $val = $ckmarks->marks;
                  }
                } else {
                  if ($this->input->post('marks')) {
                    $setval = $this->input->post('marks');
                    $val = $setval[$post->id];
                  }
                }

                $nm = 'marks[' . $post->id . ']';
                echo form_input($nm, $val, '  placeholder="Enter Marks" class="marks ' . $tot_class . ' bg-info" ');
                echo form_error('marks');
                ?>
              </td>
              <?php
              if ($assign) {
                $elc = 'done[' . $post->id . ']';
              ?>
                <td><input type="checkbox" name="<?php echo $elc; ?>" value="<?php echo $post->id; ?>" /></td>
              <?php } ?>
            </tr>
          <?php
            $i++;
          endforeach;
          ?>
        </tbody>
      </table>
      <div class='form-group'>
        <div class="col-md-10">
          <?php echo form_submit('submit', ($updType == 'edit') ? 'Update' : 'Save', (($updType == 'create') ? "id='submit' class='btn btn-primary''" : "id='submit' class='btn btn-primary'")); ?>
          <?php echo anchor('admin/igcse/exams', 'Cancel', 'class="btn btn-danger"'); ?>
        </div>
      </div>

    <?php } ?>
    <?php echo form_close(); ?>
    <div class="clearfix"></div>
  </div>
</div>

<style>
  label {
    padding-top: 0 !important;
  }

  .nof {
    padding-left: 1px !important;
    padding-right: 1px !important;
    padding-top: 1px !important;
  }
</style>
<script>
  $(document).ready(function() {
    $('#reff').click(function() {
      refresh();
    });

    function refresh() {
      $('#scores > tbody  > tr').each(function() {
        if ($(this).find(".umarks").length == 0) {
          return false;
        }
        var percent;
        var totals = 0;
        var $tr = $(this);
        var outof = $('#outof').val();
        $tr.find(".umarks").each(function() {
          totals += parseInt($(this).val()) || 0;
        });
        if (outof == 100) {
          percent = totals;
        } else {
          percent = Math.round((totals / outof) * 100);
        }
        $tr.find(".marks").val(percent);
      });
    }
    $('.marks').on('blur', function() {
      var tt = 0;
      $(this).closest('tr').find(".marks").each(function() {
        tt += parseInt($(this).val()) || 0;
      });

      $(this).closest('tr').find(".total").val(tt);
    });

    var val;
    $('.umarks').on('blur', function() {
      var unit_tot = 0;
      var $tr = $(this).closest('tr');

      var get = $.grep(this.className.split(" "), function(v, i) {
        return v.indexOf('mkd_') === 0;
      }).join();
      val = get.match(/\bmkd_(\d+)\b/)[1];

      $tr.find(".mkd_" + val).each(function() {
        unit_tot += parseInt($(this).val()) || 0;
      });
      $tr.find(".totd_" + val).val(unit_tot);
      /**--------------Update Total Score-------------------*/
      var tt = 0;
      $(this).closest('tr').find(".marks").each(function() {
        tt += parseInt($(this).val()) || 0;
      });

      $(this).closest('tr').find(".total").val(tt);
    });

    $(".marks,.umarks").on("keypress", function(event) {
      $(this).val($(this).val().replace(/[^\d].+/, ""));
      if ((event.which < 48 || event.which > 57)) {
        event.preventDefault();
        notify('Only Numbers allowed');
      }
    });
  });
</script>

<script>
  $(document).ready(function() {
    function validateTotalMarks() {
      var marksArr = [];
      // Loop through each row in the table
      $('#scores tbody tr').each(function() {
        // Get the total marks for the current row
        var marks = parseInt($(this).find('.marks').val());
        // totalMarks += marks;
        marksArr.push(marks);
      });
      // Get the "out of" value
      var outOf = parseInt($('#outof').val());

      var check = isAnyElementGreaterThan(marksArr,outOf);

      if (check == false) {
        // Show error message and prevent form submission
        alert('Some  markst exceed the specified "out of" value. Kindly Check on this');
        return false;
      }
      return true; // Total marks are valid
    }

    $('form').submit(function() {
      return validateTotalMarks(); 
    });


    function isAnyElementGreaterThan(marksArr,outof) {
      for (var i = 0; i < marksArr.length; i++) {        
          if (marksArr[i] > outof) {
            return false; 
          }
      }
      return true; 
    }


  });
</script>