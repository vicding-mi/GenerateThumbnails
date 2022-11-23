<?php echo head(array(
    'title' => __('Generate Thumbnails | Configure')
)); ?>

<?php //echo $this->partial('admin/partials/navigation.php', array('tab' => 'server')); ?>

    <div id="primary">
        <h2><?php echo __('Configure and re-generate missing thumbnails') ?></h2>
        <?php echo flash(); ?>
        <?php echo $form ?>
    </div>

<?php echo foot(); ?>