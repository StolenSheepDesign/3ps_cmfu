<?php
/*
* Custom Username Markup For User v1.0.0 written by tyteen4a03@3.studIo.
* This software is licensed under the BSD 2-Clause modified License.
* See the LICENSE file within the package for details.
*/

class ThreePointStudio_CustomMarkupForUser_DataWriter_User extends XFCP_ThreePointStudio_CustomMarkupForUser_DataWriter_User {
    protected function _getFields() {
        $fields = parent::_getFields();
        $fields['xf_user']['3ps_cmfu_options'] = array('type' => self::TYPE_SERIALIZED, 'default' => 'a:2:{s:8:"username";a:0:{}s:9:"usertitle";a:0:{}}');
        $fields['xf_user']['3ps_cmfu_render_cache'] = array('type' => self::TYPE_SERIALIZED, 'default' => 'a:2:{s:8:"username";a:0:{}s:9:"usertitle";a:0:{}}');
        return $fields;
    }

    public function rebuildCustomMarkupCache($category=null) {
        $this->_getUserModel()->rebuildCustomMarkupCache($this->get("user_id"), $category);
    }
}