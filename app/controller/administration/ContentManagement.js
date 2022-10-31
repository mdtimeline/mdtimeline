/**
 * GaiaEHR (Electronic Health Records)
 * Copyright (C) 2013 Certun, LLC.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

Ext.define('App.controller.administration.ContentManagement', {
    extend: 'Ext.app.Controller',
    refs: [
        {
            ref: 'ContentManagementGrid',
            selector: '#ContentManagementGrid'
        },
        {
            ref: 'ContentManagementWindow',
            selector: '#ContentManagementWindow'
        },
        {
            ref: 'ContentManagementWindowForm',
            selector: '#ContentManagementWindowForm'
        },
        {
            ref: 'ContentManagementWindowSaveBtn',
            selector: '#ContentManagementWindowSaveBtn'
        },
        {
            ref: 'ContentManagementWindowCancelBtn',
            selector: '#ContentManagementWindowCancelBtn'
        },
        {
            ref: 'ContentManagementWindowTextContentBody',
            selector: '#ContentManagementWindowTextContentBody'
        },
        {
            ref: 'ContentManagementWindowHtmlContentBody',
            selector: '#ContentManagementWindowHtmlContentBody'
        },
        {
            ref: 'ContentManagementWindowTokensTextArea',
            selector: '#ContentManagementWindowTokensTextArea'
        },
        {
            ref: 'ContentManagementWindowIsHtmlCheckbox',
            selector: '#ContentManagementWindowIsHtmlCheckbox'
        },
        {
            ref: 'ContentManagementAddBtn',
            selector: '#ContentManagementAddBtn'
        },
        {
            ref: 'ContentManagementGridContextMenuDelete',
            selector: '#ContentManagementGridContextMenuDelete'
        }
    ],

    init: function () {
        var me = this;

        me.control({
            '#ContentManagementGrid': {
                beforerender: me.onContentManagementGridBeforeRender,
                itemdblclick: me.onContentManagementGridItemDblClick,
                //beforeitemcontextmenu: me.onContentManagementWindowGridContextMenu
            },
            '#ContentManagementWindow': {
                close: me.onContentManagementWindowClose
            },
            '#ContentManagementWindowSaveBtn': {
                click: me.onContentManagementWindowSaveBtnClick
            },
            '#ContentManagementWindowCancelBtn': {
                click: me.onContentManagementWindowCancelBtnClick
            },
            '#ContentManagementWindowIsHtmlCheckbox': {
                change: me.onContentManagementWindowIsHtmlCheckboxChange
            },
            '#ContentManagementAddBtn': {
                click: me.onContentManagementAddBtnClick
            },
            '#ContentManagementGridContextMenuDelete': {
                click: me.onContentManagementGridContextMenuDeleteClick
            }
        });
    },

    onContentManagementWindowSaveBtnClick: function () {
        var me = this,
            form = me.getContentManagementWindowForm().getForm(),
            record = form.getRecord(),
            values = form.getValues();

        if (form.isValid()) {

            record.set(values);
            record.store.sync({
                success: function () {
                    app.msg(_('sweet'), _('record_saved'));
                    record.store.load();
                },
                failure: function () {
                    app.msg(_('oops'), _('record_error'), true);
                }
            });

            me.getContentManagementWindow().close();
        }
    },

    onContentManagementWindowCancelBtnClick: function () {
        var me = this,
            content_management_grid = me.getContentManagementGrid(),
            content_management_grid_store = content_management_grid.getStore(),
            form = me.getContentManagementWindowForm().getForm(),
            record = form.getRecord();


        if (record.get('content_type') == '' && record.get('content_body') == '') {
            content_management_grid_store.remove(record);
        }

        this.getContentManagementWindow().close();
    },

    onContentManagementWindowClose: function () {
        this.getContentManagementWindowForm().getForm().reset();
    },

    onContentManagementGridBeforeRender: function (grid) {
        grid.getStore().load();
    },

    onContentManagementGridItemDblClick: function (grid, record) {

        this.showContentWindow();

        var me = this,
            content_type = record.get('content_type'),
            form_panel = me.getContentManagementWindowForm(),
            form = form_panel.getForm(),
            is_html = record.get('is_html'),
            textareafield = form_panel.down('textareafield[action=content_body]'),
            htmleditor = form_panel.down('htmleditor[action=content_body]');

        form.reset();
        form.loadRecord(record);

        textareafield.setValue(record.get('content_body'));
        textareafield.submitValue = !is_html;
        textareafield.setDisabled(is_html);
        textareafield.setVisible(!is_html);

        htmleditor.setValue(record.get('content_body'));
        htmleditor.submitValue = is_html;
        htmleditor.setDisabled(!is_html);
        htmleditor.setVisible(is_html);

        me.setTokensTextAreaFieldByContentType(content_type);
    },

    onContentManagementWindowIsHtmlCheckboxChange: function (checkbox, isHtml, oldValue, eOpts) {
        var me = this,
            content_body = me.getContentManagementWindowTextContentBody(),
            content_html_body = me.getContentManagementWindowHtmlContentBody(),
            form_panel = me.getContentManagementWindowForm(),
            form = form_panel.getForm(),
            record = form.getRecord();

        if (isHtml == true) {
            content_body.hide();
            content_body.setDisabled(true);
            content_body.submitValue = false;

            content_html_body.show();
            content_html_body.setDisabled(false);
            content_html_body.setValue(record.get('content_body'));
            content_html_body.submitValue = true;
        } else {
            content_body.show();
            content_body.setDisabled(false);
            content_body.setValue(record.get('content_body'));
            content_body.submitValue = true;

            content_html_body.hide();
            content_html_body.setDisabled(true);
            content_html_body.submitValue = false;
        }
    },

    onContentManagementAddBtnClick: function (btn) {
        this.getContentManagementGrid().getSelectionModel().deselectAll();

        var me = this,
            content_management_grid = me.getContentManagementGrid(),
            content_management_grid_store = content_management_grid.getStore(),
            win = me.showContentWindow(),
            form = win.down('form').getForm(),
            records = content_management_grid_store.add({
                content_type: '',
                content_lang: 'en',
                content_body: '',
                is_html: false,
                content_version: '1.0'
            });

        form.loadRecord(records[0]);

        me.setTokensTextAreaFieldByContentType(records[0].get('content_type'));
    },

    showContentManagementGridContextMenu: function(content_management_grid, content_management_record, e) {
        // if (!a('access_patient_notes_transaction_log')) return;

        var me = this;

        me.ContentManagementGridContextMenu = Ext.widget('menu', {
            margin: '0 0 10 0',
            items: [
                {
                    text: _('delete_selected'),
                    icon: 'modules/billing/resources/images/cross.png',
                    itemId: 'ContentManagementGridContextMenuDelete',
                    acl: true
                }
            ]
        });

        me.ContentManagementGridContextMenu.content_management_grid = content_management_grid;
        me.ContentManagementGridContextMenu.content_management_record = content_management_record;

        me.ContentManagementGridContextMenu.showAt(e.getXY());

        return me.ContentManagementGridContextMenu;
    },

    onContentManagementGridContextMenuDeleteClick: function(btn) {
        var me = this,
            content_management_grid = this.getContentManagementGrid(),
            content_management_grid_store = content_management_grid.getStore(),
            content_management_selected_rows = content_management_grid.getSelectionModel().getSelection();

        if (content_management_selected_rows.length) {
            content_management_grid_store.remove(content_management_selected_rows);

            content_management_grid_store.sync({
                callback: function (){
                    app.msg(_('sweet'), _('records_removed'));
                }
            });
        }
    },

    onContentManagementWindowGridContextMenu: function (print_jobs_grid, print_jobs_record, item, index, e){
        e.preventDefault();

        this.showContentManagementGridContextMenu(print_jobs_grid, print_jobs_record, e)
    },

    showContentWindow: function () {
        if (!this.getContentManagementWindow()) {
            Ext.create('App.view.administration.ContentManagementWindow');
        }
        return this.getContentManagementWindow().show();
    },

    setTokensTextAreaFieldByContentType: function (content_type) {
        var me = this,
            tokens = [],
            tokenTextAreaField = me.getContentManagementWindowTokensTextArea();

        tokens = tokens.concat(this.patientTokens());

        if (content_type === 'disclosure') {
            tokens = tokens.concat(this.disclosureTokens(), this.formatTokens());
        }

        if (content_type === 'reminder_mammography_one_year' || content_type === 'reminder_mammography_six_months' || content_type === 'reminder_mammography_pathology') {
            tokens = tokens.concat(this.breastImagingReminderTokens());
        }

        if (content_type === 'sms_worklist_report_ready') {
            tokens = tokens.concat(this.smsWorklistReporReadyTokens());
        }

        if (content_type == null || content_type === '' || tokens.length <= 0) {
            tokens = tokens.concat(this.defaultTokens());
        }

        tokenTextAreaField.setValue(tokens.join("\r\n"));
    },

    patientTokens: function () {
        return [
            '[PATIENT_NAME]',
            '[PATIENT_ID]',
            '[PATIENT_RECORD_NUMBER]',
            '[PATIENT_FULL_NAME]',
            '[PATIENT_LAST_NAME]',
            '[PATIENT_SEX]',
            '[PATIENT_BIRTHDATE]',
            '[PATIENT_MARITAL_STATUS]',
            '[PATIENT_SOCIAL_SECURITY]',
            '[PATIENT_EXTERNAL_ID]',
            '[PATIENT_DRIVERS_LICENSE]',
            '[PATIENT_POSTAL_ADDRESS_LINE_ONE]',
            '[PATIENT_POSTAL_ADDRESS_LINE_TWO]',
            '[PATIENT_POSTAL_CITY]',
            '[PATIENT_POSTAL_STATE]',
            '[PATIENT_POSTAL_ZIP]',
            '[PATIENT_POSTAL_COUNTRY]',
            '[PATIENT_PHYSICAL_ADDRESS_LINE_ONE]',
            '[PATIENT_PHYSICAL_ADDRESS_LINE_TWO]',
            '[PATIENT_PHYSICAL_CITY]',
            '[PATIENT_PHYSICAL_STATE]',
            '[PATIENT_PHYSICAL_ZIP]',
            '[PATIENT_PHYSICAL_COUNTRY]',
            '[PATIENT_HOME_PHONE]',
            '[PATIENT_MOBILE_PHONE]',
            '[PATIENT_WORK_PHONE]',
            '[PATIENT_EMAIL]',
            '[PATIENT_MOTHERS_NAME]',
            '[PATIENT_GUARDIANS_NAME]',
            '[PATIENT_EMERGENCY_CONTACT]',
            '[PATIENT_EMERGENCY_PHONE]',
            '[PATIENT_PROVIDER]',
            '[PATIENT_PHARMACY]',
            '[PATIENT_AGE]',
            '[PATIENT_OCCUPATION]',
            '[PATIENT_EMPLOYEER]',
            '[PATIENT_RACE]',
            '[PATIENT_ETHNICITY]',
            '[PATIENT_LENGUAGE]',
            '[PATIENT_PICTURE]',
            '[PATIENT_QRCODE]',
        ];
    },

    billingTokens: function () {
        return [
            '[SERVICES_TABLE]',
            '[CLAIM_TABLE]',
        ];
    },

    disclosureTokens: function () {
        return [
            '[DISCLOSURE_DOCUMENTS]',
            '[DISCLOSURE_RECIPIENT]',
            '[DISCLOSURE_DESCRIPTION]',
            '[DISCLOSURE_REQUEST_DATE]',
            '[DISCLOSURE_FULFIL_DATE]',
            '[DISCLOSURE_PICKUP_DATE]',
            '[DISCLOSURE_DOCUMENT_COUNT]'
        ];
    },

    breastImagingReminderTokens: function () {
        return [
            '[PATIENT_NAME]',
            '[PATIENT_ADDRESS]',
            '[PATIENT_ADDRESS_CONT]',
            '[PATIENT_CITY]',
            '[PATIENT_STATE]',
            '[PATIENT_ZIP]',
            '[PATIENT_CITY_STATE_ZIP]',
            '[PATIENT_RECORD_NUMBER]',
            '[FACILITY_NAME]',
            '[FACILITY_ADDRESS]',
            '[FACILITY_ADDRESS_CONT]',
            '[FACILITY_CITY_STATE_ZIP]',
            '[SERVICE_DATE]',
            '[TODAY]',
            '[B]',
            '[/B]',
            '[U]',
            '[/U]',
            '[I]',
            '[/I]',
            '[TAB]'
        ];
    },

    smsWorklistReporReadyTokens: function () {
        return [
            '[FACILITY_NAME]',
            '[SERVICE_DATE]',
            '[ACCESSION_NUMBER]'
        ];
    },

    formatTokens: function () {
        return [
            '[TODAY]',
            '[B]',
            '[/B]',
            '[U]',
            '[/U]',
            '[I]',
            '[/I]',
            '[TAB]'
        ];
    },

    defaultTokens: function () {
        return [
            '[PATIENT_NAME]',
            '[PATIENT_RECORD_NUMBER]',
            '[SERVICE_DATE]',
            '[SIGNED_DATE]',
            '[TITLE]',
            '[RADIOLOGIST_SIGNATURE]',
            '[ORDERING_PHYSICIAN]',
            '[FACILITY_NAME]',
            '[FACILITY_PHONE]',
            '[DENSE_BREST]',
            '[NORMAL_BENIGN]',
            '[PROBABLY_BENIGN]',
            '[ADDITIONAL]',
            '[PREVIOUS]',
            '[ABNORMAL]',
            '[RECOMENDATIONS_LIST]',
            '[B]',
            '[/B]',
            '[U]',
            '[/U]',
            '[I]',
            '[/I]'
        ];
    }


});