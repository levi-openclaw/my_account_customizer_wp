import React from "react";
import $ from "jquery";
import { createRoot } from "react-dom/client";
import Control from "./Control";
import "./style.scss";

const api = window.wp.customize;
(() => {
	api.controlConstructor["tgwc-color_palette"] = api.Control.extend({
		ready: function () {
			var control = this;

			control.container.on("click", ".color-palette", function () {
				var $currentGroup = $(this).closest(
					".customize-control-tgwc-color_palette"
				);

				$(".customize-control-tgwc-color_palette")
					.not($currentGroup)
					.find("label")
					.removeClass("tgwc-active-color-palette");
				$(".customize-control-tgwc-color_palette")
					.not($currentGroup)
					.find('input[type="checkbox"]')
					.prop("checked", false);
				$(this)
					.find('input[type="checkbox"]')
					.prop("checked", true)
					.change();
			});

			control.container.find(".color-palette-edit-interface").hide();

			control.container.on(
				"click",
				".color-palette-edit-icon",
				function () {
					var paletteContainer = $(this).closest(
						".color-palette-container"
					);

					var iconElement = $(this);
					var editInterface = paletteContainer.find(
						".color-palette-edit-interface"
					);

					if (editInterface.length) {
						editInterface.show();
					}

					if (iconElement.html() === "✎") {
						iconElement.html("✖");
					} else {
						iconElement.html("✎");
						editInterface.hide();
					}
				}
			);
		},
		renderContent: function () {
			const container = this.container[0];
			if (!container) return;

			const root = createRoot(container);
			container.__root = root;

			const LAYOUT_SETTING_ID = "tgwc_customize[layout][menu_style]";

			const render = (layoutValue) => {
				root.render(
					<Control
						control={this}
						customizer={api}
						layout={layoutValue}
					/>
				);
			};

			api(LAYOUT_SETTING_ID, (layoutSetting) => {
				render(layoutSetting.get());

				layoutSetting.bind((newVal) => {
					render(newVal);
				});
			});
		},
	});
})();
