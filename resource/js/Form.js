"use strict";

/**
 * I am a helper for forms .
 * @author Jan Nox <jan@nox.kiwi>
 */
export default class Form
{

    constructor(pointer) {
        this.pointer = $(pointer);
        this.submit  = this.pointer.find("input[type=\"submit\"]");

        let self = this;

        this.pointer.submit(function (e) {
            e.preventDefault();
            let myForm = this;
            Form.lock(self.pointer);
            let sendData = Form.getData(self.pointer);
            $.ajax({
                async       : true,
                url         : $(myForm).attr("action"),
                type        : $(myForm).attr("method"),
                data        : JSON.stringify(sendData),
                cache       : false,
                contentType : false,
                method      : "POST",
                dataType    : "json",
                processData : false,
                success     : function (data) {
                    self.pointer.find("input").addClass("is-valid");
                    self.pointer.find(".validationFeedback").html(null);
                    if (Array.isArray(data.errors) && data.errors.length !== 0) {
                        $(data.errors).each(function (index, element) {
                            console.log(index);
                            console.log(element);
                            self.pointer.find("[name=\"" + element.fieldName + "\"]").addClass("is-invalid");
                            self.pointer.find("[name=\"" + element.fieldName + "\"]").addClass("is-invalid");
                            $("#" + element.fieldName + "error").html(element.code);
                        });
                        return;
                    }
                    // Validation OKAY:
                    self.pointer.find("input").removeClass("is-invalid");
                    list.ajax.reload();
                    $("#crudModal").modal("hide");
                },
                complete    : function () {
                    Form.unlock(self.pointer);
                }
            });
            return false;
        });
    }

    /**
     * I will disable all the form elements inside the given pointer.
     * @param string pointer
     */
    static lock(pointer) {
        $(pointer).find("input").prop("disabled", true);
        $(pointer).find("textarea").prop("disabled", true);
        $(pointer).find("select").prop("disabled", true);
        $(pointer).find("button").prop("disabled", true);
    }

    /**
     * I will enable all the form elements inside the given pointer.
     * @param string pointer
     */
    static unlock(pointer) {
        $(pointer).find("input").prop("disabled", false);
        $(pointer).find("textarea").prop("disabled", false);
        $(pointer).find("select").prop("disabled", false);
        $(pointer).find("button").prop("disabled", false);
    }

    /**
     * I will remove the errors from the fields.
     * @param string pointer
     */
    static restore(pointer) {
        $(pointer).find("input").removeClass("MFormError");
        $(pointer).find("select").removeClass("MFormError");
        $(pointer).find("textarea").removeClass("MFormError");
        $(pointer).find(".note").empty();
    }

    /**
     * I will return a FormData object for the given pointer.
     * @param string pointer
     */
    static getData(pointer) {
        let formData = {};
        $(this).find("input,textarea,select").each(function () {
            let name  = $(this).attr("name"),
                value = $(this).val(),
                type  = $(this).attr("type");
            if (type === "checkbox") {
                formData[name] = document.getElementById($(this).attr("id")).checked;
            } else {
                formData[name] = value.toString().trim();
            }
        });
        return formData;
    }

    /**
     * I will lock the form and submit it.
     */
    submit() {
        alert("A");
        let data = this.pointer.serialize();
        console.log(data);
        alert("B");
        Form.lock(this.pointer);
        // Magic here
        Form.unlock(this.pointer);
        console.log(data);
    }

    validate() {
        // Validate each field individually,
        // Output errors!
    }

}
