<?php
/*
* Custom Username Markup For User v1.0.0 written by tyteen4a03@3.studIo.
* This software is licensed under the BSD 2-Clause modified License.
* See the LICENSE file within the package for details.
*/

class ThreePointStudio_CustomMarkupForUser_Helpers extends XenForo_Template_Helper_Core { // Sorry, laziness calls
	public static function startswith($haystack, $needle) {
		return !strncmp($haystack, $needle, strlen($needle));
	}

	public static function lazyArrayShift($array) {
		unset($array[0]);
		return $array;
	}

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

		$html = self::assembleCustomMarkup($user, "username");

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
		$html = self::assembleCustomMarkup($user, "usertitle");
		$finalHTML = str_replace("{inner}", $result, $html);
		return $finalHTML;
	}

	public static function assembleCustomMarkup(array $user, $category) {
		$options = unserialize($user["3ps_cmfu_options"]);
		$finalTags = array();
		$finalizedHTML = "{inner}";
		if (!isset($options[$category])) { // No styling option set
			return $finalizedHTML;
		}
		foreach ($options[$category] as $optionName => $optionValue) {
			foreach (ThreePointStudio_CustomMarkupForUser_Constants::$availableMarkups[$optionName]["format"] as $tag => $tagArray) {
				// Create the tag entry, if not set
				if (!isset($finalTags[$tag])) {
					$finalTags[$tag] = array();
				}
				if (!isset($finalTags[$tag]["attr"])) {
					$finalTags[$tag]["attr"] = array();
				}
				foreach ($tagArray["attr"] as $attr => $attrValue) {
					if (isset($tagArray["variableFeed"])) {
						foreach ($tagArray["variableFeed"] as $item) {
							if (in_array($attr, array("style", "class"))) { // Array
								foreach ($attrValue as $rule => &$ruleValue) {
									$ruleValue = self::replacePlaceholders($item, $ruleValue, $optionValue);
								}
							} else {
								$attrValue = self::replacePlaceholders($item, $attrValue, $optionValue);
							}
						}
					}
					if (!isset($finalTags[$tag]["attr"][$attr])) {
						$finalTags[$tag]["attr"][$attr] = (in_array($attr, array("style", "class"))) ? array() : "";
					}

					if (in_array($attr, array("style", "class"))) { // Array-style attribute value
						foreach ($attrValue as $rule => $ruleValue) {
							if (isset($finalTags[$tag]["attr"][$attr][$rule])) {
								if (isset($tagArray["mergeProperties"])) { // CSS-style value merge, which is just adding a whitespace and the new value - me lazy
									$finalTags[$tag]["attr"][$attr][$rule] .= " " . $ruleValue;
								} else {
									$finalTags[$tag]["attr"][$attr][$rule] = $ruleValue;
								}
							} else {
								$finalTags[$tag]["attr"][$attr][$rule] = $ruleValue;
							}
						}
					} else { // Plain ol' string
						$finalTags[$tag]["attr"][$attr] = $attrValue;
					}
				}
			}
		}
		// Compile the HTML
		foreach ($finalTags as $tag => $tagArray) {
			$attrCompiled = array();
			foreach ($tagArray["attr"] as $attr => $attrValue) {
				if (in_array($attr, array("style", "class"))) {
					$compiledAttrValue = "";
					foreach ($attrValue as $rule => $value) {
						$compiledAttrValue .= $rule . ": " . $value . ";";
					}
				} else {
					$compiledAttrValue = $attrValue;
				}
				$attrCompiled[] = $attr . '="' . $compiledAttrValue . '"';
			}
			$finalizedHTML = str_replace("{inner}", ("<" . $tag . " " . implode($attrCompiled, " ") . ">{inner}</" . $tag . ">"), $finalizedHTML);
		}

		return $finalizedHTML;
	}

	public static function assembleCustomMarkupPermissionForUser($group) {
		$visitor = XenForo_Visitor::getInstance();
		$finalPermissions = array();
		switch ($group) {
			case "username":
				$titleCode = "UN";
				break;
			case "usertitle":
				$titleCode = "UT";
				break;
		}
		foreach (ThreePointStudio_CustomMarkupForUser_Constants::$availableMarkups as $markupName => $markupArray) {
			$finalPermissions[$markupName] = $visitor->hasPermission("3ps_cmfu", sprintf($markupArray["permission"], $titleCode));
			if ($finalPermissions[$markupName] and !isset($finalPermissions["_" . $markupArray["category"]])) {
				$finalPermissions["_" . $markupArray["category"]] = true;
			}
		}
		return $finalPermissions;
	}

	protected static function replacePlaceholders($type, $str, $value) {
		switch ($type) {
			case "_value":
				$str = str_replace("{_value}", $value, $str);
				break;
			case "fontFamily":
				$str = str_replace("{fontFamily}", ThreePointStudio_CustomMarkupForUser_Constants::$fontList[$value]["fullname"], $str);
				break;
			case "borderStyle":
				$str = str_replace("{borderStyle}", ThreePointStudio_CustomMarkupForUser_Constants::$borderList[$value], $str);
				break;
			case "borderDefaults":
				$str = str_replace("{borderDefaults}", implode(" ", ThreePointStudio_CustomMarkupForUser_Constants::$borderDefaults), $str);
				break;
		}
		return $str;
	}

	public static function verifyColour($itemValue) {
		return (preg_match("/^#[a-fA-F0-9]{6}$/", $itemValue) or $itemValue == "");
	}

	public static function verifyBool($itemValue) {
		return in_array($itemValue, array(1, 0));
	}

	public static function verifyBorderList($itemValue) {
		return in_array($itemValue, array_keys(ThreePointStudio_CustomMarkupForUser_Constants::$borderList));
	}

	public static function verifyFontList($itemValue) {
		return in_array($itemValue, array_keys(ThreePointStudio_CustomMarkupForUser_Constants::$fontList));
	}
}