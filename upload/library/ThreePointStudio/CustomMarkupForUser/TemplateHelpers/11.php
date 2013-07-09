<?php
/*
* Custom Username Markup For User v1.0.0 written by tyteen4a03@3.studIo.
* This software is licensed under the BSD 2-Clause modified License.
* See the LICENSE file within the package for details.
*/

class ThreePointStudio_CustomMarkupForUser_TemplateHelpers_11 extends XenForo_Template_Helper_Core {
	public static function helperUserName(array $user, $class = '', $rich = false) {
		if (!$rich and XenForo_Application::getOptions()->get("3ps_cmfu_overridePlainUsernameHelper")) {
			$rich = true;
		}
		return XenForo_Template_Helper_Core::callHelper('usernamehtml', array($user, '', $rich, array('class' => $class)));
	}

	public static function helperUserNameHtml(array $user, $username = '', $rich = false, array $attributes = array()) {
		if ($username == '') {
			$username = htmlspecialchars($user['username']);
		}

		if (!$rich and XenForo_Application::getOptions()->get("3ps_cmfu_overridePlainUsernameHelper") == 1) {
			$rich = true;
		}
		if (!isset($user["3ps_cmfu_options"])) {
			$user["3ps_cmfu_options"] = XenForo_Application::getDb()->fetchOne("SELECT `3ps_cmfu_options` FROM `xf_user` WHERE `user_id`=?", $user["user_id"]);
		}
		if ($rich) {
			$username = XenForo_Template_Helper_Core::callHelper('richusername', array($user, $username));
		}

		$href = self::getUserHref($user, $attributes);
		$class = (empty($attributes['class']) ? '' : ' ' . htmlspecialchars($attributes['class']));
		unset($attributes['href'], $attributes['class']);
		$attribs = self::getAttributes($attributes);
		return "<a{$href} class=\"username{$class}\"{$attribs}>{$username}</a>";
	}

	public static function helperRichUserName(array $user, $usernameHtml = '') {
		$stylingPref = intval(XenForo_Application::getOptions()->get("3ps_cmfu_usernameStylingPreference"));
		if (!is_array($user) || (!isset($user['username']) && $usernameHtml === '')) return '';

		if ($usernameHtml === '') {
			$usernameHtml = htmlspecialchars($user['username']);
		}

		if (empty($user['user_id'])) {
			$user['display_style_group_id'] = XenForo_Model_User::$defaultGuestGroupId;
		} elseif ($user['display_style_group_id'] == null) {
			$user['display_style_group_id'] = XenForo_Application::getDb()->fetchOne("SELECT `display_style_group_id` FROM `xf_user` WHERE `user_id` = ?", $user["user_id"]);
		}
		$extraClasses = null;
		if (isset($user['display_style_group_id']) && isset(XenForo_Template_Helper_Core::$_displayStyles[$user['display_style_group_id']])) {
			$style = XenForo_Template_Helper_Core::$_displayStyles[$user['display_style_group_id']];
			$extraClasses = ($style['username_css'] and $stylingPref != 2) ? 'style' . $user['display_style_group_id'] : null;
		}

		$html = ThreePointStudio_CustomMarkupForUser_Helpers::assembleCustomMarkup($user, "username");

		if ($stylingPref == 0) { // User Group markup first
			$finalHtml = str_replace("{inner}", '<span class="' . $extraClasses . '">{inner}</span>', $html);
		} elseif ($stylingPref == 1) { // Custom markup first
			$finalHtml = '<span class="' . $extraClasses . '">' . $html . '</span>';
		}
		$finalHtml = str_replace("{inner}", $usernameHtml, $finalHtml);
		return $finalHtml;
	}

	public static function helperUserTitle($user, $allowCustomTitle = true) {
		$result = parent::helperUserTitle($user, $allowCustomTitle);
		$html = ThreePointStudio_CustomMarkupForUser_Helpers::assembleCustomMarkup($user, "usertitle");
		$finalHTML = str_replace("{inner}", $result, $html);
		return $finalHTML;
	}
}