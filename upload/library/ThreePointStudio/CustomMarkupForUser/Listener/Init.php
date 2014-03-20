<?php
/*
* Custom Username Markup For User v1.0.0 written by tyteen4a03@3.studIo.
* This software is licensed under the BSD 2-Clause modified License.
* See the LICENSE file within the package for details.
*/

class ThreePointStudio_CustomMarkupForUser_Listener_Init {
	public static function initDependencies(XenForo_Dependencies_Abstract $dependencies, array $data) {
		if (XenForo_Application::$versionId < 1020000) {
			class_alias("ThreePointStudio_CustomMarkupForUser_TemplateHelpers_11", "ThreePointStudio_CustomMarkupForUser_TemplateHelpers");
		} else {
			class_alias("ThreePointStudio_CustomMarkupForUser_TemplateHelpers_12", "ThreePointStudio_CustomMarkupForUser_TemplateHelpers");
		}
		XenForo_Template_Helper_Core::$helperCallbacks["username"] = array("ThreePointStudio_CustomMarkupForUser_TemplateHelpers", "helperUserName");
		XenForo_Template_Helper_Core::$helperCallbacks["richusername"] = array("ThreePointStudio_CustomMarkupForUser_TemplateHelpers", "helperRichUserName");
		XenForo_Template_Helper_Core::$helperCallbacks["usernamehtml"] = array("ThreePointStudio_CustomMarkupForUser_TemplateHelpers", "helperUserNameHtml");
		XenForo_Template_Helper_Core::$helperCallbacks["usertitle"] = array("ThreePointStudio_CustomMarkupForUser_TemplateHelpers", "helperUserTitle");
	}
}