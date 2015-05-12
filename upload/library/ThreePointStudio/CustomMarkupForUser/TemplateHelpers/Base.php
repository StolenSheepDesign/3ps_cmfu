<?php

class ThreePointStudio_CustomMarkupForUser_TemplateHelpers_Base extends XenForo_Template_Helper_Core {
    protected static $_modelCache = array();

    public static function helperUserName(array $user, $class = '', $rich = false) {
        if (!$rich and XenForo_Application::getOptions()->get("3ps_cmfu_overridePlainUsernameHelper")) {
            $rich = true;
        }
        return XenForo_Template_Helper_Core::callHelper('usernamehtml', array($user, '', $rich, array('class' => $class)));
    }

    public static function helperUserNameHtml(array $user, $username = '', $rich = false, array $attributes = array()) {
        if ($username == '') {
            $username = htmlspecialchars($user['username']);
        }

        if (!$rich and XenForo_Application::getOptions()->get("3ps_cmfu_overridePlainUsernameHelper") == 1) {
            $rich = true;
        }

        if ($rich) {
            $username = XenForo_Template_Helper_Core::callHelper('richusername', array($user, $username));
        }

        $href = self::getUserHref($user, $attributes);
        $class = (empty($attributes['class']) ? '' : ' ' . htmlspecialchars($attributes['class']));
        unset($attributes['href'], $attributes['class']);
        $attribs = self::getAttributes($attributes);
        return "<a{$href} class=\"username{$class}\"{$attribs}>{$username}</a>";
    }

    public static function helperRichUserName(array $user, $usernameHtml = '') {
        if (!is_array($user) || (!isset($user['username']) && $usernameHtml === '')) return '';
        if ($usernameHtml === '') {
            $usernameHtml = htmlspecialchars($user['username']);
        }
        $stylingOrder = XenForo_Application::getOptions()->get("3ps_cmfu_markupStylingOrder");
        // sanity checks
        if ($stylingOrder["preset"] == 0 && $stylingOrder["user"] == "0") {
            return $usernameHtml;
        }

        if (empty($user['user_id'])) {
            $user['display_style_group_id'] = XenForo_Model_User::$defaultGuestGroupId;
        } elseif ($user['display_style_group_id'] == null) {
            $user['display_style_group_id'] = XenForo_Application::getDb()->fetchOne("SELECT `display_style_group_id` FROM `xf_user` WHERE `user_id` = ?", $user["user_id"]);
        }
        if (!isset($user["3ps_cmfu_options"])) {
            $user["3ps_cmfu_options"] = XenForo_Application::getDb()->fetchOne("SELECT `3ps_cmfu_options` FROM `xf_user` WHERE `user_id`=?", $user["user_id"]);
        }
        if (XenForo_Application::getOptions()->get("3ps_cmfu_useCache") && !isset($user["3ps_cmfu_render_cache"])) {
            $user["3ps_cmfu_render_cache"] = XenForo_Model::create("XenForo_Model_DataRegistry")->get("3ps_cmfu_render_cache_" . $user["user_id"]);
        }

        $extraClasses = array();
        if (isset($user['display_style_group_id']) && isset(XenForo_Template_Helper_Core::$_displayStyles[$user['display_style_group_id']])) {
            $style = XenForo_Template_Helper_Core::$_displayStyles[$user['display_style_group_id']];
            if ($style['username_css'] && $stylingOrder["default"] > 0) {
                $extraClasses[] = 'style' . $user['display_style_group_id'];
            }
        }

        $options = unserialize($user["3ps_cmfu_options"]);
        if (!$options) {
            $options = ThreePointStudio_CustomMarkupForUser_Constants::$defaultOptionsArray;
            XenForo_Model::create("XenForo_Model_User")->insertDefaultCustomMarkup($user["user_id"]);
        }

        $useCache = false;
        $storeResultsInCache = false;
        $dr = self::_getDataRegistryModel();

        if (empty($user["user_id"])) {
            $html = "{inner}";
        } elseif (XenForo_Application::getOptions()->get("3ps_cmfu_useCache")) {
            $renderCache = $dr->get("3ps_cmfu_render_cache_" . $user["user_id"]);
            if (!empty($renderCache) && !empty($renderCache["username"])) {
                $html = $renderCache["username"];
                $useCache = true;
            } else {
                $storeResultsInCache = true;
            }
        }
        $presetsModel = self::getModelFromCache("ThreePointStudio_CustomMarkupForUser_Model_Preset");
        $presetDefs = $presetsModel->getPresetsByIds($options["username"]["presets"]);

        if (!$useCache) {
            if (isset($options["username"]["presets"]) && !empty($options["username"]["presets"])) {
                /* @var $presetsModel ThreePointStudio_CustomMarkupForUser_Model_Preset */
                $presetsModel = self::getModelFromCache("ThreePointStudio_CustomMarkupForUser_Model_Preset");
                $presetDefs = $presetsModel->getSortedPresetsByIds($options["username"]["presets"]);
                // Squash prefix as appropriate
                if ((int)$stylingOrder["preset"] > (int)$stylingOrder["user"]) {
                    // Preset wins
                    foreach ($presetDefs as $preset) {
                        $config = unserialize($preset["config"]);
                        $options["username"] = array_merge_recursive($options["username"], $config["preset"]);
                    }
                } else {
                    // User wins
                    // Squash all prefixes
                    $finalPresetsDefs = array();
                    foreach ($presetDefs as $preset) {
                        $config = unserialize($preset["config"]);
                        $finalPresetsDefs = array_merge_recursive($finalPresetsDefs, $config);
                    }
                    // Apply it onto options
                    $options["username"] = array_merge_recursive($finalPresetsDefs["preset"], $options["username"]);
                }
            }
            $html = ThreePointStudio_CustomMarkupForUser_Helpers::assembleCustomMarkup($options, "username");
        }
        if ($storeResultsInCache) {
            $dr->set("3ps_cmfu_render_cache_" . $user["user_id"], $html);
        }

        if ($stylingPref == 0) { // User Group markup first
            if (!is_null($extraClasses)) {
                $html = str_replace("{inner}", '<span class="' . implode(" ", $extraClasses) . '">{inner}</span>', $html);
            }
        } elseif ($stylingPref == 1) { // Custom markup first
            if (!is_null($extraClasses)) {
                $html = '<span class="' . implode(" ", $extraClasses) . '">' . $html . '</span>';
            }
        }
        return str_replace("{inner}", $usernameHtml, $html);
    }

    public static function helperUserTitle($user, $allowCustomTitle = true, $withBanner = false) {
        $result = parent::helperUserTitle($user, $allowCustomTitle, $withBanner);
        $options = unserialize($user["3ps_cmfu_options"]);
        if (!$options) {
            $options = ThreePointStudio_CustomMarkupForUser_Constants::$defaultOptionsArray;
            XenForo_Model::create("XenForo_Model_User")->insertDefaultCustomMarkup($user["user_id"]);
        }
        if (XenForo_Application::getOptions()->get("3ps_cmfu_useCache")) {
            $dr = self::_getDataRegistryModel();
            $renderCache = $dr->get("3ps_cmfu_render_cache_" . $user["user_id"]);
            if (!empty($renderCache) && !empty($renderCache["usertitle"])) {
                $html = $renderCache["usertitle"];
            } else {
                $html = ThreePointStudio_CustomMarkupForUser_Helpers::assembleCustomMarkup($options, "usertitle");
                $dr->set("3ps_cmfu_render_cache_" . $user["user_id"], $html);
            }
        } else {
            $html = ThreePointStudio_CustomMarkupForUser_Helpers::assembleCustomMarkup($options, "usertitle");
        }
        $finalHTML = str_replace("{inner}", $result, $html);
        return $finalHTML;
    }

    public static function helperPlainUserTitle($user, $allowCustomTitle = true, $withBanner = false) {
        return parent::helperUserTitle($user, $allowCustomTitle, $withBanner);
    }

    protected static function _getDataRegistryModel() {
        /* @return XenForo_Model_DataRegistry */
        return self::getModelFromCache("XenForo_Model_DataRegistry");
    }

    /**
     * Gets the specified model object from the cache. If it does not exist,
     * it will be instantiated.
     *
     * @param string $class Name of the class to load
     *
     * @return XenForo_Model
     */
    protected static function getModelFromCache($class) {
        if (!isset(self::$_modelCache[$class])) {
            self::$_modelCache[$class] = XenForo_Model::create($class);
        }
        return self::$_modelCache[$class];
    }
}