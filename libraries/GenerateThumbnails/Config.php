<?php

class GenerateThumbnails_Form_Config extends Omeka_Form {
    public function init() {
        parent::init();

        // Create only missing thumbnails
        $this->addElement('checkbox', 'generatethumbnails_missingonly', array(
            'label'         => __('Re-create missing ones only?'),
            'description'   => __('Enable this option to create thumbnails only when there is no one available. '),
            'value'         => get_option('generatethumbnails_missingonly'),
            'required'      => true,
        ));


        $this->addElement('submit', 'submit', array(
            'label' => __('Save Settings')
        ));

        $this->addDisplayGroup(array(
            'generatethumbnails_missingonly',
        ), 'fields');

        $this->addDisplayGroup(array('submit'), 'submit_button');
    }
}