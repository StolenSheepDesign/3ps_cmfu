<?php
/*
* Custom Username Markup For User v1.0.0 written by tyteen4a03@3.studIo.
* This software is licensed under the BSD 2-Clause modified License.
* See the LICENSE file within the package for details.
*/

class ThreePointStudio_CustomMarkupForUser_TemplateHelpers_12 extends ThreePointStudio_CustomMarkupForUser_TemplateHelpers_Base {
    public static function helperUserTitle($user, $allowCustomTitle = true, $withBanner = false) {
        $result = parent::helperUserTitle($user, $allowCustomTitle);
        if (XenForo_Application::getOptions()->get("3ps_cmfu_useCache")) {
            $renderCache = unserialize($user["3ps_cmfu_render_cache"]);
            $html = $renderCache["usertitle"];
        } else {
            $html = ThreePointStudio_CustomMarkupForUser_Helpers::assembleCustomMarkup(unserialize($user["3ps_cmfu_options"]), "usertitle");
        }
        $finalHTML = str_replace("{inner}", $result, $html);
        return $finalHTML;
    }
}