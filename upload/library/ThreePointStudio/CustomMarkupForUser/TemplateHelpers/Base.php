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

        $stylingOrder = array_map('intval', XenForo_Application::getOptions()->get("3ps_cmfu_markupStylingOrder"));
        // sanity checks
        if ($stylingOrder["preset"] == 0 && $stylingOrder["user"] == 0) {
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

        $options = unserialize($user["3ps_cmfu_options"]);
        if (!$options) {
            $options = ThreePointStudio_CustomMarkupForUser_Constants::$defaultOptionsArray;
            XenForo_Model::create("XenForo_Model_User")->insertDefaultCustomMarkup($user["user_id"]);
        }

        $extraClasses = self::getUserExtraClasses($user);

        if (empty($user["user_id"])) {
            $html = "{inner}";
        } else {
            $html = ThreePointStudio_CustomMarkupForUser_Helpers::getCustomMarkupHtml($options, "username", $user, $extraClasses);
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

        if (empty($user["user_id"]) || empty($result)) {
            $html = "{inner}";
        } else {
            $html = ThreePointStudio_CustomMarkupForUser_Helpers::getCustomMarkupHtml($options, "usertitle", $user);
        }
        $finalHTML = str_replace("{inner}", $result, $html);
        return $finalHTML;
    }

    public static function helperPlainUserTitle($user, $allowCustomTitle = true, $withBanner = false) {
        return parent::helperUserTitle($user, $allowCustomTitle, $withBanner);
    }

    public static function getUserExtraClasses($user) {
        $extraClasses = array();
        $stylingOrder = array_map('intval', XenForo_Application::getOptions()->get("3ps_cmfu_markupStylingOrder"));
        if ($stylingOrder["default"] > 0 && isset($user['display_style_group_id']) && isset(XenForo_Template_Helper_Core::$_displayStyles[$user['display_style_group_id']])) {
            $style = XenForo_Template_Helper_Core::$_displayStyles[$user['display_style_group_id']];
            if ($style['username_css']) {
                $extraClasses[] = 'style' . $user['display_style_group_id'];
            }
        }
        return $extraClasses;
    }

    protected static function _getPresetModel() {
        /* @return ThreePointStudio_CustomMarkupForUser_Model_Preset */
        return self::getModelFromCache("ThreePointStudio_CustomMarkupForUser_Model_Preset");
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