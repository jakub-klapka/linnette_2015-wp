!function(t){var e={wraper:void 0,checkboxes:void 0,labels:void 0,figures:void 0,photoswipe:void 0,pswpSelectButton:void 0,actionsWrapper:void 0,counter:void 0,saveButton:void 0,formDisabled:void 0,form:void 0,formEndpoint:void 0,changedSinceSave:void 0,autosaveInterval:void 0,currentlySaving:void 0,init:function(e){this.wraper=e,this.checkboxes=e.find(".photo_selection__checkbox"),this.labels=e.find(".photo_selection__checkbox__label"),this.figures=e.find(".images_list__item"),this.pswpSelectButton=t(".pswp__linnete-photo-selection__button"),this.actionsWrapper=t(".photo_selection_actions:first"),this.counter=this.actionsWrapper.find("[data-photo_selection_counter]"),this.saveButton=this.actionsWrapper.find(".photo_selection_actions__save_button"),this.formDisabled=t(".pswp__linnete-photo-selection").hasClass("is-disabled"),this.form=t(".photo_selection_form"),this.formEndpoint=this.form.attr("action"),this.changedSinceSave=!1,this.autosaveInterval=6e4,this.currentlySaving=!1,this.removeCheckboxClickBubble(),this.toggleWrapClassOnCheck(),this.bindPswpEvents(),this.checkboxes.on("change",t.proxy(this.recalculateCounter,this)),this.saveButton.on("click",function(t){t.preventDefault()}),this.saveButton.on("click",t.proxy(this.saveSelection,this)),this.checkboxes.on("change",t.proxy(this.setChangedStatusForAutosave,this)),setInterval(t.proxy(this.maybeAutosave,this),this.autosaveInterval),t(window).on("beforeunload",t.proxy(this.rejectLock,this))},removeCheckboxClickBubble:function(){this.checkboxes.add(this.labels).on("click",function(t){t.stopPropagation()})},toggleWrapClassOnCheck:function(){this.checkboxes.on("change",function(){var e=t(this),i=e.parents(".images_list__item");e.is(":checked")?i.addClass("is-checked"):i.removeClass("is-checked")})},bindPswpEvents:function(){var e=this;this.figures.on("click",function(){e.initPswp(t(this))}),this.formDisabled||this.pswpSelectButton.on("click",t.proxy(this.passButtonClickToCheckbox,this))},initPswp:function(e){var i=this.figures.index(e),s=[];this.figures.each(function(){var e=t(this),i=e.find("img");s.push({src:i.data("url"),w:i.data("width"),h:i.data("height"),title:e.find("figcaption").text(),id:e.find(".photo_selection__checkbox").prop("id")})});var o={index:i,showHideOpacity:!0,loop:!1,shareEl:!1};this.photoswipe=new PhotoSwipe(document.querySelectorAll(".pswp")[0],PhotoSwipeUI_Default,s,o),this.formDisabled||(this.bindWindowXKeySelections(!0),this.photoswipe.listen("unbindEvents",t.proxy(this.bindWindowXKeySelections,this,!1))),this.photoswipe.listen("beforeChange",t.proxy(this.setPswpImageSelectorValue,this)),this.photoswipe.init()},bindWindowXKeySelections:function(e){e="undefined"==typeof e?!1:!0,e?t(document).on("keypress.linn__ps_select_picture",t.proxy(function(t){120===t.which&&this.passButtonClickToCheckbox()},this)):t(document).off("keypress.linn__ps_select_picture")},setPswpImageSelectorValue:function(){var t=this.wraper.find("#"+this.photoswipe.currItem.id);t.is(":checked")?this.pswpSelectButton.addClass("is-selected"):this.pswpSelectButton.removeClass("is-selected")},passButtonClickToCheckbox:function(){var t=this.wraper.find("#"+this.photoswipe.currItem.id);this.pswpSelectButton.hasClass("is-selected")?(t.prop("checked",!1).trigger("change"),this.pswpSelectButton.removeClass("is-selected")):(t.prop("checked",!0).trigger("change"),this.pswpSelectButton.addClass("is-selected"))},recalculateCounter:function(){var t=this.checkboxes.filter(":checked").length;this.counter.text(t)},saveSelection:function(){this.currentlySaving=!0,this.setButtonState("saving");var e=this.form.serializeArray();e.push({name:"photo_selection_action",value:"save_selection"});var i=t.ajax({dataType:"json",method:"POST",url:this.formEndpoint,data:e});i.done(t.proxy(function(t){"saved"===t.result?(this.changedSinceSave=!1,this.setButtonState("saved")):alert("Nepodařilo se uložit výběr. Prosím, dejte nám o této chybě vědět. Děkujeme."),this.currentlySaving=!1},this)),i.fail(t.proxy(function(){alert("Nepodařilo se uložit výběr. Prosím, dejte nám o této chybě vědět. Děkujeme."),this.currentlySaving=!1},this))},setButtonState:function(e){"saving"===e&&this.saveButton.removeClass("is-saved").addClass("is-saving").prop("disabled",!0),"saved"===e&&(this.saveButton.removeClass("is-saving").addClass("is-saved").prop("disabled",!1),setTimeout(t.proxy(function(){this.saveButton.removeClass("is-saving").removeClass("is-saved").prop("disabled",!1)},this),4e3))},setChangedStatusForAutosave:function(){this.changedSinceSave=!0},maybeAutosave:function(){this.currentlySaving===!1&&this.saveSelection()},rejectLock:function(){var e={action:this.form.find('input[name="action"]').val(),_wp_nonce:this.form.find('input[name="_wp_nonce"]').val(),post_id:this.form.find('input[name="post_id"]').val(),access_token:this.form.find('input[name="access_token"]').val(),photo_selection_action:"reject_lock"};t.ajax({dataType:"json",method:"POST",url:this.formEndpoint,data:e})}};t(function(){t(".photo_selection").each(function(){Object.create(e).init(t(this))}),t("form textarea").each(function(){autosize(this)})})}(jQuery);