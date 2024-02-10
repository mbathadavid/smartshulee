<?php
$range = range(date('Y') - 2, date('Y') + 2);
$yrs = array_combine($range, $range);
krsort($yrs);
?>
<div class="col-md-12">
    <div class="head"> 
        <div class="icon"><span class="icosg-target1"></span></div>		
        <h2>  Admission  </h2>
        <div class="right"> 
            <?php echo anchor('admin/admission/create', '<i class="glyphicon glyphicon-plus">
                </i> New Admission ', 'class="btn btn-primary"'); ?> 
            <?php echo anchor('admin/admission', '<i class="glyphicon glyphicon-list">
                </i> ' . lang('web_list_all', array(':name' => 'Admission')), 'class="btn btn-primary"'); ?> 
            <?php echo anchor('admin/admission/inactive/', '<i class="glyphicon glyphicon-question-sign"></i> Inactive Students', 'class="btn btn-warning"'); ?>
        </div>
    </div>

    <?php
    $attributes = array('class' => 'form-horizontal', 'id' => 'newizz');
    echo form_open_multipart(current_url(), $attributes);
    ?>
    <?php //echo form_open(current_url(), 'role="form"  method="post" id="newizz"'); ?>
    <?php //echo validation_errors(); ?>

    <!--javascript:notify('Wizard','Form #wizard_validate submited')-->
    <div class="block-fluid">
        <div class="wizcon">
            <div class="stepwizard">
                <div class="stepwizard-row setup-panel ">
                    <div class="stepwizard-step ">
                        <a href="#step-1" type="button" class="btn btn-primary btn-circle"><h4>1</h4></a>
                        <p>Student Details</p>
                    </div>
                    <div class="stepwizard-step">
                        <a href="#step-2" type="button" class="btn btn-default btn-circle" disabled="disabled"><h4>2</h4></a>
                        <p>Parent/Guardian Info</p>
                    </div>
                    <div class="stepwizard-step">
                        <a href="#step-3" type="button" class="btn btn-default btn-circle" disabled="disabled"><h4>3</h4></a>
                        <p>Admission Details</p>
                    </div>
                </div>
            </div>
            <div class="row setup-content" id="step-1">



                <div class="col-md-12"><hr></div>

                <div class="col-md-12">
                    <!-- <div class="col-md-2"></div> -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <div class="col-md-5"><strong> Student UPI Number: </strong> </div>
                            <div class="col-md-7">
                                <?php echo form_input('upi_number', $result->upi_number, 'class="validate[minSize[5]]"'); ?>

                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <div class="col-md-5"><strong> Birth Certificate Number: </strong> </div>
                            <div class="col-md-7">
                                <?php echo form_input('birth_cert_no', $result->birth_cert_no, 'class=""'); ?>

                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-12"><hr></div>

                <div class="col-md-12">
                    <div class="col-md-6">
                        <div class = "form-group">
                            <div class = "col-md-5">Upload Passport Photo</div>
                            <div class = "col-md-7">
                                <?php
                                echo form_upload('userfile', '', 'id="userfile" ');
                                echo form_input('photo', '', ' readonly="readonly" style="display:none" class="col-md-8" id="sphoto" ');
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class='form-group'>
                            <div class="col-md-5" for='birth_certificate'>Upload Birth Certificate </div>
                            <div class="col-md-7">

                                <?php
                                echo form_upload('birth_certificate', '', 'id="b_cert"  ');
                                echo form_input('birth_certificate', '', ' readonly="readonly" style="display:none"  class="col-md-8" id="birth_certificate" ');
                                ?>

                            </div>
                        </div>
                    </div>
                </div>



                <div class="col-md-12">
                    <br>
                </div>
                <div class="col-md-12">


                    <div class="col-md-6">

                        <div class='form-group'>
                            <div class="col-md-4" for='boarding_day'>Boarding / Day Scholar <span class='required'>*</span></div>
                            <div class="col-md-8">
                                <?php
                                $dis = array('Day' => 'Day Scholar', 'Boarding' => 'Boarding');
                                echo form_dropdown('boarding_day', $dis, (isset($result->boarding_day)) ? $result->boarding_day : '', ' class="validate[required]" ');
                                ?>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-4">First Name: <span class='required'>*</span> </div>
                            <div class="col-md-8">
<?php echo form_input('first_name', $result->first_name, 'class="validate[required,minSize[2]]" placeholder="E.g John"'); ?>

                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-4">Middle Name: </div>
                            <div class="col-md-8">
<?php echo form_input('middle_name', $result->middle_name, 'class=""'); ?>
                            </div>						
                        </div>
                        <div class="form-group">
                            <div class="col-md-4">Last Names: <span class='required'>*</span></div>
                            <div class="col-md-8">
<?php echo form_input('last_name', $result->last_name, 'class="validate[required,minSize[2]]" placeholder="E.g Doe Brown"'); ?>

                            </div>
                        </div>                                
                        <div class="form-group">
                            <div class="col-md-4">Date of Birth: </div>
                            <div class="col-md-8">
                                <div class="input-group form_dadtetime">
                                    <?php
                                    $dt = '';
                                    if ($result->dob)
                                    {
                                        if ((!preg_match('/[^\d]/', $result->dob)))//if it contains digits only
                                        {
                                            $dt = date('d M Y', $result->dob);
                                        }
                                        else
                                        {
                                            $dt = $result->dob;
                                        }
                                    }
                                    echo form_input('dob', $dt, 'class=" form-control datedob col-mdd-8" placeholder="Click here.."');
                                    ?>
                                    <span class="input-group-addon "><i class="glyphicon glyphicon-calendar"></i></span>
                                </div>

                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-4">Gender: <span class='required'>*</span></div>
                            <div class="col-md-8"> 
                                <?php
                                $st = '';
                                if ($result->gender == 1)
                                {
                                    $st = 'checked="checked"';
                                }
                                $sf = '';
                                if ($result->gender == 2)
                                {
                                    $sf = 'checked="checked"';
                                }
                                ?>
                                <div class = "radio"> <input type = "radio"  <?php echo $st; ?> name = "gender" class = "validate[required]" value = "1"> </div>Male
                                <div class = "radio"> <input type = "radio" <?php echo $sf; ?> name = "gender" value = "2" class = "validate[required]"> </div>Female
                            </div>
                        </div>


                        <div class='form-group'>
                            <div class="col-md-4" for='student_status'>Student Status </div>
                            <div class="col-md-8">
                                <?php
                                $st = array('Both Parents Alive' => 'Both Parents Alive', 'Total Orphan' => 'Total Orphan', 'Single Mother' => 'Single Mother', 'Single Father' => 'Single Father', 'Unknown' => 'Unknown');

                                echo form_dropdown('student_status', $st, (isset($result->student_status)) ? $result->student_status : '', ' class="validate[required]" ');
                                ?>
                            </div>
                        </div>

                        <div class='form-group'>
                            <div class="col-md-4" for='disabled'>Special Need </div>
                            <div class="col-md-8">
                                <?php
                                $dis = array('No' => 'No', 'Yes' => 'Yes');
                                echo form_dropdown('disabled', $dis, (isset($result->disabled)) ? $result->disabled : '', ' class="" ');
                                ?>
                            </div>
                        </div>

                    <!--    <div class='form-group' >
                            <div class="col-md-4" for='blood_group'>Blood Group </div>
                            <div class="col-md-8">
                                <?php
                                $dis = array('' => 'Select Option',
                                    'O-positive' => 'O-positive',
                                    'O-negative' => 'O-negative',
                                    'A-positive' => 'A-positive',
                                    'A-negative' => 'A-negative',
                                    'B-positive' => 'B-positive',
                                    'B-negative' => 'B-negative',
                                    'AB-positive' => 'AB-positive',
                                    'AB-negative' => 'AB-negative',
                                );
                                echo form_dropdown('blood_group', $dis, (isset($result->blood_group)) ? $result->blood_group : '', ' class="" ');
                                ?>
                            </div>
                        </div> -->

                    </div>
                    <div class="col-md-6">

                        <div class='form-group'>
                            <div class="col-md-3" for='blood_group'>Religion </div>
                            <div class="col-md-8">
                                <?php
                                $items = array(
                                    'Christian' => 'Christian',
                                    'Muslim' => 'Muslim',
                                    'Hindu' => 'Hindu',
                                    'Buddhist' => 'Buddhist',
                                    'Others' => 'Others',
                                );
                                echo form_dropdown('religion', $items, (isset($result->religion)) ? $result->religion : '', ' class="form-control" data-placeholder="Select Options..." ');
                                ?>
<?php echo form_error('religion'); ?>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-3">Allergies/Special Need:</div>
                            <div class="col-md-8">
                                <textarea name="allergies" class=""><?php echo isset($pero) && !empty($pero) ? $pero->allergies : $this->input->post('allergies'); ?></textarea>

                            </div>
                        </div>	

                        <div class="form-group">
                            <div class="col-md-3">Former school:</div>
                            <div class="col-md-8">
<?php echo form_input('former_school', $result->former_school, 'class=""'); ?>

                            </div>
                        </div> 
                        <div class="form-group">
                            <div class="col-md-3">Entry Marks:</div>
                            <div class="col-md-8">
<?php echo form_input('entry_marks', $result->entry_marks, 'class=""'); ?>

                            </div>
                        </div> 


                        <div class="form-group">
                            <div class="col-md-3">Doctor's Name:</div>
                            <div class="col-md-8">
<?php echo form_input('doctor_name', $result->doctor_name, 'class=""'); ?>

                            </div>
                        </div> 


                        <div class="form-group">
                            <div class="col-md-3">Doctor's Phone:</div>
                            <div class="col-md-8">
<?php echo form_input('doctor_phone', $result->doctor_phone, 'class=""'); ?>

                            </div>
                        </div> 
                        <div class="form-group">
                            <div class="col-md-3">Preferred Hospital:</div>
                            <div class="col-md-8">
<?php echo form_input('hospital', $result->hospital, 'class=""'); ?>

                            </div>
                        </div> 

                    </div>
					
			
                    <div class="col-md-12">
                        <h3>Scholarship/Sponsorship</h3>
                    </div>
                    <div class="col-md-12">
                        <div class="col-sm-6">
                            <div class='form-group'>
                                <div class="col-md-3" for='citizenship'>Scholarship</div>
                                <div class="col-md-8">
<?php
$tems = array('No' => 'No', 'Yes' => 'Yes');

echo form_dropdown('scholarship', $tems, (isset($result->scholarship)) ? $result->scholarship : '', ' class=" col-sm-12 " ');
?>
                                </div>
                                <div class="col-md-1"></div>
                            </div>

                            <div class='form-group'>
                                <div class="col-md-3" for='scholarship_type'>Type</div>
                                <div class="col-md-8">
<?php
$types = array('School' => 'School', 'Organization' => 'Organization', 'Individual' => 'Individual', 'Others' => 'Others');

echo form_dropdown('scholarship_type', array('' => 'Select Option') + $types, (isset($result->scholarship_type)) ? $result->scholarship_type : '', ' class=" col-sm-12" ');
?>	

                                </div>
                            </div>


                            <div class="form-group">
                                <div class="col-md-3">Sponsor details:</div>
                                <div class="col-md-8">
                                    <textarea name="sponsor_details" class=" col-sm-12" placeholder="E.g Equity Bank, KCB etc......."></textarea>


                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">

                            <div class="form-group">
                                <div class="col-md-3">Phone: </div>
                                <div class="col-md-8">
<?php echo form_input('sponsor_phone', $result->sponsor_phone, 'class="form-control" style="margin-left:18px;" '); ?>

                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-md-3">Location: </div>
                                <div class="col-md-8">
<?php echo form_input('sponsor_location', $result->sponsor_location, 'class="form-control" style="margin-left:18px;" '); ?>

                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-md-3">Contact Person: </div>
                                <div class="col-md-8">
<?php echo form_input('sponsor_contact_person', $result->sponsor_contact_person, 'class="form-control" style="margin-left:18px;" '); ?>

                                </div>
                            </div>

                        </div>

                    </div>


                    <div class="col-md-12">
                        <h3>Contact Details</h3>
                    </div>
                    <div class="col-md-6">

                        <div class='form-group'>
                            <div class="col-md-3" for='citizenship'>Citizenship</div>
                            <div class="col-md-8">
<?php
$country = $this->portal_m->populate('countries', 'id', 'name');

echo form_dropdown('citizenship', array('114' => 'Kenya') + $country, (isset($result->citizenship)) ? $result->citizenship : '', ' class="select col-sm-8 " ');
?>
                            </div>
                            <div class="col-md-1"></div>
                        </div>

                        <div class='form-group'>
                            <div class="col-md-3" for='county'>Home County</div>
                            <div class="col-md-8">
<?php
$counties = $this->portal_m->populate('counties', 'id', 'name');

echo form_dropdown('county', array('' => 'Select County') + $counties, (isset($result->county)) ? $result->county : '', ' class="select col-sm-12 county" id="county"');
?>	
                                <span class="bottom">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Current Residential County </span>
                            </div>
                        </div>

                      

                    </div>



                    <div class="col-md-6">	

                <div class='form-group'>
                            <div class="col-md-3" for='sub_county'>Sub County </div>
                            <div class="col-md-8">

									<?php
									echo form_dropdown('sub_county', array('' => 'Select Sub County'), (isset($result->sub_county)) ? $result->sub_county : '', ' class="select col-sm-12" id="sub_county"');
									?>	

																</div>
															</div>

								<div class="form-group">
									<div class="col-md-3">Residence:</div>
									<div class="col-md-8">
		                           <?php echo form_input('residence', $result->residence, 'class="form-control" style="margin-left:18px;" '); ?>

									</div>
								</div>					

                  <!--
                        <div class='form-group'>
                            <div class="col-md-3" for='emergency_phone'>Emergency Phone </div>
                            <div class="col-md-8">
									<?php
									echo form_input('emergency_phone', $result->emergency_phone, 'class="validate[minSize[10]] mask_mobile" id="" placeholder="Required"');
									?>
                                <span class="bottom">Example: 0720-002-002 </span>
                            </div>
                        </div>
             
                        <div class='form-group'>
                            <div class="col-md-3" for='student_phone'>Student Phone </div>
                            <div class="col-md-8">
<?php
echo form_input('student_phone', $result->student_phone, 'class="validate[minSize[10]] mask_mobile" id="" placeholder="Optional"');
?>
                                <span class="bottom">Example: 0720-002-002 </span>
                            </div>
                        </div>



                        <div class = "form-group" >
                            <div class = "col-md-3">E-mail Address:</div>
                            <div class = "col-md-8"> 
<?php
//$addi = $updType == 'edit' ? '' : ',ajax[ajaxUserCallPhp]';
echo form_input('email', $result->email, 'class="validate[custom[email]]" id="smail" placeholder="Optional"');
?>
                                <span class="bottom">Valid email - Will be used to Login</span>
                            </div>
                        </div>
						
					-->	
						
                    </div>

                </div>
                <div class="clearfix"></div>
                <div class="col-md-1"></div>		
                <button class="btn btn-primary nextBtn btn-lg pull-right" type="button" >Next</button>
            </div>
            <div class="row setup-content" id="step-2">
                <div class="col-md-12">

                    <div class="form-group" id="swtch">
                        <div class="col-md-3">Parent:</div>
                        <div class="col-md-6"> 
                            <div class = "radio"> <input type = "radio" id="pnew"  name = "ptype" class = "validate[required] " <?php echo $updType == 'edit' ? 'disabled="disabled" ' : ''; ?> value = "1"> </div>New Parent
                            <div class = "radio"> <input type = "radio" id="pexists" name = "ptype" value = "2" class = "validate[required]" <?php echo $updType == 'edit' ? 'disabled="disabled" ' : ''; ?>> </div>Existing Parent
                        </div>
                    </div>

                    <div id="pdrop" style="display: none;">
                        <div class='form-group'>
                            <div class="col-md-3" for='parent_id'>Select Parent <span class='required'>*</span></div>
                            <div class="col-md-4">
<?php echo form_dropdown('parent_id', $parents, (isset($result->parent_id)) ? $result->parent_id : '', ' class="select" ');
?><span class="bottom">Required</span>		
                            </div>
                        </div>
                    </div>

                    <div id="newp" <?php echo $updType == 'edit' ? '' : ' style="display: none;"'; ?>>
                        <div class="col-md-6">
                            <h3 style="text-align:center"> 1st Parent's Details</h3>

                            <div class='form-group' style="display:none">
                                <div class="col-sm-5">Passport Photo</div>
                                <div class="col-sm-7">

<?php
echo form_upload('parent_photo', '', 'id="f_photo" ');
echo form_input('father_photo', '', ' readonly="readonly" style="display:none" class="col-md-8" id="father_photo" ');
?>
                                </div>
                            </div>

                            <div class='form-group'>
                                <div class="col-sm-5">National ID</div>
                                <div class="col-sm-7">

<?php
echo form_upload('parent_id_copy', '', 'id="f_id_copy" ');
echo form_input('father_id_copy', '', ' readonly="readonly" style="display:none" class="col-md-8" id="father_id_copy" ');
?>
                                </div>
                            </div>

<!--
                            <div class='form-group'>
                                <div class="col-md-3" for='blood_group'>Title </div>
                                <div class="col-md-8">
                                    <?php echo form_input('f_title', isset($pero) && !empty($pero) ? $pero->f_title : $this->input->post('f_title'), 'class="" placeholder="E.g Hon Eng, Dr, Mr, Mrs"'); ?>
                                    <?php echo form_error('f_title'); ?>
                                </div>
                            </div>
-->
                            <div class='form-group'>
                                <div class="col-md-3" for='blood_group'>Relation </div>
                                <div class="col-md-9">
                                    <?php
                                    $rels = array(
                                        "Father" => "Father",
                                        "Mother" => "Mother",
                                        "Brother" => "Brother",
                                        "Sister" => "Sister",
                                        "Grandparent" => "Grandparent",
                                        "Uncle" => "Uncle",
                                        "Auntie" => "Auntie",
                                        "Guardian" => "Guardian",
                                    );
                                    echo form_dropdown('f_relation', $rels, $this->input->post('f_relation'), 'id="rel"  class="select" data-placeholder="Select Options..." ');
                                    echo form_error('f_relation');
                                    ?>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-md-3"> First Name: <span class='required'>*</span></div>
                                <div class="col-md-9">
                                    <?php echo form_input('parent_fname', isset($pero) && !empty($pero) ? $pero->first_name : $this->input->post('parent_fname'), 'class="validate[required,minSize[2]]"'); ?>
                                    <span class="bottom">required</span>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-3"> Last Name: <span class='required'>*</span></div>
                                <div class="col-md-9">
<?php echo form_input('parent_lname', isset($pero) && !empty($pero) ? $pero->last_name : $this->input->post('parent_lname'), 'class="validate[required,minSize[4]]"'); ?>
                                    <span class="bottom">required</span>
                                </div>
                            </div>

                            <input style="display:none" class="mask_mobile" >    
                            <div class='form-group'>
                                <div class="col-md-3" for='phone'> Phone <span class='required'>*</span></div>
                                <div class="col-md-9">
<?php echo form_input('phone', isset($pero) && !empty($pero) ? $pero->phone : $this->input->post('phone'), 'id="phone"  class="form-control validate[required,minSize[10]] mask_mobile" '); ?>
                                    <span class="bottom">Example: 0720-002-002 </span>

                                </div>
                            </div>

                            <div class='form-group'>
                                <div class="col-md-3" for='parent_email'> Email  </div>
                                <div class="col-md-9">
                                    <?php echo form_input('parent_email', isset($pero) && !empty($pero) ? $pero->email : $this->input->post('parent_email'), 'id="f_email"  class=" form-control f_email" onblur="email_checker()"'); ?>
                                    <span class="bottom">Optional - Will be used to Login(No Spaces)</span> 
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-md-3"> ID/Passport:</div>
                                <div class="col-md-9">
                                    <?php echo form_input('f_id', isset($pero) && !empty($pero) ? $pero->f_id : $this->input->post('f_id'), 'class=""'); ?>
                                    <span class="bottom">optional</span>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-md-3"> Occupation:</div>
                                <div class="col-md-9">
<?php echo form_input('occupation', isset($pero) && !empty($pero) ? $pero->occupation : $this->input->post('occupation'), 'class=""'); ?>
                                    <span class="bottom">optional</span>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-md-3"> Address:</div>
                                <div class="col-md-9">
                                    <textarea name="address" class=""><?php echo isset($pero) && !empty($pero) ? $pero->address : $this->input->post('address'); ?></textarea>
                                    <span class="bottom">Optional</span>
                                </div>
                            </div>
<!--							
                            <div class="form-group">
                                <div class="col-md-3"> Postal Code:</div>
                                <div class="col-md-8">
<?php echo form_input('f_postal_code', isset($pero) && !empty($pero) ? $pero->f_postal_code : $this->input->post('f_postal_code'), 'class=""'); ?>

                                </div>
                            </div>
-->
                        </div>

                        <div class="col-md-6">
                            <h3> 2nd Parent/Guardian </h3>

                            <div class='form-group' style="display:none">
                                <div class="col-sm-5">Passport Photo</div>
                                <div class="col-sm-7">
<?php
echo form_upload('parent_photo', '', 'id="m_photo" ');
echo form_input('mother_photo', '', ' readonly="readonly" style="display:none" class="col-md-8" id="mother_photo" ');
?>
                                </div>
                            </div>

                            <div class='form-group'>
                                <div class="col-sm-5">National ID Copy</div>
                                <div class="col-sm-7">

<?php
echo form_upload('parent_id_copy', '', 'id="m_id_copy" ');
echo form_input('mother_id_copy', '', ' readonly="readonly" style="display:none" class="col-md-8" id="mother_id_copy" ');
?>
                                </div>
                            </div>
<!--
                            <div class='form-group'>
                                <div class="col-md-3" for='blood_group'>Title </div>
                                <div class="col-md-8">

<?php echo form_input('m_title', isset($pero) && !empty($pero) ? $pero->m_title : $this->input->post('m_title'), 'class="" placeholder="E.g Hon Eng, Dr, Mr, Mrs"'); ?>
<?php echo form_error('m_title'); ?>


                                </div>
                            </div>
-->
                            <div class='form-group'>
                                <div class="col-md-3" for='blood_group'>Relation </div>
                                <div class="col-md-9">
                                    <?php
                                    $rels = array(
                                        '' => 'Select Option',
                                        "Mother" => "Mother",
                                        "Father" => "Father",
                                        "Brother" => "Brother",
                                        "Sister" => "Sister",
                                        "Grandparent" => "Grandparent",
                                        "Uncle" => "Uncle",
                                        "Auntie" => "Auntie",
                                        "Guardian" => "Guardian",
                                    );
                                    echo form_dropdown('m_relation', $rels, $this->input->post('m_relation'), 'class="form-control" data-placeholder="Select Options..." ');
                                    echo form_error('m_relation');
                                    ?>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-md-3"> First Name: </div>
                                <div class="col-md-9">
                                    <?php echo form_input('mother_fname', isset($pero) && !empty($pero) ? $pero->mother_fname : $this->input->post('mother_fname'), 'class=""'); ?>
                                    <span class="bottom">optional</span>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-3"> Last Name: </div>
                                <div class="col-md-9">
<?php echo form_input('mother_lname', isset($pero) && !empty($pero) ? $pero->mother_lname : $this->input->post('mother_lname'), 'class=""'); ?>
                                    <span class="bottom">optional</span>
                                </div>
                            </div>

                            <input style="display:none" class="mask_mobile" >    
                            <div class='form-group'>
                                <div class="col-md-3" for='phone'> Phone </div>
                                <div class="col-md-9">
<?php echo form_input('mother_phone', isset($pero) && !empty($pero) ? $pero->mother_phone : $this->input->post('mother_phone'), 'id="mother_phone"  class="form-control  mask_mobile" '); ?>
                                    <span class="bottom">Example: 0720-002-002 </span>

                                </div>
                            </div>

                            <div class='form-group'>
                                <div class="col-md-3" for='parent_email'> Email  </div>
                                <div class="col-md-9">
<?php echo form_input('mother_email', isset($pero) && !empty($pero) ? $pero->mother_email : $this->input->post('mother_email'), 'id="mother_email"  class=" form-control" '); ?>
                                    <span class="bottom">optional</span> 
                                </div>
                            </div>



                            <div class="form-group">
                                <div class="col-md-3"> ID/Passport:</div>
                                <div class="col-md-9">
                                    <?php echo form_input('m_id', isset($pero) && !empty($pero) ? $pero->m_id : $this->input->post('m_id'), 'class=""'); ?>
                                    <span class="bottom">optional</span>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-md-3"> Occupation:</div>
                                <div class="col-md-9">
<?php echo form_input('mother_occupation', isset($pero) && !empty($pero) ? $pero->mother_occupation : $this->input->post('mother_occupation'), 'class=""'); ?>
                                    <span class="bottom">optional</span>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-md-3"> Address:</div>
                                <div class="col-md-9">
                                    <textarea name="mother_address" class=""><?php echo isset($pero) && !empty($pero) ? $pero->mother_address : $this->input->post('mother_address'); ?></textarea>
                                    <span class="bottom"> Optional</span>
                                </div>
                            </div> 
<!--
                            <div class="form-group">
                                <div class="col-md-3"> Postal Code:</div>
                                <div class="col-md-8">
<?php echo form_input('m_postal_code', isset($pero) && !empty($pero) ? $pero->m_postal_code : $this->input->post('m_postal_code'), 'class=""'); ?>

                                </div>
                            </div>
-->
                        </div>

                        <div class="col-md-12">
                            <h2>Emergency Contact Details (Optional)</h2>
                        </div>

                        <div class="col-md-12">
                            <div class='form-group'>

                                <div class="col-md-3" for='name'>Name </div>
                                <div class="col-md-3">
<?php echo form_input('contact_name', $result->contact_name, 'id="name_"  class="form-control " placeholder="First Name"'); ?>
                                    <?php echo form_error('contact_name'); ?>
                                </div>

                                <div class="col-md-3">
<?php echo form_input('contact_m_name', $result->middle_name, 'id="name_"  class="form-control " placeholder="Middle Name"'); ?>
<?php echo form_error('contact_m_name'); ?>
                                </div>
                                <div class="col-md-3">
<?php echo form_input('contact_l_name', $result->last_name, 'id="name_"  class="form-control " placeholder="Last Name"'); ?>
                                    <?php echo form_error('contact_l_name'); ?>
                                </div>

                            </div>

                            <div class='form-group'>
                                <div class="col-md-3" for='relation'>Relation </div>
                                <div class="col-md-6">
                                    <?php
                                    $re_rels = array(
                                        "" => "Select Option",
                                        "Brother" => "Brother",
                                        "Sister" => "Sister",
                                        "Grandparent" => "Grandparent",
                                        "Uncle" => "Uncle",
                                        "Auntie" => "Auntie",
                                        "Guardian" => "Guardian",
                                        "Others" => "Others",
                                    );
                                    echo form_dropdown('contact_relation', $re_rels, (isset($result->contact_relation)) ? $result->contact_relation : '', ' class="chzn-select" data-placeholder="Select Options..." ');
                                    echo form_error('contact_relation');
                                    ?>
                                </div>
                            </div>

                            <div class='form-group'>
                                <div class="col-md-3" for='phone'>Phone </div>
                                <div class="col-md-6">
<?php echo form_input('contact_phone', $result->contact_phone, 'id="phone_"  class="form-control validate[minSize[10]] mask_mobile" '); ?>
<?php echo form_error('contact_phone'); ?>
                                </div>
                            </div>

                            <div class='form-group'>
                                <div class="col-md-3" for='email'>Email </div><div class="col-md-6">
<?php echo form_input('contact_email', $result->contact_email, 'id="email_"  class="form-control" '); ?>
<?php echo form_error('contact_email'); ?>
                                </div>
                            </div>

                            <div class='form-group'>
                                <div class="col-md-3" for='id_n'>ID Number </div><div class="col-md-6">
                                    <?php echo form_input('contact_id', $result->contact_id, 'id="email_"  class="form-control" '); ?>
<?php echo form_error('contact_id'); ?>
                                </div>
                            </div>
<!--
                            <div class='form-group'>
                                <div class="col-md-3" for='add'>Address </div>
                                <div class="col-md-6">
                                    <textarea id="contact_address"   name="contact_address"  /><?php echo set_value('contact_address', (isset($result->contact_address)) ? htmlspecialchars_decode($result->contact_address) : ''); ?></textarea>
<?php echo form_error('contact_address'); ?>
                                </div>
                            </div>
							-->

                            <div class='form-group'>
                                <div class="col-md-3" for='pb'>Info Provided By   </div><div class="col-md-6">
<?php echo form_input('contact_provided_by', $result->contact_provided_by, 'id="email_"  class="form-control" placeholder="E.g Father, Mother etc"'); ?>
<?php echo form_error('contact_provided_by'); ?>
                                </div>
                            </div>


                        </div>

                    </div>

                    <div class="col-md-11">
                        <hr>
                        <a href="#step-1" type="button" class="btn btn-primary prevBtn btn-lg pull-left" >Back</a>
                        <button class="btn btn-primary nextBtn btn-lg pull-right" type="button" >Next</button>
                    </div>
                </div>


            </div>
            <div class="row setup-content" id="step-3">
                <div class="col-md-12">
                    <div class="col-md-12">
                        <div class="form-group">
                            <div class="col-md-3">Date of Admission:</div>
                            <div class="col-md-4">
                                <div id="datetimepicker1" class="input-group date form_datetime">
                                    <?php
                                    $bt = '';
                                    if ($result->admission_date)
                                    {
                                        if ((!preg_match('/[^\d]/', $result->admission_date)))//if it contains digits only
                                        {
                                            $bt = date('d M Y', $result->admission_date);
                                        }
                                        else
                                        {
                                            $bt = $result->admission_date;
                                        }
                                    }
                                    echo form_input('admission_date', $bt, 'class="validate[required] datepicker col-md-8"');
                                    ?>
                                    <span class="input-group-addon "><i class="glyphicon glyphicon-calendar "></i></span></div>
                                <span class="bottom">Required, date</span>
                            </div>
                        </div>

                        <div class='form-group'>
                            <div class="col-md-3" for='class'>Class <span class='required'>*</span></div>
                            <div class="col-md-4">
                                <?php
                                $classes = $this->ion_auth->fetch_classes();
                                echo form_dropdown('class', $classes, (isset($result->class)) ? $result->class : '', ' class="select" data-placeholder="Select  Options..." ');
                                ?>		
                            </div>
                        </div>
                  

                        <div class='form-group' style="">
                            <div class="col-md-3" for='stream'>Current Admission No.</div>
                            <div class="col-md-4">
    <?php echo form_input('old_adm_no', $reg, 'class="validate[minSize[2]]" '); ?>
                            </div>
                        </div>
                        <div class='form-group' style="">
                            <div class="col-md-3" for='stream'>Student House</div>
                            <div class="col-md-4">
<?php
echo form_dropdown('house', $house, (isset($result->house)) ? $result->house : '', ' class="select" ');
?>	
                            </div>
                        </div>
                        <hr>
                         <?php if ($transport || $extras) { ?>
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h4>Student Fee</h4>
                                </div>
                                <div class="panel-body">

                                    <h4>Tuition Fee / Transport </h4>

                                    <div class="col-md-3">
                                        Term
                                        <?php
                                        echo form_dropdown('term', array('' => '') + $this->terms, ($this->input->post('term')) ? $this->input->post('term') : '', ' class="tsel" placeholder="Select Term" ');
                                        echo form_error('term');
                                        ?>
                                    </div>

                                    <div class="col-md-2">
                                        Year
                                        <?php
                                        echo form_dropdown('year', array('' => '') + $yrs, ($this->input->post('year')) ? $this->input->post('year') : '', ' class="tsel" placeholder="Select Year" ');
                                        echo form_error('year');
                                        ?>
                                    </div>

                                    <div class="col-md-3">
                                        Transport Zone
                                        <?php
                                        echo form_dropdown('trans', array('' => '') + $transport, ($this->input->post('trans')) ? $this->input->post('trans') : '', ' class="tsel" placeholder="Select Transport Zone" ');
                                        // echo form_error('term');
                                        ?>
                                    </div>

                                    <div class="col-md-2">
                                        Mode
                                        <?php
                                        $way = [1 => 'One Way', 2 => 'Two Way'];
                                        echo form_dropdown('way', array('' => '') + $way, ($this->input->post('way')) ? $this->input->post('way') : '', ' class="tsel" placeholder="Select Mode" ');
                                        // echo form_error('term');
                                        ?>
                                    </div>

                                    <div class="col-md-2" id="chck1">
                                        <label for="cust_btn"   >Custom</label>
                                        <input type="checkbox" id="cust_btn" name="custom_t">
                                    </div>

                                    <div class="col-md-2" id="chck2" hidden>
                                        <label   for="cust_btn1">Not Custom</label>
                                        <input type="checkbox" id="cust_btn1" name="custom_t" hidden>

                                    </div>

                                    <div id="cust" hidden>
                                        <div class="col-md-4">
                                            Day
                                            <?php
                                            $days = ['Sunday' => 'Sunday', 'Monday' => 'Monday', 'Tuesday' => 'Tuesday', 'Wednesday' => 'Wednesday', 'Thursday' => 'Thursday', 'Friday' => 'Friday', 'Saturday' => 'Saturday'];

                                            echo form_dropdown('day', array('' => 'Select day of the week') + $days, '', '   class="tsel"');
                                            ?>
                                        </div>

                                        <div class="col-md-4">
                                            Month
                                            <?php
                                            $months = [
                                                "January" => "January", "February" => "February", "March" => "March", "April" => "April",  "May" => "May", "June" => "June", "July" => "July", "August" => "August", "September" => "September", "October" => "October", 'November' => "November", 'December' => "December"
                                            ];

                                            echo form_dropdown('month', array('' => 'Select Month') + $months, '', '   class="tsel"');
                                            ?>
                                        </div>

                                        <div class="col-md-4">
                                            Amount (if changing)
                                            <input type="number" name="amount" class="form-control" placeholder="Amount">
                                        </div>
                                    </div>






                                    <div class="col-md-12">
                                        <h4>Invoice Optional Fee</h4>
                                        <?php
                                        $i = 0;
                                        foreach ($extras as $key => $x) {

                                            $i++;
                                            if ($i > 5) {
                                        ?>
                                                <div class="col-md-4">
                                                    <input type="checkbox" name="fee[<?php echo $key ?>]" value="<?php echo $key ?>"> <?php echo $x; ?><br>
                                                </div>
                                            <?php } elseif ($i < 5) { ?>
                                                <div class="col-md-4">
                                                    <input type="checkbox" name="fee[<?php echo $key ?>]" value="<?php echo $key ?>"> <?php echo $x; ?><br>
                                                </div>
                                        <?php   }
                                        } ?>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                        <a href="#step-2" type="button" class="btn btn-primary prevBtn pull-left" >Back</a>
                        <button class="btn btn-success btn-lg pull-right" type="submit">Submit</button>
                    </div>
                </div>
            </div>

        </div>  
        <div class="clearfix"></div>
    </div>

<?php echo form_close(); ?>
</div>


<script>
    $(document).ready(function() {


        $("#cust_btn1").hide("slow");

        $("#cust_btn").click(function() {
            $("#cust_btn").hide("slow");
            $("#cust_btn1").show("slow");
            $("#cust").show("slow");
            $("#chck1").hide("slow");
            $("#chck2").show("slow");

        });

        $("#cust_btn1").click(function() {
            $("#cust_btn1").hide("slow");
            $("#cust_btn").show("slow");
            $("#cust").hide("slow");
            $("#chck1").show("slow");
            $("#chck2").hide("slow");


        });

    });
</script>


<script type="text/javascript">
    function email_checker() {

        //grab the values
        var emai = document.getElementById('f_email').value;
        var yes;
        jQuery.getJSON("<?php echo base_url('admin/admission/email_verifier?email='); ?>" + emai, function (data) {

            yes = data;

            if (yes == 1) {
                swal({
                    title: "Email Verifier!",
                    text: "That email already exists use another one or leave blank!",
                    icon: "warning",
                    button: 'Close',
                    dangerMode: true,
                });

                jQuery('#f_email').empty();
            }
            else {
                //alert('yawa');
            }

        });
    }
</script>


<script>
    jQuery(function () {

        jQuery("#county").change(function () {
            jQuery('#sub_county').empty();

            var county = jQuery(".county").val();
            //alert(data);
            var options = '';
            jQuery('#sub_county').children().remove();

            jQuery.getJSON("<?php echo base_url('admin/admission/list_sub_counties'); ?>", {id: jQuery(this).val()}, function (data) {


                for (var i = 0; i < data.length; i++) {
                    options += '<option value="' + data[i].optionValue + '">' + data[i].optionDisplay + '</option>';
                }

                jQuery('#sub_county').append(options);

            });

            //alert(options);
        });

        $(".tsel").select2({'placeholder': 'Please Select', 'width': '100%'});
    });

</script>

<script type="text/javascript">
    $(document).ready(function ()
    {
        $("#newizz").validationEngine('attach', {promptPosition: "topLeft"});
        var navListItems = $('div.setup-panel div a'),
                allWells = $('.setup-content'),
                allNextBtn = $('.nextBtn');

        allWells.hide();

        navListItems.click(function (e)
        {
            e.preventDefault();
            var $target = $($(this).attr('href')),
                    $item = $(this);

            if (!$item.hasClass('disabled'))
            {
                navListItems.removeClass('btn-primary').addClass('btn-default');
                $item.addClass('btn-primary');
                allWells.hide();
                $target.show();
                $target.find('input:eq(0)').focus();
            }
        });

        allNextBtn.click(function ()
        {
            if (!$("#newizz").validationEngine('validate'))
                return false;
            var curStep = $(this).closest(".setup-content"),
                    curStepBtn = curStep.attr("id"),
                    nextStepWizard = $('div.setup-panel div a[href="#' + curStepBtn + '"]').parent().next().children("a"),
                    isValid = true;

            if (isValid)
                nextStepWizard.removeAttr('disabled').trigger('click');
        });

        $('div.setup-panel div a.btn-primary').trigger('click');
    });

    $(document).ready(
            function ()
            {
                $('#swtch input[type="radio"]').on('click', function ()
                {
                    var pt = $(this).attr('value');
                    if (pt == 1)
                    {
                        $('#pdrop').hide();
                        $('#newp').show();
                    }
                    if (pt == 2)
                    {
                        $('#newp').hide();
                        $('#pdrop').show();
                    }

                });
                $('#trswtch input[type="radio"]').on('click', function ()
                {
                    var pt = $(this).attr('value');
                    if (pt == 1)
                    {
                        if ($("#tram_val").length > 0)
                        {
                            $('#tram_val').remove();
                        }
                        $('#trswtch span[class="bottom"]').after('<input name="tramount" type="text" class="validate[required]" id="tram_val" style="opacity:100; float:left; width:140px; height:32px; display:block; placeholder="amount"  />')
                    }
                    if (pt == 0)
                    {
                        if ($("#tram_val").length > 0)
                        {
                            $('#tram_val').remove();
                        }
                    }
                });

                $('#smswtch input[type="radio"]').on('click', function ()
                {
                    var pt = $(this).attr('value');
                    if (pt == 1)
                    {
                        if ($("#sm_val").length > 0)
                        {
                            $('#sm_val').remove();
                        }
                        $('#smswtch span[class="bottom"]').after('<input name="smamount" type="text" class="validate[required]" id="sm_val" style="opacity:100; float:left; width:140px; height:32px; display:block; placeholder="amount"  />')
                    }
                    if (pt == 0)
                    {
                        if ($("#sm_val").length > 0)
                        {
                            $('#sm_val').remove();
                        }
                    }

                });

                $('#bdswtch input[type="radio"]').on('click', function ()
                {
                    var pt = $(this).attr('value');
                    if (pt == 1)
                    {
                        if ($("#bd_val").length > 0)
                        {
                            $('#bd_val').remove();
                        }
                        $('#bdswtch span[class="bottom"]').after('<input name="bdamount" type="text" class="validate[required]" id="bd_val" style="opacity:100; float:left; width:140px; height:32px; display:block; placeholder="amount"  />')
                    }
                    if (pt == 0)
                    {
                        if ($("#bd_val").length > 0)
                        {
                            $('#bd_val').remove();
                        }
                    }

                });

                $('#mlswtch input[type="radio"]').on('click', function ()
                {
                    var pt = $(this).attr('value');
                    if (pt == 1)
                    {
                        if ($("#ml_val").length > 0)
                        {
                            $('#ml_val').remove();
                        }
                        $('#mlswtch span[class="bottom"]').after('<input name="mlamount" type="text" class="validate[required]" id="ml_val" style="opacity:100; float:left; width:140px; height:32px; display:block; placeholder="amount" />')
                    }
                    if (pt == 0)
                    {
                        if ($("#ml_val").length > 0)
                        {
                            $('#ml_val').remove();
                        }
                    }

                });

                $('input#userfile').ajaxfileupload({

                    'action': BASE_URL + 'admin/admission/save_photo/',
                    'params': {
                        'extra': 'info'
                    },
                    'onComplete': function (response)
                    {
                        console.log(response);
                        if (response.status !== 'error')
                        {
                            //alert(response.status);
                            $('#files').html('<p>' + response.status + '.</p>');
                            $('#sphoto').val(response.pid);
                        }
                        //alert(JSON.stringify(response));
                    },
                    'onStart': function ()
                    {
                        //   if (weWantedTo)
                        //    return false; // cancels upload
                    },
                    'onCancel': function ()
                    {
                        console.log('no file selected');
                    }
                });

                $('input#b_cert').ajaxfileupload({
                    //alert('passport');
                    'action': BASE_URL + 'admin/admission/upload_certs/',
                    'params': {
                        'extra': 'info'
                    },
                    'onComplete': function (response)
                    {
                        console.log(response);
                        if (response.status !== 'error')
                        {
                            //alert(response.status);
                            $('#files').html('<p>' + response.status + '.</p>');
                            $('#birth_certificate').val(response.pid);
                        }
                        //alert(JSON.stringify(response));
                    },
                    'onStart': function ()
                    {
                        //   if (weWantedTo)
                        //    return false; // cancels upload
                    },
                    'onCancel': function ()
                    {
                        console.log('no file selected');
                    }
                });

                $('input#f_photo').ajaxfileupload({
                    //alert('passport');
                    'action': BASE_URL + 'admin/admission/save_parents_photo/',
                    'params': {
                        'extra': 'info'
                    },
                    'onComplete': function (response)
                    {
                        console.log(response);
                        if (response.status !== 'error')
                        {
                            //alert(response.status);
                            $('#files').html('<p>' + response.status + '.</p>');
                            $('#father_photo').val(response.pid);
                        }
                        //alert(JSON.stringify(response));
                    },
                    'onStart': function ()
                    {
                        //   if (weWantedTo)
                        //    return false; // cancels upload
                    },
                    'onCancel': function ()
                    {
                        console.log('no file selected');
                    }
                });

                $('input#m_photo').ajaxfileupload({
                    //alert('passport');
                    'action': BASE_URL + 'admin/admission/save_parents_photo/',
                    'params': {
                        'extra': 'info'
                    },
                    'onComplete': function (response)
                    {
                        console.log(response);
                        if (response.status !== 'error')
                        {
                            //alert(response.status);
                            $('#files').html('<p>' + response.status + '.</p>');
                            $('#mother_photo').val(response.pid);
                        }
                        //alert(JSON.stringify(response));
                    },
                    'onStart': function ()
                    {
                        //   if (weWantedTo)
                        //    return false; // cancels upload
                    },
                    'onCancel': function ()
                    {
                        console.log('no file selected');
                    }
                });

                //********* National IDs Upload ************/

                $('input#m_id_copy').ajaxfileupload({
                    //alert('passport');
                    'action': BASE_URL + 'admin/admission/upload_id_copies/',
                    'params': {
                        'extra': 'info'
                    },
                    'onComplete': function (response)
                    {
                        console.log(response);
                        if (response.status !== 'error')
                        {
                            //alert(response.status);
                            $('#files').html('<p>' + response.status + '.</p>');
                            $('#mother_id_copy').val(response.pid);
                        }
                        //alert(JSON.stringify(response));
                    },
                    'onStart': function ()
                    {
                        //   if (weWantedTo)
                        //    return false; // cancels upload
                    },
                    'onCancel': function ()
                    {
                        console.log('no file selected');
                    }
                });


                $('input#f_id_copy').ajaxfileupload({
                    //alert('passport');
                    'action': BASE_URL + 'admin/admission/upload_id_copies/',
                    'params': {
                        'extra': 'info'
                    },
                    'onComplete': function (response)
                    {
                        console.log(response);
                        if (response.status !== 'error')
                        {
                            //alert(response.status);
                            $('#files').html('<p>' + response.status + '.</p>');
                            $('#father_id_copy').val(response.pid);
                        }
                        //alert(JSON.stringify(response));
                    },
                    'onStart': function ()
                    {
                        //   if (weWantedTo)
                        //    return false; // cancels upload
                    },
                    'onCancel': function ()
                    {
                        console.log('no file selected');
                    }
                });

            });
</script>
<style type="text/css">
    .stepwizard-step p {
        margin-top: 10px;
    }
    .stepwizard-row {
        display: table-row;
    }
    .stepwizard {
        display: table;
        width: 50%;
        position: relative;
    }
    .stepwizard-step button[disabled] {
        opacity: 1 !important;
        filter: alpha(opacity=100) !important;
    }
    .stepwizard-step {
        display: table-cell;
        text-align: center;
        position: relative;
    }
    .btn-circle {
        width: 30px;
        height: 30px;
        text-align: center;
        padding: 3px;
        font-size: 12px;
        border-radius: 50%;
    }
    h4 {
        font-size: 17.5px;
        margin: 10px 0;
        font-family: inherit;
        font-weight: bold;
        line-height: 3px;
        color: inherit;
    }
</style>
