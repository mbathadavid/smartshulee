<?php

$subjects = $this->exams_m->get_subjects(7, 1);

echo "<pre>";
print_r($subjects);
echo "</pre>";
?>

<div class="portlet mt-2">
    <div class="portlet-heading portlet-default border-bottom">
        <h3 class="portlet-title text-dark">
            <b>Record Exam Marks</b>
        </h3>
        <div class="pull-right">


            <a class="btn btn-danger " onclick="goBack()"><i class="fa fa-caret-left"></i> Go Back</a>
        </div>

        <div class="clearfix"></div>
        <hr>
    </div>
    <div id="step1">
        <div class="panel panel-primary">
            <div class="panel-heading">Record Marks</div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12 col-sm-12">
                        <?php echo form_open(current_url()) ?>
                        <div class="col-sm-3">
                            <label>Class</label>
                            <?php
                            echo form_dropdown('class', ['' => ''] + $this->streams, $this->input->post('class'), 'class="select" required')
                            ?>
                        </div>

                        <div class="col-sm-3">
                            <label>Exam Thread</label>
                            <?php
                            $options = array('' => '');
                            foreach ($thread as $exam) {
                                // Add each exam title to the options array
                                $options[$exam->id] = $exam->title;
                            }
                            echo form_dropdown('thread', $options, $this->input->post('thread'), 'class="select" id="thread-dropdown" required');
                            ?>
                        </div>


                        <div class="col-sm-3">
                            <label>Exam</label>
                            <select name="exam" id="exam-dropdown" class="select" required>
                                <option value="">Select an exam</option>
                            </select>
                        </div>

                        <div class="col-sm-3">
                            <label>Subject</label>
                            <select name="exam" id="exam-dropdown" class="select" required>
                                <option value="">Select an exam</option>
                            </select>
                        </div>


                        <div style="float: right;">
                            <br>
                            <button type="submit" class="btn btn-primary">Record</button>
                        </div>
                        <?php echo form_close() ?>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="step2">
        <!-- Her -->
    </div>

</div>
<script>
    $(document).ready(function() {
        $('#thread-dropdown').change(function() {
            var selectedThreadId = $(this).val();

            var url = `<?php echo base_url("trs/fetch_exams/") ?>/${selectedThreadId}`;
            console.log(url);
            // console.log('Selected Thread ID:', selectedThreadId);
            $.ajax({
                url: url,
                type: 'GET',
                success: function(response) {
                    populateExamDropdown(response);

                },
                error: function(xhr, status, error) {
                    console.error(error);
                }
            });
        });

        function populateExamDropdown(examData) {
            var examDropdown = $('#exam-dropdown');
            examDropdown.empty(); // Clear existing options
            examDropdown.append($('<option>').text('Select an exam').val('')); // Add default option
            $.each(JSON.parse(examData), function(index, exam) {
                examDropdown.append($('<option>').text(exam.title).val(exam.id));
            });
        }
    });
</script>

<script>
    $("#assess_initial").on('submit', (function(e) {
        e.preventDefault();
        $.ajax({
            url: "<?php echo base_url('trs/cbc_exams/assess_init') ?>",
            type: "POST",
            contentType: false,
            cache: false,
            processData: false,

            data: new FormData(this),

            success: function(data) {
                var res = $.parseJSON(data);

                $('.select2-container').remove();
                $(".xsel").select2({
                    'placeholder': 'Please Select',
                    'width': '100%'
                });
                $('#step1').hide('slow');
                var html = ``;
                if (res.subjects.length === 0) {
                    html += `<div class="alert alert-danger">No subjects assigned for the selected class</div><br><a onclick="go_back()">Select a diffrent class</a>`;
                } else {
                    html += `
                    <?php echo form_open(base_url('trs/cbc_exams/post_marks/')) ?>

                   
                <div class="panel panel-primary">
                    <div class="panel-heading"><strong>` + res.class_name + ` Assessment</strong> <button class="btn btn-sm btn-danger" onclick="go_back()">Back</button></div>
                    <div class="panel-body">
                        
                     
 <input type="hidden" value="` + res.post.year + `" name="year">
                    <input type="hidden" value="` + res.post.term + `" name="term">
                    <input type="hidden" value="` + res.post.class + `" name="class">
                    <input type="hidden" value="` + res.post.exam + `" name="exam">
                         
                           <div class="col-sm-3">
                            <label>Subject</label><br>
                            <select class="select form-control" name="subject">
                                <option value="">Please select</option>`;
                    $.each(res.subjects, function(key, subj) {
                        html += `<option value="` + key + `">` + subj + `</option>`;
                    })

                    html += `
                            </select>
                        </div><hr><br>`;

                    html += `
                <br>
                    <table class="table table-striped table-bordered"   >
                        <thead>
                            <tr>
                                <th>Adm No</th>
                                <th>Student</th>
                                <th>Marks</th>
                            </tr>
                        </thead>
                        <tbody>
                `;

                    $.each(res.students, function(key, student) {
                        html += `<tr>   
                            <td>` + student.admission_number + `</td>
                            <td>` + student.name + `</td>
                            <td><input type="number" name="marks[` + student.student + `]" max="100" min="0" class="form-control"></td>
                    </tr>`;
                    })

                    html += `
                    </tbody></table>

                    <br>
                    <hr>
                    <div style="float:right">
                    <button class="btn btn-primary" onclick="return confirm('Are you sure to save?')">Submit</button>
                    </div>
                `;


                    html += ` </div>
                </div>
                <form>
                `;
                }


                $('#step2').html(html);
            }
        })
    }))

    function go_back() {
        $('#step2').hide('slow');
        $('#step1').show('slow');
        location.reload();
    }

    function validate_week() {
        var week = $('#week').val();

        if (week > 56) {
            notify('Weeks', 'Weeks cannot exceed 56!!!');
            $('#week').val('');
        }
    }
</script>