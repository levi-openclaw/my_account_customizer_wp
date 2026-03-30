/* global _tgwcCustomizePreviewL10n */
(function ($, api, data) {
	var parentApi = parent.window.wp.customize,
		defaultUnit = "px",
		setting = "tgwc_customize",
		$container = $("#tgwc-woocommerce"),
		$anchors = $container.find("a"),
		$avatar = $container.find(".tgwc-user-avatar"),
		$avatarUsername = $container.find(".tgwc-user-id"),
		$avatarLogout = $container.find(".tgwc-user-info .button"),
		$avatarType = $container.find(".tgwc-user-avatar-image-wrap"),
		$buttons = $container.find("button, a.woocommerce-Button, a.button"),
		$inputs = $container.find(".woocommerce-Input"),
		$nav = $container.find("nav"),
		$navWrapper = $container.find(
			".tgwc-woocommerce-MyAccount-navigation-wrap"
		),
		$navContent = $container.find(".woocommerce-MyAccount-content"),
		$controlsWrapper = $(parent.document).find("#customize-controls"),
		$previewButtons = $controlsWrapper.find(
			"#customize-footer-actions .devices button"
		),
		$editShortcut = $container.find(".customize-partial-edit-shortcut");

	// Helper function to safely parse JSON
	function safeParseJSON(value) {
		try {
			return typeof value === "string" ? JSON.parse(value) : value;
		} catch (e) {
			return {};
		}
	}

	function getCurrentLayout() {
		return $container.attr("data-menu-position") || "vertical-left";
	}

	function applyBorderRadius() {
		var layout = getCurrentLayout();
		$nav.find("li a").css("border-radius", ""); // Reset

		if (layout !== "tab") {
			$nav.find("li a").css("border-radius", "4px 0 0 4px");
		} else if (layout === "tab") {
			$nav.find("li a").css("border-radius", "4px");
		}
	}

	// Edit shortcut hover effects
	$editShortcut.on("mouseover", function () {
		$(this)
			.parent()
			.css({ outline: "1px solid #3582c4", outlineOffset: "-1px" });
	});

	$editShortcut.on("mouseleave", function () {
		$(this).parent().css({ outline: "", outlineOffset: "" });
	});

	$editShortcut.on("click", function (e) {
		e.preventDefault();
		var $parent = $(this).parent();

		if ($parent.hasClass("tgwc-woocommerce-MyAccount-navigation-wrap")) {
			parentApi.section("tgwc_customize[navigation]").focus();
		} else if ($parent.hasClass("woocommerce")) {
			parentApi.section("tgwc_customize[wrapper]").focus();
		} else if ($parent.hasClass("tgwc-user-avatar")) {
			parentApi.section("tgwc_customize[avatar]").focus();
		} else if ($parent.hasClass("woocommerce-MyAccount-content")) {
			parentApi.section("tgwc_customize[content]").focus();
		}
	});

	/**
	 * Navigation: menu style (navigation style).
	 */
	api(setting + "[layout][menu_position]", function (value) {
		var $ul;
		var menuStyle = parentApi.control("tgwc_customize[layout][menu_style]");

		if ("tab" === value.get() && "legacy" === menuStyle.setting.get()) {
			$ul = $navWrapper.find("ul").first().scrollTabs();
		}
		function updateMenuStyle(newValue) {
			$container.attr("data-menu-position", newValue);
			var menuStyle = parentApi.control(
				"tgwc_customize[layout][menu_style]"
			);

			wp.customize.preview.send("refresh");

			if (newValue === "tab") {
				$navWrapper.find("a").off();

				if ("legacy" === menuStyle.setting.get()) {
					$ul = $navWrapper.find("ul").first().scrollTabs();
				}

				$navWrapper.find(".tgwc-group > a > .tgwc-icon").hide();
			} else if ("legacy" === menuStyle.setting.get()) {
				wp.customize.preview.send("refresh");
			} else {
				$navWrapper.find("a").off();
				$navWrapper.find(".tgwc-group > a > .tgwc-icon").show();
			}
		}
		// updateMenuStyle(value.get());
		// applyBorderRadius();
		value.bind(updateMenuStyle);
	});
	function updateColors(palettes) {
		var setting = "tgwc_customize[layout]",
			layoutSetting = parentApi.control(setting + "[menu_style]"),
			layout = layoutSetting ? layoutSetting.setting._value : "default",
			paletteSettings = palettes,
			activePalette = paletteSettings.activePalette[layout],
			colorPalettes = paletteSettings.palettes,
			palette = colorPalettes[activePalette] || {};

		function getColor(path, fallback) {
			var val = palette;
			path.split(".").forEach(function (part) {
				val = val && val[part];
			});
			return val || fallback;
		}

		function resetStyles() {
			$navWrapper
				.find("li a")
				.css({
					color: "",
					"background-color": "",
					"border-color": "",
				})
				.off("mouseenter mouseleave");
			$nav.css({
				"background-color": "",
				"border-color": "",
			}).off("mouseenter mouseleave");
			$nav.find(".tgwc-user-avatar").css({
				"background-color": "",
			});
			$nav.find(".tgwc-woocommerce-MyAccount-navigation-wrap").css({
				"background-color": "",
			});
			$nav.find(".button")
				.css({
					"background-color": "",
					color: "",
					"border-color": "",
				})
				.off("mouseenter mouseleave");
			$navContent.css("background-color", "");
			$container.css("background-color", "");
		}

		resetStyles();

		// Item colors
		var itemTextColor = getColor("colors.item_text.normal"),
			itemTextHoverColor = getColor("colors.item_text.hover"),
			itemTextActiveColor = getColor("colors.item_text.active"),
			itemBackgroundColor = getColor("colors.item_background.normal"),
			itemBackgroundHoverColor = getColor("colors.item_background.hover"),
			itemBackgroundActiveColor = getColor(
				"colors.item_background.active"
			),
			itemBorderColor = getColor("colors.item_border.normal"),
			itemBorderHoverColor = getColor("colors.item_border.normal"),
			itemBorderActiveColor = getColor("colors.item_border.active"),
			contentBackgroundColor = getColor(
				"colors.content_background.normal"
			),
			inputTextColor = getColor("colors.input_text.normal"),
			inputTextFocusColor = getColor("colors.input_text.focus"),
			inputBackgroundColor = getColor("colors.input_background.normal"),
			inputBackgroundFocusColor = getColor(
				"colors.input_background.focus"
			),
			inputBorderColor = getColor("colors.input_border.normal"),
			inputBorderFocusColor = getColor("colors.input_border.focus"),
			wrapperBackgroundColor = getColor(
				"colors.wrapper_background.normal"
			);

		// Button colors
		var buttonBackgroundColor = getColor("colors.button_background.normal"),
			buttonBackgroundHoverColor = getColor(
				"colors.button_background.hover"
			),
			buttonTextColor = getColor("colors.button_text.normal"),
			buttonTextHoverColor = getColor("colors.button_text.hover"),
			buttonBorderColor = getColor("colors.button_border.normal"),
			buttonBorderHoverColor = getColor("colors.button_border.hover");

		// Nav colors
		var navBackgroundColor = getColor(
				"colors.navigation_background.normal"
			),
			navBorderColor = getColor("colors.navigation_border.normal");

		// Apply item colors
		if (itemTextColor) {
			$navWrapper
				.find("li:not(.is-active) a")
				.css("color", itemTextColor)
				.mouseleave(function () {
					$(this).css("color", itemTextColor);
				});
		}
		if (itemTextActiveColor) {
			$navWrapper
				.find(".is-active > a")
				.css("color", itemTextActiveColor)
				.mouseleave(function () {
					$(this).css("color", itemTextActiveColor);
				});
		}
		if (itemTextHoverColor) {
			$navWrapper.find("a").mouseenter(function () {
				$(this).css("color", itemTextHoverColor);
			});
		}
		if (itemBackgroundColor) {
			$navWrapper
				.find("li:not(.is-active) a")
				.css("background-color", itemBackgroundColor)
				.mouseleave(function () {
					$(this).css("background-color", itemBackgroundColor);
				});
		} else {
			if ("legacy" !== layout) {
				$navWrapper
					.find("li:not(.is-active) a")
					.css("background-color", "")
					.mouseleave(function () {
						$(this).css("background-color", "");
					});
			}
		}
		if (itemBackgroundHoverColor) {
			$navWrapper.find("a").mouseenter(function () {
				$(this).css("background-color", itemBackgroundHoverColor);
			});
		}
		if (itemBackgroundActiveColor) {
			var nav = $(document).find(
				".tgwc-woocommerce-MyAccount-navigation-wrap"
			);
			$navWrapper
				.find(".is-active > a")
				.css("background-color", itemBackgroundActiveColor)
				.mouseleave(function () {
					$(this).css("background-color", itemBackgroundActiveColor);
				});
		}

		if ("legacy" !== layout) {
			if (itemBorderActiveColor) {
				$navWrapper
					.find(".is-active > a")
					.css("border-color", itemBorderActiveColor)
					.mouseleave(function (e) {
						$(this).css("border-color", itemBorderActiveColor);
					});
			}
		} else {
			if (itemBorderColor) {
				$navWrapper
					.find("li:not(.is-active) a")
					.css("border-color", itemBorderColor)
					.mouseleave(function (e) {
						$(this).css("border-color", itemBorderColor);
					});
			}
			if (itemBorderHoverColor) {
				$navWrapper.find("a").mouseenter(function (e) {
					$(this).css("border-color", itemBorderHoverColor);
				});

				$navWrapper.find("a").mouseleave(function (e) {
					$(this).css("border-color", "");
				});
			}
			if (itemBorderActiveColor) {
				$navWrapper
					.find(".is-active > a")
					.css("border-color", itemBorderActiveColor);
				$navWrapper.find(".is-active > a").mouseleave(function (e) {
					$(this).css("border-color", itemBorderActiveColor);
				});
			}
		}

		//content
		if ("legacy" === layout) {
			if (contentBackgroundColor) {
				$navContent
					.css("background-color", contentBackgroundColor)
					.mouseleave(function () {
						$(this).css("background-color", contentBackgroundColor);
					});
			}

			if (inputTextColor) {
				$inputs.css("color", inputTextColor).focusout(function () {
					$(this).css("color", inputTextColor);
				});
			}

			if (inputTextFocusColor) {
				$inputs.focus(function (e) {
					$(this).css("color", inputTextFocusColor);
				});
			}

			if (inputBackgroundColor) {
				$inputs
					.css("background-color", inputBackgroundColor)
					.focusout(function () {
						$(this).css("background-color", inputBackgroundColor);
					});
			}

			if (inputBackgroundFocusColor) {
				$inputs.focus(function (e) {
					$(this).css("background-color", inputBackgroundFocusColor);
				});
			}

			if (inputBorderColor) {
				$inputs
					.css("border-color", inputBorderColor)
					.focusout(function (e) {
						$(this).css("border-color", inputBorderColor);
					});
			}

			if (inputBorderFocusColor) {
				$inputs.focus(function (e) {
					$(this).css("border-color", inputBorderFocusColor);
				});
			}

			if (wrapperBackgroundColor) {
				$container.css("background-color", wrapperBackgroundColor);
			}

			if (buttonTextHoverColor) {
				$buttons.mouseenter(function (e) {
					$(this).css("color", buttonTextHoverColor);
				});
			}

			if (buttonBorderColor) {
				$buttons
					.css("border-color", buttonBorderColor)
					.mouseleave(function (e) {
						$(this).css("border-color", buttonBorderColor);
					});
			}

			if (buttonBorderHoverColor) {
				$buttons.mouseenter(function (e) {
					$(this).css("border-color", buttonBorderHoverColor);
				});
			}
		} else {
			$container.css("background-color", "transparent");
			$navContent
				.css("background-color", "transparent")
				.mouseleave(function () {
					$(this).css("background-color", "transparent");
				});
			$buttons
				.css("border-color", "transparent")
				.mouseenter(function (e) {
					$(this).css("border-color", "transparent");
				});
		}

		// Apply button colors
		var $button = $nav.find(".button");

		if (buttonBackgroundColor) {
			$button
				.css("background-color", buttonBackgroundColor)
				.mouseleave(function () {
					$(this).css("background-color", buttonBackgroundColor);
				});
		}
		if (buttonBackgroundHoverColor) {
			$button.mouseenter(function () {
				$(this).css("background-color", buttonBackgroundHoverColor);
			});
		}
		if (buttonTextColor) {
			$button.css("color", buttonTextColor);
		}

		// Apply nav colors
		if (navBackgroundColor && "modern" === layout) {
			if (
				$nav.find(".tgwc-woocommerce-MyAccount-navigation-wrap")
					.length &&
				$nav.find(".tgwc-user-avatar").length
			) {
				$nav.find(".tgwc-woocommerce-MyAccount-navigation-wrap").css(
					"background-color",
					navBackgroundColor
				);
				$nav.find(".tgwc-user-avatar").css(
					"background-color",
					navBackgroundColor
				);
			} else {
				$nav.css("background-color", navBackgroundColor);
			}
		}
		if (navBackgroundColor && "classic" === layout) {
			$nav.css("background-color", navBackgroundColor);
		}

		if (navBorderColor) {
			$nav.css("border-color", navBorderColor).mouseleave(function () {
				$(this).css("border-color", navBorderColor);
			});
		}
	}
	/**
	 * Navigation: color palette.
	 */
	api(setting + "[layout][color_palette]", function (value) {
		updateColors(value.get());
		value.bind(function (colorPalettes) {
			updateColors(colorPalettes);
		});
	});

	/**
	 * Layout: menu style.
	 */
	api(setting + "[layout][menu_style]", function (value) {
		function updateLayout(layout) {
			$container.attr("data-menu-layout", layout);
			var menuPosition = parentApi.control(
				"tgwc_customize[layout][menu_position]"
			);
			if (layout === "legacy" && menuPosition.setting.get() === "tab") {
				wp.customize.preview.send("refresh");

				var $ul = $navWrapper.find("ul").first();

				if (
					typeof $.fn.scrollTabs === "function" &&
					!$ul.closest(".scroll_tabs_container").length
				) {
					// Initialize only if not already initialized
					$ul.scrollTabs();
				}
			} else {
				var $ul = $navWrapper.find("ul").first();

				if (
					typeof $.fn.scrollTabs === "function" &&
					$ul.closest(".scroll_tabs_container").length
				) {
					wp.customize.preview.send("refresh");
				}
			}

			var paletteSetting = parentApi.control(
				"tgwc_customize[layout][color_palette]"
			);

			var paletteSettings = paletteSetting
				? paletteSetting.setting.get()
				: {
						activePalette: {},
						palettes: {},
				  };

			value.bind(function (menu_style) {
				updateColors(paletteSettings);
			});

			// applyLayoutStyles(layout);
		}

		function applyLayoutStyles(layout) {
			var menuStyle = $container.attr("data-menu-position");

			$navWrapper.find("a").css({
				"border-radius": "",
				padding: "",
			});

			if (menuStyle === "tab") {
				$navWrapper.find("li a").css({
					"border-radius": "4px",
					padding: "12px 16px",
				});
			} else {
				$navWrapper.find("li a").css({
					"border-radius": "4px 0 0 4px",
					padding: "12px 0",
				});
			}
		}

		// Initialize and bind changes
		// updateLayout(value.get());
		value.bind(function (newStyle) {
			updateLayout(newStyle);
		});
	});

	/**
	 * Navigation: font size.
	 */
	api(setting + "[navigation][font_size]", function (value) {
		function updateFontSize(newValue) {
			if (newValue >= 40) {
				newValue = 40;
			}

			$nav.css("font-size", newValue + defaultUnit)
				.find("p")
				.css("font-size", newValue + defaultUnit);
		}

		if (parentApi.control(value.id).setting._dirty) {
			updateFontSize(value.get());
		}
		value.bind(updateFontSize);
	});

	/**
	 * Navigation: Show Icon.
	 */
	api(setting + "[navigation][show_icon]", function (value) {
		function updateIconVisibility(show) {
			$nav.find("li").each(function () {
				$this = $(this);

				$this.find("img, svg").toggleClass("tgwc-hide", !show);
				$this.find("img, svg").toggleClass("tgwc-hide", !show);
				$this.find(".tgwc-icon--chevron-down").removeClass("tgwc-hide");
				$this
					.find(".tgwc-icon--chevron-right")
					.removeClass("tgwc-hide");
			});
			// $nav.find("li > a")
			// 	.find("img, svg")
			// 	.toggleClass("tgwc-hide", !show);
		}

		updateIconVisibility(value.get());
		value.bind(updateIconVisibility);
	});

	api(setting + "[navigation][icon_position]", function (value) {
		function updateIconVisibility($position) {
			$nav.find("li").each(function () {
				$this = $(this);
				console.log($position);

				if ("left" === $position) {
					if (!$this.hasClass("tgwc-navicon-left")) {
						$this.addClass("tgwc-navicon-left");
						$this.removeClass("tgwc-navicon-right");
					}
				}
				if ("right" === $position) {
					if (!$this.hasClass("tgwc-navicon-right")) {
						$this.addClass("tgwc-navicon-right");
						$this.removeClass("tgwc-navicon-left");
					}
				}
			});
		}

		value.bind(updateIconVisibility);
	});

	/**
	 * Avatar: Logout Style
	 */
	api(setting + "[navigation][show_logout_btn]", function (value) {
		function updateLogoutVisibility(show) {
			$avatarLogout.toggleClass("tgwc-hide", !show);
			$nav.find(
				".woocommerce-MyAccount-navigation-link--customer-logout"
			).toggleClass("tgwc-hide", show);
		}

		updateLogoutVisibility(value.get());
		value.bind(updateLogoutVisibility);
	});

	/**
	 * Group accordion default state.
	 */
	api(
		setting + "[navigation][group_accordion_default_state]",
		function (value) {
			function updateAccordionState(show) {
				const $groups = $container.find(".tgwc-group");
				const isExpanded = show === "expanded";

				$groups.each(function () {
					const $group = $(this);

					if ($group.attr("data-is_loaded") === "yes") {
						$group.attr("data-is_loaded", "no");
					}

					var $icon = $group.find(".tgwc-icon--chevron-down");

					if (!$icon.length) {
						$icon = $group.find(".tgwc-icon--chevron-right");
					}

					const isChevronRight = $icon.hasClass(
						"tgwc-icon--chevron-right"
					);

					$group.attr("data-collapsed", !isExpanded);
					$group.find("> ul").toggle(isExpanded);

					let rotation;
					if (isExpanded) {
						rotation = isChevronRight ? 90 : 0;
					} else {
						rotation = isChevronRight ? 0 : -90;
					}

					$icon.css("transform", `rotate(${rotation}deg)`);
				});
			}

			// updateAccordionState(value.get());
			value.bind(updateAccordionState);
		}
	);

	/**
	 * Wrapper: Padding.
	 */
	api(setting + "[spacing][page_container][padding]", function (value) {
		function updatePadding(device) {
			var controlValue = safeParseJSON(value.get());
			$container.css("padding", "");

			if (controlValue && controlValue[device]) {
				$.each(controlValue[device], function (prop, val) {
					$container.css("padding-" + prop, val + defaultUnit);
				});
			}
		}

		$previewButtons.click(function () {
			updatePadding($(this).data("device") || "desktop");
		});

		if (parentApi.control(value.id).setting._dirty) {
			updatePadding(
				$previewButtons.filter(".active").data("device") || "desktop"
			);
		}

		value.bind(function () {
			updatePadding(
				$previewButtons.filter(".active").data("device") || "desktop"
			);
		});
	});

	/**
	 * Wrapper: Margin.
	 */
	api(setting + "[spacing][page_container][margin]", function (value) {
		function updateMargin(device) {
			var controlValue = safeParseJSON(value.get());
			$container.css("margin", "");

			if (controlValue && controlValue[device]) {
				$.each(controlValue[device], function (prop, val) {
					$container.css("margin-" + prop, val + defaultUnit);
				});
			}
		}

		$previewButtons.click(function () {
			updateMargin($(this).data("device") || "desktop");
		});

		if (parentApi.control(value.id).setting._dirty) {
			updateMargin(
				$previewButtons.filter(".active").data("device") || "desktop"
			);
		}

		value.bind(function () {
			updateMargin(
				$previewButtons.filter(".active").data("device") || "desktop"
			);
		});
	});

	/**
	 * Avatar: Username Style
	 */
	api(setting + "[avatar][username]", function (value) {
		function updateUsernameVisibility(show) {
			$avatarUsername.toggleClass("tgwc-hide", !show);
		}

		// updateUsernameVisibility(value.get());
		value.bind(updateUsernameVisibility);
	});

	/**
	 * Avatar: Thumbnail Image Style.
	 */
	api(setting + "[avatar][type]", function (value) {
		var types = ["square", "circle"];

		function updateType(newValue) {
			$.each(types, function (index, type) {
				$avatarType.removeClass("tgwc-user-avatar-image-wrap--" + type);
			});
			$avatarType.addClass("tgwc-user-avatar-image-wrap--" + newValue);
		}

		// updateType(value.get());
		value.bind(updateType);
	});

	/**
	 * Avatar: Padding.
	 */
	api(setting + "[spacing][avatar][padding]", function (value) {
		function updatePadding(device) {
			var controlValue = safeParseJSON(value.get());
			$avatar.css("padding", "");

			if (controlValue && controlValue[device]) {
				$.each(controlValue[device], function (prop, val) {
					$avatar.css("padding-" + prop, val + defaultUnit);
				});
			}
		}

		$previewButtons.click(function () {
			updatePadding($(this).data("device") || "desktop");
		});

		if (parentApi.control(value.id).setting._dirty) {
			updatePadding(
				$previewButtons.filter(".active").data("device") || "desktop"
			);
		}

		value.bind(function () {
			updatePadding(
				$previewButtons.filter(".active").data("device") || "desktop"
			);
		});
	});

	/**
	 * Navigation: Wrapper Padding
	 */
	api(setting + "[spacing][menu_container][padding]", function (value) {
		function updatePadding(device) {
			var controlValue = safeParseJSON(value.get());
			$nav.css("padding", "");

			if (controlValue && controlValue[device]) {
				$.each(controlValue[device], function (prop, val) {
					$nav.css("padding-" + prop, val + defaultUnit);
				});
			}
		}

		$previewButtons.click(function () {
			updatePadding($(this).data("device") || "desktop");
		});

		// if (parentApi.control(value.id).setting._dirty) {
		// 	updatePadding(
		// 		$previewButtons.filter(".active").data("device") || "desktop"
		// 	);
		// }

		value.bind(function () {
			updatePadding(
				$previewButtons.filter(".active").data("device") || "desktop"
			);
		});
	});

	/**
	 * Navigation: Wrapper Margin
	 */
	api(setting + "[spacing][menu_container][margin]", function (value) {
		function updateMargin(device) {
			var controlValue = safeParseJSON(value.get());
			$nav.css("margin", "");

			if (controlValue && controlValue[device]) {
				$.each(controlValue[device], function (prop, val) {
					$nav.css("margin-" + prop, val + defaultUnit);
				});
			}
		}

		$previewButtons.click(function () {
			updateMargin($(this).data("device") || "desktop");
		});

		// if (parentApi.control(value.id).setting._dirty) {
		// 	updateMargin(
		// 		$previewButtons.filter(".active").data("device") || "desktop"
		// 	);
		// }

		value.bind(function () {
			updateMargin(
				$previewButtons.filter(".active").data("device") || "desktop"
			);
		});
	});
	// applyBorderRadius();

	//Legacy

	/**
	 * Add Google font link into header.
	 *
	 * @param {string} font_name Google Font Name.
	 */
	function addGoogleFont(font_name) {
		var font_plus = "",
			font_name = font_name.split(" ");

		if ($.isArray(font_name)) {
			font_plus = font_name[0];
			for (var i = 1; i < font_name.length; i++) {
				font_plus = font_plus + "+" + font_name[i];
			}
		}

		$(
			'<link href="https://fonts.googleapis.com/css?family=' +
				font_plus +
				'" rel="stylesheet" type="text/css">'
		).appendTo("head");
	}

	/**
	 * Wrapper: menu style (navigation style).
	 */
	api(setting + "[wrapper][menu_style]", function (value) {
		var $ul = undefined;
		$container.attr("data-menu-layout", value.get());

		value.bind(function (newValue) {
			$container.attr("data-menu-layout", newValue);

			if ("tab" === newValue) {
				$navWrapper.find("a").off();
				$navWrapper.find(".tgwc-group > a > .tgwc-icon").hide();
			} else {
				$navWrapper.find("a").find("a").off();
				$navWrapper.find(".tgwc-group > a > .tgwc-icon").show();
				if (undefined === $ul) {
					var $navContainer = $nav.find(".scroll_tab_inner").clone();
					$navContainer.find("li").removeAttr("style");

					$lis = $navContainer
						.children()
						.filter(function (index, li) {
							return $(li).prop("tagName") === "LI";
						});

					$navWrapper.find(".scroll_tabs_container").remove();
					$navWrapper.html("<ul>");
					$nav.find("ul").append($lis);
				} else {
					$ul.destroy();
				}
			}

			// When menu style is changed from tab to sidebar,
			// apply all the navigation normal styles.
			var setting = "tgwc_customize[navigation][normal]",
				navColor = parentApi.control(setting + "[color]"),
				navBackgroundColor = parentApi.control(
					setting + "[background_color]"
				),
				navBorderStyle = parentApi.control(setting + "[border_style]"),
				navBorderWidth = parentApi.control(setting + "[border_width]"),
				navBorderColor = parentApi.control(setting + "[border_color]"),
				navHoverColor = parentApi.control(
					"tgwc_customize[navigation][hover][color]"
				),
				navHoverBackgroundColor = parentApi.control(
					"tgwc_customize[navigation][hover][background_color]"
				),
				navHoverBorderStyle = parentApi.control(
					"tgwc_customize[navigation][hover][border_style]"
				),
				navHoverBorderWidth = parentApi.control(
					"tgwc_customize[navigation][hover][border_width]"
				),
				navHoverBorderColor = parentApi.control(
					"tgwc_customize[navigation][hover][border_color]"
				),
				navActiveColor = parentApi.control(
					"tgwc_customize[navigation][active][color]"
				),
				navActiveBackgroundColor = parentApi.control(
					"tgwc_customize[navigation][active][background_color]"
				),
				navActiveBorderStyle = parentApi.control(
					"tgwc_customize[navigation][active][border_style]"
				),
				navActiveBorderWidth = parentApi.control(
					"tgwc_customize[navigation][active][border_width]"
				),
				navActiveBorderColor = parentApi.control(
					"tgwc_customize[navigation][active][border_color]"
				),
				navWrapperPadding = parentApi.control(
					"tgwc_customize[navigation][general][wrapper_padding]"
				),
				navWrapperMargin = parentApi.control(
					"tgwc_customize[navigation][general][wrapper_margin]"
				),
				previewedDevice = parentApi.previewedDevice();

			if (navWrapperPadding.setting._dirty) {
				var padding = navWrapperPadding.setting.get();

				if ("sidebar" === newValue) {
					if (padding.hasOwnProperty(previewedDevice)) {
						$.each(padding[previewedDevice], function (prop, val) {
							$nav.css("padding-" + prop, val + "px");
						});
					}
				} else {
					$.each(
						["top", "right", "bottom", "left"],
						function (_, val) {
							$nav.css("padding-" + val, "");
						}
					);
				}
			}

			if (navWrapperMargin.setting._dirty) {
				var margin = navWrapperMargin.setting.get();

				if ("tab" === newValue) {
					if (margin.hasOwnProperty(previewedDevice)) {
						$.each(margin[previewedDevice], function (prop, val) {
							$navWrapper.css("margin-" + prop, val + "px");
						});
					}
				} else {
					$.each(
						["top", "right", "bottom", "left"],
						function (_, val) {
							$navWrapper.css("margin-" + val, "");
						}
					);
				}
			}

			if (navHoverColor.setting._dirty) {
				$navWrapper.find("a").mouseenter(function (e) {
					$(this).css("color", navHoverColor.setting.get());
				});
			}

			if (navHoverBackgroundColor.setting._dirty) {
				$navWrapper.find("a").mouseenter(function (e) {
					$(this).css(
						"background-color",
						navHoverBackgroundColor.setting.get()
					);
				});
			}

			if (navHoverBorderStyle.setting._dirty) {
				$navWrapper.find("a").mouseenter(function (e) {
					$(this).css(
						"border-style",
						navHoverBorderStyle.setting.get()
					);
				});
			}

			if (navHoverBorderColor.setting._dirty) {
				$navWrapper.find("a").mouseenter(function (e) {
					$(this).css(
						"border-color",
						navHoverBorderColor.setting.get()
					);
				});
			}

			if (navHoverBorderWidth.setting._dirty) {
				let newValue = navHoverBorderWidth.setting.get();

				if (typeof newValue != "object") {
					newValue = JSON.parse(newValue);
				}

				$navWrapper.find("a").mouseenter(function (e) {
					$.each(
						newValue,
						$.proxy(function (prop, val) {
							$(this).css(
								"border-" + prop + "-width",
								val + "px"
							);
						}, this)
					);
				});
			}

			if (navColor.setting._dirty) {
				$navWrapper.find("a").css("color", navColor.setting.get());
				$navWrapper.find("a").mouseleave(function (e) {
					$(this).css("color", navColor.setting.get());
				});
			}

			if (navBackgroundColor.setting._dirty) {
				$navWrapper
					.find("a")
					.css("background-color", navBackgroundColor.setting.get());
				$navWrapper.find("a").mouseleave(function (e) {
					$(this).css(
						"background-color",
						navBackgroundColor.setting.get()
					);
				});
			}

			if (navBorderStyle.setting._dirty) {
				$navWrapper
					.find("a")
					.css("border-style", navBorderStyle.setting.get());
				$navWrapper.find("a").mouseleave(function (e) {
					$(this).css("border-style", navBorderStyle.setting.get());
				});
			}

			if (navBorderColor.setting._dirty) {
				$navWrapper
					.find("a")
					.css("border-color", navBorderColor.setting.get());
				$navWrapper.find("a").mouseleave(function (e) {
					$(this).css("border-color", navBorderColor.setting.get());
				});
			}

			if (navBorderWidth.setting._dirty) {
				let newValue = navBorderWidth.setting.get();

				if (typeof newValue != "object") {
					newValue = JSON.parse(newValue);
				}

				$.each(newValue, function (prop, val) {
					$navWrapper
						.find("a")
						.css("border-" + prop + "-width", val + defaultUnit);
				});

				$navWrapper.find("a").mouseleave(function (e) {
					$.each(
						newValue,
						$.proxy(function (prop, val) {
							$(this).css(
								"border-" + prop + "-width",
								val + "px"
							);
						}, this)
					);
				});

				$navWrapper.find("a").mouseleave(function (e) {
					let newValue = navBorderWidth.setting.get();

					if (typeof newValue != "object") {
						newValue = JSON.parse(newValue);
					}

					$.each(newValue, function (prop, val) {
						$navWrapper
							.find("a")
							.css(
								"border-" + prop + "-width",
								val + defaultUnit
							);
					});

					$navWrapper.find("a").mouseleave(function (e) {
						$.each(
							newValue,
							$.proxy(function (prop, val) {
								$(this).css(
									"border-" + prop + "-width",
									val + "px"
								);
							}, this)
						);
					});
				});
			}
		});
	});

	/**
	 * Wrapper: sidebar position.
	 */
	api(setting + "[wrapper][sidebar_position]", function (value) {
		$container.attr("data-sidebar-position", value.get());

		value.bind(function (newValue) {
			$container.attr("data-sidebar-position", newValue);
		});
	});

	/**
	 * Wrapper: font family.
	 */
	api(setting + "[wrapper][font_family]", function (value) {
		if (parentApi.control(value.id).setting._dirty) {
			addGoogleFont(value.get());
			$container.css("font-family", value.get());
			$container.find("p").css("font-family", value.get());
			$container
				.find(".tgwc-field-title h3")
				.css("font-family", "inherit");
		}

		value.bind(function (newValue) {
			var paletteSetting = parentApi.control(
				"tgwc_customize[layout][menu_style]"
			);

			if ("legacy" !== paletteSetting.setting.get()) {
				return;
			}

			if ("" === newValue) {
				$container.css("font-family", "inherit");
				$container.find("p").css("font-family", "inherit");
				$container
					.find(".tgwc-field-title h3")
					.css("font-family", "inherit");
			} else {
				addGoogleFont(newValue);
				$container.css("font-family", newValue);
				$container.find("p").css("font-family", newValue);
				$container
					.find(".tgwc-field-title h3")
					.css("font-family", "inherit");
			}
		});
	});

	/**
	 * Wrapper: font size.
	 */
	api(setting + "[wrapper][font_size]", function (value) {
		if (parentApi.control(value.id).setting._dirty) {
			$container.css("font-size", value.get() + defaultUnit);
		}

		value.bind(function (newValue) {
			var paletteSetting = parentApi.control(
				"tgwc_customize[layout][menu_style]"
			);

			if ("legacy" !== paletteSetting.setting.get()) {
				return;
			}

			$container.css("font-size", newValue + defaultUnit);
			$container.find("p").css("font-size", newValue + defaultUnit);
		});
	});

	/**
	 * Wrapper: Background color.
	 */
	api(setting + "[wrapper][background_color]", function (value) {
		if (parentApi.control(value.id).setting._dirty) {
			$container.css("background-color", value.get());
		}

		value.bind(function (newValue) {
			$container.css("background-color", newValue);
		});
	});

	/**
	 * Wrapper: Padding.
	 */
	api(setting + "[wrapper][padding]", function (value) {
		$previewButtons.click(function (e) {
			var controlValue = value.get(),
				activeResponseDevice = $(this).data("device");

			$container.css("padding", "");
			if (typeof controlValue[activeResponseDevice] === undefined) {
				activeResponseDevice = "desktop";
			}

			$.each(controlValue[activeResponseDevice], function (prop, val) {
				$container.css("padding-" + prop, val + defaultUnit);
			});
		});

		if (parentApi.control(value.id).setting._dirty) {
			$previewButtons.filter(".active").trigger("click");
		}

		value.bind(function (newValue) {
			var activeResponseDevice = $previewButtons
				.filter(".active")
				.data("device");

			if (typeof newValue != "object") {
				newValue = JSON.parse(newValue);
			}

			$.each(newValue[activeResponseDevice], function (prop, val) {
				$container.css("padding-" + prop, val + defaultUnit);
			});
		});
	});

	/**
	 * Wrapper: Margin.
	 */
	api(setting + "[wrapper][margin]", function (value) {
		$previewButtons.click(function (e) {
			var controlValue = value.get(),
				activeResponseDevice = $(this).data("device");

			$container.css("margin", "");
			if (typeof controlValue[activeResponseDevice] === undefined) {
				activeResponseDevice = "desktop";
			}

			$.each(controlValue[activeResponseDevice], function (prop, val) {
				$container.css("margin-" + prop, val + defaultUnit);
			});
		});

		if (parentApi.control(value.id).setting._dirty) {
			$previewButtons.filter(".active").trigger("click");
		}

		value.bind(function (newValue) {
			var activeResponseDevice = $previewButtons
				.filter(".active")
				.data("device");

			if (typeof newValue != "object") {
				newValue = JSON.parse(newValue);
			}

			$.each(newValue[activeResponseDevice], function (prop, val) {
				$container.css("margin-" + prop, val + defaultUnit);
			});
		});
	});

	/**
	 * Color: heading color.
	 */
	api(setting + "[color][heading]", function (value) {
		if (parentApi.control(value.id).setting._dirty) {
			$container.find(":header").css("color", value.get());
		}

		value.bind(function (newValue) {
			$container.find(":header").css("color", newValue);
		});
	});

	/**
	 * Color: body color.
	 */
	api(setting + "[color][body]", function (value) {
		if (parentApi.control(value.id).setting._dirty) {
			$container.css("color", value.get());
		}

		value.bind(function (newValue) {
			$container.css("color", newValue);
		});
	});

	/**
	 * Color: link color.
	 */
	api(setting + "[color][link]", function (value) {
		if (parentApi.control(value.id).setting._dirty) {
			$anchors.css("color", value.get());
		}

		value.bind(function (newValue) {
			$anchors.css("color", newValue);
			$anchors.mouseleave(function (e) {
				$(this).css("color", newValue);
			});
		});
	});

	/**
	 * Color: link hover color.
	 */
	api(setting + "[color][link_hover]", function (value) {
		value.bind(function (newValue) {
			var linkControl = parentApi.control(setting + "[color][link]");

			if (!linkControl.setting._dirty) {
				$anchors.mouseleave(function (e) {
					$(this).css("color", "");
				});
			}

			$anchors.mouseenter(function (e) {
				$(this).css("color", newValue);
			});
		});
	});

	/**
	 * Avatar: Layout Style
	 */
	api(setting + "[avatar][layout]", function (value) {
		var align = ["left", "right", "center", "vertical"];
		$avatar.addClass("tgwc-user-avatar--" + value.get() + "-aligned");

		value.bind(function (newValue) {
			$.each(align, function (index, align) {
				var avatarClass = "tgwc-user-avatar--" + align + "-aligned";
				if ($avatar.hasClass(avatarClass)) {
					$avatar.removeClass(avatarClass);
					return false;
				}
			});

			$avatar.addClass("tgwc-user-avatar--" + newValue + "-aligned");
		});
	});

	/**
	 * Avatar: default.
	 */
	api(setting + "[avatar][default]", function (value) {
		function updateDefaultImage(value) {
			var $imageContainer = $($container).find(
				".tgwc-user-avatar-image-wrap"
			);
			var imgUrl = value;

			var newImg = new Image();
			newImg.src = imgUrl + "?t=" + Date.now();
			newImg.alt = "";
			newImg.width = 96;
			newImg.height = 96;

			newImg.onload = function () {
				$imageContainer.find("img").remove();

				$imageContainer.prepend(newImg);

				$imageContainer
					.find(".tgwc-remove-image")
					.css("display", "block");
			};
		}
		value.bind(updateDefaultImage);
	});
	/**
	 * Avatar: Username Style
	 */
	api(setting + "[avatar][username]", function (value) {
		$avatarUsername.toggleClass("tgwc-hide", !value.get());

		value.bind(function (newValue) {
			$avatarUsername.toggleClass("tgwc-hide", !newValue);
		});
	});

	/**
	 * Avatar:  Logout Style
	 */
	api(setting + "[avatar][logout]", function (value) {
		$avatarLogout.toggleClass("tgwc-hide", !value.get());

		value.bind(function (newValue) {
			$avatarLogout.toggleClass("tgwc-hide", !newValue);
		});
	});

	/**
	 * Avatar: Thumbnail Image Style.
	 */
	api(setting + "[avatar][type]", function (value) {
		var type = ["square", "circle"];
		$avatarType.addClass("tgwc-user-avatar-image-wrap--" + value.get());

		value.bind(function (newValue) {
			$.each(type, function (index, type) {
				var avatarClass = "tgwc-user-avatar-image-wrap--" + type;
				if ($avatarType.hasClass(avatarClass)) {
					$avatarType.removeClass(avatarClass);
					return false;
				}
			});

			$avatarType.addClass("tgwc-user-avatar-image-wrap--" + newValue);
		});
	});

	/**
	 * Avatar: Padding.
	 */
	api(setting + "[avatar][padding]", function (value) {
		$previewButtons.click(function (e) {
			var controlValue = value.get(),
				activeResponseDevice = $(this).data("device");

			$avatar.css("padding", "");
			if (typeof controlValue[activeResponseDevice] === undefined) {
				activeResponseDevice = "desktop";
			}

			$.each(controlValue[activeResponseDevice], function (prop, val) {
				$avatar.css("padding-" + prop, val + defaultUnit);
			});
		});

		if (parentApi.control(value.id).setting._dirty) {
			$previewButtons.filter(".active").trigger("click");
		}

		value.bind(function (newValue) {
			var activeResponseDevice = $previewButtons
				.filter(".active")
				.data("device");

			if (typeof newValue != "object") {
				newValue = JSON.parse(newValue);
			}

			$.each(newValue[activeResponseDevice], function (prop, val) {
				$avatar.css("padding-" + prop, val + defaultUnit);
			});
		});
	});

	/**
	 * Navigation -> General: Padding.
	 */
	api(setting + "[navigation][general][padding]", function (value) {
		$previewButtons.click(function (e) {
			var controlValue = value.get(),
				activeResponseDevice = $(this).data("device");

			$nav.css("padding", "");
			if (typeof controlValue[activeResponseDevice] === undefined) {
				activeResponseDevice = "desktop";
			}

			$.each(controlValue[activeResponseDevice], function (prop, val) {
				$navWrapper.find("a").css("padding-" + prop, val + defaultUnit);
			});
		});

		if (parentApi.control(value.id).setting._dirty) {
			$previewButtons.filter(".active").trigger("click");
		}

		value.bind(function (newValue) {
			var activeResponseDevice = $previewButtons
				.filter(".active")
				.data("device");

			if (typeof newValue != "object") {
				newValue = JSON.parse(newValue);
			}

			$.each(newValue[activeResponseDevice], function (prop, val) {
				$navWrapper.find("a").css("padding-" + prop, val + defaultUnit);
			});
		});
	});

	api(setting + "[navigation][general][wrapper_padding]", function (value) {
		$previewButtons.click(function (e) {
			var controlValue = value.get(),
				activeResponseDevice = $(this).data("device");

			$nav.css("padding", "");
			if (typeof controlValue[activeResponseDevice] === undefined) {
				activeResponseDevice = "desktop";
			}

			$.each(controlValue[activeResponseDevice], function (prop, val) {
				$nav.css("padding-" + prop, val + defaultUnit);
			});
		});

		if (parentApi.control(value.id).setting._dirty) {
			$previewButtons.filter(".active").trigger("click");
		}

		value.bind(function (newValue) {
			var activeResponseDevice = $previewButtons
				.filter(".active")
				.data("device");

			if (typeof newValue != "object") {
				newValue = JSON.parse(newValue);
			}

			$.each(newValue[activeResponseDevice], function (prop, val) {
				$nav.css("padding-" + prop, val + defaultUnit);
			});
		});
	});

	api(setting + "[navigation][general][wrapper_margin]", function (value) {
		$previewButtons.click(function (e) {
			var controlValue = value.get(),
				activeResponseDevice = $(this).data("device");

			$navWrapper.css("margin", "");
			if (typeof controlValue[activeResponseDevice] === undefined) {
				activeResponseDevice = "desktop";
			}

			$.each(controlValue[activeResponseDevice], function (prop, val) {
				$navWrapper.css("margin-" + prop, val + defaultUnit);
			});
		});

		if (parentApi.control(value.id).setting._dirty) {
			$previewButtons.filter(".active").trigger("click");
		}

		value.bind(function (newValue) {
			var activeResponseDevice = $previewButtons
				.filter(".active")
				.data("device");

			if (typeof newValue != "object") {
				newValue = JSON.parse(newValue);
			}

			$.each(newValue[activeResponseDevice], function (prop, val) {
				$navWrapper.css("margin-" + prop, val + defaultUnit);
			});
		});
	});

	/**
	 * Navigation -> Normal: Text color.
	 */
	api(setting + "[navigation][normal][color]", function (value) {
		if (parentApi.control(value.id).setting._dirty) {
			$navWrapper.find("li:not(.is-active) a").css("color", value.get());
		}

		value.bind(function (newValue) {
			$navWrapper.find("li:not(.is-active) a").css("color", newValue);
			$navWrapper.find("li:not(.is-active) a").mouseleave(function (e) {
				$(this).css("color", newValue);
			});
		});
	});

	/**
	 * Navigation -> Normal: Background color.
	 */
	api(setting + "[navigation][normal][background_color]", function (value) {
		if (parentApi.control(value.id).setting._dirty) {
			$navWrapper
				.find("li:not(.is-active) a")
				.css("background-color", value.get());
		}

		value.bind(function (newValue) {
			$navWrapper
				.find("li:not(.is-active) a")
				.css("background-color", newValue);
			$navWrapper.find("li:not(.is-active) a").mouseleave(function (e) {
				$(this).css("background-color", newValue);
			});
		});
	});

	/**
	 * Navigation -> Normal: Border.
	 */
	api(setting + "[navigation][normal][border_style]", function (value) {
		// if (parentApi.control(value.id).setting._dirty) {
		$navWrapper
			.find("li:not(.is-active) a")
			.css("border-style", value.get(0));
		// }

		value.bind(function (newValue) {
			$navWrapper
				.find("li:not(.is-active) a")
				.css("border-style", newValue);
			$navWrapper.find("li:not(.is-active) a").mouseleave(function (e) {
				$(this).css("border-style", newValue);
			});
		});
	});

	/**
	 * Navigation -> Normal: Border width.
	 */
	api(setting + "[navigation][normal][border_width]", function (value) {
		// if (parentApi.control(value.id).setting._dirty) {
		if (typeof value.get() != "object") {
			var newValue = JSON.parse(value.get());
			$.each(newValue, function (prop, val) {
				$navWrapper
					.find("li:not(.is-active) a")
					.css("border-" + prop + "-width", val + defaultUnit);
			});

			$navWrapper.find("li:not(.is-active) a").mouseleave(function (e) {
				$.each(
					newValue,
					$.proxy(function (prop, val) {
						$(this).css("border-" + prop + "-width", val + "px");
					}, this)
				);
			});
		}

		// }

		value.bind(function (newValue) {
			if (typeof newValue != "object") {
				newValue = JSON.parse(newValue);
			}

			$.each(newValue, function (prop, val) {
				$navWrapper
					.find("li:not(.is-active) a")
					.css("border-" + prop + "-width", val + defaultUnit);
			});

			$navWrapper.find("li:not(.is-active) a").mouseleave(function (e) {
				$.each(
					newValue,
					$.proxy(function (prop, val) {
						$(this).css("border-" + prop + "-width", val + "px");
					}, this)
				);
			});
		});
	});

	/**
	 * Navigation -> Normal: Border color.
	 */
	api(setting + "[navigation][normal][border_color]", function (value) {
		if (parentApi.control(value.id).setting._dirty) {
			$navWrapper
				.find("li:not(.is-active) a")
				.css("border-color", value.get());
		}
		value.bind(function (newValue) {
			$navWrapper
				.find("li:not(.is-active) a")
				.css("border-color", newValue);
			$navWrapper.find("li:not(.is-active) a").mouseleave(function (e) {
				$(this).css("border-color", newValue);
			});
		});
	});

	/**
	 * Navigation -> Active: Text color.
	 */
	api(setting + "[navigation][active][color]", function (value) {
		if (parentApi.control(value.id).setting._dirty) {
			$navWrapper.find(".is-active > a").css("color", value.get());
		}

		value.bind(function (newValue) {
			$navWrapper.find(".is-active > a").css("color", newValue);
			$navWrapper.find(".is-active > a").mouseleave(function (e) {
				$(this).css("color", newValue);
			});
		});
	});

	/**
	 * Navigation -> Active: Background color.
	 */
	api(setting + "[navigation][active][background_color]", function (value) {
		if (parentApi.control(value.id).setting._dirty) {
			$navWrapper
				.find(".is-active > a")
				.css("background-color", value.get());
		}

		value.bind(function (newValue) {
			$navWrapper
				.find(".is-active > a")
				.css("background-color", newValue);
			$navWrapper.find(".is-active > a").mouseleave(function (e) {
				$(this).css("background-color", newValue);
			});
		});
	});

	/**
	 * Navigation -> Active: Border.
	 */
	api(setting + "[navigation][active][border_style]", function (value) {
		if (parentApi.control(value.id).setting._dirty) {
			$navWrapper
				.find(".is-active > a")
				.css("border-style", value.get(0));
		}

		value.bind(function (newValue) {
			$navWrapper.find(".is-active > a").css("border-style", newValue);
			$navWrapper.find(".is-active > a").mouseleave(function (e) {
				$(this).css("border-style", newValue);
			});
		});
	});

	/**
	 * Navigation -> Active: Border width.
	 */
	api(setting + "[navigation][active][border_width]", function (value) {
		// if (parentApi.control(value.id).setting._dirty) {
		if (typeof value.get() != "object") {
			var newValue = JSON.parse(value.get());
		}

		$.each(newValue, function (prop, val) {
			$navWrapper
				.find(".is-active > a")
				.css("border-" + prop + "-width", val + defaultUnit);
		});

		$navWrapper.find(".is-active > a").mouseleave(function (e) {
			$.each(
				newValue,
				$.proxy(function (prop, val) {
					$(this).css("border-" + prop + "-width", val + "px");
				}, this)
			);
		});
		// }

		value.bind(function (newValue) {
			if (typeof newValue != "object") {
				newValue = JSON.parse(newValue);
			}

			$.each(newValue, function (prop, val) {
				$navWrapper
					.find(".is-active > a")
					.css("border-" + prop + "-width", val + defaultUnit);
			});

			$navWrapper.find(".is-active > a").mouseleave(function (e) {
				$.each(
					newValue,
					$.proxy(function (prop, val) {
						$(this).css("border-" + prop + "-width", val + "px");
					}, this)
				);
			});
		});
	});

	/**
	 * Navigation -> Active: Border color.
	 */
	api(setting + "[navigation][active][border_color]", function (value) {
		if (parentApi.control(value.id).setting._dirty) {
			$navWrapper.find(".is-active > a").css("border-color", value.get());
		}
		value.bind(function (newValue) {
			$navWrapper.find(".is-active > a").css("border-color", newValue);
			$navWrapper.find(".is-active > a").mouseleave(function (e) {
				$(this).css("border-color", newValue);
			});
		});
	});

	/**
	 * Navigation -> Hover: Text color.
	 */
	api(setting + "[navigation][hover][color]", function (value) {
		var handler = function (newValue) {
			var normalControl = parentApi.control(
				setting + "[navigation][normal][color]"
			);

			if (!normalControl.setting._dirty) {
				$navWrapper.find("a").mouseleave(function (e) {
					$(this).css("color", "");
				});
			}

			$navWrapper.find("a").mouseenter(function (e) {
				$(this).css("color", newValue);
			});
		};

		if (parentApi.control(value.id).setting._dirty) {
			handler(value.get());
		}

		value.bind(handler);
	});

	/**
	 * Navigation -> Hover: Background color.
	 */
	api(setting + "[navigation][hover][background_color]", function (value) {
		var handler = function (newValue) {
			var normalControl = parentApi.control(
				setting + "[navigation][normal][background_color]"
			);

			if (!normalControl.setting._dirty) {
				$navWrapper.find("a").mouseleave(function (e) {
					$(this).css("background-color", "");
				});
			}

			$navWrapper.find("a").mouseenter(function (e) {
				$(this).css("background-color", newValue);
			});
		};

		if (parentApi.control(value.id).setting._dirty) {
			handler(value.get());
		}

		value.bind(handler);
	});

	/**
	 * Navigation -> Hover: Border style.
	 */
	api(setting + "[navigation][hover][border_style]", function (value) {
		var handler = function (newValue) {
			var normalControl = parentApi.control(
				setting + "[navigation][normal][border_style]"
			);

			if (!normalControl.setting._dirty) {
				$navWrapper.find("a").mouseleave(function (e) {
					$(this).css("border-style", "");
				});
			}

			$navWrapper.find("a").mouseenter(function (e) {
				$(this).css("border-style", newValue);
			});
		};

		if (parentApi.control(value.id).setting._dirty) {
			handler(value.get());
		}
		value.bind(handler);
	});

	/**
	 * Navigation -> Hover: Border width.
	 */
	api(setting + "[navigation][hover][border_width]", function (value) {
		var handler = function (newValue) {
			var normalControl = parentApi.control(
				setting + "[navigation][normal][border_width]"
			);

			if (typeof newValue !== "object") {
				newValue = JSON.parse(newValue);
			}

			if (!normalControl.setting._dirty) {
				$navWrapper.find("a").mouseleave(function (e) {
					$(this).css({
						"border-top-width": "",
						"border-right-width": "",
						"border-bottom-width": "",
						"border-left-width": "",
					});
				});
			}

			$navWrapper.find("a").mouseenter(function (e) {
				var newValue =
					wp.customize.get()[
						setting + "[navigation][hover][border_width]"
					];

				if (typeof newValue !== "object") {
					newValue = JSON.parse(newValue);
				}

				$.each(
					newValue,
					$.proxy(function (prop, val) {
						$(this).css("border-" + prop + "-width", val + "px");
					}, this)
				);
			});
		};

		if (parentApi.control(value.id).setting._dirty) {
			handler(value.get());
		}

		value.bind(handler);
	});

	/**
	 * Content: Background Color.
	 */
	api(setting + "[content][background_color]", function (value) {
		var $contentWrapper = $container.find(".woocommerce-MyAccount-content");
		if (parentApi.control(value.id).setting._dirty) {
			$contentWrapper.css("background-color", value.get());
		}

		value.bind(function (newValue) {
			$contentWrapper.css("background-color", newValue);
		});
	});

	/**
	 * Content: Margin.
	 */
	api(setting + "[content][margin]", function (value) {
		var $contentWrapper = $container.find(".woocommerce-MyAccount-content");
		$previewButtons.click(function (e) {
			var controlValue = value.get(),
				activeResponseDevice = $(this).data("device");

			$contentWrapper.css("margin", "");
			if (typeof controlValue[activeResponseDevice] === undefined) {
				activeResponseDevice = "desktop";
			}

			$.each(controlValue[activeResponseDevice], function (prop, val) {
				$contentWrapper.css("margin-" + prop, val + defaultUnit);
			});
		});

		if (parentApi.control(value.id).setting._dirty) {
			$previewButtons.filter(".active").trigger("click");
		}

		value.bind(function (newValue) {
			var activeResponseDevice = $previewButtons
				.filter(".active")
				.data("device");

			if (typeof newValue != "object") {
				newValue = JSON.parse(newValue);
			}

			$.each(newValue[activeResponseDevice], function (prop, val) {
				$contentWrapper.css("margin-" + prop, val + defaultUnit);
			});
		});
	});

	/**
	 * Content: Padding.
	 */
	api(setting + "[content][padding]", function (value) {
		var $contentWrapper = $container.find(".woocommerce-MyAccount-content");
		$previewButtons.click(function (e) {
			var controlValue = value.get(),
				activeResponseDevice = $(this).data("device");

			$contentWrapper.css("padding", "");
			if (typeof controlValue[activeResponseDevice] === undefined) {
				activeResponseDevice = "desktop";
			}

			$.each(controlValue[activeResponseDevice], function (prop, val) {
				$contentWrapper.css("padding-" + prop, val + defaultUnit);
			});
		});

		if (parentApi.control(value.id).setting._dirty) {
			$previewButtons.filter(".active").trigger("click");
		}

		value.bind(function (newValue) {
			var activeResponseDevice = $previewButtons
				.filter(".active")
				.data("device");

			if (typeof newValue != "object") {
				newValue = JSON.parse(newValue);
			}

			$.each(newValue[activeResponseDevice], function (prop, val) {
				$contentWrapper.css("padding-" + prop, val + defaultUnit);
			});
		});
	});

	/**
	 * Navigation -> Hover: Border color.
	 */
	api(setting + "[navigation][hover][border_color]", function (value) {
		var handler = function (newValue) {
			var normalControl = parentApi.control(
				setting + "[navigation][normal][border_color]"
			);

			if (!normalControl.setting._dirty) {
				$navWrapper.find("a").mouseleave(function (e) {
					$(this).css("border-color", "");
				});
			}

			$navWrapper.find("a").mouseenter(function (e) {
				$(this).css("border-color", newValue);
			});
		};

		if (parentApi.control(value.id).setting._dirty) {
			handler(value.get());
		}

		value.bind(handler);
	});

	/**
	 * Input Field: General.
	 */
	api(setting + "[input_field][general][padding]", function (value) {
		$previewButtons.click(function (e) {
			var controlValue = value.get(),
				activeResponseDevice = $(this).data("device");

			$inputs.css("padding", "");
			if (typeof controlValue[activeResponseDevice] === undefined) {
				activeResponseDevice = "desktop";
			}

			$.each(controlValue[activeResponseDevice], function (prop, val) {
				$inputs.css("padding-" + prop, val + defaultUnit);
			});
		});

		if (parentApi.control(value.id).setting._dirty) {
			$previewButtons.filter(".active").trigger("click");
		}

		value.bind(function (newValue) {
			var activeResponseDevice = $previewButtons
				.filter(".active")
				.data("device");

			if (typeof newValue != "object") {
				newValue = JSON.parse(newValue);
			}

			$.each(newValue[activeResponseDevice], function (prop, val) {
				$inputs.css("padding-" + prop, val + defaultUnit);
			});
		});
	});

	/**
	 * Input Field -> Normal: Text color.
	 */
	api(setting + "[input_field][normal][color]", function (value) {
		var handler = function (newValue) {
			$inputs.css("color", newValue);
			$inputs.focusout(function (e) {
				$(this).css("color", newValue);
			});
		};

		if (parentApi.control(value.id).setting._dirty) {
			handler(value.get());
		}
		value.bind(handler);
	});

	/**
	 * Input Field -> Normal: Background color.
	 */
	api(setting + "[input_field][normal][background_color]", function (value) {
		var handler = function (newValue) {
			$inputs.css("background-color", newValue);
			$inputs.focusout(function (e) {
				$(this).css("background-color", newValue);
			});
		};

		if (parentApi.control(value.id).setting._dirty) {
			handler(value.get());
		}

		value.bind(handler);
	});

	/**
	 * Input Field -> Normal: Border.
	 */
	api(setting + "[input_field][normal][border_style]", function (value) {
		var handler = function (newValue) {
			$inputs.css("border-style", newValue);
			$inputs.focusout(function (e) {
				$(this).css("border-style", newValue);
			});
		};

		if (parentApi.control(value.id).setting._dirty) {
			handler(value.get());
		}

		value.bind(handler);
	});

	/**
	 * Input Field -> Normal: Border width.
	 */
	api(setting + "[input_field][normal][border_width]", function (value) {
		var handler = function (newValue) {
			if (typeof newValue != "object") {
				var newValue = JSON.parse(newValue);
			}

			$.each(newValue, function (prop, val) {
				$inputs.css("border-" + prop + "-width", val + defaultUnit);
			});

			$inputs.focusout(function (e) {
				$.each(
					newValue,
					$.proxy(function (prop, val) {
						$(this).css("border-" + prop + "-width", val + "px");
					}, this)
				);
			});
		};

		// if (parentApi.control(value.id).setting._dirty) {
		handler(value.get());
		// }

		value.bind(handler);
	});

	/**
	 * Input Field -> Normal: Border color.
	 */
	api(setting + "[input_field][normal][border_color]", function (value) {
		var handler = function (newValue) {
			$inputs.css("border-color", newValue);
			$inputs.focusout(function (e) {
				$(this).css("border-color", newValue);
			});
		};

		if (parentApi.control(value.id).setting._dirty) {
			handler(value.get());
		}
		value.bind(handler);
	});

	/**
	 * Input Field -> Focus: Text color.
	 */
	api(setting + "[input_field][focus][color]", function (value) {
		var handler = function (newValue) {
			$inputs.focus(function (e) {
				$(this).css("color", newValue);
			});
		};

		if (parentApi.control(value.id).setting._dirty) {
			handler(value.get());
		}

		value.bind(handler);
	});

	/**
	 * Input Field -> Focus: Background color.
	 */
	api(setting + "[input_field][focus][background_color]", function (value) {
		var handler = function (newValue) {
			var normalControl = parentApi.control(
				setting + "[input_field][normal][background_color]"
			);

			if (!normalControl.setting._dirty) {
				$inputs.focusout(function (e) {
					$(this).css("background-color", "");
				});
			}

			$inputs.focus(function (e) {
				$(this).css("background-color", newValue);
			});
		};

		if (parentApi.control(value.id).setting._dirty) {
			handler(value.get());
		}

		value.bind(handler);
	});

	/**
	 * Input Field -> Focus: Border style.
	 */
	api(setting + "[input_field][focus][border_style]", function (value) {
		var handler = function (newValue) {
			var normalControl = parentApi.control(
				setting + "[input_field][normal][border_style]"
			);

			if (!normalControl.setting._dirty) {
				$inputs.focusout(function (e) {
					$(this).css("border-style", "");
				});
			}

			$inputs.focus(function (e) {
				$(this).css("border-style", newValue);
			});
		};

		if (parentApi.control(value.id).setting._dirty) {
			handler(value.get());
		}

		value.bind(handler);
	});

	/**
	 * Input Field -> Focus: Border width.
	 */
	api(setting + "[input_field][focus][border_width]", function (value) {
		var handler = function (newValue) {
			var normalControl = parentApi.control(
				setting + "[input_field][normal][border_width]"
			);

			if (typeof newValue !== "object") {
				newValue = JSON.parse(newValue);
			}

			if (!normalControl.setting._dirty) {
				$inputs.focusout(function (e) {
					$(this).css({
						"border-top-width": "",
						"border-right-width": "",
						"border-bottom-width": "",
						"border-left-width": "",
					});
				});
			}

			$inputs.focus(function (e) {
				var newValue =
					wp.customize.get()[
						setting + "[input_field][focus][border_width]"
					];

				if (typeof newValue !== "object") {
					newValue = JSON.parse(newValue);
				}

				$.each(
					newValue,
					$.proxy(function (prop, val) {
						$(this).css("border-" + prop + "-width", val + "px");
					}, this)
				);
			});
		};

		if (parentApi.control(value.id).setting._dirty) {
			handler(value.get());
		}

		value.bind(handler);
	});

	/**
	 * Input Field -> Focus: Border color.
	 */
	api(setting + "[input_field][focus][border_color]", function (value) {
		var handler = function (newValue) {
			var normalControl = parentApi.control(
				setting + "[input_field][normal][border_color]"
			);

			if (!normalControl.setting._dirty) {
				$inputs.focusout(function (e) {
					$(this).css("border-color", "");
				});
			}

			$inputs.focus(function (e) {
				$(this).css("border-color", newValue);
			});
		};

		if (parentApi.control(value.id).setting._dirty) {
			handler(value.get());
		}

		value.bind(handler);
	});

	/**
	 * Buttons -> General: Font Size.
	 */
	api(setting + "[button][general][font_size]", function (value) {
		var handler = function (newValue) {
			$buttons.css("font-size", newValue + defaultUnit);
		};

		if (parentApi.control(value.id).setting._dirty) {
			handler(value.get());
		}

		value.bind(handler);
	});

	/**
	 * Buttons -> General: Font Size.
	 */
	api(setting + "[button][general][line_height]", function (value) {
		var handler = function (newValue) {
			$buttons.css("line-height", newValue);
		};

		if (parentApi.control(value.id).setting._dirty) {
			handler(value.get());
		}

		value.bind(handler);
	});

	/**
	 * Buttons -> General: Padding.
	 */
	api(setting + "[button][general][padding]", function (value) {
		$previewButtons.click(function (e) {
			var controlValue = control._value,
				activeResponseDevice = $(this).data("device");

			$buttons.css("padding", "");
			if (typeof controlValue[activeResponseDevice] === undefined) {
				activeResponseDevice = "desktop";
			}

			$.each(controlValue[activeResponseDevice], function (prop, val) {
				$buttons.css("padding-" + prop, val + defaultUnit);
			});
		});

		var control = parentApi.control(value.id).setting;
		if (control._dirty) {
			$previewButtons.filter(".active").trigger("click");
		}

		value.bind(function (newValue) {
			var activeResponseDevice = $previewButtons
				.filter(".active")
				.data("device");

			if (typeof newValue != "object") {
				newValue = JSON.parse(newValue);
			}

			$.each(newValue[activeResponseDevice], function (prop, val) {
				$buttons.css("padding-" + prop, val + defaultUnit);
			});
		});
	});

	/**
	 * Buttons -> General: Margin.
	 */
	api(setting + "[button][general][margin]", function (value) {
		$previewButtons.click(function (e) {
			var controlValue = control._value;
			activeResponseDevice = $(this).data("device");

			$buttons.css("margin", "");
			if (typeof controlValue[activeResponseDevice] === undefined) {
				activeResponseDevice = "desktop";
			}

			$.each(controlValue[activeResponseDevice], function (prop, val) {
				$buttons.css("margin-" + prop, val + defaultUnit);
			});
		});

		var control = parentApi.control(value.id).setting;
		if (control._dirty) {
			$previewButtons.filter(".active").trigger("click");
		}

		value.bind(function (newValue) {
			var activeResponseDevice = $previewButtons
				.filter(".active")
				.data("device");

			if (typeof newValue != "object") {
				newValue = JSON.parse(newValue);
			}

			$.each(newValue[activeResponseDevice], function (prop, val) {
				$buttons.css("margin-" + prop, val + defaultUnit);
			});
		});
	});

	/**
	 * Buttons -> Normal: Text color.
	 */
	api(setting + "[button][normal][color]", function (value) {
		var handler = function (newValue) {
			$buttons.css("color", newValue);
			$buttons.mouseleave(function (e) {
				$(this).css("color", newValue);
			});
		};

		if (parentApi.control(value.id).setting._dirty) {
			handler(value.get());
		}

		value.bind(handler);
	});

	/**
	 * Buttons -> Normal: Background color.
	 */
	api(setting + "[button][normal][background_color]", function (value) {
		var handler = function (newValue) {
			$buttons.css("background-color", newValue);
			$buttons.mouseleave(function (e) {
				$(this).css("background-color", newValue);
			});
		};

		if (parentApi.control(value.id).setting._dirty) {
			handler(value.get());
		}

		value.bind(handler);
	});

	/**
	 * Buttons -> Normal: Border Color.
	 */
	api(setting + "[button][normal][border_color]", function (value) {
		var handler = function (newValue) {
			$buttons.css("border-color", newValue);
			$buttons.mouseleave(function (e) {
				$(this).css("border-color", newValue);
			});
		};

		if (parentApi.control(value.id).setting._dirty) {
			handler(value.get());
		}

		value.bind(handler);
	});

	/**
	 * Buttons -> Normal: Border width.
	 */
	api(setting + "[button][normal][border_width]", function (value) {
		var handler = function (newValue) {
			if (typeof newValue != "object") {
				newValue = JSON.parse(newValue);
			}

			$.each(newValue, function (prop, val) {
				$buttons.css("border-" + prop + "-width", val + defaultUnit);
			});

			$buttons.mouseleave(function (e) {
				$.each(
					newValue,
					$.proxy(function (prop, val) {
						$(this).css("border-" + prop + "-width", val + "px");
					}, this)
				);
			});
		};

		if (parentApi.control(value.id).setting._dirty) {
			handler(value.get());
		}

		value.bind(handler);
	});

	/**
	 * Buttons -> Normal: Border style.
	 */
	api(setting + "[button][normal][border_style]", function (value) {
		var handler = function (newValue) {
			$buttons.css("border-style", newValue);
			$buttons.mouseleave(function (e) {
				$(this).css("border-style", newValue);
			});
		};

		if (parentApi.control(value.id).setting._dirty) {
			handler(value.get());
		}

		value.bind(handler);
	});

	/**
	 * Button -> Hover: Text color.
	 */
	api(setting + "[button][hover][color]", function (value) {
		var handler = function (newValue) {
			var normalControl = parentApi.control(
				setting + "[button][normal][color]"
			);

			if (!normalControl.setting._dirty) {
				$buttons.mouseleave(function (e) {
					$(this).css("color", "");
				});
			}

			$buttons.mouseenter(function (e) {
				$(this).css("color", newValue);
			});
		};

		if (parentApi.control(value.id).setting._dirty) {
			handler(value.get());
		}

		value.bind(handler);
	});

	/**
	 * Button -> Hover: Background color.
	 */
	api(setting + "[button][hover][background_color]", function (value) {
		var handler = function (newValue) {
			var normalControl = parentApi.control(
				setting + "[button][normal][background_color]"
			);

			if (!normalControl.setting._dirty) {
				$buttons.mouseleave(function (e) {
					$(this).css("background-color", "");
				});
			}

			$buttons.mouseenter(function (e) {
				$(this).css("background-color", newValue);
			});
		};

		if (parentApi.control(value.id).setting._dirty) {
			handler(value.get());
		}

		value.bind(handler);
	});

	/**
	 * Button -> Hover: Border style.
	 */
	api(setting + "[button][hover][border_style]", function (value) {
		var handler = function (newValue) {
			var normalControl = parentApi.control(
				setting + "[button][normal][border_style]"
			);

			if (!normalControl.setting._dirty) {
				$buttons.mouseleave(function (e) {
					$(this).css("border-style", "");
				});
			}

			$buttons.mouseenter(function (e) {
				$(this).css("border-style", newValue);
			});
		};

		if (parentApi.control(value.id).setting._dirty) {
			handler(value.get());
		}

		value.bind(handler);
	});

	/**
	 * Button -> Hover: Border width.
	 */
	api(setting + "[button][hover][border_width]", function (value) {
		var handler = function (newValue) {
			var normalControl = parentApi.control(
				setting + "[button][normal][border_width]"
			);

			if (typeof newValue !== "object") {
				newValue = JSON.parse(newValue);
			}

			if (!normalControl.setting._dirty) {
				$buttons.mouseleave(function (e) {
					$(this).css({
						"border-top-width": "",
						"border-right-width": "",
						"border-bottom-width": "",
						"border-left-width": "",
					});
				});
			}

			$buttons.mouseenter(function (e) {
				var newValue =
					wp.customize.get()[
						setting + "[button][hover][border_width]"
					];

				if (typeof newValue !== "object") {
					newValue = JSON.parse(newValue);
				}

				$.each(
					newValue,
					$.proxy(function (prop, val) {
						$(this).css("border-" + prop + "-width", val + "px");
					}, this)
				);
			});
		};

		if (parentApi.control(value.id).setting._dirty) {
			handler(value.get());
		}

		value.bind(handler);
	});

	/**
	 * Button -> Hover: Border color.
	 */
	api(setting + "[button][hover][border_color]", function (value) {
		var handler = function (newValue) {
			var normalControl = parentApi.control(
				setting + "[button][normal][border_color]"
			);

			if (!normalControl.setting._dirty) {
				$buttons.mouseleave(function (e) {
					$(this).css("border-color", "");
				});
			}

			$buttons.mouseenter(function (e) {
				$(this).css("border-color", newValue);
			});
		};

		if (parentApi.control(value.id).setting._dirty) {
			handler(value.get());
		}

		value.bind(handler);
	});
})(jQuery, wp.customize, _tgwcCustomizePreviewL10n);
