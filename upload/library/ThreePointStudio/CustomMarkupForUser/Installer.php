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
			$db->query("ALTER TABLE `xf_user` ADD `3ps_cmfu_options` BLOB NOT NULL");
		}
	}

	public static final function uninstall() {
		$db = XenForo_Application::getDb();
		$db->query("ALTER TABLE `xf_user` DROP `3ps_cmfu_options`");
	}
}