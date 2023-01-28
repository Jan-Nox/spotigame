"use strict";

let coreAppType = "ajax";

/**
 * @requires Log
 */
class Core
{
    /**
     * Based on the appType of this instance, I will either use a webSocket or AJAX to get data for the given parameters.
     * @param context
     * @param view
     * @param action
     * @param data
     * @returns {*}
     */
    static async runRequest(context, view, action, data) {
        let url = "/?context=" + context + "&view=" + view;

        if (typeof (action) === "string" && action.length !== 0) {
            url = url + "&action=" + action;
        }
        return await Core.ajaxRequest({
            url  : url,
            data : data
        });
    }

    /**
     *
     * @param options
     * @param callback
     */
    static async ajaxRequest(options, callback = null) {
        console.log(options);
        let defer    = $.Deferred(),
            defaults = {
                accepts     : "application/json",
                beforeSend  : null,
                cache       : false,
                complete    : typeof (callback) === "callable" ? callback : null,
                contentType : "application/json",
                data        : {},
                method      : "POST",
                success     : null,
                timeout     : 1000
            };

        let myRequest = {
            accepts     : (typeof (options.accepts) !== "undefined") ? options.accepts : defaults.accepts,
            beforeSend  : (typeof (options.beforeSend) !== "undefined") ? options.beforeSend : defaults.beforeSend,
            cache       : (typeof (options.cache) !== "undefined") ? options.cache : defaults.cache,
            contentType : (typeof (options.contentType) !== "undefined") ? options.contentType : defaults.contentType,
            data        : (typeof (options.data) !== "undefined") ? options.data : defaults.data,
            method      : (typeof (options.method) !== "undefined") ? options.method : defaults.method,
            timeout     : (typeof (options.timeout) !== "undefined") ? options.timeout : defaults.timeout,
            complete    : function (data) {
                if (typeof (options.complete) === "function") {
                    options.complete(data);
                }
            },
            error       : function (xmlhttprequest, textstatus, message) {
                if (textstatus === "timeout") {
                    return;
                }
                if (textstatus === "error") {
                    return;
                }
                if (textstatus === "parsererror") {
                    return;
                }
            },
            url         : (typeof (options.url) !== "undefined") ? options.url : defaults.url,
            dataType    : "json"
        };

        if (typeof (myRequest.data) === "object") {
            myRequest.data = JSON.stringify(myRequest.data);
        }
        console.log(myRequest);
        return $.ajax(myRequest).done(function (response) {
            return defer.resolve(response);
        }).fail(function (jqXHR, textStatus, errorThrown) {
            return defer.resolve({
                "jqXHR"       : jqXHR,
                "textStatus"  : textStatus,
                "errorThrown" : errorThrown
            });
        });
    }

    static doModal(ajaxResponse) {
        let modalStyle  = ajaxResponse.modalStyle || "",
            modalId     = ajaxResponse.modaId || "mainModal",
            modal       = $("#" + modalId),
            modalHead   = $("#" + modalId + " .modal-title"),
            modalBody   = $("#" + modalId + " .modal-body"),
            modalFoot   = $("#" + modalId + " .modal-foot"),
            modalDialog = $("#" + modalId + " .modal-dialog");

        if (modal.Length === 0) {
            return;
        }
        modalDialog.removeClass("modal-sm");
        modalDialog.removeClass("modal-lg");
        modalDialog.removeClass("modal-xl");
        modalDialog.addClass(ajaxResponse.modalSize || "");
        modalHead.html(ajaxResponse.modalHead || "");
        modalBody.html(ajaxResponse.modalBody || "");
        modalFoot.html(ajaxResponse.modalFoot || "");
        $('select').selectize({
            plugins: ['drag_drop', 'remove_button'],
            persist: false,
            create: false
        });
        modal.modal({ show : true });
    }

    /**
     *
     * @param ajaxResponse
     * @returns {boolean}
     */
    static doCallbacks(ajaxResponse) {
        if (typeof (ajaxResponse.jslines) !== "object") {
            return false;
        }

        let cbCount = ajaxResponse.jslines.length;
        let cbIndex = 0;
        for (cbIndex = 0; cbIndex <= cbCount - 1; cbIndex++) {
            let myCallback = ajaxResponse.jslines[cbIndex];
            try {
                eval("" + myCallback);
            } catch (Error) {
            }
        }
    }

    static ajaxModal(url, data, modalId) {
        let options = {
            url  : url,
            data : data
        };
        Core.ajaxRequest(options, data).then(function (response) {
            let modal       = $("#" + modalId),
                modalHead   = $("#" + modalId + " .modal-title"),
                modalBody   = $("#" + modalId + " .modal-body"),
                modalFoot   = $("#" + modalId + " .modal-foot-container"),
                modalDialog = $("#" + modalId + " .modal-dialog"),
                script      = "";

            script = response.script;
            if (typeof script !== "undefined" && script.length !== 0) {
                eval(script);
            }

            if (typeof (response.body) !== "string" || response.body.Length === 0) {
                alert("Body is empty!");
                return;
            }
            if (modal.Length === 0) {
                alert("Modal not found!");
                return;
            }
            // Clean the dialogue
            modalDialog.removeClass("modal-xs");
            modalDialog.removeClass("modal-sm");
            modalDialog.removeClass("modal-md");
            modalDialog.removeClass("modal-lg");
            modalDialog.removeClass("modal-xl");
            modalDialog.addClass(response.size || "");
            // Clean the styles
            modalBody.removeClass("alert-primary");
            modalBody.removeClass("alert-secondary");
            modalBody.removeClass("alert-success");
            modalBody.removeClass("alert-danger");
            modalBody.removeClass("modal-warning");
            modalBody.removeClass("modal-info");
            modalBody.removeClass("modal-light");
            modalBody.removeClass("modal-dark");
            modalBody.addClass(response.type);
            // Clean the Content
            modalHead.html(response.head || "");
            modalBody.html(response.body || "");
            modalFoot.html(response.foot || "");
            // Set it up correctly
            modal.modal({
                backdrop : "static",
                keyboard : false
            });
            // Show it.
            modal.modal("show");
            // Render...
            $('select').selectize({
                plugins: ['drag_drop', 'remove_button'],
                persist: false,
                create: false
            });
        });
    }
}
