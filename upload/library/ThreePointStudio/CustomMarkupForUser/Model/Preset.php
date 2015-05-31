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

    public function getPresetsByIds(array $ids) {
        return $this->fetchAllKeyed('SELECT * FROM xf_3ps_cmfu_presets WHERE preset_id IN (' . implode(",", $ids) . ') ORDER BY preset_id', 'preset_id');
    }

    /**
     * Gets an array of sorted presets.
     *
     * @param array $ids
     * @return array
     */
    public function getSortedPresetsByIds(array $ids) {
        return $this->sortPresetsByStylingPriority($this->getPresetsByIds($ids));
    }

    public function getAllPresets() {
        return $this->fetchAllKeyed('SELECT * FROM xf_3ps_cmfu_presets ORDER BY preset_id', 'preset_id');
    }

    public function updatePreset($presetId, array $presetInfo)
    {
        $dw = XenForo_DataWriter::create('ThreePointStudio_CustomMarkupForUser_DataWriter_Preset');
        if ($presetId && $presetId > 0) {
            $dw->setExistingData($presetId);
        }

        $dw->bulkSet($presetInfo);
        $dw->save();
        return $dw->get('preset_id');
    }

    /**
     * Gets presets that are enabled for x group.
     *
     * @param string $group The group name.
     * @return array
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

    /**
     * Sorts presets by ascending styling order.
     *
     * @param array $presets An array of presets.
     * @return array
     */
    protected function sortPresetsByStylingPriority(array &$presets) {
        usort($presets, function ($a, $b) {
            return ($a["display_style_priority"] < $b["display_style_priority"] ? -1 : 1);
        });
        return $presets;
    }
}