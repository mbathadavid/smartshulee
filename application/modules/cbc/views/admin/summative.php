<?php
$range = range(date('Y') - 5, date('Y') + 2);
$yrs = array_combine($range, $range);
krsort($yrs);
$cl_s = [];
foreach ($this->classlist as $cid => $cl)
{
    $cc = (object) $cl;
    $cl_s[$cid] = $cc->name;
}
?>


<div class="row hidden-print">
        <div class="col-md-4 st-t">
        </div>
        <div class="col-md-4 st-t p-10">
            <a href="<?php echo base_url('admin/cbc/summative'); ?>" class="btn btn-primary">Summative Report</a>
            <a href="<?php echo base_url('admin/cbc/assessment'); ?>" class="btn btn-info">Formative Report</a>
        </div>
        <div class="col-md-4 st-t">

        </div>
    </div>
<div class="row actions hidden-print">
    <div class="">
        <center><h3>CBC Summative Report </h3>	</center>
        <?php echo form_open(current_url()); ?>
        <div class='form-group'> 
            <div class="col-md-2">&nbsp; </div>		
            <div class="col-md-4">
                Select Class <br>
                <?php echo form_dropdown('class', ['' => ''] + $cl_s, $this->input->post('class'), 'class="select"') ?> 
            </div>
            <div class="col-md-1">
                <h3> OR </h3>
            </div>
            <div class="col-md-4">
                Select Students
                <?php
                $students = $this->ion_auth->students_full_details();
                echo form_dropdown('students[]', $students, $this->input->post('students'), ' class="Qsel" multiple ');
                echo form_error('students');
                ?>
            </div>
        </div>
        <div class='form-group'>
            <div class="col-md-2">&nbsp; </div>
            <div class="col-md-4">
                Term
                <?php
                echo form_dropdown('term', array('' => '') + $this->terms, $this->input->post('term'), ' class="tsel" placeholder="Select Term" ');
                echo form_error('term');
                ?>
            </div>
            <div class="col-md-1">&nbsp; </div>
            <div class="col-md-4">
                Year
                <?php
                echo form_dropdown('year', array('' => '') + $yrs, $this->input->post('year') ? $this->input->post('year') : date('Y'), ' class="tsel" placeholder="Select Year" ');
                echo form_error('year');
                ?>
            </div>
        </div>
        <br>
        <hr>
        <div class="row">
            <div class="col-md-8">
                <center>
                    <button class="btn btn-success" style="height:30px;" type="submit">View Report</button>
                </center>
            </div>
            <div class="col-md-4">
                <a href="" onClick="window.print();
                        return false" class="btn btn-primary pull-right"><i class="icos-printer"></i> Print
                </a>
            </div>
        </div>

        <?php echo form_close(); ?>
        <br>
    </div>
</div>

<div class="widget">
    <?php
    foreach ($result as $rst)
    {
        $r = (object) $rst;
        ?>
        <hr class="hidden-print"> 
        <div class="clearfix"></div>
        <div class="slip page-break">
            <div class="img-container">
                <img src="<?php echo base_url('uploads/files/' . $this->school->document); ?>" style="height:68px;" alt="header">
            </div>
            <div class="text-center">
                <h4><strong>SUMMATIVE REPORT</strong></h4>
                <h4 class="text-uppercase"><ins>NAME:</ins> <?php echo $r->student->first_name . ' ' . $r->student->last_name; ?>  &nbsp;&nbsp;&nbsp;<ins>ADM.</ins> &nbsp;&nbsp;&nbsp; <?php echo $r->student->admission_number; ?> &nbsp;&nbsp;&nbsp; <ins>Age:</ins> <?php echo $r->student->age; ?></h4>
                <p>
                    <span class="text-uppercase"><?php echo $r->student->cl->name ?> &nbsp; Term <?php echo $term; ?> - <?php echo $year; ?></span>
                </p>
            </div>

            <div class="clearfix"></div>
            <?php
            if (empty($r->assess))
            {
                ?>
                <div class="alert alert-danger" role="alert">
                    <strong>Sorry!</strong> No result found.
                </div>
                <?php
            }
            else
            {
                ?>
                <div class="">
                    <center><img src="<?php echo base_url('/uploads/files/key.png'); ?>"></center>
                </div>
                <div>
                    <table class="table table-bordered">
                        <tbody>
                            <tr class="fbg">
                                <td>#</td>
                                <td class="text-uppercase">SUBJECT</td>
                                <td>Opener Exam </td>
                                <td> Mid Term</td>
                                <td>End of Term </td>
                                <td> Term Average</td>
                            </tr>
                            <?php
                            $k = 0;
                            foreach ($r->assess as $sr)
                            {
                                $s = (object) $sr;
                                $k++;
                                ?>
                                <tr>
                                    <td><?php echo $k ?>.</td>
                                    <td class="text-uppercase"><?php echo $s->subject; ?></td>
                                    <td><?php echo isset($s->exams['exam_1']) ? $s->exams['exam_1'] : ''; ?></td>
                                    <td><?php echo isset($s->exams['exam_2']) ? $s->exams['exam_2'] : ''; ?></td>
                                    <td><?php echo isset($s->exams['exam_3']) ? $s->exams['exam_3'] : ''; ?></td>
                                    <td><?php echo isset($s->exams['exam_4']) ? $s->exams['exam_4'] : ''; ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                    <hr>                    
                    <div class="form-group fl">
                        <ins>GENERAL REMARKS ON SUMMATIVE ASSESMENT</ins><br>
                        <?php echo $r->summ->gen_remarks; ?>
                    </div>
                    <hr>
                    <div class="form-group fl">
                        <ins>Class teacher’s comments:</ins><br>
                        <?php echo $r->summ->tr_remarks; ?>
                    </div>
                    <hr>
                    <table class="table  m_0">
                        <tbody>
                            <tr>
                                <th>&nbsp;</th>
                                <td></td>
                                <td>
                                    <span class=""></span>
                                </td>
                                <td>
                                    <div class="row">
                                        <div class="col-md-6"><span class="pull-right">Closing Date:</span></div>
                                        <div class="col-md-6"><?php echo $r->summ->closing; ?></div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th>&nbsp;</th>
                                <td></td>
                                <td>&nbsp; </td>
                                <td>
                                    <div class="row">
                                        <div class="col-md-6"><span class="pull-right">Opening Date:</span></div>
                                        <div class="col-md-6"> <?php echo $r->summ->opening; ?></div>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            <?php } ?>
        </div>
    <?php } ?>   
</div>


<style>
    .amt{
        text-align: right;
    }
    .fless{
        width:100%;
        border:0;
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
    .fl{
        border:none !important;
    }
    @page {
        size: A4;
        margin: 0;
    }
    @media print{
        .slip{
            margin: 0;
            border: initial;
            border-radius: initial;
            width: initial;
            min-height: initial;
            box-shadow: initial;
            background: initial;
            page-break-after: always;
        }
        td.nob{
            border:none !important;
            background-color:#fff !important;
        }
        .stt td, th {
            border: 1px solid #ccc;
        }
        table tr{
            border:1px solid #666 !important;
        }
        table th{
            border:1px solid #666 !important;
        }
        table td{
            border:1px solid #666 !important;
        }
        .highlight{
            background-color:#000 !important;
            color:#fff !important;
        }

    }
    .actions{
        background-color: #fff;
        padding: 8px
    }
</style>

<script type="text/javascript">
    $(function ()
    {
        $(".tsel").select2({'placeholder': 'Please Select', 'width': '100%'});
        $(".Qsel").select2({'placeholder': 'Select Students', 'width': '100%'});
    });
</script>
