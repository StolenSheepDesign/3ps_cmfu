<?php
/*
* Custom Username Markup For User v1.0.0 written by tyteen4a03@3.studIo.
* This software is licensed under the BSD 2-Clause modified License.
* See the LICENSE file within the package for details.
*/

class ThreePointStudio_CustomMarkupForUser_ControllerAdmin_CMFU extends XenForo_ControllerAdmin_Abstract  {
    public function actionPresets() {
        /* @var ThreePointStudio_CustomMarkupForUser_Model_Preset $presetModel */
        $presetModel = $this->_getPresetModel();
        $presets = $presetModel->getAllPresets();
        $viewParams = array(
            "presets" => $presets
        );
        return $this->responseView('ThreePointStudio_CustomMarkupForUser_ViewAdmin_Presets', '3ps_cmfu_presets_list', $viewParams);
    }

    public function actionPresetsEdit() {
        $preset = $this->_getPresetOrError();
        return $this->_getPresetAddEditResponse($preset);
    }

    public function actionPresetsAdd() {
        return $this->_getPresetAddEditResponse();
    }

    public function actionPresetsSave() {
        $this->_assertPostOnly();
        $preset_id = $this->_input->filterSingle("preset_id", XenForo_Input::UINT);
        $dwInput = $this->_input->filter(array(
            "title" => XenForo_Input::STRING,
            "enable_for" => array(XenForo_Input::UINT, 'array' => true),
            "user_groups" => array(XenForo_Input::UINT, 'array' => true)
        ));

        $options = $this->_input->filterSingle("3ps_cmfu_options", XenForo_Input::ARRAY_SIMPLE);

        foreach ($options as $category => $catArray) {
            foreach ($catArray as $itemName => $itemValue) {
                if (ThreePointStudio_CustomMarkupForUser_Helpers::startsWith($itemName, "enable_")) {
                    unset($options[$category][$itemName]); // Ignore any placeholders
                    continue;
                }
                $options[$category][$itemName] = XenForo_Input::rawFilter($itemValue, ThreePointStudio_CustomMarkupForUser_Constants::$availableMarkups[$itemName]["type"]);
            }
        }

        foreach ($options as $category => $catArray) {
            foreach ($catArray as $itemName => $itemValue) {
                $itemArray = ThreePointStudio_CustomMarkupForUser_Constants::$availableMarkups[$itemName];
                // Check if we have dependencies
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

        $dwInput["config"] = serialize($options);
        $dwInput["user_groups"] = serialize($dwInput["user_groups"]);
        $dwInput["enable_for"] = serialize($dwInput["enable_for"]);

        $dw = XenForo_DataWriter::create('ThreePointStudio_CustomMarkupForUser_DataWriter_Preset');
        if ($preset_id) {
            $dw->setExistingData($preset_id);
        }
        $dw->bulkSet($dwInput);
        $dw->save();

        return $this->responseRedirect(
            XenForo_ControllerResponse_Redirect::SUCCESS,
            XenForo_Link::buildAdminLink('3ps-cmfu/presets') . $this->getLastHash($dw->get('preset_id'))
        );
    }

    public function actionPresetsDelete() {
        if ($this->isConfirmedPost()) {
            return $this->_deleteData(
                'ThreePointStudio_CustomMarkupForUser_DataWriter_Preset', 'preset_id',
                XenForo_Link::buildAdminLink('3ps-cmfu/presets')
            );
        } else { // show confirmation dialog
            $presetID = $this->_input->filterSingle('preset_id', XenForo_Input::UINT);

            $viewParams = array(
                'preset' => $this->_getPresetOrError($presetID)
            );
            return $this->responseView('ThreePointStudio_CustomMarkupForUser_ViewAdmin_Preset_Delete', '3ps_cmfu_presets_delete', $viewParams);
        }
    }

    protected function _getPresetAddEditResponse(array $preset = null) {
        $user = XenForo_Visitor::getInstance()->toArray();
        if ($preset == null) {
            $preset = array(
                "config" => array("preset" => array()),
                "enable_for" => ThreePointStudio_CustomMarkupForUser_Constants::$defaultOptionsArray,
                "user_groups" => array(),
            );
        } else {
            $preset["enable_for"] = unserialize($preset["enable_for"]);
            $preset["user_groups"] = unserialize($preset["user_groups"]);
        }
        $options = $preset["config"];
        $viewOptions = ThreePointStudio_CustomMarkupForUser_Helpers::prepareOptionsForView($options);
        $html = str_replace("{inner}", $user["username"], ThreePointStudio_CustomMarkupForUser_Helpers::assembleCustomMarkup(unserialize($options), "preset"));

        $userGroups = array();
        foreach ($this->_getUserGroupModel()->getAllUserGroupTitles() AS $userGroupId => $title) {
            $userGroups[] = array(
                'label' => $title,
                'value' => $userGroupId,
                'selected' => in_array($userGroupId, $preset["user_groups"])
            );
        }
        $viewParams = array_merge(array(
            "userGroups" => $userGroups,
            "preset" => $preset,
            "title" => new XenForo_Phrase("3ps_cmfu_preset"),
            "titleCode" => "preset",
            "userOptions" => $viewOptions["preset"],
            "currentMarkupRender" => $html
        ), ThreePointStudio_CustomMarkupForUser_Helpers::getBaseViewParams());
        return $this->responseView('ThreePointStudio_CustomMarkupForUser_ViewAdmin_Presets', '3ps_cmfu_presets_edit', $viewParams);
    }

    /**
     * Gets the specified record or errors.
     *
     * @param string $id
     * @return array $preset
     * @throws XenForo_ControllerResponse_Exception
     */
    protected function _getPresetOrError($id = null) {
        if ($id === null) {
            $id = $this->_input->filterSingle('preset_id', XenForo_Input::UINT);
        }

        $info = $this->_getPresetModel()->getPresetById($id);
        if (!$info) {
            throw $this->responseException($this->responseError(new XenForo_Phrase('3ps_cmfu_requested_preset_not_found'), 404));
        }

        return $info;
    }

    /**
     * @return XenForo_Model_UserGroup
     */
    protected function _getUserGroupModel() {
        return $this->getModelFromCache('XenForo_Model_UserGroup');
    }

    /**
     * @return ThreePointStudio_CustomMarkupForUser_Model_Preset
     */
    protected function _getPresetModel() {
        return $this->getModelFromCache('ThreePointStudio_CustomMarkupForUser_Model_Preset');
    }
}