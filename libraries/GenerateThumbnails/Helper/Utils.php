<?php

class GenerateThumbnails_Helper_Utils {
    protected static $_config;

    /**
     * Returns the most recent jobs related to reindexing the site.
     *
     * @return array
     */
    public static function isJobRunning(string $classType): bool
    {
        $select = get_db()->select()->from(get_db()->Process);
        $select->where("status = 'in progress'");
        $select->where("args like '%" . $classType . "%'");
        $select->where('not isnull(pid)');
        $select->order("id desc");
        $select->limit(1);

        $workingJob = $select->query()->fetchAll(Zend_Db::FETCH_COLUMN);
        if (count($workingJob) > 0) {
            return true;
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
