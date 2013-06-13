<?php
/*
* Custom Username Markup For User v1.0.0 written by tyteen4a03@3.studIo.
* This software is licensed under the BSD 2-Clause modified License.
* See the LICENSE file within the package for details.
*/

class ThreePointStudio_CustomMarkupForUser_ControllerPublic_Account extends XFCP_ThreePointStudio_CustomMarkupForUser_ControllerPublic_Account {

	public function actionPreferencesSave() {
		$response = parent::actionPreferencesSave();
		$options = $this->_input->filterSingle("3ps_cmfu_options", XenForo_Input::ARRAY_SIMPLE);
		$userPermissions = array(
			"username" => ThreePointStudio_CustomMarkupForUser_Helpers::assembleCustomMarkupPermissionForUser("username"),
			"usertitle" => ThreePointStudio_CustomMarkupForUser_Helpers::assembleCustomMarkupPermissionForUser("usertitle")
		);
		// For I am lazy
		$finalData = array();

		// Pre-check cleanup
		foreach ($options as $category => $catArray) {
			foreach ($catArray as $itemName => $itemValue) {
				if (ThreePointStudio_CustomMarkupForUser_Helpers::startswith($itemName, "enable_")) {
					unset($options[$category][$itemName]); // Ignore any placeholders
					continue;
				}
				$options[$category][$itemName] = XenForo_Input::rawFilter($itemValue, ThreePointStudio_CustomMarkupForUser_Constants::$availableMarkups[$itemName]["type"]);
			}
		}

		foreach ($options as $category => $catArray) {
			foreach ($catArray as $itemName => $itemValue) {
				$itemArray = ThreePointStudio_CustomMarkupForUser_Constants::$availableMarkups[$itemName];
				// Can we do this?
				if (!$userPermissions[$category][$itemName]) {
					unset($options[$category][$itemName]); // Validation failed
					continue;
				}
				// Yes we can! Check if we have dependencies
				if (isset($itemArray["requires"])) {
					foreach ($itemArray["requires"] as $requirement) {
						if ($catArray[$requirement[0]] !== $requirement[1]) {
							unset($options[$category][$itemName]); // Dependency not match, skipping
							continue;
						}
					}
				}
				if (!call_user_func($itemArray["verify"]["func"], $itemValue)) {
					return $this->responseError(new XenForo_Phrase($itemArray["verify"]["error"]));  // Validation failed, ragequit
				}
			}
		}

		$dw = XenForo_DataWriter::create('XenForo_DataWriter_User');
		$dw->setExistingData(XenForo_Visitor::getUserId());
		$dw->set("3ps_cmfu_options", serialize($options));
		$dw->save();
		return $response; // No errors from our end, continue execution
	}
}