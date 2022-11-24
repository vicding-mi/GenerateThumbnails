<?php echo head(array(
    'title' => __('Generate Thumbnails | Configure')
)); ?>

    <div id="primary">
        <h2><?php echo __('Configure and re-generate missing thumbnails') ?></h2>
        <?php echo flash(); ?>
        <?php echo $form ?>
    </div>

<?php echo foot(); ?>