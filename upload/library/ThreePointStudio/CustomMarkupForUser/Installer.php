<?php
/*
* Custom Username Markup For User v1.0.0 written by tyteen4a03@3.studIo.
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
                      `preset_id` bigint(20) unsigned NOT NULL,
                      `title` text CHARACTER SET latin1 NOT NULL,
                      `enable_for` text CHARACTER SET latin1 NOT NULL,
                      `config` text CHARACTER SET latin1 NOT NULL,
                      `user_groups` text CHARACTER SET latin1 NOT NULL
                    ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
                ");
            $db->query("ALTER TABLE `xf_3ps_cmfu_presets` ADD UNIQUE KEY `preset_id` (`preset_id`);");
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
                $db->query("ALTER TABLE `xf_user` DROP COLUMN `3ps_cmfu_render_cache`");
                $db->query("CREATE TABLE IF NOT EXISTS `xf_3ps_cmfu_presets` (
                      `preset_id` bigint(20) unsigned NOT NULL,
                      `title` text CHARACTER SET latin1 NOT NULL,
                      `enable_for` text CHARACTER SET latin1 NOT NULL,
                      `config` text CHARACTER SET latin1 NOT NULL,
                      `user_groups` text CHARACTER SET latin1 NOT NULL
                    ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
                ");
                $db->query("ALTER TABLE `xf_3ps_cmfu_presets` ADD UNIQUE KEY `preset_id` (`preset_id`);");
            }
        }
    }

    public static final function uninstall() {
        $db = XenForo_Application::getDb();
        $db->query("ALTER TABLE `xf_user`
                    DROP COLUMN `3ps_cmfu_options`");
        $db->query("DROP TABLE IF EXISTS `xf_3ps_cmfu_presets`");
    }
}
