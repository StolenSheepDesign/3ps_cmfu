<?php
/*
* Custom Username Markup For User v1.0.0 written by tyteen4a03@3.studIo.
* This software is licensed under the BSD 2-Clause modified License.
* See the LICENSE file within the package for details.
*/

class ThreePointStudio_CustomMarkupForUser_Listener_Template {
	public static function templateHook($hookName, &$contents, array $hookParams, XenForo_Template_Abstract $template) {
		if ($hookName == "account_preferences_options") {
			$visitor = XenForo_Visitor::getInstance();
			if (!$visitor->hasPermission("3ps_cmfu", "canUseCMFUSystem")) return;
			$renderHTML = "";

			$fullUserOptions = unserialize($visitor["3ps_cmfu_options"]);
			foreach ($fullUserOptions as $category => $catArray) {
				foreach ($catArray as $itemName => $itemValue) {
					if (isset(ThreePointStudio_CustomMarkupForUser_Constants::$availableMarkups[$itemName]["enable_prefix"])) { // This item has an enable_ marker, tick it as well
						$fullUserOptions[$category]["enable_" . $itemName] = true;
					}
				}
			}

			$baseViewParams = array(
				"colourList" => ThreePointStudio_CustomMarkupForUser_Constants::$colourList,
				"borderList" => ThreePointStudio_CustomMarkupForUser_Helpers::lazyArrayShift(ThreePointStudio_CustomMarkupForUser_Constants::$borderList),
				"fontList" => ThreePointStudio_CustomMarkupForUser_Helpers::lazyArrayShift(ThreePointStudio_CustomMarkupForUser_Constants::$fontList)
			);
			$template = $template->create("3ps_cmfu_account_cmcontrol", $template->getParams());
			// Render username
			if ($visitor->hasPermission("3ps_cmfu", "canUseCustomUNM")) {
				$template->setParams(array_merge(array(
					"title" => new XenForo_Phrase("user_name"),
					"titleCode" => "username",
					"permissions" => ThreePointStudio_CustomMarkupForUser_Helpers::assembleCustomMarkupPermissionForUser("username"),
					"userOptions" => $fullUserOptions["username"],
					"currentMarkupRender" => XenForo_Template_Helper_Core::callHelper("usernamehtml", array($visitor->toArray(), "", true))
				), $baseViewParams));
				$renderHTML .= $template->render();
			}
			// Render user title
			if ($visitor->hasPermission("3ps_cmfu", "canUseCustomUTM")) {
				$template->setParams(array_merge(array(
					"title" => new XenForo_Phrase("3ps_cmfu_user_title"),
					"titleCode" => "usertitle",
					"permissions" => ThreePointStudio_CustomMarkupForUser_Helpers::assembleCustomMarkupPermissionForUser("usertitle"),
					"userOptions" => $fullUserOptions["usertitle"],
					"currentMarkupRender" => XenForo_Template_Helper_Core::callHelper("usertitle", array($visitor->toArray()))
				), $baseViewParams));
				$renderHTML .= $template->render();
			}
			/* Things */
			$contents .= $renderHTML;
		} elseif ($hookName == "") { // 
		
		}
	}
}