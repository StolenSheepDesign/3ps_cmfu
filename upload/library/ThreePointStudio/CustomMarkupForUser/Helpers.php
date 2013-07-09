<?php
/*
* Custom Username Markup For User v1.0.0 written by tyteen4a03@3.studIo.
* This software is licensed under the BSD 2-Clause modified License.
* See the LICENSE file within the package for details.
*/

class ThreePointStudio_CustomMarkupForUser_Helpers {
	public static function startswith($haystack, $needle) {
		return !strncmp($haystack, $needle, strlen($needle));
	}

	public static function lazyArrayShift($array) {
		unset($array[0]);
		return $array;
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

	public static function prepareSerializedOptionsForView($options) {
		$fullUserOptions = unserialize($options);
		if (!$fullUserOptions) {
			$fullUserOptions = ThreePointStudio_CustomMarkupForUser_Constants::$defaultOptionsArray;
		}
		foreach ($fullUserOptions as $category => $catArray) {
			foreach ($catArray as $itemName => $itemValue) {
				if (isset(ThreePointStudio_CustomMarkupForUser_Constants::$availableMarkups[$itemName]["enable_prefix"])) { // This item has an enable_ marker, tick it as well
					$fullUserOptions[$category]["enable_" . $itemName] = true;
				}
			}
		}
		return $fullUserOptions;
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

	public static function determineVersion() {
		$versionStrSplit = str_split(XenForo_Application::$versionId);
		return strval($versionStrSplit[0] . $versionStrSplit[2]);
	}
}