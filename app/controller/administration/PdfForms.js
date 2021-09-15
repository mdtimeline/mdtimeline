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

Ext.define('App.controller.administration.PdfForms', {
    extend: 'Ext.app.Controller',
    refs: [
        {
            ref: 'AdministrationPdfForms',
            selector: '#AdministrationPdfForms'
        },
        {
            ref: 'AdministrationPdfFormsGrid',
            selector: '#AdministrationPdfFormsGrid'
        },
        {
            ref: 'AdministrationPdfFormsTokenPanel',
            selector: '#AdministrationPdfFormsTokenPanel'
        },
        {
            ref: 'AdministrationPdfFormsAddBtn',
            selector: '#AdministrationPdfFormsAddBtn'
        }
    ],

    init: function () {
        var me = this;

        me.control({
            '#AdministrationPdfForms': {
                activate: me.onAdministrationPdfFormsActivate
            },
            '#AdministrationPdfFormsTokenPanel': {
                beforerender: me.onAdministrationPdfFormsTokenPanelBeforeRender
            },
            '#AdministrationPdfFormsAddBtn': {
                click: me.onAdministrationPdfFormsAddBtnClick
            }
        });
    },

    onAdministrationPdfFormsActivate: function () {
        this.getAdministrationPdfFormsGrid().getStore().load();
    },

    onAdministrationPdfFormsAddBtnClick: function () {
        var grid = this.getAdministrationPdfFormsGrid(),
            store = grid.getStore(),
            plugin = grid.editingPlugin;

        plugin.cancelEdit();

        plugin.startEdit(store.add({
            signature_x: 0,
            signature_y: 0,
            signature_w: 0,
            signature_h: 0,
            flatten: true
        })[0], 0);

    },

    onAdministrationPdfFormsTokenPanelBeforeRender: function (panel){
        panel.update(this.patientTokens().join('<br>'));
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
            '[PATIENT_EMPLOYER]',
            '[PATIENT_RACE]',
            '[PATIENT_ETHNICITY]',
            '[PATIENT_LANGUAGE]',
            '[PATIENT_PICTURE]',
            '[PATIENT_QRCODE]',
            '[FACILITY_NAME]',
            '[FACILITY_PHONE]',
        ];
    }


});