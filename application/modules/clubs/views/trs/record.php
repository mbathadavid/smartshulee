<?php
$cls = $this->input->post('class');
$item = $this->input->post('bitem');
$term = $this->input->post('term');
$year = $this->input->post('year');
$club = $this->input->post('club');
?>

<div class="portlet mt-2">
    <div class="portlet-heading portlet-default border-bottom">
        <h3 class="portlet-title text-dark">
            <b>Fill Clubs Template</b>
        </h3>
        <div class="pull-right">
            <a class="btn btn-success " href="<?php echo base_url('clubs/trs/comments'); ?>">General Comments</a>
            <a class="btn btn-danger " onclick="goBack()"><i class="fa fa-caret-left"></i> Go Back</a>
        </div>

        <div class="clearfix"></div>
        <hr>
    </div>
    <div id="step1">

        <?php if ($this->session->flashdata('update_success')) : ?>
            <div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                <?php echo $this->session->flashdata('update_success'); ?>
            </div>
        <?php endif; ?>

        <?php if ($this->session->flashdata('insertion_success')) : ?>
            <div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                <?php echo $this->session->flashdata('insertion_success'); ?>
            </div>
        <?php endif; ?>


        <div class="panel panel-primary">
            <div class="panel-heading">Clubs Template</div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12 col-sm-12">
                        <?php
                        echo form_open(current_url()); ?>
                        <div class="col-sm-2">
                            <label>Class</label>
                            <?php
                            echo form_dropdown('class', ['' => 'select class'] + $this->streams, $this->input->post('class'), 'class="select" id="class-dropdown" required');
                            ?>
                        </div>

                        <div class="col-sm-2">
                            <label>Club</label>
                            <?php
                            $clubs = $this->clubs_m->populate('clubs','id','name');

                            echo form_dropdown('club', ['' => 'Select Club'] + $clubs, $this->input->post('club'), 'class="select" id="clubhh" required');
                            ?>
                        </div>

                        <div class="col-sm-2">
                            <label>Item</label>
                            <?php
                            $bitems = array(
                                1 => 'Punctuality',
                                2 => 'Equipped for Club',
                                3 => 'Commitment',
                                4 => 'Co-operation with other learners',
                            );

                            echo form_dropdown('bitem', ['' => 'Select Item'] + $bitems, $this->input->post('bitem'), 'class="select" id="bitems" required');
                            ?>
                        </div>


                        <div class="col-sm-2">
                            <label>Term</label>
                            <?php
                            $terms = array(
                                1 => 'Term 1',
                                2 => 'Term 2',
                                3 => 'Term 3'
                            );

                            echo form_dropdown('term', ['' => 'select Term'] + $terms, $this->input->post('term'), 'class="select" id="term" required');
                            ?>
                        </div>

                        <div class="col-sm-2">
                            <label>Year</label>
                            <?php
                            krsort($yrs);
                            echo form_dropdown('year', ['' => 'select Year'] + $yrs, $this->input->post('year'), 'id="year"  class="select" ');
                            ?>
                        </div>


                        <div style="float: right;">
                            <br>
                            <button type="submit" class="btn btn-primary">Record</button>
                        </div>
                        <?php echo form_close() ?>

                    </div>
                </div>

                <?php 
                    if (count($students) > 0) {               
                ?>
                <div class="row">
                <?php echo form_open(base_url('clubs/trs/submit_ratings')); ?>
                    <input type="number" name="item" value="<?php echo $item ?>" hidden>
                    <input type="number" name="class" value="<?php echo $cls ?>" hidden>
                    <input type="number" name="term" value="<?php echo $term ?>" hidden>
                    <input type="number" name="year" value="<?php echo $year ?>" hidden>
                    <input type="number" name="club" value="<?php echo $club ?>" hidden>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">ADM NO</th>
                                    <th scope="col">STUDENT</th>
                                    <th colspan="5"></th>
                                </tr>
                                <tr>
                                    <th colspan="3"></th>
                                    <th>Good</th>
                                    <th>Satisfactory</th>
                                    <th>Cause for Concern</th>
                                    <th>Remove (By Unchecking)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                    $i = 0;

                                    foreach ($students as $st) {
                                        $i++;
                                        $check = $this->clubs_m->checkratings($st->id,$term,$year,$item,$club);

                                        if ($check) {
                                            $rate = $check->rating;
                                        } else {
                                            $rate = 0;
                                        }
                                        
                                ?>
                                <tr id="tr_<?php echo $st->id ?>">
                                    <td><?php echo $i; ?></td>
                                    <td><?php echo $st->admission_number; ?></td>
                                    <td><?php echo ucwords($st->first_name.' '.$st->last_name); ?></td>
                                    <td><input type="radio" name="rating[<?php echo $st->id ?>]" id="" value="1" class="form-control" <?php echo $rate == 1 ? 'checked' : '' ?>></td>
                                    <td><input type="radio" name="rating[<?php echo $st->id ?>]" id="" value="2" class="form-control" <?php echo $rate == 2 ? 'checked' : '' ?>></td>
                                    <td><input type="radio" name="rating[<?php echo $st->id ?>]" id="" value="3" class="form-control" <?php echo $rate == 3 ? 'checked' : '' ?>></td>
                                    <td><input type="checkbox" class="form-control" name="students[]" value="student[<?php echo $st->id ?>]" id="stu_<?php echo $st->id ?>" checked></td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                <button type="submit" class="btn btn-primary pull-right">Submit Records</button>
                <?php echo form_close(); ?>
                </div>

                <?php } else { ?>
                    <h6 class="text-center">No Students Found for this Class</h6>
                <?php } ?>
            </div>
        </div>
    </div>

</div>
<script>
    $(document).ready(function(){
        <?php 
            foreach ($students as $st) {
        ?>
        $("#stu_<?php echo $st->id ?>").change(function(){
            var val = $(this).attr('id');
            var stval = val.split('_')[1];
           
            if($(this).prop('checked')) {
                console.log('Checkbox is checked');
                var $row = $("#tr_<?php echo $st->id ?>");
                $row.find('input[type="radio"]').prop('disabled', false);
            } else {
                var $row = $("#tr_<?php echo $st->id ?>");
                $row.find('input[type="radio"]').prop('checked', false);
                $row.find('input[type="radio"]').prop('disabled', true);
            }
        });

        <?php } ?>
    });
</script>