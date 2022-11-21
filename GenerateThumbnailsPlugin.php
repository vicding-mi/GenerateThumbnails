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
        'search_form_default_action'
    );

    public function hookInstall() {
        $this->_setOptions();
    }

    public function hookUninstall() {
        $this->_clearOptions();
    }

    public function hookDefineRoutes($args) {
        $config = new Zend_Config_Ini(ELASTICSEARCH_PLUGIN_DIR.'/routes.ini');
        $args['router']->addConfig($config);
    }

    public function filterAdminNavigationMain($nav) {
        if(Elasticsearch_Utils::hasAdminPermission()) {
            $nav[] = array(
                'label' => __('Elasticsearch'),
                'uri' => url('elasticsearch/admin/server')
            );
        }
        return $nav;
    }

    public function filterSearchFormDefaultAction($uri) {
        if (!is_admin_theme()) {
            $uri = url('elasticsearch/search/interceptor');
        }
        return $uri;
    }

    protected function _setOptions() {
        set_option('elasticsearch_index', $docIndex);
        set_option('elasticsearch_aws', $service == 'aws' ? 1 : 0);
        set_option('elasticsearch_host', $host['host']);
        set_option('elasticsearch_port', $host['port']);
        set_option('elasticsearch_scheme', $host['scheme']);
        set_option('elasticsearch_user', $host['user']);
        set_option('elasticsearch_pass', $host['pass']);
        set_option('elasticsearch_show_timestamps', true);
    }

    protected function _clearOptions() {
        delete_option('elasticsearch_index');
        delete_option('elasticsearch_aws');
        delete_option('elasticsearch_host');
        delete_option('elasticsearch_port');
        delete_option('elasticsearch_scheme');
        delete_option('elasticsearch_user');
        delete_option('elasticsearch_pass');
        delete_option('elasticsearch_show_timestamps');
    }

}
