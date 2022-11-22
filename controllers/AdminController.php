<?php

/**
 * Admin Controller for Elasticsearch plugin.
 *
 * Provides actions to configure the elasticserach server URL, reindex the site, etc.
 *
 */
class GenerateThumbnails_AdminController extends Omeka_Controller_AbstractActionController {

    protected function _handlePermissions() {
        if(!GenerateThumbnails_Utils::hasAdminPermission()) {
            throw new Omeka_Controller_Exception_403;
        }
    }

    public function configAction() {
        $this->_handlePermissions();

        $form = new GenerateThumbnails_Form_Config();

//        if($this->_request->isPost() && $form->isValid($_POST)) {
//            foreach($form->getValues() as $option => $value) {
//                set_option($option, $value);
//            }
//
//            try {
//                $client = Elasticsearch_Client::create(['timeout' => 2]);
//                $res = $client->cat()->health();
//                $msg = "Elasticsearch endpoint health check successful. Cluster status is {$res[0]['status']} with {$res[0]['node.total']} total nodes.";
//                $this->_helper->flashMessenger($msg, 'success');
//            } catch(Exception $e) {
//                $msg = "Elasticsearch endpoint health check failed. Error: ".$e->getMessage();
//                $this->_helper->flashMessenger($msg, 'error');
//            }
//        }

        $this->view->form = $form;
    }

    public function serverAction() {
        $this->_handlePermissions();

        $form = new GenerateThumbnails_Form_Config();

//        if($this->_request->isPost() && $form->isValid($_POST)) {
//            foreach($form->getValues() as $option => $value) {
//                set_option($option, $value);
//            }
//
//            try {
//                $client = Elasticsearch_Client::create(['timeout' => 2]);
//                $res = $client->cat()->health();
//                $msg = "Elasticsearch endpoint health check successful. Cluster status is {$res[0]['status']} with {$res[0]['node.total']} total nodes.";
//                $this->_helper->flashMessenger($msg, 'success');
//            } catch(Exception $e) {
//                $msg = "Elasticsearch endpoint health check failed. Error: ".$e->getMessage();
//                $this->_helper->flashMessenger($msg, 'error');
//            }
//        }

        $this->view->form = $form;
    }
}
