<div class="col-md-8">
        <div class="head"> 
             <div class="icon"><span class="icosg-target1"></span></div>		
            <h2>  Sales Items  </h2>
             <div class="right"> 
             <?php echo anchor( 'admin/sales_items/create' , '<i class="glyphicon glyphicon-plus">
                </i> '.lang('web_add_t', array(':name' => 'Sales Items')), 'class="btn btn-primary"');?> 
              <?php echo anchor( 'admin/sales_items' , '<i class="glyphicon glyphicon-list">
                </i> '.lang('web_list_all', array(':name' => 'Sales Items')), 'class="btn btn-primary"');?> 
             
                </div>
                </div>
         	                    
               
				   <div class="block-fluid">

<?php 
$attributes = array('class' => 'form-horizontal', 'id' => '');
echo   form_open_multipart(current_url(), $attributes); 
?>
<div class='form-group'>
	<div class="col-md-3" for='item_name'>Item Name <span class='required'>*</span></div><div class="col-md-6">
	<?php echo form_input('item_name' ,$result->item_name , 'id="item_name_"  class="form-control" ' );?>
 	<?php echo form_error('item_name'); ?>
</div>
</div>

<div class='form-group'>
	<div class="col-md-3" for='category'>Category <span class='required'>*</span></div>
<div class="col-md-6">
                <?php 		
     echo form_dropdown('category', $cats,  (isset($result->category)) ? $result->category : ''     ,   ' class="select" data-placeholder="Select Options..." ');
     echo form_error('category'); ?>
</div></div>

<div class='widget'>
  <div class='head dark'>
        <div class='icon'><i class='icos-pencil'></i></div>
	<h2>Description </h2></div>
	 <div class="block-fluid editor">
	<textarea id="description"   style="height: 300px;" class=" wysiwyg "  name="description"  /><?php echo set_value('description', (isset($result->description)) ? htmlspecialchars_decode($result->description) : ''); ?></textarea>
	<?php echo form_error('description'); ?>
</div>
</div>

<div class='form-group'><div class="col-md-3"></div><div class="col-md-6">
    

    <?php echo form_submit( 'submit', ($updType == 'edit') ? 'Update' : 'Save', (($updType == 'create') ? "id='submit' class='btn btn-primary''" : "id='submit' class='btn btn-primary'")); ?>
	<?php echo anchor('admin/sales_items','Cancel','class="btn  btn-default"');?>
</div></div>
 
<?php echo form_close(); ?>
<div class="clearfix"></div>
 </div>
  </div>
  	
	  <div class="col-md-4">
	  
	       <div class="widget">
                    <div class="head dark">
                        <div class="icon"></div>
                        <h2>Add Item Category</h2>
                    </div>
					
                    <div class="block-fluid">
                         <?php echo form_open('admin/sales_items_category/quick_add','class=""'); ?>
                        <div class="form-group">
                            <div class="col-md-3">Name:</div>
                            <div class="col-md-9">                                      
                                 <?php echo form_input('name','', 'id="title_1"  placeholder=" e.g Uniform,Books etc"' );?>
 	                           <?php echo form_error('title'); ?>
                            </div>
                        </div>
                                                    
                        <div class="form-group">
						 <div class="col-md-3">Description:</div>
                            <div class="col-md-9">
                                <textarea name="description"></textarea> 
                            </div>
                        </div>                        
                   
                    <div class="toolbar TAR">
                        <button class="btn btn-primary">Add Category</button>
                    </div>
					   <?php echo form_close(); ?> 
					</div>
            </div>
  
	</div>