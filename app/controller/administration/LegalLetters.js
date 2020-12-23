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

Ext.define('App.controller.administration.LegalLetters', {
    extend: 'Ext.app.Controller',
    refs: [
        {
            ref: 'AdministrationLegalLetters',
            selector: '#AdministrationLegalLetters'
        },
        {
            ref: 'AdministrationLegalLettersGrid',
            selector: '#AdministrationLegalLettersGrid'
        },
        {
            ref: 'AdministrationLegalLetterWindow',
            selector: '#AdministrationLegalLetterWindow'
        }
    ],

    init: function () {
        var me = this;

        me.control({
            '#AdministrationLegalLetters': {
                activate: me.onAdministrationLegalLettersActivate
            },
            '#AdministrationLegalLettersGrid': {
                itemdblclick: me.onAdministrationLegalLettersGridItemDblClick
            },
            '#AdministrationLegalLettersAddBtn': {
                click: me.onAdministrationLegalLettersAddBtnClick
            },
            '#AdministrationLegalLetterCancelBtn': {
                click: me.onAdministrationLegalLetterCancelBtnClick
            },
            '#AdministrationLegalLetterSaveBtn': {
                click: me.onAdministrationLegalLetterSaveBtnClick
            }
        });
    },

    onAdministrationLegalLettersGridItemDblClick: function (grid, letter_record){
        var win = this.showAdministrationLegalLetterWindow();
        win.down('form').getForm().loadRecord(letter_record);
    },

    onAdministrationLegalLetterCancelBtnClick: function (btn){
        var win = btn.up('window');
        win.down('form').getForm().reset(true);
        this.getAdministrationLegalLettersGrid().getStore().rejectChanges();
        win.close();
    },

    onAdministrationLegalLetterSaveBtnClick: function (btn){
        var win = btn.up('window'),
            form = win.down('form').getForm(),
            record = form.getRecord(),
            values = form.getValues()

        if(!form.isValid()){
            return;
        }

        record.set(values);

        if(record.store.getModifiedRecords().length > 0){
            record.store.sync({
                callback: function (){
                    win.close();
                }
            });
        }else{
            win.close();
        }
    },

    onAdministrationLegalLettersActivate: function () {
        this.getAdministrationLegalLettersGrid().getStore().load();
    },

    onAdministrationLegalLettersAddBtnClick: function () {
        var win = this.showAdministrationLegalLetterWindow(),
            added_records = this.getAdministrationLegalLettersGrid().getStore().add({
            workflow: 'PRE-REGISTRATION',
            days_valid_for: 365,
            version: '1.0',
            active: false
        })

        win.down('form').getForm().loadRecord(added_records[0]);

    },

    showAdministrationLegalLetterWindow: function (){

        if(!this.getAdministrationLegalLetterWindow()){

            Ext.create('Ext.window.Window',{
                title: _('legal_letter'),
                layout: 'fit',
                bodyPadding: 5,
                width: 800,
                itemId: 'AdministrationLegalLetterWindow',
                items: [
                    {
                        xtype: 'form',
                        bodyPadding: 10,
                        items: [
                            {
                                xtype: 'textfield',
                                fieldLabel: _('title'),
                                name: 'title',
                                anchor: '100%',
                                allowBlank: false
                            },
                            {
                                xtype: 'activefacilitiescombo',
                                fieldLabel: _('facility'),
                                name: 'facility_id'
                            },
                            {
                                xtype: 'combobox',
                                fieldLabel: _('workflow'),
                                name: 'workflow',
                                editable: false,
                                store: ['PRE-REGISTRATION'],
                                allowBlank: false
                            },
                            {
                                xtype: 'htmleditor',
                                fieldLabel: _('content'),
                                name: 'content',
                                height: 400,
                                anchor: '100%',
                                allowBlank: false
                            },
                            {
                                xtype: 'numberfield',
                                fieldLabel: _('days_valid_for'),
                                name: 'days_valid_for',
                                allowBlank: false,
                                minValue: -1,
                                maxValue: 9999
                            },
                            {
                                xtype: 'textfield',
                                fieldLabel: _('version'),
                                name: 'version',
                                allowBlank: false
                            },
                            {
                                xtype: 'checkbox',
                                fieldLabel: _('active'),
                                name: 'active'
                            }
                        ]
                    }
                ],
                buttons: [
                    {
                        xtype: 'button',
                        text: _('cancel'),
                        itemId: 'AdministrationLegalLetterCancelBtn'
                    },
                    {
                        xtype: 'button',
                        text: _('save'),
                        itemId: 'AdministrationLegalLetterSaveBtn'
                    }
                ]
            });

        }
        return this.getAdministrationLegalLetterWindow().show();
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