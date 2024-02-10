<?php $avt = strtolower(substr($this->user->first_name, 0, 1)); ?>
<div class="row card-box table-responsive">

    <div class="col-md-12">
        <div class="card-bsox">
            <h4 class="header-title m-t-0 m-b-20">Record Exam Marks </h4>
            <?php if (isset($exams)){ ?>
                <table id="datatable-buttons" class="table table-striped table-bordered">
               
                    <tbody>
                     
                            <tr>
                                <th>#</th>
                                <th>Title</th>
                                <th>Term</th>
                                <th>Year</th>
                                <th>Start</th>
                                <th>End</th>
                                <th>Recording Deadline</th>
                                <th class="text-center" width="38%"> Option  </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $i = 0;
                          
                            foreach ($exams as $p){

                                    $i++;
                                    ?>
                                    <tr>
                                        <td><?php echo $i . '.'; ?></td>	
                                        <td><?php echo $p->title; ?></td>
                                        <td> <?php echo isset($this->terms[$p->term]) ? $this->terms[$p->term] : ' '; ?></td>
                                        <td><?php echo $p->year; ?></td>
                                        <td><?php echo date("d M Y", $p->start_date); ?></td>
                                        <td><?php echo date("d M Y", $p->end_date); ?></td>
                                        <td><?php echo $p->recording_end_date ? date('d M Y', $p->recording_end_date) : ''; ?></td>
                                        <td class="text-center">
                                        <?php
                                        if($p->recording_end_date < time() ){ ?>
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-danger dropdown-toggle waves-effect" ><span class="mdi mdi-lock  m-r-5"></span> Recording Locked  </button>
                                                
                                            </div>
                                        <?php }else{?>
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-primary dropdown-toggle waves-effect" data-toggle="dropdown"><i class="mdi mdi-view-dashboard m-r-5"></i>  Record   Exam <span class="caret"></span> </button>
                                                <ul class="dropdown-menu">
                                                    <?php
                                                    foreach ($classes as $cl)
                                                    {
                                                             ?>    
                                                                   
                                                                    <li><?php echo anchor('trs/bulk_edit/' . $p->id . '/' . $cl->id, $cl->name); ?></li>
                                                                    <?php
                                                            
                                                    }
                                                    ?>
                                                </ul>
                                            </div>
                                            <?php }?>
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-success dropdown-toggle waves-effect" data-toggle="dropdown">
                                                    <i class="mdi mdi-file-export"></i> View  Results <span class="caret"></span> 
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <?php
                                                    foreach ($classes as $cl)
                                                    {
                                                            
                                                                    ?>    
                                                                    <li><?php echo anchor('trs/results/' . $p->id . '/' . $cl->id, $cl->name); ?></li>
                                                                    <?php
                                                            
                                                    }
                                                    ?>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                            <?php } ?>
                            
                        </tbody>
                    </table>
            <?php }  else{ ?>
                <div class="alert alert-icon alert-danger alert-dismissible fade in" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                    <i class="mdi mdi-alert"></i>
                    No Exams Have been Added Yet.
                </div>
            <?php
            }
            echo $links;
            ?>            
        </div>
    </div>
</div>






