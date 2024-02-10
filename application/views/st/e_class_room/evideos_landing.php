<div class="portlet mt-2">
    <div class="portlet-heading portlet-default border-bottom">
        <h3 class="portlet-title text-dark">
           <?php $classes = $this->ion_auth->fetch_classes(); echo $classes[$this->student->class]; ?> E-Videos
        </h3>
       <a class="btn btn-sm btn-danger pull-right" onclick="goBack()"><i class="fa fa-caret-left"></i> Go Back</a>
        <div class="clearfix"></div>
        <hr>
    </div>
 <div class="content">
	  <div class="row">
	
	
 <div class="col-sm-12">


 <?php if ($subjects): ?>
      
		     	<?php 
					 $i = 0;
		
            foreach ($subjects as $p ): 
                 $i++;
				 $counter = $this->st_m->count_video_files($class,$p->subject_id);
                     ?>
					 
					 <?php 
							$url = site_url('st/watch/'.$p->subject_id.'/'.$this->session->userdata['session_id']);
							if($counter<=0){
								$url='#';
							}
					?>
					 
							
						 <a href='<?php echo $url;?>'>
					 <div class="col-sm-3" style="text-align:center"> 
					 
					   <?php 
							
							
							$tx = 'text-green';
							if($counter>0){
								echo theme_image('videos.png');
							}else{
								echo theme_image('videos-empty.png');
								$tx ='text-grey';
								$url='#';
							}
					?>
						<br>
					    
						 <?php $ln = strlen($subs[$p->subject_id]); echo strtoupper(substr($subs[$p->subject_id],0,20)); if($ln >20) echo '...'?> 
						 <br>
						 <b class="<?php echo $tx?>">( <?php echo number_format($counter);?> Videos )</b>
						 
						 <hr>
				     </div>	</a>		

					
        <?php endforeach ?>	

            <?php 
							$url = site_url('st/watch_general/'.$this->session->userdata['session_id']);
							if($count_general<=0){
								$url='#';
							}
					?>
        
                 <a href='<?php echo $url;?>'>
					 <div class="col-sm-3" style="text-align:center"> 
					 
					   <?php 
							
							
							$tx = 'text-green';
							if($count_general>0){
								echo theme_image('videos.png');
							}else{
								echo theme_image('videos-empty.png');
								$tx ='text-grey';
							
							}
					?>
						<br>
					    
						<b class="text-red"> GENERAL VIDEOS </b>
						 <br>
						 <b class="<?php echo $tx?>">( <?php echo number_format($count_general);?> Videos )</b>
						 
						 
				     </div>	
				</a>	

					 

		<?php else: ?>
			<p class='text'><?php echo lang('web_no_elements');?></p>
		<?php endif ?>
   
  </div>
  </div>
  </div>
  </div>
  
  
		
		
		
		
		
		
		