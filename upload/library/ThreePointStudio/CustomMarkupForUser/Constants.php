<?php
/*
* Custom Username Markup For User v1.0.0 written by tyteen4a03@3.studIo.
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
				"span" => array("attr" => array("style" => array("font-weight" => "bold")))
			),
		),
		"italic" => array(
			"category" => "textEffects",
			"verify" => array("func" => "ThreePointStudio_CustomMarkupForUser_Helpers::verifyBool", "error" => "3ps_cmfu_italic_option_selection_invalid"),
			"type" => XenForo_Input::UINT,
			"permission" => "canUseItalicIn%sM",
			"format" => array(
				"span" => array("attr" => array("style" => array("font-style" => "italic")))
			),
		),
		"underline" => array(
			"category" => "textEffects",
			"verify" => array("func" => "ThreePointStudio_CustomMarkupForUser_Helpers::verifyBool", "error" => "3ps_cmfu_underline_option_selection_invalid"),
			"type" => XenForo_Input::UINT,
			"permission" => "canUseUnderlineIn%sM",
			"format" => array(
				"span" => array("attr" => array("style" => array("text-decoration" => "underline")), "mergeProperties" => true)
			),
		),
		"overline" => array(
			"category" => "textEffects",
			"verify" => array("func" => "ThreePointStudio_CustomMarkupForUser_Helpers::verifyBool", "error" => "3ps_cmfu_overline_option_selection_invalid"),
			"type" => XenForo_Input::UINT,
			"permission" => "canUseOverlineIn%sM",
			"format" => array(
				"span" => array("attr" => array("style" => array("text-decoration" => "overline")), "mergeProperties" => true)
			),
		),
		"strike" => array(
			"category" => "textEffects",
			"verify" => array("func" => "ThreePointStudio_CustomMarkupForUser_Helpers::verifyBool", "error" => "3ps_cmfu_strike_option_selection_invalid"),
			"type" => XenForo_Input::UINT,
			"permission" => "canUseStrikeIn%sM",
			"format" => array(
				"span" => array("attr" => array("style" => array("text-decoration" => "line-through")), "mergeProperties" => true)
			),
		),
		"text_colour" => array(
			"category" => "textEffects",
			"verify" => array("func" => "ThreePointStudio_CustomMarkupForUser_Helpers::verifyColour", "error" => "3ps_cmfu_colour_code_invalid"),
			"type" => XenForo_Input::STRING,
			"enable_prefix" => true,
			"permission" => "canUseTextColourIn%sM",
			"format" => array(
				"span" => array("attr" => array("style" => array("color" => "{_value}")), "variableFeed" => array("_value"))
			),
		),
		"font_face" => array(
			"category" => "textEffects",
			"verify" => array("func" => "ThreePointStudio_CustomMarkupForUser_Helpers::verifyFontList", "error" => "3ps_cmfu_font_code_invalid"),
			"type" => XenForo_Input::UINT,
			"enable_prefix" => true,
			"permission" => "canUseFontIn%sM",
			"format" => array(
				"span" => array("attr" => array("style" => array("font-family" => "{fontFamily}")), "variableFeed" => array("fontFamily"))
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
				"span" => array("attr" => array("style" => array("background-color" => "{_value}")), "variableFeed" => array("_value"))
			),
		),
		"border" => array(
			"category" => "bgBorders",
			"verify" => array("func" => "ThreePointStudio_CustomMarkupForUser_Helpers::verifyBool", "error" => "3ps_cmfu_border_option_selection_invalid"),
			"type" => XenForo_Input::UINT,
			"permission" => "canUseBorderIn%sM",
			"format" => array(
				"span" => array("attr" => array("style" => array("border" => "{borderDefaults}")), "variableFeed" => array("borderDefaults"))
			),
		),
		"border_colour" => array(
			"category" => "bgBorders",
			"verify" => array("func" => "ThreePointStudio_CustomMarkupForUser_Helpers::verifyColour", "error" => "3ps_cmfu_colour_code_invalid"),
			"type" => XenForo_Input::STRING,
			"enable_prefix" => true,
			"permission" => "canUseBorderColourIn%sM",
			'requires' => array(array("border", 1)),
			"format" => array(
				"span" => array("attr" => array("style" => array("border-color" => "{_value}")), "variableFeed" => array("_value"))
			),
		),
		"border_style" => array(
			"category" => "bgBorders",
			"verify" => array("func" => "ThreePointStudio_CustomMarkupForUser_Helpers::verifyBorderList", "error" => "3ps_cmfu_border_style_option_invalid"),
			"type" => XenForo_Input::UINT,
			"enable_prefix" => true,
			"permission" => "canUseBorderStyleIn%sM",
			"format" => array(
				"span" => array("attr" => array("style" => array("border-style" => "{borderStyle}"), "fakeThing" => "thing"), "variableFeed" => array("borderStyle"))
			),
		),
	);

	public static $borderDefaults = array(
		"border_colour" => "",
		"border_style" => "solid",
		"border_width" => "1.7px" // Reserved
	);

	public static $colourList = array(
		"" => "",
		"AliceBlue" => "#F0F8FF",
		"Aquamarine" => "#7FFFD4",
		"Azure" => "#F0FFFF",
		"Beige" => "#F5F5DC",
		"Bisque" => "#FFE4C4",
		"Black" => "#000000",
		"BlanchedAlmond" => "#FFEBCD",
		"Blue" => "#0000FF",
		"BlueViolet" => "#8A2BE2",
		"Brown" => "#A52A2A",
		"BurlyWood" => "#DEB887",
		"CadetBlue" => "#5F9EA0",
		"Chartreuse" => "#7FFF00",
		"Chocolate" => "#D2691E",
		"Coral" => "#FF7F50",
		"CornflowerBlue" => "#6495ED",
		"Cornsilk" => "#FFF8DC",
		"Crimson" => "#DC143C",
		"Cyan" => "#00FFFF",
		"DarkBlue" => "#00008B",
		"DarkCyan" => "#008B8B",
		"DarkGoldenRod" => "#B8860B",
		"DarkGray" => "#A9A9A9",
		"DarkGreen" => "#006400",
		"DarkKhaki" => "#BDB76B",
		"DarkMagenta" => "#8B008B",
		"DarkOliveGreen" => "#556B2F",
		"DarkOrange" => "#FF8C00",
		"DarkOrchid" => "#9932CC",
		"DarkRed" => "#8B0000",
		"DarkSalmon" => "#E9967A",
		"DarkSeaGreen" => "#8FBC8F",
		"DarkSlateBlue" => "#483D8B",
		"DarkSlateGray" => "#2F4F4F",
		"DarkTurquoise" => "#00CED1",
		"DarkViolet" => "#9400D3",
		"DeepPink" => "#FF1493",
		"DeepSkyBlue" => "#00BFFF",
		"DimGrey" => "#696969",
		"DodgerBlue" => "#1E90FF",
		"FireBrick" => "#B22222",
		"FloralWhite" => "#FFFAF0",
		"ForestGreen" => "#228B22",
		"Gainsboro" => "#DCDCDC",
		"GhostWhite" => "#F8F8FF",
		"Gold" => "#FFD700",
		"GoldenRod" => "#DAA520",
		"Gray" => "#808080",
		"Green" => "#008000",
		"GreenYellow" => "#ADFF2F",
		"HoneyDew" => "#F0FFF0",
		"HotPink" => "#FF69B4",
		"IndianRed " => "#CD5C5C",
		"Indigo " => "#4B0082",
		"Ivory" => "#FFFFF0",
		"Khaki" => "#F0E68C",
		"Lavender" => "#E6E6FA",
		"LavenderBlush" => "#FFF0F5",
		"LawnGreen" => "#7CFC00",
		"LemonChiffon" => "#FFFACD",
		"LightBlue" => "#ADD8E6",
		"LightCoral" => "#F08080",
		"LightCyan" => "#E0FFFF",
		"LightGoldenRodYellow" => "#FAFAD2",
		"LightGray" => "#D3D3D3",
		"LightGreen" => "#90EE90",
		"LightPink" => "#FFB6C1",
		"LightSalmon" => "#FFA07A",
		"LightSeaGreen" => "#20B2AA",
		"LightSkyBlue" => "#87CEFA",
		"LightSlateGray" => "#778899",
		"LightSteelBlue" => "#B0C4DE",
		"LightYellow" => "#FFFFE0",
		"Lime" => "#00FF00",
		"LimeGreen" => "#32CD32",
		"Linen" => "#FAF0E6",
		"Magenta" => "#FF00FF",
		"Maroon" => "#800000",
		"MediumAquaMarine" => "#66CDAA",
		"MediumBlue" => "#0000CD",
		"MediumOrchid" => "#BA55D3",
		"MediumPurple" => "#9370DB",
		"MediumSeaGreen" => "#3CB371",
		"MediumSlateBlue" => "#7B68EE",
		"MediumSpringGreen" => "#00FA9A",
		"MediumTurquoise" => "#48D1CC",
		"MediumVioletRed" => "#C71585",
		"MidnightBlue" => "#191970",
		"MintCream" => "#F5FFFA",
		"MistyRose" => "#FFE4E1",
		"Moccasin" => "#FFE4B5",
		"NavajoWhite" => "#FFDEAD",
		"Navy" => "#000080",
		"OldLace" => "#FDF5E6",
		"Olive" => "#808000",
		"OliveDrab" => "#6B8E23",
		"Orange" => "#FFA500",
		"OrangeRed" => "#FF4500",
		"Orchid" => "#DA70D6",
		"PaleGoldenRod" => "#EEE8AA",
		"PaleGreen" => "#98FB98",
		"PaleTurquoise" => "#AFEEEE",
		"PaleVioletRed" => "#DB7093",
		"PapayaWhip" => "#FFEFD5",
		"PeachPuff" => "#FFDAB9",
		"Peru" => "#CD853F",
		"Pink" => "#FFC0CB",
		"Plum" => "#DDA0DD",
		"PowderBlue" => "#B0E0E6",
		"Purple" => "#800080",
		"Red" => "#FF0000",
		"RosyBrown" => "#BC8F8F",
		"RoyalBlue" => "#4169E1",
		"SaddleBrown" => "#8B4513",
		"Salmon" => "#FA8072",
		"SandyBrown" => "#F4A460",
		"SeaGreen" => "#2E8B57",
		"SeaShell" => "#FFF5EE",
		"Sienna" => "#A0522D",
		"Silver" => "#C0C0C0",
		"SkyBlue" => "#87CEEB",
		"SlateBlue" => "#6A5ACD",
		"SlateGray" => "#708090",
		"Snow" => "#FFFAFA",
		"SpringGreen" => "#00FF7F",
		"SteelBlue" => "#4682B4",
		"Tan" => "#D2B48C",
		"Teal" => "#008080",
		"Thistle" => "#D8BFD8",
		"Tomato" => "#FF6347",
		"Turquoise" => "#40E0D0",
		"Violet" => "#EE82EE",
		"Wheat" => "#F5DEB3",
		"White" => "#FFFFFF",
		"WhiteSmoke" => "#F5F5F5",
		"Yellow" => "#FFFF00",
		"YellowGreen" => "#9ACD32"
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
		7 => array("name" => "Lucid Console", "fullname" => "'Lucida Console', Monaco, monospace"),
		8 => array("name" => "Lucid Sans Unicode", "fullname" => "'Lucida Sans Unicode', 'Lucida Grande', sans-serif"),
		9 => array("name" => "Palatino Linotype", "fullname" => "'Palatino Linotype', 'Book Antiqua', Palatino, serif"),
		10 => array("name" => "Tahoma", "fullname" => 'Tahoma, Geneva, sans-serif'),
		11 => array("name" => "Times New Roman", "fullname" => "'Times New Roman', Times, serif"),
		12 => array("name" => "Trebuchet MS", "fullname" => "'Trebuchet MS', Helvetica, sans-serif"),
		13 => array("name" => "Verdana", "fullname" => 'Verdana, Geneva, sans-serif'),
	);
}