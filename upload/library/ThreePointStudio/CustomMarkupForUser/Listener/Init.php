<?php
/*
* Custom Username Markup For User v1.0.0 written by tyteen4a03@3.studIo.
* This software is licensed under the BSD 2-Clause modified License.
* See the LICENSE file within the package for details.
*/

class ThreePointStudio_CustomMarkupForUser_Listener_Init {
	public static function initDependencies(XenForo_Dependencies_Abstract $dependencies, array $data) {
		$versionDD = ThreePointStudio_CustomMarkupForUser_Helpers::determineVersion(); // Double Digits, in case you're wondering
		XenForo_Template_Helper_Core::$helperCallbacks["username"] = array("ThreePointStudio_CustomMarkupForUser_TemplateHelpers_" . $versionDD, "helperUserName");
		XenForo_Template_Helper_Core::$helperCallbacks["richusername"] = array("ThreePointStudio_CustomMarkupForUser_TemplateHelpers_" . $versionDD, "helperRichUserName");
		XenForo_Template_Helper_Core::$helperCallbacks["usernamehtml"] = array("ThreePointStudio_CustomMarkupForUser_TemplateHelpers_" . $versionDD, "helperUserNameHtml");
		XenForo_Template_Helper_Core::$helperCallbacks["usertitle"] = array("ThreePointStudio_CustomMarkupForUser_TemplateHelpers_" . $versionDD, "helperUserTitle");
	}
}