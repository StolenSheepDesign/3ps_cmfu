<?php
/*
* Custom Username Markup For User v1.0.0 written by tyteen4a03@3.studIo.
* This software is licensed under the BSD 2-Clause modified License.
* See the LICENSE file within the package for details.
*/

class ThreePointStudio_CustomMarkupForUser_Model_Preset extends XenForo_Model {
    public function getPresetById($id) {
        return $this->_getDb()->fetchRow('SELECT * FROM xf_3ps_cmfu_presets WHERE preset_id = ?', $id);
    }

    public function getAllPresets() {
        return $this->fetchAllKeyed('SELECT * FROM xf_3ps_cmfu_presets ORDER BY preset_id', 'preset_id');
    }

    public function addNewPreset() {
        $dw = XenForo_DataWriter::create("ThreePointStudio_CustomMarkupForUser_DataWriter_Preset");
        $dw->save();
        return $dw->get("preset_id");
    }

    public function updatePresetById($id) {
        $dw = XenForo_DataWriter::create("ThreePointStudio_CustomMarkupForUser_DataWriter_Preset");
        $dw->setExistingData($id);
    }

    /**
     * Gets presets that are enabled for x group.
     */
    public function getPresetsByGroup($group) {
        $allPresets = $this->getAllPresets();
        $toReturn = array();
        foreach ($allPresets as $preset) {
            $enable_for = unserialize($preset["enable_for"]);
            if ($enable_for[$group]) {
                $toReturn[(int) $preset["preset_id"]] = $preset;
            }
        }
        return $toReturn;
    }
}