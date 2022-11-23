<?php

class GenerateThumbnails_Helper_Utils {
    protected static $_config;

    /**
     * Truncates a string for display.
     *
     * @param string $text the text to truncate
     * @param int $length the length to truncate to
     * @param boolean $ellipsis show an ellipsis or not
     * @return string
     */
    public static function truncateText($text, $length, $ellipsis=true) {
        $truncated = substr($text, 0, $length);
        if($ellipsis && strlen($truncated) > $length) {
            return "$truncated...";
        }
        return $truncated;
    }

    /**
     * Formats an ISO8601 date for display.
     */
    public static function formatDate($iso8601date) {
        return date_format(date_create($iso8601date), "F j, Y");
    }

    /**
     * Returns the most recent jobs related to reindexing the site.
     *
     * @return array
     */
    public static function isJobRunning(string $classType): bool
    {
        $table = get_db()->getTable('Process');
        $select = $table->getSelect();
        $job_objects = $table->fetchObjects($select);

        foreach($job_objects as $job_object) {
            // Because job args are serialized to a string using some combination of PHP serialize() and json_encode(),
            // just do a simple string search rather than try to deal with that.
            if(!empty($job_object->args) && strrpos($job_object->args, '$classType') !== FALSE) {
                return true;
            }
        }

        return false;
    }

    public static function load(): Zend_Config_Ini
    {
        if(self::$_config) {
            return self::$_config;
        }
        self::$_config = new Zend_Config_Ini(GENERATETHUMBNAILS_PLUGIN_DIR.'/config.ini');
        return self::$_config;
    }

    public static function roles(): array
    {
        $config = self::load();
        $roles = explode(",", $config->get('roles', 'admin,super'));
        return array_map('trim', $roles);
    }

    /**
     * Returns true if the user is allowed to access the admin functionality.
     *
     * Superusers always have permission, but other roles must be explicitly
     * allowed.
     *
     * @return boolean
     */
    public static function hasAdminPermission() {
        $user = Zend_Registry::get('bootstrap')->getResource('CurrentUser');
        if(!$user) {
            return false;
        }
        $roles = array_unique(array_merge(self::roles(), array('super')));
        return in_array($user->role, $roles);
    }
}
