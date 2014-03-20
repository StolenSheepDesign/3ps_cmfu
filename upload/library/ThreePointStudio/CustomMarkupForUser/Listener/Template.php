<?php
/*
* Custom Username Markup For User v1.0.0 written by tyteen4a03@3.studIo.
* This software is licensed under the BSD 2-Clause modified License.
* See the LICENSE file within the package for details.
*/

class ThreePointStudio_CustomMarkupForUser_Listener_Template {
	public static function templateHook($hookName, &$contents, array $hookParams, XenForo_Template_Abstract $template) {
		$baseViewParams = array(
			"colourList" => ThreePointStudio_CustomMarkupForUser_Constants::$colourList,
			"borderList" => ThreePointStudio_CustomMarkupForUser_Helpers::lazyArrayShift(ThreePointStudio_CustomMarkupForUser_Constants::$borderList),
			"fontList" => ThreePointStudio_CustomMarkupForUser_Helpers::lazyArrayShift(ThreePointStudio_CustomMarkupForUser_Constants::$fontList)
		);
		$renderHTML = "";

		switch ($hookName) {
			case "account_preferences_options":
				$visitor = XenForo_Visitor::getInstance();
				if (!$visitor->hasPermission("3ps_cmfu", "canUseCMFUSystem")) return;
				$fullUserOptions = ThreePointStudio_CustomMarkupForUser_Helpers::prepareSerializedOptionsForView($visitor["3ps_cmfu_options"]);

				$settingsTemplate = $template->create("3ps_cmfu_account_cmcontrol", $template->getParams());
				// Render username
				if ($visitor->hasPermission("3ps_cmfu", "canUseCustomUNM")) {
					$settingsTemplate->setParams(array_merge(array(
						"title" => new XenForo_Phrase("user_name"),
						"titleCode" => "username",
						"permissions" => ThreePointStudio_CustomMarkupForUser_Helpers::assembleCustomMarkupPermissionForUser("username"),
						"userOptions" => $fullUserOptions["username"],
						"currentMarkupRender" => XenForo_Template_Helper_Core::callHelper("usernamehtml", array($visitor->toArray(), "", true))
					), $baseViewParams));
					$renderHTML .= $settingsTemplate->render();
				}
				// Clean up
				$settingsTemplate = $template->create("3ps_cmfu_account_cmcontrol", $template->getParams());
				// Render user title
				if (empty($user["custom_title"])) { // No user title
					$user["custom_title"] = "(No Title Set)";
				}
				if ($visitor->hasPermission("3ps_cmfu", "canUseCustomUTM")) {
					$settingsTemplate->setParams(array_merge(array(
						"title" => new XenForo_Phrase("3ps_cmfu_user_title"),
						"titleCode" => "usertitle",
						"permissions" => ThreePointStudio_CustomMarkupForUser_Helpers::assembleCustomMarkupPermissionForUser("usertitle"),
						"userOptions" => $fullUserOptions["usertitle"],
						"currentMarkupRender" => XenForo_Template_Helper_Core::callHelper("usertitle", array($visitor->toArray()))
					), $baseViewParams));
					$renderHTML .= $settingsTemplate->render();
				}
				/* Things - Account Panel */
				$contents .= $renderHTML;
				break;
			case "admin_user_edit_tabs": // ACP - User Tab
				$user = $template->getParam("user");
				if ($user["user_id"] == 0) break; // New user, don't show this
				$contents .= $template->create("3ps_cmfu_user_edit_tab", $template->getParams())->render();
				break;
			case "admin_user_edit_panes": // ACP - User Tab content
				$user = $template->getParam("user");

				if ($user["user_id"] == 0) break; // New user, don't show this
				$fullUserOptions = ThreePointStudio_CustomMarkupForUser_Helpers::prepareSerializedOptionsForView($user["3ps_cmfu_options"]);

				$settingsTemplate = $template->create("3ps_cmfu_cmcontrol", $template->getParams());
				$settingsTemplate->setParams(array_merge(array(
					"title" => new XenForo_Phrase("user_name"),
					"titleCode" => "username",
					"userOptions" => $fullUserOptions["username"],
					"currentMarkupRender" => XenForo_Template_Helper_Core::callHelper("usernamehtml", array($user, "", true))
				), $baseViewParams));
				$renderHTML .= $settingsTemplate->render();
				// Clean up
				$settingsTemplate = $template->create("3ps_cmfu_cmcontrol", $template->getParams());
				// Render user title
				if (empty($user["custom_title"])) {
					// No user title
					$user["custom_title"] = "(No Custom Title Set)";
				}
				$settingsTemplate->setParams(array_merge(array(
					"title" => new XenForo_Phrase("3ps_cmfu_user_title"),
					"titleCode" => "usertitle",
					"userOptions" => $fullUserOptions["usertitle"],
					"currentMarkupRender" => XenForo_Template_Helper_Core::callHelper("usertitle", array($user, true))
				), $baseViewParams));
				$renderHTML .= $settingsTemplate->render();
				/* Things - AdminCP */
				$contents .= "<li>" . $renderHTML . "</li>";
				break;
		}
	}
}