<?php
/*
* Custom Username Markup For User v1.0.0 written by tyteen4a03@3.studIo.
* This software is licensed under the BSD 2-Clause modified License.
* See the LICENSE file within the package for details.
*/

class ThreePointStudio_CustomMarkupForUser_DataWriter_Preset extends XenForo_DataWriter {
    var $fields = array(
        "xf_3ps_cmfu_presets" => array(
            "preset_id" => array(
                'type' => self::TYPE_UINT,
                'autoIncrement' => true
            ),
            "title" => array(
                'type' => self::TYPE_STRING,
                'default' => ""
            ),
            "enable_for" => array(
                'type' => self::TYPE_SERIALIZED,
                'default' => ''
            ),
            "config" => array(
                'type' => self::TYPE_SERIALIZED,
                'default' => ''
            ),
            'user_groups' => array(
                'type' => self::TYPE_SERIALIZED,
                'default' => ''
            )
        )
    );

    protected function _getFields() {
        return $this->fields;
    }

    protected function _getExistingData($data) {
        if (!$id = $this->_getExistingPrimaryKey($data, 'preset_id')) {
            return false;
        }
        return array('xf_3ps_cmfu_presets' => $this->_getPresetModel()->getPresetById($id));
    }

    protected function _getUpdateCondition($tableName) {
        return 'preset_id = ' . $this->_db->quote($this->getExisting('preset_id'));
    }

    /* @return ThreePointStudio_CustomMarkupForUser_Model_Preset */
    protected function _getPresetModel() {
        return $this->getModelFromCache('ThreePointStudio_CustomMarkupForUser_Model_Preset');
    }
}