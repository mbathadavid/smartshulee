<?php
// echo "<pre>";
// print_r($this->school);
// echo "</pre>";
?>

<div class="head">
  <div class="icon"></div>
  <h2><?php echo $thread->title . ' - (Term ' . $thread->term . ' ' . $thread->year . ')' ?></h2>
  <div class="right"></div>
</div>
<?php
$sslist = array();
foreach ($this->classlist as $ssid => $s) {
  $sslist[$ssid] = $s['name'];
}

// $s1 = $rank ? '' : ' checked="checked" ';
// $s2 = '';
// $s3 = '';
// if ($rank)
// {
//     $s1 = $rank == 1 ? ' checked="checked" ' : '';
//     $s2 = $rank == 2 ? ' checked="checked" ' : '';
//     $s3 = $rank == 3 ? ' checked="checked" ' : '';
// }
?>
<div class="toolbar">
  <div class="row row-fluid">
    <div class="col-md-12 span12">
      <?php echo form_open(current_url()); ?>
      <div class="row mb-3">
        <div class="col-lg-3 col-md-3">
          Class
          <?php echo form_dropdown('group', array("" => " Select ") + $this->classes, $this->input->post('group'), 'class ="tsel"'); ?>
        </div>
        <div class="col-lg-1 col-md-1">
          OR
        </div>
        <div class="col-lg-3 col-md-3">
          Stream
          <?php echo form_dropdown('class', array('' => 'Select') + $sslist, $this->input->post('class'), 'class ="tsel" '); ?>
        </div>
        <div class="col-lg-3 col-md-3">
          Compare With (For Deviation)
          <?php
          $gradings = $this->igcse_m->populate('grading_system', 'id', 'title');
          echo form_dropdown('thread', array('' => 'Select') + $threads, $this->input->post('thread'), 'class ="tsel"');
          ?>
        </div>
        <div class="col-lg-2 col-md-2">
          View <br>
          <button class="btn btn-primary" type="submit">View Results</button>
        </div>
      </div>

      <div class="pull-right">
        <a href="" onClick="window.print(); return false" class="btn btn-warning"><i class="icos-printer"></i> Print </a>
      </div>
      <?php echo form_close(); ?>
    </div>
  </div>
</div>

<?php $settings = $this->ion_auth->settings(); ?>


<?php
if (isset($results)) {

  // echo "<pre>";
  // print_r($results);
  // print_r($resultpositions);
  // print_r($thread);
  // echo "</pre>";


  foreach ($results as $key => $result) {
    $stu = $this->worker->get_student($result->student);

?>
    <div class="invoice">
      <div class="row row-fluid">
        <div class="col-md-12">
          <div class="row">
            <div class="col-md-6 logo">
              <h1><img src="<?php echo base_url('uploads/files/' . $settings->document); ?>" width="80" height="80" /></h1>
            </div>
            <div class="col-md-6 text-right">
              <h5 class="blue-text"><?php echo ucwords($settings->school); ?></h5>
              <p><?php echo $this->school->postal_addr ?></p>
              <p><?php echo $this->school->cell ?></p>
              <p><?php echo $this->school->email ?></p>
            </div>
          </div>
        </div>
      </div>
      <div class="row row-fluid">
        <div class="row row-fluid" style="margin-top: 20px;">
          <div class="col">
            <div class="blue-bg">
              ACADEMIC REPORT FORM - <?php echo $this->classes[$result->class_group] ?> - <?php echo $thread->title ?> - (<?php echo $thread->year ?>/Term <?php echo $thread->term ?>)
            </div>
          </div>
        </div>
        <div class="row row-fluid" style="margin-top:20px; margin-bottom: 20px; margin-left:0px">
          <div class="col-md-12">
            <div class="row row-fluid">
              <div class="col-md-6">
                <div class="row row-fluid">
                  <div class="col-md-6" style="border-right: 1px solid #ccc; height: 100%;">
                    <div class="profile">
                      <?php
                      if (!empty($st->photo)) :
                        $passport = $this->ion_auth->passport($st->photo);
                        if ($passport) {
                      ?>
                          <image src="<?php echo base_url('uploads/' . $passport->fpath . '/' . $passport->filename); ?>" width="100" height="100" class="img-polaroid" style="align:left">
                          <?php } ?>

                        <?php else : ?>
                          <?php echo theme_image("thumb.png", array('class' => "img-polaroid", 'style' => "width:100px; height:100px; align:left")); ?>
                        <?php endif; ?>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <p><strong>NAME:</strong> <?php echo ucwords($stu->first_name . " " . $stu->last_name) ?></p>
                    <p><strong>ADMNO:<?php echo $stu->admission_number ?></strong> </p>
                    <p><strong>CLASS: <?php echo $this->streams[$stu->class] ?></strong></p>
                  </div>
                </div>
              </div>
              <div class="col-md-6" style="border-left: 1px solid #ccc; height: 180px;">
                <p>graph</p>
              </div>
            </div>
          </div>
        </div>

        <div class="row row-fluid">
          <div class="col-md-12 bg-light">
            <div class="col-md-3" style="padding: 5px;">
              <b>
                <p>Mean</p>
              </b>
              <p><strong><?php echo  $result->mean_grade ?>|<?php echo  $result->mean_mark ?>%</strong></p>
            </div>
            <div class="col-md-3" style="border-left: 2px solid black; height: 60px;"><b>
                <p>Total Marks</p>
              </b>
              <p><strong><?php echo  $result->total ?>|<?php echo  $result->outof ?></strong></p>
            </div>
            <div class="col-md-3" style="border-left: 2px solid black; height: 60px;"><b>
                <p>Total Points</p>
              </b>
              <p><strong>73|84</strong></p>
            </div>
            <div class="col-md-3" style="border-left: 2px solid black; height: 60px;">
              <div class="row">
                <div class="col-md-6">
                  <p><b>Overall Position</b></p>
                  <?php
                  foreach ($resultpositions as $keeey => $posi) {

                    if ($keeey === 'ovrpositions') {
                      foreach ($posi as $posikey => $pos) {
                        if ($posikey == $key) {

                          $ovrpos = $pos;
                          $ovroutof = count($posi);
                        }
                      }
                    } elseif ($keeey === 'strpositions') {
                      foreach ($posi as $posikey => $pos) {
                        if ($posikey == $key) {

                          $strpos = $pos;
                          $stroutof = count($posi);
                        }
                      }
                    }
                  }
                  ?>
                  <p><strong><?php echo $ovrpos ?>|<?php echo $ovroutof ?></strong></p>
                </div>
                <div class="col-md-6">
                  <p><b>Stream Position</b></p>
                  <p><strong><?php echo $strpos ?>|<?php echo $stroutof ?></strong></p>
                </div>
              </div>

            </div>

          </div>

        </div>

        <div class="row row-fluid">
          <div class="col">
            <div class="table-row">
              <table class="table table-bordered custom-table">
                <thead>
                  <tr>
                    <th scope="col" style="background-color: white; border: 1px solid black;">SUBJECTS</th>
                    <th scope="col" style="background-color: white; border: 1px solid black;">MARKS</th>
                    <th scope="col" style="background-color: white; border: 1px solid black;">DEV.</th>
                    <th scope="col" style="background-color: white; border: 1px solid black;">GRADE</th>
                    <th scope="col" style="background-color: white; border: 1px solid black;">RANK</th>
                    <th scope="col" style="background-color: white; border: 1px solid black;">COMMENT</th>
                    <th scope="col" style="background-color: white; border: 1px solid black;">TEACHER</th>
                  </tr>
                </thead>
                <tbody>

                </tbody>
              </table>
            </div>
          </div>
        </div>

        <div class="row row-fluid" style="margin-top: 20px; height: 300px;">
          <div class="col-md-12">
            <div class="row">
              <div class="col-md-6">
                <h5>Students performance overtime graph</h5>
              </div>
              <div class="col-md-6">
                <div class="report-form">
                  <div class="row">
                    <div class="col-md-8">
                      <h5><strong>Remarks:</strong></h5>
                      <h5><strong>Class Teacher</strong></h5>
                      <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. </p>
                    </div>
                    <div class="col-md-4">
                      <h5><strong>Signature:</strong></h5>
                      <div class="signature">

                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-8">
                      <h5><strong>Principal</strong></h5>
                      <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. </p>
                    </div>
                    <div class="col-md-4">
                      <div class="signature">

                      </div>
                    </div>
                  </div>

                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>


<?php
  }
} ?>



<script>
  $(document).ready(
    function() {
      $(".tsel").select2({
        'placeholder': 'Please Select',
        'width': '200px'
      });
      $(".tsel").on("change", function(e) {
        notify('Select', 'Value changed: ' + e.added.text);
      });
      $(".fsel").select2({
        'placeholder': 'Please Select',
        'width': '400px'
      });
      $(".fsel").on("change", function(e) {
        notify('Select', 'Value changed: ' + e.added.text);
      });
    });
</script>

<style>
  .report-form {
    padding: 10px;

  }

  .remarks h3 {
    margin-bottom: 10px;
  }

  .signature {
    border-bottom: 2px solid black;
    padding-top: 50px;

  }

  .table-row {
    margin-top: 20px;
  }

  .custom-table {
    border: 2px solid black;
    background-color: white;
    /* Set border to black */
  }

  .custom-table th,
  .custom-table td {
    border: 2px solid black;
    /* Set border for table cells to black */
  }

  .bg-light {

    background-color: #e0eaf1;
    color: black;
    border-radius: 2px;
    height: 70px;
    margin-top: 20px;
    padding-top: 5px;


  }

  .profile {
    margin-left: 0%;
    width: 180px;
    height: 180px;
    overflow: hidden;
    border: 2px solid white;

  }

  .profile-pic {
    width: 100%;
    height: auto;
  }

  .blue-bg {
    background-color: #7fc1fc;
    color: white;
    padding: 10px;
    height: 40px;
    text-align: center;
    font-size: medium;
    font-family: sans-serif;
  }

  .blue-text {
    color: #7fc1fc;
    font-size: medium;
    font-weight: bold;


  }

  .text-right {
    padding-top: 0px;
  }

  .logo {
    margin-top: 10px;
  }

  .details {
    margin-right: 1px;
  }

  .xxd,
  .editableform textarea {
    height: 150px !important;
  }

  .editable-container.editable-inline {
    width: 89%;
  }

  .col-sm-2 {
    width: 16.66666667%;
  }

  .col-sm-8 {
    width: 66.66666667%;
  }

  .editable-input {
    display: inline;
    width: 89%;
  }

  .editableform .form-control {
    width: 89%;
  }

  .invoice {
    padding: 20px;
  }

  .topdets {
    width: 85%;
    margin: 6px auto;
    border: 0;
  }

  .topdets th,
  .topdets td,
  .topdets {
    border: 0;
  }

  .morris-hover {
    position: absolute;
    z-index: 1000;
  }

  .morris-hover.morris-default-style {
    border-radius: 10px;
    padding: 6px;
    color: #666;
    background: rgba(255, 255, 255, 0.8);
    border: solid 2px rgba(230, 230, 230, 0.8);
    font-family: sans-serif;
    font-size: 12px;
    text-align: center;
  }

  .morris-hover.morris-default-style .morris-hover-row-label {
    font-weight: bold;
    margin: 0.25em 0;
  }

  .morris-hover.morris-default-style .morris-hover-point {
    white-space: nowrap;
    margin: 0.1em 0;
  }

  .tablex {
    width: 95% !important;
    margin: auto 15px !important;
    border: 1px solid #000 !important;
  }

  .tablex tr {
    border: 1px solid #000 !important;
  }

  .tablex td {
    border: 1px solid #000;
  }

  .tablex th {
    border: 1px solid #000 !important;
  }

  .page-break {
    margin-bottom: 15px;
  }

  .dropped {
    border-bottom: 3px solid silver !important;
  }

  legend {
    width: auto;
    padding: 4px;
    margin-bottom: 0;
    border: 0;
    font-size: 11px;
  }

  fieldset {
    padding: 5px;
    border: 1px solid silver;
    border-radius: 7px;
  }

  @media print {
    .invoice {
      padding: 20px !important;
    }

    .text-right {
      padding-top: 0px;
    }

    .col-md-6 {
      width: 50%;

    }

    .row {
      width: 100%;
    }

    .logo {
      width: 50%;
    }



    .topdets {
      width: 85% !important;
      margin: auto 15px !important;
      border: 0;
    }

    .tablex {
      width: 100%;
    }

    .page-break {
      display: block;
      page-break-after: always;
      position: relative;
    }

    table td,
    table th {
      padding: 4px;
    }

    .editable-click,
    a.editable-click,
    a.editable-click:hover {
      text-decoration: none;
      border-bottom: none !important;
    }

    .dropped {
      border-bottom: 3px solid silver !important;
    }


    .row-fluid {
      width: 100%;
    }
  }
</style>