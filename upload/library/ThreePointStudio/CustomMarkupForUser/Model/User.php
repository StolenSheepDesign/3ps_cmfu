<?php
/*
* Custom Markup For User v1.1.0 written by tyteen4a03@3.studIo.
* This software is licensed under the BSD 2-Clause modified License.
* See the LICENSE file within the package for details.
*/

class ThreePointStudio_CustomMarkupForUser_Model_User extends XFCP_ThreePointStudio_CustomMarkupForUser_Model_User {
    public function rebuildCustomMarkupCache($userId, $category=null) {
        $user = $this->getUserById($userId);
        $options = unserialize($user["3ps_cmfu_options"]);
        /* @var $dr XenForo_Model_DataRegistry */
        $dr = self::create("XenForo_Model_DataRegistry");
        $extraClasses = ThreePointStudio_CustomMarkupForUser_TemplateHelpers::getUserExtraClasses($user);
        if ($category) {
            if (!in_array($category, ThreePointStudio_CustomMarkupForUser_Constants::$categories)) {
                throw new UnexpectedValueException("Unknown category");
            }
            $dr->set("3ps_cmfu_render_cache_" . $userId . "_" . $category, ThreePointStudio_CustomMarkupForUser_Helpers::getCustomMarkupHtml($options, $category, $user, (($category == "username" ? $extraClasses : array()))));
        } else {
            foreach (ThreePointStudio_CustomMarkupForUser_Constants::$categories as $category) {
                $dr->set("3ps_cmfu_render_cache_" . $userId . "_" . $category, ThreePointStudio_CustomMarkupForUser_Helpers::getCustomMarkupHtml($options, $category, $user, (($category == "username" ? $extraClasses : array()))));
            }
        }
    }

    public function insertDefaultCustomMarkup($userId) {
        $db = $this->_getDb();
        $db->update("xf_user",
            array("3ps_cmfu_options" => serialize(ThreePointStudio_CustomMarkupForUser_Constants::$defaultOptionsArray)),
            'user_id = ' . $db->quote($userId)
        );
    }
}