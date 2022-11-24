<?php

/**
 * Admin Controller for GenerateThumbnails plugin.
 *
 * Provides actions to configure and re-generate thumbnails.
 *
 */
class GenerateThumbnails_AdminController extends Omeka_Controller_AbstractActionController
{
    protected function _handlePermissions()
    {
        if (!GenerateThumbnails_Helper_Utils::hasAdminPermission()) {
            throw new Omeka_Controller_Exception_403;
        }
    }

    public function configAction()
    {
        $this->_handlePermissions();
        $form = new GenerateThumbnails_Helper_Config();

        if ($this->_request->isPost() && $form->isValid($_POST)) {
            foreach ($form->getValues() as $option => $value) {
                set_option($option, $value);
            }
            try {
                $job_dispatcher = Zend_Registry::get('job_dispatcher');
                $job_dispatcher->setUser($this->getCurrentUser());

                 $job_dispatcher->sendLongRunning('GenerateThumbnails_Job_Regenerate');

                $this->_helper->flashMessenger(__('Re-generation requested.'), 'success');
            } catch (Exception $err) {
                $this->_helper->flashMessenger($err->getMessage(), 'error');
            }
            $this->redirect('/generatethumbnails/config');
        } else {
            if (GenerateThumbnails_Helper_Utils::isJobRunning("GenerateThumbnails_Job_Regenerate")) {
                $this->_helper->flashMessenger(__('Re-generation running in background, you do not have to run it again.'), 'success');
            } else {
                $this->_helper->flashMessenger(__('no job found'), 'success');
            }
            $this->view->form = $form;
        }
    }
}
