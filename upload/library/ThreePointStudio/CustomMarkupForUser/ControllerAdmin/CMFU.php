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
        /* @var ThreePointStudio_CustomMarkupForUser_Model_Preset $presetModel */
        $presetModel = $this->_getPresetModel();
        $preset = $this->_getPresetOrError();
        return $this->_getPresetAddEditResponse("3ps_cmfu_cmcontrol_preset", $preset);
    }

    public function actionPresetsAdd() {
        return $this->_getPresetAddEditResponse("3ps_cmfu_cmcontrol_preset");
    }

    public function actionPresetsSave() {
        $this->_assertPostOnly();
        $preset_id = $this->_input->filterSingle("preset_id", XenForo_Input::UINT);
        $dwInput = $this->_input->filter(array(
            "title" => XenForo_Input::STRING,
            "active" => XenForo_Input::BOOLEAN,
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

    protected function _getPresetAddEditResponse($templateName, array $preset = null, $user = null) {
        if (!$user) {
            $user = XenForo_Visitor::getInstance()->toArray();
        }
        if (empty($user["custom_title"])) {
            // No user title
            $user["custom_title"] = "(No Custom Title Set)";
        }
        if (!$preset["config"]) {
            $options = serialize(ThreePointStudio_CustomMarkupForUser_Constants::$defaultOptionsArray);
        } else {
            $options = $preset["config"];
        }
        $options = ThreePointStudio_CustomMarkupForUser_Helpers::prepareSerializedOptionsForView($options);
        $renderHTML = "";

        $settingsTemplate = new XenForo_Template_Admin($templateName, array_merge(array(
            "title" => new XenForo_Phrase("user_name"),
            "titleCode" => "username",
            "userOptions" => $options["username"],
            "currentMarkupRender" => XenForo_Template_Helper_Core::callHelper("usernamehtml", array($user, "Username", true))
        ), $this->_getBaseViewParams()));
        $settingsTemplate->setLanguageId(1);
        $renderHTML .= $settingsTemplate->render();

        // Render user title
        $settingsTemplate = new XenForo_Template_Admin($templateName, array_merge(array(
            "title" => new XenForo_Phrase("3ps_cmfu_user_title"),
            "titleCode" => "usertitle",
            "userOptions" => $options["usertitle"],
            "currentMarkupRender" => XenForo_Template_Helper_Core::callHelper("usertitle", array($user, true))
        ), $this->_getBaseViewParams()));
        $settingsTemplate->setLanguageId(1);
        $renderHTML .= $settingsTemplate->render();

        $viewParams = array(
            "optionsHTML" => $renderHTML,
            "userGroups" => $this->_getUserGroupModel()->getAllUserGroupTitles(),
            "preset" => $preset
        );
        return $this->responseView('ThreePointStudio_CustomMarkupForUser_ViewAdmin_Presets', '3ps_cmfu_presets_edit', $viewParams);
    }

    protected function _getPresetModel() {
        /* @var ThreePointStudio_CustomMarkupForUser_Model_Preset $presetModel */
        $presetModel = $this->getModelFromCache('ThreePointStudio_CustomMarkupForUser_Model_Preset');
        return $presetModel;
    }

    /**
     * Gets the specified record or errors.
     *
     * @param string $id
     *
     * @return array
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

    protected function _getBaseViewParams() {
        return array(
            'jQuerySource' => XenForo_Dependencies_Abstract::getJquerySource(),
            'xenOptions' => XenForo_Application::get('options')->getOptions(),
            '_styleModifiedDate' => XenForo_Application::get('adminStyleModifiedDate'),
            "borderList" => ThreePointStudio_CustomMarkupForUser_Helpers::lazyArrayShift(ThreePointStudio_CustomMarkupForUser_Constants::$borderList),
            "fontList" => ThreePointStudio_CustomMarkupForUser_Helpers::lazyArrayShift(ThreePointStudio_CustomMarkupForUser_Constants::$fontList)
        );
    }

    /**
     * @return XenForo_Model_UserGroup
     */
    protected function _getUserGroupModel()
    {
        return $this->getModelFromCache('XenForo_Model_UserGroup');
    }

}