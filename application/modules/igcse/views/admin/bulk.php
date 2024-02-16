<?php $settings = $this->ion_auth->settings(); ?>
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

<?php
if (isset($results)) {


    // print_r($results);
    // print_r($compareresults);
    // print_r($resultpositions);
    $subjects = $this->igcse_m->populate('subjects','id','name');
   

    foreach ($results as $key => $result) {
        $stu = $this->worker->get_student($result->student);

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

        $subscores = $this->igcse_m->student_scores($thread->id,$result->student);

?>
        <div class="invoice">
            <!-- Transcript Start -->
            <div class="row" id="headerdiv">
                <div class="col-md-6 col-lg-6">
                    <img src="<?php echo base_url('uploads/files/' . $settings->document); ?>" width="80" height="80" />
                </div>
                <div class="col-md-6 col-lg-6 text-right">
                    <h5 class="blue-text"><b><?php echo strtoupper($this->school->school) ?></b></h5>
                    <h6><b><?php echo strtoupper($this->school->postal_addr) ?></b></h6>
                    <h6><b><?php echo $this->school->tel ?></b></h6>
                    <h6><b><?php echo $this->school->email ?></b></h6>
                </div>
            </div>

            <h5 class="text-center blue-bg">ACADEMIC TRANSCRIPT FOR - <?php echo $this->classes[$result->class_group] ?> - <?php echo $thread->title ?> - (<?php echo $thread->year ?>/Term <?php echo $thread->term ?>)</h5>

            <div class="row" id="studentsdiv">
                <div class="col-md-3 col-lg-3">
                    <?php
                    $passport = $this->admission_m->passport($stu->photo);
                    $fake = base_url('uploads/files/member.png');

                    if (count($passport) !== 0) {
                        $path = base_url('uploads/' . $passport->fpath . '/' . $passport->filename);
                    }

                    ?>
                    <img src="<?php echo $fake ?>" alt="Student Profile" class="img-fluid img-thumbnail">
                </div>
                <div class="col-md-3 col-lg-3">
                    <h5><b>Name : <?php echo ucwords($stu->first_name . ' ' . $stu->last_name) ?></b></h5>
                    <h5><b>ADM NO : <?php echo $stu->admission_number ?></b></h5>
                    <h5><b>CLASS : <?php echo $this->streams[$stu->class] ?></b></h5>
                </div>
                <div class="col-md-6 col-lg-6">
                    <h6 class="text-center"><b>Student vs Class Perfomance subjectwise</b></h6>
                </div>
            </div>

            <div class="row mb-2" id="positionsdiv">
                <div class="col-md-3 col-lg-3">
                    <div style="width: 70%;">
                        <h6 class="text-center"><b>Mean</b></h6>
                        <h6 class="text-center"><b><?php echo  $result->mean_grade ?>|<?php echo  $result->mean_mark ?>%</b></h6>
                    </div>
                    <div style="width: 30%;">

                    </div>
                </div>
                <div class="col-md-2 col-lg-2">
                    <div style="width: 70%;">
                        <h6 class="text-center"><b>Total</b></h6>
                        <h6 class="text-center"><b><?php echo  $result->total ?>/<?php echo  $result->outof ?></b></h6>
                    </div>
                    <div style="width: 30%;">

                    </div>
                </div>
                <div class="col-md-2 col-lg-2">
                    <div style="width: 70%;">
                        <h6 class="text-center"><b>Total Points</b></h6>
                        <h6 class="text-center"><b><?php echo  $result->total ?>/<?php echo  $result->outof ?></b></h6>
                    </div>
                    <div style="width: 30%;">

                    </div>
                </div>
                <div class="col-md-2 col-lg-2">
                    <div style="width: 70%;">
                        <h6 class="text-center"><b>Overall Position</b></h6>
                        <h6 class="text-center"><b><?php echo $ovrpos ?>/<?php echo $ovroutof ?></b></h6>
                    </div>
                    <div style="width: 30%;">

                    </div>
                </div>
                <div class="col-md-3 col-lg-3">
                    <div style="width: 70%;">
                        <h6 class="text-center"><b>Stream Position</b></h6>
                        <h6 class="text-center"><b><?php echo $strpos ?>/<?php echo $stroutof ?></b></h6>
                    </div>
                    <div style="width: 30%;">

                    </div>
                </div>
            </div>

            <div class="row" style="margin-top: 10px;">
                <div class="col-lg-12 col-md-12">
                    <table class="table" cellpadding="0" cellspacing="0" width="100%">
                        <thead>
                            <th>SUBJECTS</th>
                            <th>MARKS</th>
                            <th>DEV.</th>
                            <th>GRADE</th>
                            <th>CLASS RANK</th>
                            <th>STREAM RANK</th>
                            <th>COMMENT</th>
                            <th>TEACHER</th>
                        </thead>
                        <tbody>
                            <?php 
                                foreach ($subscores as $key => $score) {
                            ?>
                                <tr>
                                    <td><?php echo $subjects[$score->subject] ?></td>
                                    <td><?php echo $score->total ?>%</td>
                                    <td></td>
                                    <td><?php echo $score->grade ?></td>
                                    <td><?php echo $score->ovr_rank ?></td>
                                    <td><?php echo $score->stream_rank ?></td>
                                    <td><?php echo $score->comment ?></td>
                                    <td></td>
                                </tr>
                            <?php } ?>
                        </tbody>

                    </table>
                </div>
            </div>

            <div class="row" id="footerdiv">
                <div class="col-lg-6 col-md-6">
                    <h6><b>Perfomance Over Time</b></h6>
                </div>
                <div class="col-lg-6 col-md-6">
                    <table style="width: 100%;">
                        <tr>
                            <th style="border: none;">Remarks</th>
                            <th style="border: none;">Signature</th>
                        </tr>
                        <tbody>
                            <tr>
                                <td style="border: none;"></td>
                                <td style="border: none;"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- Transcript Start -->
        </div>
        <div class="page-break"></div>
<?php
    }
}
?>
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
    .blue-text {
        color: #00aaff;
    }

    .blue-bg {
        background-color: #00aaff;
        color: white;
        padding: 8px;
    }

    #positionsdiv {
        background-color: #e6f7ff;
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

        #headerdiv::after {
            content: "";
            display: table;
            clear: both;
        }

        #headerdiv .col-md-6,
        #headerdiv .col-lg-6 {
            float: left;
            width: 50%;
        }

        #studentsdiv::after {
            content: "";
            display: table;
            clear: both;
        }

        #studentsdiv .col-md-3,
        #studentsdiv .col-lg-3,
        #studentsdiv .col-md-6,
        #studentsdiv .col-lg-6 {
            float: left;
        }

        #studentsdiv .col-md-3,
        #studentsdiv .col-lg-3 {
            width: 25%;
        }

        #studentsdiv .col-md-6,
        #studentsdiv .col-lg-6 {
            width: 50%;
        }

        #positionsdiv::after {
            content: "";
            display: table;
            clear: both;
        }

        #positionsdiv .col-md-3,
        #positionsdiv .col-lg-3,
        #positionsdiv .col-md-2,
        #positionsdiv .col-lg-2 {
            float: left;
        }

        #positionsdiv .col-md-3,
        #positionsdiv .col-lg-3 {
            width: 25%;
        }

        #positionsdiv .col-md-2,
        #positionsdiv .col-lg-2 {
            width: 16.66667%;
        }

        #footerdiv::after {
            content: "";
            display: table;
            clear: both;
        }
        #footerdiv .col-md-6,
        #footerdiv .col-lg-6 {
            float: left;
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
    }
</style>
