<?php
$range = range(date('Y') - 5, date('Y') + 2);
$yrs = array_combine($range, $range);
krsort($yrs);
$cl_s = [];
foreach ($this->classlist as $cid => $cl) {
    $cc = (object) $cl;
    $cl_s[$cid] = $cc->name;
}

$settings = $this->school;

$cls = $this->input->post('class');
$trm = $this->input->post('term');
$yr = $this->input->post('year');

?>



<div class="row actions hidden-print">
    <div class="">
        <center>
            <!-- <h3>End Term Report Cards </h3> -->
        </center>
        <?php echo form_open(current_url()); ?>
        <div class="row mb-2">
            <!-- <div class='form-group'> -->
            <div class="col-md-4">
                Select Class 
                <?php echo form_dropdown('class', ['' => ''] + $cl_s, $this->input->post('class'), 'class="select"') ?>
            </div>
            <div class="col-md-1">
                OR
            </div>
            <div class="col-md-3">
                Select Student
                <?php
                $students = $this->ion_auth->students_full_details();
                echo form_dropdown('students[]', $students, $this->input->post('students'), ' class="Qsel" multiple ');
                echo form_error('students');
                ?>
            </div>
            <div class="col-md-3">
                Term
                <?php
                echo form_dropdown('term', array('' => '') + $this->terms, $this->input->post('term'), ' class="tsel" placeholder="Select Term" ');
                echo form_error('term');
                ?>
            </div>
            <div class="col-md-3">
                Year
                <?php
                echo form_dropdown('year', array('' => '') + $yrs, $this->input->post('year') ? $this->input->post('year') : date('Y'), ' class="tsel" placeholder="Select Year" ');
                echo form_error('year');
                ?>
            </div>
            <!-- </div> -->
        </div>
        <div class="row">
            <div class="col-md-10">

            </div>
            <div class="col-md-2">
                    <button class="btn btn-success" style="height:30px;" type="submit">View Report</button>
                    <a href="" onClick="window.print();
                        return false" class="btn btn-primary"><i class="icos-printer"></i> Print
                    </a>
            </div>
        </div>
        
        <hr>
        

        <?php echo form_close(); ?>
        <br>
    </div>
</div>

<div class="widget">
    <?php 
        if (isset($records)) {
           foreach ($list as $ky => $stu) {
            
           $student = $this->worker->get_student($stu);
           $class = $this->boarding_m->get_class_teacher($student->cl->id);
           $teacher = $this->boarding_m->get($student->cl->class_teacher);
           $comment = $this->boarding_m->get_comment($stu,$trm,$yr);
           
    ?>
        <div class="invoice">
            <!-- Transcript Start -->
                <div class="row" id="hearderrow">
                    <div class="col-md-3 col-lg-3 col-img">
                        <img style="margin-right: 0;" src="<?php echo base_url('uploads/files/' . $settings->document); ?>" width="80" height="80" />
                    </div>
                    <div class="col-md-9 col-lg-9 col-dets">
                        <h2 class="text-center"><b>BOARDING REPORT</b></h2>
                        <?php 
                            if ($trm == 1) {
                                $txt = 'st';
                            } elseif($trm == 2) {
                                $txt = 'nd';
                            } elseif($trm == 3) {
                                $txt = 'rd';
                            }
                            
                        ?>
                        <h6 class="text-center">Report for <?php echo $trm.'<sup>'.$txt.'</sup>' ?> Term <?php echo $yr ?>.Issued By <?php echo date('Y') ?></h6>
                    </div>
                </div>
                <div class="row">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Learner's Name:  <?php echo ucwords($student->first_name.' '.$student->last_name) ?></th>
                                <th colspan="4">
                                    <h6><b>Grade: <?php echo $student->cl->name; ?></b></h6>
                                    <h6><b>Class Teacher: <?php echo ucwords($teacher->first_name.' '.$teacher->last_name) ?></b></h6>
                                </th>
                            </tr>
                            <tr>
                                <th></th>
                                <th class="text-center">Excellent</th>
                                <th class="text-center">Good</th>
                                <th class="text-center">Satisfactory</th>
                                <th class="text-center">Cause for Concern</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                                $bitems = array(
                                    1 => 'Time Keeping',
                                    2 => 'Personal Organisation',
                                    3 => 'Concentration and ability to work in quiet time',
                                    4 => 'Personal tidiness',
                                    5 => 'Cooperation with staff',
                                    6 => 'Cooperation with other students',
                                    7 => 'Respect for boarding rules',
                                    8 => 'Participation in boarding activities',
                                    9 => 'Leadership skills',
                                    10 => 'Concern and consideration for others'
                                );

                                foreach ($bitems as $key => $item) {
                                    $check = $this->boarding_m->checkratings($stu,$trm,$yr,$key);
                                    if ($check) {
                                        $rt = $check->rating;
                            ?>
                                <tr>
                                    <td><?php echo $item ?></td>
                                    <td class="text-center"><input type="checkbox" name="" id="" <?php echo $rt == 1 ? 'checked' : '' ?>></td>
                                    <td class="text-center"><input type="checkbox" name="" id="" <?php echo $rt == 2 ? 'checked' : '' ?>></td>
                                    <td class="text-center"><input type="checkbox" name="" id="" <?php echo $rt == 3 ? 'checked' : '' ?>></td>
                                    <td class="text-center"><input type="checkbox" name="" id="" <?php echo $rt == 4 ? 'checked' : '' ?>></td>
                                </tr>
                                <?php } else { ?>
                                <tr>
                                    <td><?php echo $item ?></td>
                                    <td class="text-center"><input type="checkbox" name="" id=""></td>
                                    <td class="text-center"><input type="checkbox" name="" id=""></td>
                                    <td class="text-center"><input type="checkbox" name="" id=""></td>
                                    <td class="text-center"><input type="checkbox" name="" id=""></td>
                                </tr>
                                <?php } ?>
                            <?php } ?>
                            <tr>
                                <td colspan="5" style="height: 150px;"><b>General Comments:</b> <?php echo ucwords($comment->comment) ?></td>
                            </tr>
                            <tr>
                                <td colspan="5"><b>House Parent Name:</b> <?php echo ucwords($comment->parent_name) ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
            <!-- Transcript Start -->
        </div>
        <div class="page-break"></div>
    <?php } } ?>
</div>

<style>
/* Style for the disabled checkbox */
input[type="checkbox"] {
    cursor: not-allowed; 
    pointer-events: none;
}

@media print {
    input[type="checkbox"] {
        /* display: inline !important;
        visibility: visible !important; */
        -webkit-print-color-adjust: exact; /* Chrome, Safari */
        color-adjust: exact; /* Firefox */
        display: inline !important; /* Ensure checkboxes are displayed */
    }
}
</style>

<style>
    .amt {
        text-align: right;
    }

    .fless {
        width: 100%;
        border: 0;
    }

    .slip {
        width: 21cm;
        min-height: 29.7cm;
        padding: 1cm;
        margin: 1cm auto;
        border: 1px #D3D3D3 solid;
        border-radius: 5px;
        background: white;
        box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
    }

    .fl {
        border: none !important;
    }

    @page {
        size: A4;
        margin: 0;
    }

    @media print {
        input[type="checkbox"] {
            display: inline !important;
            visibility: visible !important;
        }

        .slip {
            margin: 0;
            border: initial;
            border-radius: initial;
            width: initial;
            min-height: initial;
            box-shadow: initial;
            background: initial;
            page-break-after: always;
        }

        td.nob {
            border: none !important;
            background-color: #fff !important;
        }

        .stt td,
        th {
            border: 1px solid #ccc;
        }

        table tr {
            border: 1px solid #666 !important;
        }

        table th {
            border: 1px solid #666 !important;
        }

        table td {
            border: 1px solid #666 !important;
        }

        .highlight {
            background-color: #000 !important;
            color: #fff !important;
        }

    }

    .actions {
        background-color: #fff;
        padding: 8px
    }
</style>

<style>
    .col-img {
        /* position: relative; */
    }
    .col-img img {
        /* position: absolute; */
        /* right: 0; */
        /* top: 0; */
    }

    .qrcode {
        width: 150px;
        height: auto;
    }

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

        /* .graphdiv {
            width: 100%;
            overflow: hidden;
        } */

        /* .graphdiv {
            width: 50%;
        } */

        #hearderrow::after {
            content: "";
            display: table;
            clear: both;
        }

        #hearderrow .col-lg-9 {
            /* float: left; */
            width: 75%;
        }

        /* .col-img {
            position: relative;
        }

        .col-img img {
            position: fixed;
            right: 0;
            top: 0;
        } */

        /* #hearderrow .col-md-3 {
            width: 25%;
        }

        #studentsdiv::after {
            content: "";
            display: table;
            clear: both;
        } */

        #hearderrow{
            display: flex;
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

<script type="text/javascript">
    $(function() {
        $(".tsel").select2({
            'placeholder': 'Please Select',
            'width': '100%'
        });
        $(".Qsel").select2({
            'placeholder': 'Select Students',
            'width': '100%'
        });
    });
</script>