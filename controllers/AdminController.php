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
                $this->_helper->flashMessenger(__('Re-creation started.'), 'success');
            } catch (Exception $err) {
                $this->_helper->flashMessenger($err->getMessage(), 'error');
            }
            $this->redirect('/generatethumbnails/config');
        } else {
//            $jobs = Elasticsearch_Helper_Index::getReindexJobs();
//            $this->view->assign("jobs", $jobs);
//            $this->view->form = new Elasticsearch_Form_Index();
            $this->_helper->flashMessenger(__('Re-creation waiting.'), 'success');
        }


//        $query = $this->params()->fromPost();
//
//        $job = $this->jobDispatcher()->dispatch(CreateMissingThumbnails::class, ['query' => $query]);
//
//        $jobUrl = $this->url()->fromRoute('admin/id', [
//            'controller' => 'job',
//            'action' => 'show',
//            'id' => $job->getId(),
//        ]);
//
//        $message = new Message(
//            $this->translate('Thumbnails creation has started. %s'),
//            sprintf(
//                '<a href="%s">%s</a>',
//                htmlspecialchars($jobUrl),
//                $this->translate('Go to background job')
//            )
//        );
//        $message->setEscapeHtml(false);
//        $this->messenger()->addSuccess($message);
//        return $this->redirect()->toRoute(null, [], [], true);


        $this->view->form = $form;
    }
}
