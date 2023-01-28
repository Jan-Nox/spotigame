"use strict";

/**
 * I am the CRUD manager JS class.
 */
export default class Crud
{
    constructor(crudId, configuration) {
        this.pointer = $("#crudList" + crudId);
        this.config  = configuration;
        this.buildTable();
    }

    buildTable() {
        list = this.pointer.DataTable(this.config);
    }

    checkAll(pointer) {
        $(this.pointer).find("input.chkbxPrimary").each(function () {
            $(this).prop("checked", ! $(this).prop("checked"));
        });
    }
}
