<?php
/*
* Custom Markup For User v1.1.0 written by tyteen4a03@3.studIo.
* This software is licensed under the BSD 2-Clause modified License.
* See the LICENSE file within the package for details.
*/

class ThreePointStudio_CustomMarkupForUser_Constants {
    public static $availableMarkups = array(
        // Text Effects
        "bold" => array(
            "category" => "textEffects",
            "verify" => array("func" => "ThreePointStudio_CustomMarkupForUser_Helpers::verifyBool", "error" => "3ps_cmfu_bold_option_selection_invalid"),
            "type" => XenForo_Input::UINT,
            "permission" => "canUseBoldIn%sM",
            "format" => array(
                array("span", array("style" => array("font-weight" => array("bold"))))
            ),
        ),
        "italic" => array(
            "category" => "textEffects",
            "verify" => array("func" => "ThreePointStudio_CustomMarkupForUser_Helpers::verifyBool", "error" => "3ps_cmfu_italic_option_selection_invalid"),
            "type" => XenForo_Input::UINT,
            "permission" => "canUseItalicIn%sM",
            "format" => array(
                array("span", array("style" => array("font-style" => array("italic"))))
            ),
        ),
        "underline" => array(
            "category" => "textEffects",
            "verify" => array("func" => "ThreePointStudio_CustomMarkupForUser_Helpers::verifyBool", "error" => "3ps_cmfu_underline_option_selection_invalid"),
            "type" => XenForo_Input::UINT,
            "permission" => "canUseUnderlineIn%sM",
            "format" => array(
                array("span", array("style" => array("text-decoration" => array("underline"))), array('mergeProperties' => true))
            ),
        ),
        "overline" => array(
            "category" => "textEffects",
            "verify" => array("func" => "ThreePointStudio_CustomMarkupForUser_Helpers::verifyBool", "error" => "3ps_cmfu_overline_option_selection_invalid"),
            "type" => XenForo_Input::UINT,
            "permission" => "canUseOverlineIn%sM",
            "format" => array(
                array("span", array("style" => array("text-decoration" => array("overline"))), array('mergeProperties' => true))
            ),
        ),
        "strike" => array(
            "category" => "textEffects",
            "verify" => array("func" => "ThreePointStudio_CustomMarkupForUser_Helpers::verifyBool", "error" => "3ps_cmfu_strike_option_selection_invalid"),
            "type" => XenForo_Input::UINT,
            "permission" => "canUseStrikeIn%sM",
            "format" => array(
                array("span", array("style" => array("text-decoration" => array("line-through"))), array('mergeProperties' => true))
            ),
        ),
        "text_colour" => array(
            "category" => "textEffects",
            "verify" => array("func" => "ThreePointStudio_CustomMarkupForUser_Helpers::verifyColour", "error" => "3ps_cmfu_colour_code_invalid"),
            "type" => XenForo_Input::STRING,
            "enable_prefix" => true,
            "permission" => "canUseTextColourIn%sM",
            "format" => array(
                array("span", array("style" => array("color" => array("{_value}"))), array("variableFeed" => array("_value")))
            ),
        ),
        "font_face" => array(
            "category" => "textEffects",
            "verify" => array("func" => "ThreePointStudio_CustomMarkupForUser_Helpers::verifyFontList", "error" => "3ps_cmfu_font_code_invalid"),
            "type" => XenForo_Input::UINT,
            "enable_prefix" => true,
            "permission" => "canUseFontIn%sM",
            "format" => array(
                array("span", array("style" => array("font-family" => array("{fontFamily}"))), array("variableFeed" => array("fontFamily")))
            ),
        ),
        // Background and Borders
        "background_colour" => array(
            "category" => "bgBorders",
            "verify" => array("func" => "ThreePointStudio_CustomMarkupForUser_Helpers::verifyColour", "error" => "3ps_cmfu_colour_code_invalid"),
            "type" => XenForo_Input::STRING,
            "enable_prefix" => true,
            "permission" => "canUseBGColourIn%sM",
            "format" => array(
                array("span", array("style" => array("background-color" => array("{_value}"))), array("variableFeed" => array("_value")))
            ),
        ),
        "border" => array(
            "category" => "bgBorders",
            "verify" => array("func" => "ThreePointStudio_CustomMarkupForUser_Helpers::verifyBool", "error" => "3ps_cmfu_border_option_selection_invalid"),
            "type" => XenForo_Input::UINT,
            "permission" => "canUseBorderIn%sM",
            "format" => array(
                array("span", array("style" => array("border-style" => array("solid"), "border-width" => array("1.7px"))))
            ),
        ),
        "border_colour" => array(
            "category" => "bgBorders",
            "verify" => array("func" => "ThreePointStudio_CustomMarkupForUser_Helpers::verifyColour", "error" => "3ps_cmfu_colour_code_invalid"),
            "type" => XenForo_Input::STRING,
            "enable_prefix" => true,
            "permission" => "canUseBorderColourIn%sM",
            "requires" => array(array("border", 1)),
            "format" => array(
                array("span", array("style" => array("border-color" => array("{_value}"))), array("variableFeed" => array("_value")))
            ),
        ),
        "border_style" => array(
            "category" => "bgBorders",
            "verify" => array("func" => "ThreePointStudio_CustomMarkupForUser_Helpers::verifyBorderList", "error" => "3ps_cmfu_border_style_option_invalid"),
            "type" => XenForo_Input::UINT,
            "enable_prefix" => true,
            "permission" => "canUseBorderStyleIn%sM",
            "requires" => array(array("border", 1)),
            "format" => array(
                array("span", array("style" => array("border-style" => array("{borderStyle}"))), array("variableFeed" => array("borderStyle")))
            ),
        ),
    );

    public static $borderList = array(
        0 => "none",
        1 => "dotted",
        2 => "dashed",
        3 => "solid",
        4 => "double",
        5 => "groove",
        6 => "ridge",
        7 => "inset",
        8 => "outset"
    );

    // Mostly copied from http://www.ampsoft.net/webdesign-l/WindowsMacFonts.html
    public static $fontList = array(
        0 => array("name" => "", "fullname" => "inherit"),
        1 => array("name" => "Arial", "fullname" => 'Arial, Helvetica, sans-serif'),
        2 => array("name" => "Arial Black", "fullname" => "'Arial Black', Gadget, sans-serif"),
        3 => array("name" => "Comic Sans MS", "fullname" => "'Comic Sans MS', cursive, sans-serif"),
        4 => array("name" => "Courier New", "fullname" => "'Courier New', Courier, monospace"),
        5 => array("name" => "Georgia", "fullname" => 'Georgia, serif'),
        6 => array("name" => "Impact", "fullname" => 'Impact, Charcoal, sans-serif'),
        7 => array("name" => "Lucida Console", "fullname" => "'Lucida Console', Monaco, monospace"),
        8 => array("name" => "Lucida Sans Unicode", "fullname" => "'Lucida Sans Unicode', 'Lucida Grande', sans-serif"),
        9 => array("name" => "Palatino Linotype", "fullname" => "'Palatino Linotype', 'Book Antiqua', Palatino, serif"),
        10 => array("name" => "Tahoma", "fullname" => 'Tahoma, Geneva, sans-serif'),
        11 => array("name" => "Times New Roman", "fullname" => "'Times New Roman', Times, serif"),
        12 => array("name" => "Trebuchet MS", "fullname" => "'Trebuchet MS', Helvetica, sans-serif"),
        13 => array("name" => "Verdana", "fullname" => 'Verdana, Geneva, sans-serif'),
    );

    public static $defaultOptionsArray = array(
        "username" => array(),
        "usertitle" => array()
    );

    public static $categories = array("username", "usertitle");
}