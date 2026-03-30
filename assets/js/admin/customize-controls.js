/* global wp, _wpCustomizeBackground, _tgwcCustomizeControlsL10n */
(function ($, api, data) {
	"use strict";

	var $resetBtn = $(
			`<button class="button button-secondary tgwc-customizer-reset" style="float: right; margin-top: 9px">${data.resetText}</button>`
		),
		$customizeHeaderActions = $("#customize-header-actions");

	// Modify customize info.
	api.bind("ready", function () {
		$("#customize-info")
			.find(".panel-title.site-title")
			.text(data.panelTitle);
		$("#customize-info")
			.find(".customize-panel-description:first")
			.text(data.panelDescription);
		api.section("custom_css").active(false);

		$customizeHeaderActions.append($resetBtn);
		$customizeHeaderActions.append(
			"<style>.tgwc-customizer-reset.updating-message:before { color: #2271b1 !important; }</style>"
		);

		$resetBtn.on("click", function (e) {
			e.preventDefault();
			if (confirm(data.resetConfirm)) {
				$.ajax({
					type: "POST",
					url: ajaxurl,
					data: {
						action: "tgwc_customizer_reset",
						security: data.nonce,
					},
					beforeSend: function () {
						$resetBtn.addClass("updating-message");
					},
				})
					.done(function () {
						api.state("saved").set(true);
						location.reload();
					})
					.always(function () {
						$resetBtn.removeClass("updating-message");
					});
			}
		});
	});

	/**
	 * A toggle switch control.
	 *
	 * @class    wp.customize.ToggleControl
	 * @augments wp.customize.Control
	 */
	api.ToggleControl = api.Control.extend({
		/**
		 * Initialize behaviors.
		 *
		 * @returns {void}
		 */
		ready: function () {
			var control = this;

			control.container.on("change", "input:checkbox", function () {
				var value = !!this.checked;
				control.setting.set(value);
			});
		},
	});

	/**
	 * A range slider control.
	 *
	 * @class    wp.customize.SliderControl
	 * @augments wp.customize.Class
	 */
	api.SliderControl = api.Control.extend({
		/**
		 * Initialize behaviors.
		 *
		 * @returns {void}
		 */
		ready: function () {
			var control = this;
			var $container = control.container;
			var $slider = $container.find(".tgwc-customizer-slider");
			var $input = $container.find(
				'.tgwc-customizer-slider-input input[type="number"]'
			);

			if (!$slider.length || !$input.length) return;

			$slider.html('<input type="range" class="slider-range">');
			var $range = $slider.find(".slider-range");

			$range.attr({
				min: $input.attr("min") || 12,
				max: $input.attr("max") || 100,
				step: $input.attr("step") || 1,
				value: $input.val() || 12,
			});

			$range.on("input", function () {
				$input.val(this.value).trigger("change");
				control.setting.set(this.value);
			});

			$input.on("input change", function () {
				$range.val(this.value);
				control.setting.set(this.value);
			});

			$container.on("click", ".reset", function (e) {
				e.preventDefault();
				var defaultValue =
					$input.attr("value") || $input.attr("min") || 12;
				$range.val(defaultValue);
				$input.val(defaultValue).trigger("change");
				control.setting.set(defaultValue);
			});
		},
	});

	/**
	 * A enhanced select2 control.
	 *
	 * @class    wp.customize.Select2Control
	 * @augments wp.customize.Class
	 */
	api.Select2Control = api.Control.extend({
		/**
		 * Initialize behaviors.
		 *
		 * @returns {void}
		 */
		ready: function ready() {
			var control = this,
				$container = control.container,
				$select_input = $container.find(".tgwc-select2");

			// Enhanced Select2.
			$select_input.select2({
				minimumResultsForSearch: 10,
				allowClear: !!$select_input.data("allow_clear"),
				placeholder: $select_input.data("placeholder"),
			});
		},
	});

	/**
	 * A dimension control.
	 *
	 * @class    wp.customize.DimensionControl
	 * @augments wp.customize.Class
	 */
	api.DimensionControl = api.Control.extend({
		/**
		 * Initialize behaviors.
		 *
		 * @returns {void}
		 */
		ready: function () {
			var control = this,
				$container = control.container,
				$inputs = $container.find(".dimension-input");

			// Hide except first responsive item
			control.container.find(".responsive-tabs li:not(:first)").hide();

			control.container.on(
				"keyup input",
				".dimension-input",
				function () {
					var this_input = $(this),
						key = this_input.attr("name"),
						min = parseInt(this_input.attr("min")),
						max = parseInt(this_input.attr("max"));

					// Number validation for min or max value.
					if (this_input.val() < min) {
						this_input.val(this_input.attr("min"));
					}
					if (this_input.val() > max) {
						this_input.val(this_input.attr("max"));
					}
					if (control.is_anchor()) {
						$inputs.each(function (index, input) {
							$(input).val(this_input.val());
							control.saveValue(
								$(input).attr("name"),
								this_input.val()
							);
						});
					} else {
						control.saveValue(key, this_input.val());
					}
				}
			);

			control.container.on(
				"change",
				'.dimension-unit-item input[type="radio"]',
				function () {
					control.saveValue("unit", $(this).val());
				}
			);

			control.container.on("change", ".dimension-anchor", function () {
				if ($(this).is(":checked")) {
					$(this)
						.parent("label")
						.removeClass("unlinked")
						.addClass("linked");
					$inputs.first().trigger("keyup");
				} else {
					$(this)
						.parent("label")
						.removeClass("linked")
						.addClass("unlinked");
				}
			});

			control.container.on(
				"change",
				'.responsive-tab-item input[type="radio"]',
				function () {
					var value = control.get_value();
					var this_value = $(this).val();

					if (value[this_value] !== undefined) {
						$inputs.each(function (index, input) {
							$(input).val(
								value[this_value][$(input).attr("name")]
							);
						});
						control.container
							.find(
								'.dimension-unit-item input[value="' +
									value[this_value].unit +
									'"]'
							)
							.attr("checked", "checked");
					} else {
						$inputs.val("");
					}
					control.saveValue(
						"top",
						$container.find('input[name="top"]').val()
					);
				}
			);

			// Hide show buttons.
			control.container.on(
				"click",
				'.responsive-tab-item input[type="radio"]',
				function () {
					var $this = $(this);
					var current_tab = $this.val();
					var $all_responsive_tabs = $("#customize-controls")
						.find(
							'.responsive-tab-item input[type="radio"][value="' +
								current_tab +
								'"]'
						)
						.prop("checked", true);
					$all_responsive_tabs.each(function (index, element) {
						var $tab_item = $(element)
							.closest(".responsive-tab-item")
							.closest("li");
						if ($tab_item.index() === 0) {
							$tab_item.siblings().toggle();
						}
					});
					// Set the toggled device.
					api.previewedDevice.set(current_tab);
				}
			);
		},

		/**
		 * Returns anchor status.
		 */
		is_anchor: function () {
			return $(this.container).find(".dimension-anchor").is(":checked");
		},

		/**
		 * Returns responsive selected.
		 */
		selected_responsive: function () {
			return $(this.container)
				.find('.responsive-tab-item input[type="radio"]:checked')
				.val();
		},

		/**
		 * Returns Unit selected.
		 */
		selected_unit: function () {
			return $(this.container)
				.find('.dimension-unit-item input[type="radio"]:checked')
				.val();
		},

		/**
		 * Returns Value Object.
		 */
		get_value: function () {
			return Object.assign({}, this.setting._value);
		},

		/**
		 * Saves the value.
		 */
		saveValue: function (property, value) {
			var control = this,
				input = control.container.find(".dimension-hidden-value"),
				val = control.get_value();

			if (control.params.responsive === true) {
				if (undefined === val[control.selected_responsive()]) {
					val[control.selected_responsive()] = {};
				}

				val[control.selected_responsive()][property] = value;
				if (control.params.unit_choices.length > 0) {
					val[control.selected_responsive()].unit =
						control.selected_unit();
				}
			} else {
				val[property] = value;
				if (Object.keys(control.params.unit_choices).length > 0) {
					val.unit = control.selected_unit();
				}
			}

			jQuery(input).val(JSON.stringify(val)).trigger("change");
			control.setting.set(val);
		},
	});

	/**
	 * An image checkbox control.
	 *
	 * @class    wp.customize.ImageCheckboxControl
	 * @augments wp.customize.Class
	 */
	api.ImageCheckboxControl = api.Control.extend({
		/**
		 * Initialize behaviors.
		 *
		 * @returns {void}
		 */
		ready: function ready() {
			var control = this,
				$container = control.container;

			$container.on("change", 'input[type="checkbox"]', function () {
				control.saveValue($(this).val(), $(this).is(":checked"));
			});
		},

		/**
		 * Saves the value.
		 */
		saveValue: function (property, value) {
			var control = this,
				input = control.container.find(".image-checkbox-hidden-value"),
				val = control.params.value;

			val[property] = value;
			val = Object.assign({}, val);

			jQuery(input).val(JSON.stringify(val)).trigger("change");
			control.setting.set(val);
		},
	});

	api.controlConstructor = $.extend(api.controlConstructor, {
		"tgwc-color": api.ColorControl,
		"tgwc-toggle": api.ToggleControl,
		"tgwc-slider": api.SliderControl,
		"tgwc-select2": api.Select2Control,
		"tgwc-dimension": api.DimensionControl,
		"tgwc-background": api.BackgroundControl,
		"tgwc-image_checkbox": api.ImageCheckboxControl,
		"tgwc-background_image": api.BackgroundImageControl,
	});
})(jQuery, wp.customize, _tgwcControlsData);

/**
 * Custom js for control.
 */
jQuery(function ($) {
	var api = wp.customize,
		setting = "tgwc_customize";

	/**
	 * Layout: Menu Style.
	 */
	api.control(setting + "[layout][menu_position]", function (control) {
		var accordianDefaultState = api.control(
			setting + "[navigation][group_accordion_default_state]"
		);

		control.setting.bind("change", function (value) {
			var active = "tab" === value;
			var groupCount = _tgwcControlsData.group_count;

			accordianDefaultState.active(!active && groupCount > 0);
		});
	});

	/**
	 * Navigation -> normal: Border.
	 */
	api.control(
		setting + "[navigation][normal][border_style]",
		function (control) {
			var borderWidthControl = api.control(
				setting + "[navigation][normal][border_width]"
			);
			// var borderColorControl = api.control(
			// 	setting + "[navigation][normal][border_color]"
			// );

			control.setting.bind("change", function (value) {
				var active = "none" !== value;
				borderWidthControl.active(active);
				// borderColorControl.active(active);
			});
		}
	);

	/**
	 * Navigation -> hover: Border.
	 */
	api.control(
		setting + "[navigation][hover][border_style]",
		function (control) {
			var borderWidthControl = api.control(
				setting + "[navigation][hover][border_width]"
			);
			var borderColorControl = api.control(
				setting + "[navigation][hover][border_color]"
			);

			control.setting.bind("change", function (value) {
				var active = "none" !== value;
				borderWidthControl.active(active);
				// borderColorControl.active(active);
			});
		}
	);

	/**
	 * Input Field -> Normal: Border
	 */
	api.control(
		setting + "[input_field][normal][border_style]",
		function (control) {
			var borderWidthControl = api.control(
				setting + "[input_field][normal][border_width]"
			);
			// var
			// 	borderColorControl = api.control(
			// 		setting + "[input_field][normal][border_color]"
			// 	);

			control.setting.bind("change", function (value) {
				var active = "none" !== value;
				borderWidthControl.active(active);
				// borderColorControl.active(active);
			});
		}
	);

	/**
	 * Input Field -> Focus : Border
	 */
	api.control(
		setting + "[input_field][focus][border_style]",
		function (control) {
			var borderWidthControl = api.control(
					setting + "[input_field][focus][border_width]"
				),
				borderColorControl = api.control(
					setting + "[input_field][focus][border_color]"
				);

			control.setting.bind("change", function (value) {
				var active = "none" !== value;
				borderWidthControl.active(active);
				// borderColorControl.active(active);
			});
		}
	);

	/**
	 * Buttons -> Normal: Border
	 */
	api.control(setting + "[button][normal][border_style]", function (control) {
		var borderWidthControl = api.control(
				setting + "[button][normal][border_width]"
			),
			borderColorControl = api.control(
				setting + "[button][normal][border_color]"
			);

		control.setting.bind("change", function (value) {
			var active = "none" !== value;
			borderWidthControl.active(active);
			// borderColorControl.active(active);
		});
	});

	/**
	 * Buttons -> Focus : Border
	 */
	api.control(setting + "[button][hover][border_style]", function (control) {
		var borderWidthControl = api.control(
				setting + "[button][hover][border_width]"
			),
			borderColorControl = api.control(
				setting + "[button][hover][border_color]"
			);

		control.setting.bind("change", function (value) {
			var active = "none" !== value;
			borderWidthControl.active(active);
			// borderColorControl.active(active);
		});
	});

	function updateMenuMarginPadding(menuStyle, menuPosition) {
		let paddingSetting = api(
			setting + "[spacing][menu_container][padding]"
		);
		let marginSetting = api(setting + "[spacing][menu_container][margin]");

		const paddingInputs = {
			top: document.getElementById(
				`${setting}[spacing][menu_container][padding]-top`
			),
			left: document.getElementById(
				`${setting}[spacing][menu_container][padding]-left`
			),
			right: document.getElementById(
				`${setting}[spacing][menu_container][padding]-right`
			),
			bottom: document.getElementById(
				`${setting}[spacing][menu_container][padding]-bottom`
			),
		};
		const marginInputs = {
			top: document.getElementById(
				`${setting}[spacing][menu_container][margin]-top`
			),
			left: document.getElementById(
				`${setting}[spacing][menu_container][margin]-left`
			),
			right: document.getElementById(
				`${setting}[spacing][menu_container][margin]-right`
			),
			bottom: document.getElementById(
				`${setting}[spacing][menu_container][margin]-bottom`
			),
		};

		// Clone current values to avoid mutating directly
		let currentPadding = JSON.parse(JSON.stringify(paddingSetting.get()));
		let currentMargin = JSON.parse(JSON.stringify(marginSetting.get()));

		if (menuStyle === "minimal") {
			if ("vertical-left" === menuPosition) {
				currentPadding.desktop = {
					top: "30",
					left: "0",
					bottom: "30",
					right: "0",
				};
				currentMargin.desktop = {
					top: "0",
					left: "0",
					bottom: "0",
					right: "0",
				};
				currentPadding.tablet = {
					top: "30",
					left: "16",
					bottom: "30",
					right: "0",
				};
				currentPadding.mobile = {
					top: "30",
					left: "16",
					bottom: "30",
					right: "0",
				};
			} else if ("vertical-right" === menuPosition) {
				currentPadding.desktop = {
					top: "0",
					left: "30",
					bottom: "0",
					right: "30",
				};
				currentMargin.desktop = {
					top: "0",
					left: "0",
					bottom: "0",
					right: "0",
				};
			} else if ("tab" === menuPosition) {
				currentPadding.desktop = {
					top: "0",
					left: "0",
					bottom: "0",
					right: "0",
				};
				currentMargin.desktop = {
					top: "0",
					left: "0",
					bottom: "0",
					right: "0",
				};
			}
		} else if (menuStyle === "modern") {
			if (
				"vertical-left" === menuPosition ||
				"vertical-right" === menuPosition
			) {
				currentPadding.desktop = {
					top: "12",
					left: "12",
					bottom: "12",
					right: "12",
				};
				currentMargin.desktop = {
					top: "0",
					left: "0",
					bottom: "0",
					right: "0",
				};
			} else {
				currentPadding.desktop = {
					top: "12",
					left: "12",
					bottom: "12",
					right: "12",
				};
				currentMargin.desktop = {
					top: "0",
					left: "0",
					bottom: "0",
					right: "0",
				};
			}
		} else if (menuStyle === "classic") {
			if (
				"vertical-left" === menuPosition ||
				"vertical-right" === menuPosition
			) {
				currentPadding.desktop = {
					top: "0",
					left: "0",
					bottom: "0",
					right: "0",
				};
				currentMargin.desktop = {
					top: "30",
					left: "0",
					bottom: "30",
					right: "0",
				};
			} else {
				currentPadding.desktop = {
					top: "16",
					left: "16",
					bottom: "28",
					right: "16",
				};
				currentMargin.desktop = {
					top: "0",
					left: "0",
					bottom: "0",
					right: "0",
				};
			}
		}

		paddingSetting.set(currentPadding);
		marginSetting.set(currentMargin);

		if (marginInputs.top)
			marginInputs.top.value = currentMargin.desktop.top;
		if (marginInputs.left)
			marginInputs.left.value = currentMargin.desktop.left;
		if (marginInputs.right)
			marginInputs.right.value = currentMargin.desktop.right;
		if (marginInputs.bottom)
			marginInputs.bottom.value = currentMargin.desktop.bottom;

		// Trigger change events
		Object.values(marginInputs).forEach((input) => {
			if (input)
				input.dispatchEvent(new Event("change", { bubbles: true }));
		});

		if (paddingInputs.top)
			paddingInputs.top.value = currentPadding.desktop.top;
		if (paddingInputs.left)
			paddingInputs.left.value = currentPadding.desktop.left;
		if (paddingInputs.right)
			paddingInputs.right.value = currentPadding.desktop.right;
		if (paddingInputs.bottom)
			paddingInputs.bottom.value = currentPadding.desktop.bottom;

		// Trigger change events
		Object.values(paddingInputs).forEach((input) => {
			if (input)
				input.dispatchEvent(new Event("change", { bubbles: true }));
		});
	}
	api.control(setting + "[layout][menu_style]", function (control) {
		var $li = jQuery("#accordion-section-tgwc_customize\\[legacy\\]");
		control.setting.bind(function (menuStyle) {
			let menuPosition = api(setting + "[layout][menu_position]").get();
			updateMenuMarginPadding(menuStyle, menuPosition);
			// wp.customize.previewer.refresh();

			var $li = jQuery("#accordion-section-tgwc_customize\\[legacy\\]");

			if (menuStyle === "legacy") {
				$li.show();
			} else {
				$li.hide();
			}
		});

		if (control.setting.get() === "legacy") {
			$li.show();
		} else {
			$li.hide();
		}
	});
	api.control(setting + "[layout][menu_position]", function (control) {
		control.setting.bind(function (menuPosition) {
			let menuStyle = api(setting + "[layout][menu_style]").get();
			updateMenuMarginPadding(menuStyle, menuPosition);
		});
	});
});
