<?php
/*
* Custom Markup For User v1.1.0 written by tyteen4a03@3.studIo.
* This software is licensed under the BSD 2-Clause modified License.
* See the LICENSE file within the package for details.
*/

class ThreePointStudio_CustomMarkupForUser_Installer {
    public static final function install($installedAddon) {
        $db = XenForo_Application::getDb();
        $version = is_array($installedAddon) ? $installedAddon['version_id'] : 0;
        if ($version == 0) {
            $db->query("ALTER TABLE `xf_user`
                        ADD COLUMN  `3ps_cmfu_options` BLOB NULL AFTER `is_staff`");
            $db->query("CREATE TABLE IF NOT EXISTS `xf_3ps_cmfu_presets` (
                  `preset_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
                  `title` text COLLATE utf8_unicode_ci NOT NULL,
                  `enable_for` text COLLATE utf8_unicode_ci NOT NULL,
                  `display_style_priority` int(10) unsigned NOT NULL,
                  `config` text COLLATE utf8_unicode_ci NOT NULL,
                  `user_groups` text COLLATE utf8_unicode_ci NOT NULL
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
            ");
            $db->query("ALTER TABLE `xf_3ps_cmfu_presets`
                ADD UNIQUE KEY `preset_id` (`preset_id`);
            ");
            $db->query("ALTER TABLE `xf_data_registry` CHANGE `data_key` `data_key` VARBINARY(50) NOT NULL");
        }
        if ($version > 0) { // Upgrade section
            if ($version < 3) { // 1.0.0 Beta 1 - 1.0.0
                $db->query("ALTER TABLE `xf_user` ADD COLUMN `3ps_cmfu_render_cache` BLOB NULL AFTER `3ps_cmfu_options`");
            }

            if ($version < 4) { // 1.0.0 - 1.0.1
                $db->query("ALTER TABLE `xf_user`
                    CHANGE `3ps_cmfu_options` `3ps_cmfu_options` BLOB NULL,
                    CHANGE `3ps_cmfu_render_cache` `3ps_cmfu_render_cache` BLOB NULL
                ");
            }

            if ($version < 5) { // 1.0.1 - 1.1.0
                try {
                    $db->query("ALTER TABLE `xf_user` DROP COLUMN `3ps_cmfu_render_cache`");
                } catch (Zend_Db_Statement_Mysqli_Exception $e) {}
                $db->query("CREATE TABLE IF NOT EXISTS `xf_3ps_cmfu_presets` (
                    `preset_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
                    `title` text COLLATE utf8_unicode_ci NOT NULL,
                    `enable_for` text COLLATE utf8_unicode_ci NOT NULL,
                    `display_style_priority` int(10) unsigned NOT NULL,
                    `config` text COLLATE utf8_unicode_ci NOT NULL,
                    `user_groups` text COLLATE utf8_unicode_ci NOT NULL
                  ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
                ");
                $db->query("ALTER TABLE `xf_3ps_cmfu_presets`
                  ADD UNIQUE KEY `preset_id` (`preset_id`);
                ");
                $db->query("ALTER TABLE `xf_data_registry` CHANGE `data_key` `data_key` VARBINARY(50) NOT NULL");
            }
        }
    }

    public static final function uninstall() {
        $db = XenForo_Application::getDb();
        try {
            $db->query("ALTER TABLE `xf_user` DROP COLUMN `3ps_cmfu_render_cache`");
        } catch (Zend_Db_Statement_Mysqli_Exception $e) {}
        $db->query("DROP TABLE IF EXISTS `xf_3ps_cmfu_presets`");
        $db->query("ALTER TABLE `xf_data_registry` CHANGE `data_key` `data_key` VARBINARY(25) NOT NULL");
    }
}
