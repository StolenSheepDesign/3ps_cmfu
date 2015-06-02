<?php
/*
* Custom Username Markup For User v1.0.0 written by tyteen4a03@3.studIo.
* This software is licensed under the BSD 2-Clause modified License.
* See the LICENSE file within the package for details.
*/

class ThreePointStudio_CustomMarkupForUser_Listener_Template {
    public static function accountPreferencesOptions($hookName, &$contents, array $hookParams, XenForo_Template_Abstract $template) {
        $visitor = XenForo_Visitor::getInstance();
        if (!$visitor->hasPermission("3ps_cmfu", "canUseCMFUSystem")) return;
        $fullUserOptions = ThreePointStudio_CustomMarkupForUser_Helpers::prepareOptionsForView($visitor["3ps_cmfu_options"]);
        /* @var $presetModel ThreePointStudio_CustomMarkupForUser_Model_Preset */
        $presetModel = XenForo_Model::create("ThreePointStudio_CustomMarkupForUser_Model_Preset");

        $renderHTML = "";
        $settingsTemplate = $template->create("3ps_cmfu_account_cmcontrol", $template->getParams());
        // Render username
        if ($visitor->hasPermission("3ps_cmfu", "canUseCustomUNM")) {
            $permissions = ThreePointStudio_CustomMarkupForUser_Helpers::assembleCustomMarkupPermissionForUser("username");
            $presets = array();
            if (!is_array($fullUserOptions["username"]["presets"])) {
                $fullUserOptions["username"]["presets"] = array();
            }
            foreach ($presetModel->getPresetsByGroup("username") as $presetId => $preset) {
                $enable_for = unserialize($preset["enable_for"]);
                if ($enable_for) {
                    $presets[$presetId] = array(
                        "label" => $preset["title"],
                        "value" => $presetId,
                        "selected" => in_array($presetId, $fullUserOptions["username"]["presets"])
                    );
                }
            }
            $presetsTemplate = $template->create("3ps_cmfu_presets_select", $template->getParams());
            $presetsTemplate->setParams(array(
                "title" => new XenForo_Phrase("user_name"),
                "titleCode" => "username",
                "presets" => $presets,
                "permissions" => $permissions
            ));
            $presetHTML = $presetsTemplate->render();
            $settingsTemplate->setParams(array_merge(array(
                "title" => new XenForo_Phrase("user_name"),
                "titleCode" => "username",
                "permissions" => $permissions,
                "userOptions" => $fullUserOptions["username"],
                "currentMarkupRender" => XenForo_Template_Helper_Core::callHelper("usernamehtml", array($visitor->toArray(), "", true)),
                "presetHTML" => $presetHTML
            ), ThreePointStudio_CustomMarkupForUser_Helpers::getBaseViewParams()));
            $renderHTML .= $settingsTemplate->render();
        }
        // Clean up
        $settingsTemplate = $template->create("3ps_cmfu_account_cmcontrol", $template->getParams());
        // Render user title
        if (empty($user["custom_title"])) { // No user title
            $user["custom_title"] = "(No Title Set)";
        }
        if ($visitor->hasPermission("3ps_cmfu", "canUseCustomUTM")) {
            $permissions = ThreePointStudio_CustomMarkupForUser_Helpers::assembleCustomMarkupPermissionForUser("usertitle");
            $presets = array();
            if (!is_array($fullUserOptions["usertitle"]["presets"])) {
                $fullUserOptions["usertitle"]["presets"] = array();
            }
            foreach ($presetModel->getPresetsByGroup("usertitle") as $presetId => $preset) {
                $presets[$presetId] = array(
                    "label" => $preset["title"],
                    "value" => $presetId,
                    "selected" => in_array($presetId, $fullUserOptions["usertitle"]["presets"])
                );
            }
            $presetsTemplate = $template->create("3ps_cmfu_presets_select", $template->getParams());
            $presetsTemplate->setParams(array(
                "title" => new XenForo_Phrase("3ps_cmfu_user_title"),
                "titleCode" => "usertitle",
                "presets" => $presets,
                "permissions" => $permissions
            ));
            $presetHTML = $presetsTemplate->render();
            $settingsTemplate->setParams(array_merge(array(
                "title" => new XenForo_Phrase("3ps_cmfu_user_title"),
                "titleCode" => "usertitle",
                "permissions" => $permissions,
                "userOptions" => $fullUserOptions["usertitle"],
                "currentMarkupRender" => XenForo_Template_Helper_Core::callHelper("usertitle", array($visitor->toArray())),
                "presetHTML" => $presetHTML
            ), ThreePointStudio_CustomMarkupForUser_Helpers::getBaseViewParams()));
            $renderHTML .= $settingsTemplate->render();
        }
        /* Things - Account Panel */
        $contents .= $renderHTML;
    }

    /**
     * Template hook for Admin User Edit page (tabs).
     */
    public static function adminUserEditTabs($hookName, &$contents, array $hookParams, XenForo_Template_Abstract $template) {
        $user = $template->getParam("user");
        if ($user["user_id"] == 0) return; // New user, don't show this
        $contents .= $template->create("3ps_cmfu_user_edit_tab", $template->getParams())->render();
    }

    /**
     * Template hook for Admin User Edit page (panes).
     */
    public static function adminUserEditPanes($hookName, &$contents, array $hookParams, XenForo_Template_Abstract $template) {
        $user = $template->getParam("user");
        $renderHTML = "";
        /* @var $presetModel ThreePointStudio_CustomMarkupForUser_Model_Preset */
        $presetModel = XenForo_Model::create("ThreePointStudio_CustomMarkupForUser_Model_Preset");

        if ($user["user_id"] == 0) return; // New user, don't show this
        $fullUserOptions = ThreePointStudio_CustomMarkupForUser_Helpers::prepareOptionsForView($user["3ps_cmfu_options"]);
        if (!array_key_exists("presets", $fullUserOptions["username"])) {
            $fullUserOptions["username"]["presets"] = array();
        } elseif (is_int($fullUserOptions["username"]["presets"])) {
            $fullUserOptions["username"]["presets"] = array($fullUserOptions["username"]["presets"]);
        }
        if (!array_key_exists("presets", $fullUserOptions["usertitle"])) {
            $fullUserOptions["usertitle"]["presets"] = array();
        } elseif (is_int($fullUserOptions["usertitle"]["presets"])) {
            $fullUserOptions["usertitle"]["presets"] = array($fullUserOptions["usertitle"]["presets"]);
        }
        $settingsTemplate = $template->create("3ps_cmfu_cmcontrol", $template->getParams());
        $presets = array();
        foreach ($presetModel->getPresetsByGroup("username") as $presetId => $preset) {
            $presets[$presetId] = array(
                "label" => $preset["title"],
                "value" => $presetId,
                "selected" => in_array($presetId, $fullUserOptions["username"]["presets"])
            );
        }
        $presetsTemplate = $template->create("3ps_cmfu_options_presets_list", $template->getParams());
        $presetsTemplate->setParams(array(
            "title" => new XenForo_Phrase("user_name"),
            "titleCode" => "username",
            "presets" => $presets
        ));
        $presetHTML = $presetsTemplate->render();
        $settingsTemplate->setParams(array_merge(array(
            "title" => new XenForo_Phrase("user_name"),
            "titleCode" => "username",
            "userOptions" => $fullUserOptions["username"],
            "currentMarkupRender" => XenForo_Template_Helper_Core::callHelper("usernamehtml", array($user, "", true)),
            "presetsHTML" => $presetHTML
        ), ThreePointStudio_CustomMarkupForUser_Helpers::getBaseViewParams()));
        $renderHTML .= $settingsTemplate->render();
        // Render user title
        if (empty($user["custom_title"])) {
            // No user title
            $user["custom_title"] = "(No Custom Title Set)";
        }
        $settingsTemplate = $template->create("3ps_cmfu_cmcontrol", $template->getParams());
        $presets = array();
        foreach ($presetModel->getPresetsByGroup("usertitle") as $presetId => $preset) {
            $presets[$presetId] = array(
                "label" => $preset["title"],
                "value" => $presetId,
                "selected" => in_array($presetId, $fullUserOptions["usertitle"]["presets"])
            );
        }
        $presetsTemplate = $template->create("3ps_cmfu_options_presets_list", $template->getParams());
        $presetsTemplate->setParams(array(
            "title" => new XenForo_Phrase("3ps_cmfu_user_title"),
            "titleCode" => "usertitle",
            "presets" => $presets
        ));
        $presetHTML = $presetsTemplate->render();

        $settingsTemplate->setParams(array_merge(array(
            "title" => new XenForo_Phrase("3ps_cmfu_user_title"),
            "titleCode" => "usertitle",
            "userOptions" => $fullUserOptions["usertitle"],
            "currentMarkupRender" => XenForo_Template_Helper_Core::callHelper("usertitle", array($user, true)),
            "presetsHTML" => $presetHTML
        ), ThreePointStudio_CustomMarkupForUser_Helpers::getBaseViewParams()));
        $renderHTML .= $settingsTemplate->render();
        /* Things - AdminCP */
        $contents .= "<li>" . $renderHTML . "</li>";
    }

    /**
     * Footer credits
     */
    public static function footerCredits($hookName, &$contents, array $hookParams, XenForo_Template_Abstract $template) {
        $displayCredit = (bool) XenForo_Application::getOptions()->get("3ps_cmfu_displayCreditNotice");
        $creditNotice = ($displayCredit) ? new XenForo_Phrase("3ps_cmfu_credit_notice") : '';
        $copyrightText = new XenForo_Phrase("xenforo_copyright");
        $search = '<div id="copyright">' . $copyrightText;
        $replace = '<div id="copyright">' . $copyrightText .
            ($displayCredit ? '<br /><div id="3ps_cmfu_credit_notice">' . $creditNotice . '</div>' : '') .
            '<!-- This forum uses [3.studIo] Custom Markup For User, licensed under the BSD 2-Clause Modified License. DO NOT REMOVE THIS NOTICE! -->' . PHP_EOL;
        $contents = str_replace($search, $replace, $contents);
    }
}