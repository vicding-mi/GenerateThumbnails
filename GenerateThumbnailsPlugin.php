<?php

/**
 * @package Elasticsearch
 * @subpackage elasticsearch
 * @copyright 2017 President and Fellows of Harvard College
 * @license https://opensource.org/licenses/BSD-3-Clause
 */

define('GENERATETHUMBNAILS_PLUGIN_DIR', dirname(__FILE__));
require (GENERATETHUMBNAILS_PLUGIN_DIR.'/autoload.php');

class GenerateThumbnailsPlugin extends Omeka_Plugin_AbstractPlugin {

    protected $_hooks = array(
        'install',
        'uninstall',
        'define_routes'
    );

    protected $_filters = array(
        'admin_navigation_main',
    );

    public function hookInstall() {
        $this->_setOptions();
    }

    public function hookUninstall() {
        $this->_clearOptions();
    }

    public function hookDefineRoutes($args) {
//        $config = new Zend_Config_Ini(GENERATETHUMBNAILS_PLUGIN_DIR.'/routes.ini');
//        $args['router']->addConfig($config);
        $router = $args['router'];
        $mapRoute = new Zend_Controller_Router_Route('generatethumbnails/config',
            array('controller' => 'admin',
                'action'     => 'config'));
        $router->addRoute('generatethumbnails/config', $mapRoute);
    }

    public function filterAdminNavigationMain($nav) {
        if(GenerateThumbnails_Utils::hasAdminPermission()) {
            $nav[] = array(
                'label' => __('Generate Thumbnails'),
                'uri' => url('generatethumbnails/config')
            );
        }
        return $nav;
    }

    protected function _setOptions() {
        set_option('generatethumbnails_missingonly', true);
    }

    protected function _clearOptions() {
        delete_option('generatethumbnails_missingonly');
    }

}
