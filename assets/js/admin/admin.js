/**
 * Javascript related to admin pages.
 */

"use strict";

/**
 * Custom tab functionality.
 */
jQuery(function ($) {
	var currentHref = "",
		$tabs = $("#tgwc-tabs"),
		$lis = $tabs.find(".dd-item"),
		$deleteDialog = $("#tgwc-dialog-delete"),
		$addNewTabDialog = $("#tgwc-dialog-add-tab");

	$(window).on("load added_endpoint", function () {
		var $endpointEnable = $('input[name*="enable"]');

		// $endpointEnable.on("change", function () {
		// 	var $endpointSlug = $(this).closest(".tgwc-tabs-panel").prop("id"),
		// 		$endpointItem = $('.dd-item[data-id="' + $endpointSlug + '"]');

		// 	if ($(this).prop("checked")) {
		// 		$endpointItem
		// 			.find(".tgwc-sidenav-tab-anchor span:nth-child(2)")
		// 			.hide();
		// 	} else {
		// 		$endpointItem
		// 			.find(".tgwc-sidenav-tab-anchor span:nth-child(2)")
		// 			.show();
		// 	}
		// });
	});

	$.each($lis, function (index, li) {
		var $a = $(li).find("a"),
			href = $a.attr("href"),
			id = $(li).data("id");

		let endpoint = window.tgwc_admin.active_endpoint;

		if (id === endpoint) {
			var $input = $(document)
				.find("#tgwc-active-endpoint")
				.find("input");
			if ($input.length) {
				$($input).val(id);
			}

			var childMenuWrap = $(li).closest(".child-menu-wrap");
			if (childMenuWrap.length > 0) {
				var parentMenu = childMenuWrap
					.closest("li")
					.find(".tgwc-sidenav-tab-action-wrap");
				parentMenu.addClass("active");
			}
			$(li).find(".tgwc-sidenav-tab-action-wrap").addClass("active");
			$(href).addClass("active");
			$(href).show();
			currentHref = href;

			return;
		} else {
			$(href).hide();
		}
	});

	/**
	 * Handle the endpoint slug change and append current slug to active endpoint input.
	 */
	$(document).on("keyup", ".tgwc-endpoint-slug", function () {
		var slug = $(this).val();
		$(document).find("#tgwc-active-endpoint").find("input").val(slug);
	});

	$tabs.on("click", ".dd-item a", function (e) {
		e.preventDefault();
		e.stopPropagation();

		var href = $(this).attr("href");
		var $tabContainer = $(href);

		if (currentHref === href) {
			return;
		}

		// Remove active class.
		$tabs.find(".tgwc-sidenav-tab-action-wrap").removeClass("active");
		$(currentHref).removeClass("active");

		// Add active classes to the tab and tab content.
		$(this)
			.parents(".tgwc-sidenav-tab-action-wrap")
			.first()
			.addClass("active");
		$(href).addClass("active");

		var childMenuWrap = $(this).closest(".child-menu-wrap");
		if (childMenuWrap.length > 0) {
			var parentMenu = childMenuWrap.closest(".dd-item");
			parentMenu
				.find(".tgwc-sidenav-tab-action-wrap:first")
				.addClass("active");
		}

		// Fade in and fadeOut the tab container.
		$(currentHref).fadeOut(
			$.proxy(function () {
				$(this).fadeIn();
			}, $tabContainer)
		);

		var $input = $(document).find("#tgwc-active-endpoint").find("input");
		if ($input.length) {
			var id = $(this).parents(".dd-item").data("id");
			$($input).val(id);
		}

		currentHref = href;
	});

	$tabs.on("click", ".tgwc-add-tab-btn", function (e) {
		e.preventDefault();

		$(document).find(".tgwc-sidenav").addClass("tgwc-sidenav--collapsed");
		$(document)
			.find(".tgwc-tabs-with-sidenav")
			.removeClass("tgwc-sidecontent-over-shadow");
		$(document)
			.find(".tgwc-sidenav-toggle")
			.toggleClass("tgwc-sidenav-toggle--collapsed");

		$addNewTabDialog.dialog({
			resizable: false,
			height: "auto",
			width: 400,
			modal: true,
			autoOpen: true,
			dialogClass: "tgwc-add-tab-btn",
			open: function () {
				$(this)
					.parent(".ui-dialog")
					.find(".tgwc-button--danger:eq(0)")
					.focus();
			},
			buttons: [],
		});
	});

	/**
	 * Handle the endpoints deletion.
	 */
	$tabs.on("click", ".tgwc-delete-endpoints", function (e) {
		e.preventDefault();
		var isFreeEndpoint = $(this).data("is_free") || false;

		$(document).find(".tgwc-sidenav").addClass("tgwc-sidenav--collapsed");
		$(document)
			.find(".tgwc-tabs-with-sidenav")
			.removeClass("tgwc-sidecontent-over-shadow");
		$(document)
			.find(".tgwc-sidenav-toggle")
			.toggleClass("tgwc-sidenav-toggle--collapsed");

		$deleteDialog.dialog({
			resizable: false,
			height: "auto",
			width: 400,
			modal: true,
			autoOpen: true,
			dialogClass: "tgwc-delete-endpoints",
			open: function () {
				$(this)
					.parent(".ui-dialog")
					.find(".tgwc-button--danger:eq(0)")
					.focus();
				if (!$(this).parent().find(".tgwc-poup-icon-wrap").length) {
					$(this)
						.parent()
						.find(".ui-dialog-title")
						.prepend(
							"<div class='tgwc-poup-icon-wrap'>" +
								tgwc.deleteIcon +
								"</div>"
						);
				}
			},
			buttons: [
				{
					text: window.tgwc.i18n.cancel,
					class: "tgwc-button tgwc-button--link tgwc-endpoint-dialog-form-cancel",
					tabindex: 2,
					click: function () {
						$(this).dialog("close");
					},
				},
				{
					text: window.tgwc.i18n.delete,
					class: "tgwc-button tgwc-button--danger",
					tabindex: 1,
					click: $.proxy(function () {
						$deleteDialog.dialog("close");

						if (isFreeEndpoint) {
							$(document)
								.find("#tgwc-free-endpoint-state")
								.find("input")
								.val("deleted");
						}

						var topPanel = $(this).closest(
								".tgwc-tabs-with-sidenav"
							),
							slug = $(this).data("slug"),
							$panel = topPanel.find("#" + slug),
							$tab = $(this).closest("li"),
							$tabSibling = $tab.siblings(".dd-item").first();

						if (!$tabSibling.length) {
							$tabSibling = $tab.parents(".dd-item").first();
						}

						$tabSibling.first().find("a").first().trigger("click");
						$tab.fadeOut(function () {
							$(this).remove();
							$panel.remove();
						});
					}, this),
				},
			],
		});
	});

	jQuery(document).ready(function ($) {
		$(document).on(
			"click",
			".tgwc_custom_icon_uploader_box, .tgwc_browse_files, .tgwc_uploader_edit_action",
			function (e) {
				e.preventDefault();
				e.stopPropagation();

				if ($(this).hasClass("has_icon")) {
					return;
				}

				var $box = $(this).closest(".tgwc_custom_icon_uploader_box");
				var $input = $box.find(".tgwc-custom-icon-id");
				var $preview = $box.find(".tgwc-custom-icon-preview");

				var frame = wp.media({
					title: "Select or Upload Icon",
					button: { text: "Use this icon" },
					multiple: false,
				});

				frame.on("select", function () {
					var attachment = frame
						.state()
						.get("selection")
						.first()
						.toJSON();
					$input.val(attachment.id);

					if ($preview.find(".attachment-thumbnail").length) {
						$preview.find(".attachment-thumbnail").remove();
					}

					if (attachment.sizes && attachment.sizes.thumbnail) {
						$preview.append(
							'<img class="attachment-thumbnail size-thumbnail" src="' +
								attachment.sizes.thumbnail.url +
								'" />'
						);
					} else {
						$preview.append(
							'<img class="attachment-thumbnail size-thumbnail" src="' +
								attachment.url +
								'" />'
						);
					}
					$preview
						.siblings(
							".tgwc_custom_icon_uploader_inner_content_wrapper "
						)
						.addClass("tgwc_hidden")
						.addClass("has_icon");
					$preview
						.closest(".tgwc_custom_icon_uploader_box ")
						.addClass("has_icon");
					$preview.removeClass("tgwc_hidden");
				});

				frame.open();
			}
		);

		$(".tgwc_custom_icon_uploader_box").each(function () {
			var $box = $(this);
			var $input = $box.find(".tgwc-custom-icon-id");
			var $preview = $box.find(".tgwc-custom-icon-preview");

			$box.on(
				"drag dragstart dragend dragover dragenter dragleave drop",
				function (e) {
					e.preventDefault();
					e.stopPropagation();
				}
			)
				.on("dragover dragenter", function () {
					$box.addClass("is-dragover");
				})
				.on("dragleave dragend drop", function () {
					$box.removeClass("is-dragover");
				})
				.on("drop", function (e) {
					/**
					 * Stop when icon is already.
					 */
					if ($box.hasClass("has_icon")) {
						return;
					}
					var droppedFiles = e.originalEvent.dataTransfer.files;
					if (droppedFiles.length > 0) {
						uploadDroppedFile(droppedFiles[0], $input, $preview);
					}
				});
		});

		function uploadDroppedFile(file, $input, $preview) {
			var $box = $input.closest(".tgwc_custom_icon_uploader_box");
			var $spinner = $box.find(".tgwc-loader");

			$spinner.show();

			var data = new FormData();
			data.append("file", file);
			data.append("action", "tgwc_handle_dropped_icon");
			data.append("security", tgwc_admin.tgwc_upload_nonce);
			data.append(
				"endpoint_key",
				$input
					.closest(".tgwc_custom_icon_uploader_box")
					.data("endpoint-key")
			);

			$.ajax({
				url: tgwc_admin.ajaxurl,
				type: "POST",
				data: data,
				contentType: false,
				processData: false,
				success: function (response) {
					if (response.success) {
						var attachmentId = response.data.attachment.id;
						if (attachmentId) {
							$input.val(attachmentId);
							wp.media
								.attachment(attachmentId)
								.fetch()
								.then(function (attachment) {
									if (
										$preview.find(".attachment-thumbnail")
											.length
									) {
										$preview
											.find(".attachment-thumbnail")
											.remove();
									}

									if (
										attachment.sizes &&
										attachment.sizes.thumbnail
									) {
										$preview.append(
											'<img class="attachment-thumbnail size-thumbnail" src="' +
												attachment.sizes.thumbnail.url +
												'" />'
										);
									} else {
										$preview.append(
											'<img class="attachment-thumbnail size-thumbnail" src="' +
												attachment.url +
												'" />'
										);
									}
									$spinner.hide();
									$preview
										.siblings(
											".tgwc_custom_icon_uploader_inner_content_wrapper "
										)
										.addClass("tgwc_hidden")
										.addClass("has_icon");
									$preview
										.closest(
											".tgwc_custom_icon_uploader_box "
										)
										.addClass("has_icon");
									$preview.removeClass("tgwc_hidden");
								});
						}
					} else {
						alert("Error: " + response.data.message);
					}
				},
				error: function () {
					alert("Error uploading file");
				},
			});
		}

		$(document).on("click", ".tgwc_uploader_delete_action", function (e) {
			e.preventDefault();
			e.stopPropagation();
			console.log($(this).closest(".tgwc-custom-icon-preview"));

			var $preview = $(this).closest(".tgwc-custom-icon-preview");
			var $input = $preview.siblings(".tgwc-custom-icon-id");

			console.log($preview);

			$preview
				.siblings(".tgwc_custom_icon_uploader_inner_content_wrapper ")
				.removeClass("has_icon")
				.removeClass("tgwc_hidden");
			$preview
				.closest(".tgwc_custom_icon_uploader_box  ")
				.removeClass("has_icon");
			$input.val("");
			$preview.addClass("tgwc_hidden");
			$preview.find(".attachment-thumbnail").remove();
		});

		//Handle click on icon type.
		$(document).on(
			"change",
			".tgwc_choose_icon_type_radio_input",
			function () {
				var $el = $(this),
					chooseType = $el.val(),
					endpointType = $el.data("endpoint_type");

				$el.closest(".tgwc_choose_icon_type_inner_wrapper")
					.addClass("active")
					.siblings()
					.removeClass("active");

				if ("choose_icon" === chooseType) {
					$("#tgwc_choose_icon_" + endpointType).removeClass(
						"tgwc_hidden"
					);
					$("#tgwc_custom_icon_uploader_" + endpointType).addClass(
						"tgwc_hidden"
					);
				} else if ("custom_icon_upload" === chooseType) {
					$("#tgwc_choose_icon_" + endpointType).addClass(
						"tgwc_hidden"
					);
					$("#tgwc_custom_icon_uploader_" + endpointType).removeClass(
						"tgwc_hidden"
					);
				}
			}
		);

		$(document).on(
			"click",
			".tgwc_choose_icon_type_inner_wrapper",
			function (e) {
				if (e.target.tagName !== "INPUT") {
					var $radio = $(this).find('input[type="radio"]');
					$radio.prop("checked", true).trigger("change");
				}
			}
		);
	});
});

/**
 * Initialize select an icon list.
 *
 * @see https://stackoverflow.com/questions/15041058/select2-performance-for-large-set-of-items
 */
(function ($, tgwc) {
	var $icons = $('select[name$="[icon]"]'),
		pageSize = 50;

	$.fn.select2.amd.require(
		["select2/data/array", "select2/utils"],
		function (ArrayData, Utils) {
			function CustomData($element, options) {
				CustomData.__super__.constructor.call(this, $element, options);
			}

			Utils.Extend(CustomData, ArrayData);

			CustomData.prototype.query = function (params, callback) {
				var results = [];
				if (params.term && "" !== params.term) {
					results = _.filter(tgwc.icons, function (e) {
						return (
							e.text
								.toUpperCase()
								.indexOf(params.term.toUpperCase()) >= 0
						);
					});
				} else {
					results = tgwc.icons;
				}

				if (!("page" in params)) {
					params.page = 1;
				}

				var data = {};
				data.results = results.slice(
					(params.page - 1) * pageSize,
					params.page * pageSize
				);
				data.pagination = {};
				data.pagination.more = params.page * pageSize < results.length;
				callback(data);
			};

			$.each($icons, function (index, icon) {
				$(icon).select2({
					placeholder: tgwc.i18n.selectAnIcon,
					allowClear: true,
					ajax: {},
					dataAdapter: CustomData,
					escapeMarkup: function (markup) {
						return markup;
					},
				});

				var selectedIcon = $(icon).data("selected");
				if ("" !== selectedIcon || undefined !== selectedIcon) {
					var text = selectedIcon.replace("fas fa-", "");
					text = text.replace("-", " ");
					text = text.charAt(0).toUpperCase() + text.slice(1);
					text = `<i class="${selectedIcon}"></i> ${text}`;

					var newOption = new Option(text, selectedIcon, true, true);
					$(icon).append(newOption).trigger("change");
				}
			});
		}
	);
})(jQuery, window.tgwc);

/**
 * Handle addition of endpoint, links and group.
 */
jQuery(function ($) {
	var $tabs = $("#tgwc-tabs"),
		$endpointDialog = $("#tgwc-endpoint-dialog"),
		$endpointDialogType = $("#tgwc-endpoint-dialog-type"),
		$endpointDialogName = $("#tgwc-endpoint-dialog-name"),
		$endpointActions = $(".tgwc-endpoint-actions");

	/**
	 * Handle click on 'Add endpoint'.
	 */
	$endpointActions.on("click", "button", function (e) {
		e.preventDefault();

		if ($(this).hasClass("tgwc-add-tab-btn")) {
			$(document)
				.find(".tgwc-sidenav")
				.addClass("tgwc-sidenav--collapsed");
			$(document)
				.find(".tgwc-tabs-with-sidenav")
				.removeClass("tgwc-sidecontent-over-shadow");
			$(document)
				.find(".tgwc-sidenav-toggle")
				.toggleClass("tgwc-sidenav-toggle--collapsed");

			$("#tgwc-dialog-add-tab").dialog({
				resizable: false,
				height: "auto",
				width: 400,
				modal: true,
				autoOpen: true,
				dialogClass: "tgwc-add-tab-btn",
				open: function () {
					$(this)
						.parent(".ui-dialog")
						.find(".tgwc-button--danger:eq(0)")
						.focus();
				},
				buttons: [],
			});
			return;
		}

		$(".ui-dialog-content").dialog("close");

		var type = $(this).data("type");
		$endpointDialogType.val(type);
		var icon =
			"endpoint" == type
				? tgwc.endpointIcon
				: "link" === type
				? tgwc.linkIcon
				: tgwc.groupIcon;

		$endpointDialog.dialog({
			draggable: false,
			resizable: false,
			autoOpen: true,
			modal: true,
			title: `Add ${type}`,
			minWidth: 460,
			buttons: [
				{
					text: window.tgwc.i18n.cancel,
					class: "tgwc-button tgwc-button--link tgwc-endpoint-dialog-form-cancel",
					tabindex: 2,
					click: function () {
						$(this).dialog("close");
					},
				},
				{
					text: `${window.tgwc.i18n.add} ${type}`,
					class: "tgwc-button tgwc-button--primary tgwc-endpoint-dialog-form-add",
					tabindex: 1,
					click: function () {
						if (addTab(this)) {
							$(this).dialog("close");
						}
					},
				},
			],
			open: function () {
				if (!$(this).parent().find(".tgwc-poup-icon-wrap").length) {
					$(this)
						.parent()
						.find(".ui-dialog-title")
						.prepend(
							"<div class='tgwc-poup-icon-wrap " +
								type +
								"'>" +
								icon +
								"</div>"
						);
				}
			},
			close: function () {
				$endpointDialog.find("form").get(0).reset();
				$endpointDialog.find(".tgwc-error-message").html("");
			},
		});
	});

	/**
	 * Handle enter key press in the endpoint dialog.
	 */
	$("body").on("keydown", "#tgwc-endpoint-dialog", function (e) {
		if (e.keyCode === $.ui.keyCode.ENTER) {
			if (addTab(this)) {
				$(this).dialog("close");
			}
			e.preventDefault();
			e.stopPropagation();
		}
	});

	$(document.body).on("click", "#tgwc-smart-tags-selector", function () {
		var $this = $(this);
		$(this)
			.siblings(".select-smart-tags")
			.select2({
				placeholder: "",
				templateResult: function (data, container) {
					if ($this.siblings(".tgwc_advance_setting").length > 0) {
						if (data.element) {
							$(container).addClass("tgwc-select-smart-tag");
						}
					}
					return data.text;
				},
			})
			.on("select2:open", function () {
				// Add a class when dropdown opens
				$(".select2-container--open .select2-dropdown").addClass(
					"tgwc-select2-dropdown"
				);
			});

		$(this).siblings(".select2-container").addClass("tgwc-hide-select2");

		$(this).siblings(".select-smart-tags").select2("open");
		$(this)
			.siblings(".select2-container")
			.find(".select2-selection__rendered")
			.show();
		$(this)
			.siblings(".select2-container")
			.find(".select2-selection--open")
			.show();

		var buttonOffset = $(this).offset(),
			buttonOffsetTop = Math.round(
				buttonOffset.top + $(this).innerHeight()
			),
			buttonOffsetRight = Math.round(buttonOffset.left);

		var select2_container = $(
			".select2-container--open:not(.tgwc-hide-select2)"
		);
		select2_container.css({
			top: buttonOffsetTop,
			left: buttonOffsetRight - $(this).innerHeight() - 10,
		});

		var newDiv =
			'<span class="tgwc-select2-title"><p>' +
			tgwc_admin.smart_tags_dropdown_title +
			"</p></span>";
		$(newDiv).insertBefore(select2_container.find(".select2-search"));

		var searchField = select2_container.find(".select2-search__field");
		searchField.attr(
			"placeholder",
			tgwc_admin.smart_tags_dropdown_search_placeholder
		);
		searchField.before(
			'<span class="search-icon"><svg xmlns="http://www.w3.org/2000/svg" height="16px" width="16px" viewBox="0 0 24 24" fill="#a1a4b9"><path d="M21.71,20.29,18,16.61A9,9,0,1,0,16.61,18l3.68,3.68a1,1,0,0,0,1.42,0A1,1,0,0,0,21.71,20.29ZM11,18a7,7,0,1,1,7-7A7,7,0,0,1,11,18Z"></path></svg></span>'
		);

		$(".select-smart-tags").on("change", function (event) {
			event.preventDefault();
			event.stopPropagation();

			const selectedValue = $(this).val();

			if (!selectedValue) return;

			const $textarea = $(this)
				.closest(".wp-editor-wrap")
				.find("textarea");
			const editorId = $textarea.attr("id");

			const editor = window.tinymce && window.tinymce.get(editorId);

			if (editor && !editor.isHidden()) {
				editor.insertContent(selectedValue);
			} else {
				const textarea = $textarea[0];
				const startPos = textarea.selectionStart;
				const endPos = textarea.selectionEnd;

				textarea.value =
					textarea.value.substring(0, startPos) +
					selectedValue +
					textarea.value.substring(endPos);

				textarea.selectionStart = textarea.selectionEnd =
					startPos + selectedValue.length;

				$textarea.trigger("input");
			}

			$(this).val("").trigger("change.select2");
		});
	});

	/**
	 * Add tab.
	 *
	 * @since 0.1.0
	 * @return void
	 */
	function addTab(form) {
		var name = $endpointDialogName.val(),
			type = $endpointDialogType.val(),
			prefixType = type.substring(0, 1).toUpperCase(),
			slug = textToID(name);

		// Generate the slug even if th slug is already registered.
		slug = generateEndpointSlug(slug);

		var icon =
			"endpoint" == type
				? tgwc.endpointIcon
				: "group" == type
				? tgwc.groupIcon
				: tgwc.linkIcon;

		var li = [
			`<li class="dd-item ${type}" data-id="${slug}" data-type="${type}">
    <div class="tgwc-sidenav-tab-anchor-wrap">
	<div class="tgwc_dd_custom_handle" >
	<svg class="dd-custom-handle" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
	  <path d="m20 11 .102.005a1 1 0 0 1 0 1.99L20 13H4a1 1 0 1 1 0-2h16Zm0-6 .102.005a1 1 0 0 1 0 1.99L20 7H4a1 1 0 0 1 0-2h16Zm0 12 .102.005a1 1 0 0 1 0 1.99L20 19H4a1 1 0 1 1 0-2h16Z"></path>
	</svg>
	</div>
      <div class="tgwc-sidenav-tab-action-wrap">`,

			`<a class="tgwc-sidenav-tab-anchor" href="#${slug}">
        <div class="type-wrap">
          <div class="${type}">
            ${icon}
            <span class="label">${name}</span>
          </div>
        </div>
      </a>
      <div class="actions-wrap">
        <div class="tgwc-toggle-section">
          <span class="tgwc-toggle-form">
            <input type="checkbox"
              checked
              name="tgwc_endpoints[${slug}][enable]"
              style="min-width: 350px;" />
            <span class="slider round"></span>
          </span>
        </div>
        <button type="button" class="tgwc-button tgwc-button--small tgwc-delete-endpoints" data-slug="${slug}">
          ${tgwc.deleteIcon}
        </button>
		</div>${
			type === "group"
				? '<ul class="dd-list child-menu-wrap no-child"><div class="dd-drop-item-container">Drag items here</div></ul>'
				: ""
		}`,

			`</div>
    </div>
  </li>`,
		].join("");

		if ("" === name) {
			$(form)
				.find(".tgwc-error-message")
				.html("Empty name is not allowed.");
			return false;
		}

		var template = wp.template(`tgwc-${type}`);

		// Add tab and tab template.
		$tabs.find(".dd-list").first().append(li);
		$tabs.find(".tgwc-sidecontent").append(
			template({
				slug: slug,
				text: name,
				type: type,
				i18n: window.tgwc.i18n,
			})
		);
		$(`#tgwc-${slug}`).hide(0);

		$(document).trigger("added_endpoint");

		$.fn.select2.amd.require(
			["select2/data/array", "select2/utils"],
			function (ArrayData, Utils) {
				var pageSize = 50;
				function CustomData($element, options) {
					CustomData.__super__.constructor.call(
						this,
						$element,
						options
					);
				}

				Utils.Extend(CustomData, ArrayData);

				CustomData.prototype.query = function (params, callback) {
					var results = [];
					if (params.term && "" !== params.term) {
						results = _.filter(window.tgwc.icons, function (e) {
							return (
								e.text
									.toUpperCase()
									.indexOf(params.term.toUpperCase()) >= 0
							);
						});
					} else {
						results = window.tgwc.icons;
					}

					if (!("page" in params)) {
						params.page = 1;
					}

					var data = {};
					data.results = results.slice(
						(params.page - 1) * pageSize,
						params.page * pageSize
					);
					data.pagination = {};
					data.pagination.more =
						params.page * pageSize < results.length;
					callback(data);
				};

				$(`select[name$="tgwc_endpoints[${slug}][icon]"]`).select2({
					placeholder: window.tgwc.i18n.selectAnIcon,
					allowClear: true,
					ajax: {},
					dataAdapter: CustomData,
					escapeMarkup: function (markup) {
						return markup;
					},
				});
			}
		);

		$(`select[name$="tgwc_endpoints[${slug}][user_role]"]`).select2({
			placeholder: tgwc.i18n.selectUserRoles,
			allowClear: true,
			multiple: true,
			data: window.tgwc.roles,
		});

		$(".settings-table-wrapper .select2-selection").each(function (
			index,
			select
		) {
			$(select).append(
				'<span class="select2-selection__arrow" role="presentation"><b role="presentation"></b></span>'
			);
		});

		// Initialize the custom content.
		var textAreaId = `tgwc_endpoints_${slug}_content`;
		wp.editor.initialize(textAreaId, {
			tinymce: {
				wpautop: true,
				plugins:
					"charmap colorpicker compat3x directionality fullscreen hr image lists media paste tabfocus textcolor wordpress wpautoresize wpdialogs wpeditimage wpemoji wpgallery wplink wptextpattern wpview",
				toolbar1:
					"bold italic underline strikethrough | bullist numlist | blockquote hr wp_more | alignleft aligncenter alignright | link unlink | fullscreen | wp_adv",
				toolbar2:
					"formatselect alignjustify forecolor | pastetext removeformat charmap | outdent indent | undo redo | wp_help",
				media_strict: false,
				setup: function (editor) {
					editor.on("init", function () {
						if (window.tgwcSmartTagsButton) {
							$(document)
								.find(
									"#wp-tgwc_endpoints_" +
										slug +
										"_content-wrap"
								)
								.find(".wp-media-buttons")
								.append(window.tgwcSmartTagsButton);
							$(document).find(".wp-core-ui.wp-editor-wrap").css({
								width: "100%",
							});
							$(document)
								.find(".wp-editor-container")
								.find("textarea")
								.css({
									width: "100%",
								});
						}
					});
				},
			},
			quicktags: true,
			mediaButtons: true,
		});

		// Select the currently added tab.
		$tabs.find(".dd-item").last().find("a").trigger("click");

		// Customize tooltip.
		$('[data-toggle="tgwc-tooltip"]').tooltip({
			position: {
				my: "center top-120%",
				at: "center top",
				collision: "none",
			},
			tooltipClass: "tgwc-tooltip",
		});
		initializeSelect2();
		return true;
	}

	function initializeSelect2() {
		jQuery(document).ready(function ($) {
			$(".tgwc_custom_content_position").select2({
				placeholder: "Choose content position...",
				minimumResultsForSearch: Infinity,
			});

			$(".tgwc_settings_default_endpoints").select2({
				placeholder: "Choose default Endpoint...",
				minimumResultsForSearch: Infinity,
			});
		});
	}

	initializeSelect2();
	/**
	 * Check whether the endpoint or link or group exists or not.
	 *
	 * @since 0.1.0
	 *
	 * @param {string} endpoint Endpoint or link or group.
	 *
	 * @return {boolean}
	 */
	function isEndpointExists(endpoint) {
		endpoint = "#" + endpoint;

		var $anchors = $tabs.find("li a");
		var hrefs = $.map($anchors, function (anchor) {
			return $(anchor).attr("href");
		});

		return -1 !== hrefs.indexOf(endpoint);
	}

	/**
	 * Generate the endpoint slug.
	 *
	 * @since 0.2.0
	 *
	 * @param {string} slug Endpoint or link or group.
	 *
	 * @return {string} Slug.
	 */
	function generateEndpointSlug(slug) {
		var pattern = /[^\w- ]+/g;

		// Remove characters except alphanumberic, underscore(_) and dash(-).
		slug = slug.replace(pattern, "");

		// Remove underscore and dash from start the slug.
		slug = slug.trim();
		var firstCharacter = slug.substring(0, 1);
		if ("-" === firstCharacter || "_" === firstCharacter) {
			slug = slug.substring(1, slug.length);
		}

		// Remove underscore and dash from end of the slug.
		slug = slug.trim();
		var lastCharacter = slug.substring(slug.length - 1, slug.length);
		if ("-" === lastCharacter || "_" === lastCharacter) {
			slug = slug.substring(0, slug.length - 1);
		}

		slug = "#" + slug;

		var $anchors = $tabs.find("li a");
		var hrefs = $.map($anchors, function (anchor) {
			return $(anchor).attr("href");
		});

		hrefs = hrefs.concat(["#pagename", "#page", "#page_id", "#preview"]);

		var index = 0;
		while (true) {
			if (hrefs.indexOf(slug) > -1) {
				slug = slug + 1;
			} else {
				break;
			}
		}

		return slug.substr(1);
	}
});

/**
 * Nestable js related code.
 */
(function ($) {
	/**
	 * Initialize the nestable jquery plugin.
	 */
	function initSortableList($list) {
		new Sortable($list[0], {
			group: {
				name: "nested",
				pull: true,
				put: function (to, from, dragEl) {
					const dragType = $(dragEl).data("type");
					const targetType = $(to.el)
						.closest(".dd-item")
						.data("type");

					if (dragType === "group" && targetType === "group")
						return false;
					if (
						(dragType === "endpoint" || dragType === "link") &&
						targetType === "group"
					)
						return true;
					if (dragType === "group" && !targetType) return true;
					return true;
				},
			},
			animation: 150,
			fallbackOnBody: true,
			swapThreshold: 0.65,
			handle: ".tgwc_dd_custom_handle",
			ghostClass: "dd-ghost",
			chosenClass: "dragging",
			dragClass: "dd-drag",

			onChange: function (evt) {
				$(".dd-list.child-menu-wrap").each(function () {
					if (0 === $(this).find(".dd-item").length) {
						$(this).addClass("no-child");
						$(this)
							.find(".dd-drop-item-container")
							.removeClass("tgwc-hide");
					} else if ($(this).find(".dd-item").length > 0) {
						$(this).removeClass("no-child");
						$(this)
							.find(".dd-drop-item-container")
							.addClass("tgwc-hide");
					}
				});
			},
		});
	}

	$(".dd-list").each(function () {
		initSortableList($(this));
	});

	const observer = new MutationObserver(function (mutations) {
		mutations.forEach(function (mutation) {
			$(mutation.addedNodes).each(function () {
				const $node = $(this);
				if ($node.is(".dd-item.group")) {
					const $childList = $node.find(".child-menu-wrap").first();
					if ($childList.length) {
						initSortableList($childList);
					}
				}
			});
		});
	});

	observer.observe($(".dd-list").first()[0], {
		childList: true,
		subtree: true,
	});

	function customSerialize() {
		var result = [];

		$(".dd > .dd-list > .dd-item").each(function () {
			result.push(processItem($(this)));
		});

		return result;

		function processItem($item) {
			var item = {
				id: $item.data("id"),
				type: $item.data("type"),
				children: [],
			};

			// Find direct child items (looking in child-menu-wrap)
			$item.find(".dd-list > .dd-item").each(function () {
				item.children.push(processItem($(this)));
			});

			return item;
		}
	}

	/**
	 * Serialize the nested list and submit the form.
	 */
	$("#tgwc-customization-form").submit(function (e) {
		if ($(".dd").length) {
			var data = customSerialize();
			// console.log(data);

			// e.preventDefault();
			// return false;
			data = JSON.stringify(data);
			var input = `<input type='hidden' name='tgwc_endpoints[endpoints_order]' value='${data}' />`;
			$(this).append(input);

			var activePanel = $(this).find(".tgwc-tabs-panel.active");
			var linkInput = activePanel.find(".tgwc-link-url");

			if (linkInput.length > 0) {
				var url = linkInput.val().trim();
				var correctedUrl = correctAndValidateUrl(url);

				if (correctedUrl.isValid) {
					if (correctedUrl.corrected !== url) {
						linkInput.val(correctedUrl.corrected);
					}
				} else {
					removeUrlError(linkInput);
					linkInput.after(
						'<span class="error">' +
							tgwc_admin.tgwc_link_url_err +
							"</span>"
					);
					e.preventDefault();
					return false;
				}
			}
		}
	});

	$(".tgwc-link-url").on("keyup change", function () {
		var url = $(this).val().trim();
		removeUrlError($(this));

		var result = correctAndValidateUrl(url);
		if (!result.isValid) {
			$(this).after(
				'<span class="error">' +
					tgwc_admin.tgwc_link_url_err +
					"</span>"
			);
		} else if (result.corrected !== url) {
			$(this).val(result.corrected);
		}
	});

	function removeUrlError(input) {
		input.closest(".col-input").find(".error").remove();
	}

	function correctAndValidateUrl(url) {
		if (!url) return { isValid: false, corrected: url };

		if (url === "#" || /^#\w+/.test(url)) {
			return { isValid: true, corrected: url };
		}

		const originalUrl = url;

		// Add https:// if no protocol exists
		if (!/^(https?|ftp):\/\//i.test(url)) {
			if (
				/^www\./i.test(url) ||
				/^[a-z0-9-]+(\.[a-z0-9-]+)+/i.test(url)
			) {
				url = "https://" + url;
			}
		}

		if (/^http:\/\//i.test(url)) {
			url = url.replace(/^http:\/\//i, "https://");
		}

		const pattern = /^(https?|ftp):\/\/[^\s/$.?#].[^\s]*$/i;
		const isValid = pattern.test(url);

		return {
			isValid: isValid,
			corrected: isValid ? url : originalUrl,
			wasCorrected: isValid && url !== originalUrl,
		};
	}
})(jQuery);

/**
 * Initializes the user roles select.
 */
(function ($) {
	$("#tgwc-endpoints")
		.find('select[name$="[user_role][]"]')
		.each(function (index, userRole) {
			var $userRole = $(userRole);
			$userRole.select2({
				placeholder: window.tgwc.i18n.selectUserRoles,
				multiple: true,
				allowClear: true,
				data: window.tgwc.roles,
			});

			$userRole.val($userRole.data("selected"));
			$userRole.trigger("change");
		});

	$(".settings-table-wrapper .select2-selection").each(function (
		index,
		select
	) {
		$(select).append(
			'<span class="select2-selection__arrow" role="presentation"><b role="presentation"></b></span>'
		);
		$(select).find("input").attr("style", "");
	});
})(jQuery);

/**
 * Display restore options.
 */
(function ($) {
	var data = window.tgwc,
		$settingsForm = $("#tgwc-customization-form"),
		$restoreDefaultsDialog = $("#tgwc-dialog-restore-defaults"),
		$setting = $("#tgwc-restore-defaults-settings"),
		$customization = $("#tgwc-restore-defaults-customization"),
		$debug = $("#tgwc-restore-defaults-debug-settings"),
		$formInputs = $settingsForm
			.find("input, select, textarea")
			.filter(function () {
				return this.name && this.name.startsWith("tgwc_endpoints");
			}),
		$isFormSaved = true,
		$saveChangesDialog = $("#tgwc-dialog-save-changes");
	$formInputs.each(function (index, element) {
		const $element = $(element);

		// For text inputs, textareas, selects - use keyup/undo/redo
		if ($element.is('input[type="text"], textarea, select')) {
			$(document).on("keyup undo redo", element, function () {
				if ($isFormSaved) {
					$isFormSaved = false;
				}
			});
		}
		// For checkboxes and radios - use change
		else if ($element.is('input[type="checkbox"], input[type="radio"]')) {
			$element.on("change", function () {
				if ($isFormSaved) {
					$isFormSaved = false;
				}
			});
		}
	});

	$("#customizer_tab").on("click", function (e) {
		if ($isFormSaved) {
			return;
		}
		e.preventDefault();
		var $this = $(this);
		$saveChangesDialog.dialog({
			resizable: false,
			height: "auto",
			width: 400,
			modal: true,
			autoOpen: true,
			dialogClass: "tgwc-dialog-save-changes",
			open: function () {
				$(this)
					.parent(".ui-dialog")
					.find(".tgwc-button--danger:eq(0)")
					.focus();

				if (!$(this).parent().find(".tgwc-poup-icon-wrap").length) {
					$(this)
						.parent()
						.find(".ui-dialog-title")
						.prepend(
							"<div class='tgwc-poup-icon-wrap'>" +
								tgwc.alertTriangleIcon +
								"</div>"
						);
				}
			},
			buttons: [
				{
					text: window.tgwc.i18n.discard,
					class: "tgwc-button tgwc-button--danger tgwc-endpoint-dialog-form-cancel",
					tabindex: 2,
					click: function () {
						$(this).dialog("close");
						window.location = $this.attr("href");
					},
				},
				{
					text: window.tgwc.i18n.save,
					class: "tgwc-button tgwc-button--primary",
					tabindex: 1,
					click: function () {
						$(this).dialog("close");
						$("#tgwc-submit").trigger("click");
					},
				},
			],
		});
	});

	/**
	 * Handle setting checkbox.
	 */
	$setting
		.add($customization)
		.add($debug)
		.change(function (e) {
			e.preventDefault();

			var $noticeContainer = $("#tgwc-dialog-restore-defaults").find(
					".tgwc-dialog-notice"
				),
				title = "",
				message = "",
				noticeId = $(this).attr("id") + "-notice";

			if ("tgwc-restore-defaults-settings-notice" === noticeId) {
				title = data.i18n.settings;
				message = data.i18n.restoreSettingsInfo;
			} else if (
				"tgwc-restore-defaults-customization-notice" === noticeId
			) {
				title = data.i18n.designCustomization;
				message = data.i18n.restoreCustomizationInfo;
			}

			var html = [
				'<div class="notice notice-warning" style="display: none" id="' +
					noticeId +
					'">',
				"<p>",
				"<strong>" + title + ": </strong>",
				message,
				"</p>",
				"</div>",
			].join("");

			if ($(this).prop("checked")) {
				$noticeContainer.append(html);
				$("#" + noticeId).fadeIn();
			} else {
				$("#" + noticeId).fadeOut(function () {
					$(this).remove();
				});
			}
		});

	/**
	 * Display restore dialog box.
	 */
	$("#tgwc-reset").click(function (e) {
		e.preventDefault();
		$("#tgwc-dialog-restore-defaults").dialog({
			modal: true,
			resizable: false,
			minWidth: 460,
			dialogClass: "tgwc-dialog-restore-defaults",
			open: function () {
				if (!$(this).parent().find(".tgwc-poup-icon-wrap").length) {
					$(this)
						.parent()
						.find(".ui-dialog-title")
						.prepend(
							"<div class='tgwc-poup-icon-wrap'>" +
								tgwc.alertTriangleIcon +
								"</div>"
						);
				}
			},
			buttons: [
				{
					text: window.tgwc.i18n.cancel,
					class: "tgwc-button tgwc-button--link tgwc-endpoint-dialog-form-cancel",
					tabindex: 2,
					click: function () {
						$(this).dialog("close");
					},
				},
				{
					text: window.tgwc.i18n.reset,
					class: "tgwc-button tgwc-button--danger tgwc-endpoint-dialog-form-add",
					tabindex: 1,
					click: function () {
						var $setting = $restoreDefaultsDialog.find(
							"#tgwc-restore-defaults-settings"
						);

						var $endpoint = $restoreDefaultsDialog.find(
							"#tgwc-restore-defaults-endpoints"
						);

						var hasFreeEndpoint = $(document)
							.find(".tgwc-endpoint-is-free")
							.val();

						if (hasFreeEndpoint) {
							$(document)
								.find("#tgwc-free-endpoint-state")
								.find("input")
								.val("deleted");
						}
						var $customization = $restoreDefaultsDialog.find(
							"#tgwc-restore-defaults-customization"
						);
						// Remove previous reset options hidden fields.
						$settingsForm
							.find("input[name^=tgwc_reset_options]")
							.remove();

						var html = "";
						if ($endpoint.prop("checked")) {
							html +=
								'<input type="hidden" name="tgwc_reset_options[endpoints]" value="endpoints" />';
						}
						if ($setting.prop("checked")) {
							html +=
								'<input type="hidden" name="tgwc_reset_options[setting]" value="setting" />';
						}

						if ($customization.prop("checked")) {
							html +=
								'<input type="hidden" name="tgwc_reset_options[customization]" value="customization" />';
						}

						if ($debug.prop("checked")) {
							html +=
								'<input type="hidden" name="tgwc_reset_options[debug]" value="debug" />';
						}
						$settingsForm.append(html);
						$("#tgwc-submit").trigger("click");
						$(this).dialog("close");
					},
				},
			],
			close: function () {
				$restoreDefaultsDialog.find("form").get(0).reset();
				$restoreDefaultsDialog.find(".notice").hide();
			},
		});
	});
})(jQuery);

jQuery(function ($) {
	/**
	 * Customize tooltip.
	 */
	$('[data-toggle="tgwc-tooltip"]').tooltip({
		position: {
			my: "center top-120%",
			at: "center top",
			collision: "none",
		},
		tooltipClass: "tgwc-tooltip",
	});

	/**
	 * Customize tooltip down.
	 */
	$('[data-toggle="tgwc-tooltip-down"]').tooltip({
		position: {
			my: "center top+10",
			at: "center bottom",
			collision: "none",
		},
		tooltipClass: "tgwc-tooltip-down",
	});
});

/**
 * Change the title of the endpoints, group or link when the label is changed.
 */
(function ($) {
	var $container = $("#tgwc-customization");
	var data = window.tgwc;

	/**
	 * Change the title of the endpoints, group or link when the label is changed.
	 */
	$container.on("keyup keydown", 'input[name$="[label]"]', function (e) {
		if (e.keyCode === $.ui.keyCode.ENTER) {
			e.preventDefault();
		}

		var label = $(this).val(),
			isValid = true,
			errorMessage = "",
			panelId = $(this)
				.parents(".tgwc-tabs-panel.active")
				.first()
				.attr("id"),
			eroorLabel = $("#tgwc-tabs")
				.find('[data-id="' + panelId + '"]')
				.find(".dd3-content")
				.text()
				.trim()
				.substring(4);

		$(this)
			.parents(".tgwc-tabs-panel.active")
			.find(".tgwc-tabs-panel-header h2")
			.html(label);
		$('.dd-item[data-id="' + panelId + '"]')
			.find(".tgwc-sidenav-tab-anchor span:nth-child(3)")
			.html(label);
		$(this).siblings(".error").remove();

		if ("" === label) {
			errorMessage = data.i18n.labelCannotBeEmpty;
			$(this).after('<span class="error">' + errorMessage + "</span>");
			isValid = false;
		}

		$(this).data("isValid", isValid);
		$(this).data("error", {
			label: eroorLabel,
			message: errorMessage,
		});
	});

	/**
	 * Verify the slug.
	 */
	$container.on("keyup keydown", 'input[name$="[slug]"]', function (e) {
		if (e.keyCode === $.ui.keyCode.ENTER) {
			e.preventDefault();
		}

		var currentSlug = $(this).parents(".tgwc-tabs-panel.active").attr("id"),
			panelId = $(this)
				.parents(".tgwc-tabs-panel.active")
				.first()
				.attr("id"),
			label = $("#tgwc-tabs")
				.find('[data-id="' + panelId + '"]')
				.find(".dd3-content")
				.text()
				.trim()
				.substring(4),
			slug = $(this).val().toLowerCase(),
			message = data.i18n.available,
			errorMessage = "",
			$endpoints = $container.find("li.endpoint"),
			cssClass = "notice-success",
			isValid = true,
			patternValidCharacterCheck = /^[\w-]+$/g,
			patternBeginWithCheck = /^[a-zA-Z0-9]+[\w-]*[a-zA-Z0-9]$/g;

		var endpoints = $.map($endpoints, function (endpoint) {
			var slug = $(endpoint).data("id");
			if (currentSlug !== slug) {
				return slug;
			}
		});

		// Add kyewords which cannot be used as slugs.
		endpoints = endpoints.concat([
			"pagename",
			"page",
			"page_id",
			"preview",
		]);

		if (endpoints.indexOf(slug) >= 0) {
			message = data.i18n.notAvailable;
			errorMessage = data.i18n.notAvailable;
			cssClass = "notice-error";
			isValid = false;
		}

		if (!patternBeginWithCheck.test(slug)) {
			message = data.i18n.slugMustBeginWith;
			errorMessage = data.i18n.slugMustBeginWith;
			cssClass = "notice-error";
			isValid = false;
		}

		if (!patternValidCharacterCheck.test(slug)) {
			message = data.i18n.slugCanOnlyContains;
			errorMessage = data.i18n.slugCanOnlyContains;
			cssClass = "notice-error";
			isValid = false;
		}

		if (slug.length < 3) {
			message = data.i18n.slugMustBeOfLength;
			errorMessage = data.i18n.slugMustBeOfLength;
			cssClass = "notice-error";
			isValid = false;
		}

		if ("" === slug) {
			message = data.i18n.slugCannotBeEmpty;
			errorMessage = data.i18n.slugCannotBeEmpty;
			cssClass = "notice-error";
			isValid = false;
		}

		$(this).siblings(".notice").remove();
		$(this).closest(".col-input").css({
			"flex-direction": "column",
		});
		$(this).after(
			'<span class="notice ' + cssClass + '">' + message + "</span>"
		);
		$(this).data("isValid", isValid);
		$(this).data("error", {
			label: label,
			message: errorMessage,
		});
		this.value = slug;
	});

	/**
	 * Disable submit when enter is pressed in class input field.
	 */
	$container.on("keyup keydown", 'input[name$="[class]"]', function (e) {
		if (e.keyCode === $.ui.keyCode.ENTER) {
			e.preventDefault();
		}
	});

	/**
	 * Dislay error dialog if the form is invalid.
	 */
	$container.find("form").submit(function (e) {
		var isValid = true,
			errorHtml = "",
			$inputs = $container.find(
				'input[name$="[slug]"], input[name$="[label]"] '
			);

		$.each($inputs, function (index, input) {
			var valid = $(input).data("isValid");
			var error = $(input).data("error");
			var html = "";

			valid = undefined === valid ? true : valid;
			isValid = isValid && valid;

			if (!valid) {
				html = [
					'<div class="notice notice-error">',
					"<p>",
					"<strong>" + error.label + ": </strong>",
					error.message,
					"</p>",
					"</div>",
				].join("");
			}

			errorHtml = errorHtml + html;
		});

		if (!isValid) {
			e.preventDefault();
			$("<div/>")
				.html(
					'<div class="tgwc-dialog-content"><span class="ui-icon ui-icon-alert"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-alert-triangle"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg></span><div class="tgwc-dialog-content__detail"><p>' +
						data.i18n.resolveFormErrors +
						"</p></div></div>" +
						'<div class="tgwc-dialog-content-list">' +
						errorHtml +
						"</div>"
				)
				.dialog({
					title: data.i18n.couldNotSaveChanges,
					dialogClass: "no-close",
					resizable: false,
					width: "400px",
					modal: true,
					buttons: [
						{
							text: data.i18n.ok.toUpperCase(),
							class: "tgwc-button tgwc-button--link tgwc-endpoint-dialog-form-cancel",
							click: function () {
								$(this).dialog("close");
							},
						},
					],
				});
		}
	});

	$(document).on("click", ".tgwc-sidenav-toggle", function (e) {
		e.preventDefault();
		var $this = $(this);
		var $sidenav = $this.closest(".tgwc-sidenav");
		$sidenav.toggleClass("tgwc-sidenav--collapsed");
		$(document)
			.find(".tgwc-tabs-with-sidenav")
			.toggleClass("tgwc-sidecontent-over-shadow");
		$this.toggleClass("tgwc-sidenav-toggle--collapsed");
	});

	$('input[name="tgwc_endpoints[customer-logout][enable]"]').on(
		"change",
		function () {
			if (tgwc_admin.customizer_logout_btn_enabled) {
				$(this).prop("checked", !$(this).is(":checked"));
				return false;
			}
		}
	);
})(jQuery);
