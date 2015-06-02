<?php
/*
* Custom Markup For User v1.1.0 written by tyteen4a03@3.studIo.
* This software is licensed under the BSD 2-Clause modified License.
* See the LICENSE file within the package for details.
*/

class ThreePointStudio_CustomMarkupForUser_ControllerPublic_Account extends XFCP_ThreePointStudio_CustomMarkupForUser_ControllerPublic_Account {

    public function actionPreferencesSave() {
        $response = parent::actionPreferencesSave();
        /* @var $userModel XenForo_Model_User */
        $userModel = $this->getModelFromCache("XenForo_Model_User");
        $user = $userModel->getUserById(XenForo_Visitor::getUserId());
        $user["user_groups"] = array_merge(array((int) $user["user_group_id"]), explode(",", $user["secondary_group_ids"]));
        $options = $this->_input->filterSingle("3ps_cmfu_options", XenForo_Input::ARRAY_SIMPLE);
        /* @var $presetsModel ThreePointStudio_CustomMarkupForUser_Model_Preset */
        $presetsModel = $this->getModelFromCache("ThreePointStudio_CustomMarkupForUser_Model_Preset");
        $userPermissions = array(
            "username" => ThreePointStudio_CustomMarkupForUser_Helpers::assembleCustomMarkupPermissionForUser("username"),
            "usertitle" => ThreePointStudio_CustomMarkupForUser_Helpers::assembleCustomMarkupPermissionForUser("usertitle")
        );

        if (empty($options)) {
            // Nothing in here, populate it with nothingness
            $options = ThreePointStudio_CustomMarkupForUser_Constants::$defaultOptionsArray;
        }
        // Pre-check cleanup
        foreach ($options as $category => $catArray) {
            foreach ($catArray as $itemName => $itemValue) {
                if (ThreePointStudio_CustomMarkupForUser_Helpers::startsWith($itemName, "enable_")) {
                    unset($options[$category][$itemName]); // Ignore any placeholders
                    continue;
                }
                if ($itemName == "presets") {
                    $options[$category][$itemName] = XenForo_Input::rawFilter($itemValue, XenForo_Input::ARRAY_SIMPLE);
                } else {
                    $options[$category][$itemName] = XenForo_Input::rawFilter($itemValue, ThreePointStudio_CustomMarkupForUser_Constants::$availableMarkups[$itemName]["type"]);
                }
            }
        }

        foreach ($options as $category => $catArray) {
            foreach ($catArray as $itemName => $itemValue) {
                if ($itemName == "presets") {
                    if (!is_array($itemValue)) {
                        $options[$category]["presets"] = array();
                    }
                    foreach ($itemValue as $index => $presetId) {
                        // Can we do this?
                        $thePreset = $presetsModel->getPresetById($presetId);
                        $user_groups = unserialize($thePreset["user_groups"]);
                        $intersection = array_intersect($user_groups, $user["user_groups"]);
                        if (empty($intersection)) {
                            unset($options[$category]["presets"][$index]); // Validation failed
                            continue;
                        }
                    }
                } else {
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
        }
        $dw = XenForo_DataWriter::create('XenForo_DataWriter_User');
        $dw->setExistingData(XenForo_Visitor::getUserId());
        $dw->set("3ps_cmfu_options", serialize($options));
        $dw->save();
        if (XenForo_Application::getOptions()->get("3ps_cmfu_useCache")) {
            $dw->rebuildCustomMarkupCache();
        }
        return $response; // No errors from our end, continue execution
    }
}