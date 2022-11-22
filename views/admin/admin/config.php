<?php echo head(array(
    'title' => __('Generate Thumbnails | Configuration')
)); ?>

<?php //echo $this->partial('admin/partials/navigation.php', array('tab' => 'server')); ?>

    <div id="primary">
        <h2><?php echo __('Server Configuration') ?></h2>
        <?php echo flash(); ?>
        <?php echo $form ?>
    </div>

<?php echo foot(); ?>