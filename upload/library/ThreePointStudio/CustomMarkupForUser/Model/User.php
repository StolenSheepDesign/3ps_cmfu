<?php
/*
* Custom Username Markup For User v1.0.0 written by tyteen4a03@3.studIo.
* This software is licensed under the BSD 2-Clause modified License.
* See the LICENSE file within the package for details.
*/

class ThreePointStudio_CustomMarkupForUser_Model_User extends XFCP_ThreePointStudio_CustomMarkupForUser_Model_User {
    public function rebuildCustomMarkupCache($userId, $category=null) {
        $user = $this->getUserById($userId);
        $options = unserialize($user["3ps_cmfu_options"]);
        $renderCache = array();
        if ($category) {
            if (!in_array($category, ThreePointStudio_CustomMarkupForUser_Constants::$categories)) {
                throw new UnexpectedValueException("Unknown category");
            }
            $renderCache = unserialize($user["3ps_cmfu_render_cache"]);
            $renderCache[$category] = ThreePointStudio_CustomMarkupForUser_Helpers::assembleCustomMarkup($options, $category);
        } else {
            foreach (ThreePointStudio_CustomMarkupForUser_Constants::$categories as $category) {
                $renderCache[$category] = ThreePointStudio_CustomMarkupForUser_Helpers::assembleCustomMarkup($options, $category);
            }
        }
        $db = $this->_getDb();
        $db->update('xf_user',
            array("3ps_cmfu_render_cache" => serialize($renderCache)),
            'user_id = ' . $db->quote($userId)
        );
    }

    public function insertDefaultCustomMarkup($userId) {
        $db = $this->_getDb();
        $db->update("xf_user",
            array("3ps_cmfu_options" => serialize(ThreePointStudio_CustomMarkupForUser_Constants::$defaultOptionsArray)),
            'user_id = ' . $db->quote($userId)
        );
    }
}