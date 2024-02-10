<div class="head">
    <div class="icon"><span class="icosg-target1"></span></div>
    <h2>CBC Subjects </h2>
    <div class="right">
        <?php echo anchor('admin/cbc/add_subject', '<i class="glyphicon glyphicon-plus"></i> ' . lang('web_add_t', array(':name' => 'Subjects')), 'class="btn btn-primary"'); ?>
    </div>
</div>
<?php if ($subjects): ?>
    <div class="toolbar">
        <?php echo form_open(current_url()); ?>
        Class:
        <?php echo form_dropdown('class', ['' => ''] + $this->classes, $this->input->post('class'), 'class ="tsel" '); ?>
        <button class="btn btn-primary"  type="submit">Filter Class</button>
        <?php echo form_close(); ?>
    </div>
    <div class="block-fluid">
        <hr>
        <table class="table" cellpadding="0" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Classes</th>
                    <th>Category</th>
                    <th width="30%"><?php echo lang('web_options'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php
                $i = 0;
                if ($this->uri->segment(4) && ((int) $this->uri->segment(4) > 0))
                {
                    $i = ($this->uri->segment(4) - 1) * $per;
                }
                foreach ($subjects as $p):
                    $i++;
                    ?>
                    <tr>
                        <td><?php echo $i . '.'; ?></td>
                        <td><?php echo $p->name; ?></td>
                        <td width="30%"><?php
                            foreach ($p->classes as $title)
                            {
                                echo '<span class="label label-info">' . $title . '</span> ';
                            }
                            ?></td>
                        <td><?php echo isset($cats[$p->cat]) ? $cats[$p->cat] : ' - '; ?></td>
                        <td width='20%'>
                            <div class="btn-group">
                                <a class="btn btn-primary" href="<?php echo site_url('admin/cbc/edit_subject/' . $p->id); ?>"><i class="glyphicon glyphicon-edit"></i> Edit</a>
                                <a class="btn btn-success" href="<?php echo site_url('admin/cbc/learning_areas/' . $p->id); ?>"><i class="glyphicon glyphicon-list"></i> Learning Areas</a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    </div>
<?php else: ?>
    <p class='text'><?php echo lang('web_no_elements'); ?></p>
<?php endif ?>
<script>
    $(document).ready(function ()
    {
        $(".tsel").select2({'placeholder': '-- Class --', 'width': '340px'});
        $(".tsel").on("change", function (e)
        {
            notify('Select', 'Value changed: ' + e.added.text);
        });
    });
</script>
