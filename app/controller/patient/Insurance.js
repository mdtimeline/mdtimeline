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
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

Ext.define('App.controller.patient.Insurance', {
    extend: 'Ext.app.Controller',
    requires: [],
    refs: [
        {
            ref: 'PatientInsuranceFormSubscribeRelationshipCmb',
            selector: '#PatientInsuranceFormSubscribeRelationshipCmb'
        },
        {
            ref: 'PatientInsurancesPanel',
            selector: '#PatientInsurancesPanel'
        },
        {
            ref: 'PatientInsurancesForm',
            selector: '#PatientInsurancesForm'
        },
        {
            ref: 'PatientInsurancesPanelSaveBtn',
            selector: '#PatientInsurancesPanelSaveBtn'
        },
        {
            ref: 'PatientInsurancesPanelCancelBtn',
            selector: '#PatientInsurancesPanelCancelBtn'
        },
        {
            ref: 'InsuranceCardFirstNameField',
            selector: '#InsuranceCardFirstNameField'
        },
        {
            ref: 'InsuranceCardMiddleNameField',
            selector: '#InsuranceCardMiddleNameField'
        },
        {
            ref: 'InsuranceCardLastNameField',
            selector: '#InsuranceCardLastNameField'
        },
        {
            ref: 'InsuranceAddressSameAsPatientField',
            selector: '#InsuranceAddressSameAsPatientField'
        },


        {
            ref: 'BillingPatientInsuranceCoverInformationCoverGrid',
            selector: '#BillingPatientInsuranceCoverInformationCoverGrid'
        },
        {
            ref: 'BillingPatientInsuranceCoverInformationCoverGridUpdateDateField',
            selector: '#BillingPatientInsuranceCoverInformationCoverGridUpdateDateField'
        },
        {
            ref: 'BillingPatientInsuranceCoverInformationCoverExceptionSearchField',
            selector: '#BillingPatientInsuranceCoverInformationCoverExceptionSearchField'
        },
        {
            ref: 'BillingPatientInsuranceCoverInformationDeductibleField',
            selector: '#BillingPatientInsuranceCoverInformationDeductibleField'
        }

    ],

    init: function () {
        var me = this;

        me.control({
            'viewport': {
                demographicsrecordload: me.onDemographicsRecordLoad
            },
            '#PatientInsurancesPanel': {
                beforeadd: me.onPatientInsurancesPanelBeforeAdd,
                newtabclick: me.onPatientInsurancesPanelNewTabClick
            },

            'patientinsuranceform': {
                loadrecord: me.onPatientInsurancesFormLoadRecord
            },

            '#PatientInsuranceFormSubscribeRelationshipCmb': {
                select: me.onPatientInsuranceFormSubscribeRelationshipCmbSelect
            },
            '#InsuranceSameAsPatientField': {
                change: me.onInsuranceSameAsPatientFieldChange
            },
            '#InsuranceAddressSameAsPatientField': {
                change: me.onInsuranceAddressSameAsPatientFieldChange
            },
            '#PatientInsurancesPanelSaveBtn': {
                click: me.onPatientInsurancesPanelSaveBtnClick
            },
            '#PatientInsurancesPanelCancelBtn': {
                click: me.onPatientInsurancesPanelCancelBtnClick
            },

            '#BillingPatientInsuranceCoverInformationCoverGrid': {
                edit: me.onBillingPatientInsuranceCoverInformationCoverGridEdit,
                validateedit: me.onBillingPatientInsuranceCoverInformationCoverGridValidateEdit
            },

            '#BillingPatientInsuranceCoverInformationCoverExceptionSearchField': {
                select: me.onBillingPatientInsuranceCoverInformationCoverExceptionSearchFieldSelect,
                change: me.onBillingPatientInsuranceCoverInformationCoverExceptionSearchFieldchange,
                render: me.onBillingPatientInsuranceCoverInformationCoverExceptionSearchFieldRender
            }

        });
    },


    getActiveInsuranceFormPanel: function () {
        return this.getPatientInsurancesPanel().getActiveTab();
    },

    patientInsurancePanelHandler: function (insurance_store) {
        var me = this,
            insurance_panel = me.getPatientInsurancesPanel();

        insurance_store.sort([
            {
                sorterFn: function (o1, o2) {
                    var getRank = function (o) {
                            var insurance_type = o.get('insurance_type');

                            if (insurance_type === 'P') {
                                return 1;
                            } else if (insurance_type === 'C') {
                                return 2;
                            } else if (insurance_type === 'S') {
                                return 3;
                            } else if (insurance_type === 'T') {
                                return 4;
                            } else {
                                return 5;
                            }
                        },
                        rank1 = getRank(o1),
                        rank2 = getRank(o2);

                    if (rank1 === rank2) {
                        return 0;
                    }

                    return rank1 < rank2 ? -1 : 1;
                }
            }
        ]);

        // set the insurance panel
        insurance_panel.removeAll(true);

        var insurance_records = insurance_store.data.items;

        for (var i = 0; i < insurance_records.length; i++) {
            insurance_panel.add(
                Ext.widget('patientinsuranceform', {
                    closable: false,
                    insurance: insurance_records[i],
                    action: insurance_records[i].get('insurance_type')
                })
            );
        }

        if (insurance_panel.items.length > 0) insurance_panel.setActiveTab(0);

    },

    onDemographicsRecordLoad: function (patient_record, patient_panel) {

        var me = this,
            insurance_store = patient_record.insurance();

        insurance_store.load({
            filters: [
                {
                    property: 'pid',
                    value: patient_record.get('pid')
                }
            ],
            callback: function (records) {
                me.patientInsurancePanelHandler(insurance_store);
            }
        });
    },

    insuranceFormLoadRecord: function (form, record) {
        form.getForm().loadRecord(record);
        app.fireEvent('insurancerecordload', form, record);
    },

    onPatientInsurancesFormLoadRecord: function (form, insurance_record) {
        var cover_grid = form.owner.down('grid'), //this.getBillingPatientInsuranceCoverInformationCoverGrid(),
            patient_id = insurance_record.get('pid'),
            patient_insurance_id = insurance_record.get('id');

        if (!cover_grid) return;

        cover_grid.getStore().load({
            filters: [
                {
                    property: 'patient_id',
                    value: patient_id
                },
                {
                    property: 'patient_insurance_id',
                    value: patient_insurance_id
                }
            ]
        })
    },

    getPatientInsurancesByType: function (insurance_type) {

        var me = this,
            insurance_panel = me.getPatientInsurancesPanel(),
            insurance_tabs = insurance_panel.query(Ext.String.format('panel[action={0}]', insurance_type)),
            insurance_records = [];

        for (var i = 0; i < insurance_tabs.length; i++) {
            insurance_records.push(insurance_tabs[0].insurance);
        }

        return insurance_records;

    },

    onPatientInsurancesPanelBeforeAdd: function (tapPanel, panel) {
        var me = this,
            form = panel.getForm(),
            record = panel.insurance || Ext.create('App.model.patient.Insurance', {pid: me.pid});

        me.insuranceFormLoadRecord(panel, record);

        if (record.get('image') !== '') panel.down('image').setSrc(record.get('image'));

        panel.title = record.get('ins_synonym') + ' (' + (record.get('insurance_type') ? record.get('insurance_type') : _('new')) + ')';
    },

    onPatientInsurancesPanelNewTabClick: function (form) {

        var me = this,
            insurance_panel = me.getPatientInsurancesPanel(),
            insuranceTabs = insurance_panel.items.length;

        /**
         * SEL = Self
         */

        var record = Ext.create('App.model.patient.Insurance', {
            code: insuranceTabs + '~' + app.patient.pubpid,
            pid: app.patient.pid,

            card_name_same_as_patient: true,
            card_first_name: app.patient.record.get('fname'),
            card_middle_name: app.patient.record.get('mname'),
            card_last_name: app.patient.record.get('lname'),

            subscriber_relationship: 'SEL',
            subscriber_title: app.patient.record.get('title'),
            subscriber_given_name: app.patient.record.get('fname'),
            subscriber_middle_name: app.patient.record.get('mname'),
            subscriber_surname: app.patient.record.get('lname'),
            subscriber_dob: app.patient.record.get('DOB'),
            subscriber_sex: app.patient.record.get('sex'),
            subscriber_phone: (app.patient.record.get('phone_mobile') || app.patient.record.get('phone_home')),
            subscriber_employer: app.patient.record.get('employer_name'),

            subscriber_street: app.patient.record.get('postal_address') + ', ' + app.patient.record.get('postal_address_cont'),
            subscriber_city: app.patient.record.get('postal_city'),
            subscriber_state: app.patient.record.get('postal_state'),
            subscriber_country: app.patient.record.get('postal_country'),
            subscriber_postal_code: app.patient.record.get('postal_zip'),
            subscriber_address_same_as_patient: true,

            create_uid: app.user.id,
            update_uid: app.user.id,
            create_date: new Date(),
            update_date: new Date()
        });

        say('app.patient.record');
        say(app.patient.record);

        this.insuranceFormLoadRecord(form, record);
    },

    onInsuranceSameAsPatientFieldChange: function (field, value) {
        field.up('fieldcontainer').down('#InsuranceCardFirstNameField').setDisabled(value);
        field.up('fieldcontainer').down('#InsuranceCardMiddleNameField').setDisabled(value);
        field.up('fieldcontainer').down('#InsuranceCardLastNameField').setDisabled(value);
    },

    onInsuranceAddressSameAsPatientFieldChange: function (field, value) {
        field.up('fieldset').down('#InsuranceSubscriberStreetField').setDisabled(value);
        field.up('fieldset').down('#InsuranceSubscriberCityField').setDisabled(value);
        field.up('fieldset').down('#InsuranceSubscriberStatetField').setDisabled(value);
        field.up('fieldset').down('#InsuranceSubscriberPostalField').setDisabled(value);
        field.up('fieldset').down('#InsuranceSubscriberCountryField').setDisabled(value);
    },

    onPatientInsuranceFormSubscribeRelationshipCmbSelect: function (cmb, records) {
        var form = cmb.up('form').getForm(),
            ins_record = form.getRecord();

        form.findField('subscriber_relationship').setValue(records[0].get('option_value'));

        /**
         * SEL = Self
         */

        if (records[0].get('option_value') !== 'SEL') return;

        form.findField('subscriber_title').setValue(app.patient.record.get('title'));
        form.findField('subscriber_given_name').setValue(app.patient.record.get('fname'));
        form.findField('subscriber_middle_name').setValue(app.patient.record.get('mname'));
        form.findField('subscriber_surname').setValue(app.patient.record.get('lname'));
        form.findField('subscriber_dob').setValue(app.patient.record.get('DOB'));
        form.findField('subscriber_sex').setValue(app.patient.record.get('sex'));
        form.findField('subscriber_phone').setValue(app.patient.record.get('phone_mobile') || app.patient.record.get('phone_home'));
        form.findField('subscriber_employer').setValue(app.patient.record.get('employer_name'));

        // this.getInsuranceAddressSameAsPatientField().setValue(true);

        // form.findField('subscriber_address_same_as_patient').setValue(true);
    },

    onPatientInsurancesPanelCancelBtnClick: function (btn) {
        var form_panel = this.getActiveInsuranceFormPanel(),
            form = form_panel.getForm(),
            record = form.getRecord();

        if (record.get('id') > 0) {
            form.reset();
            form.loadRecord(record);

            return;
        }

        this.getPatientInsurancesPanel().remove(form_panel);

    },


    /**
     * PATIENT INSURANCE COVER PANEL FUNCTIONS
     */

    onBillingPatientInsuranceCoverInformationCoverGridEdit: function (plugin, context) {

    },

    onBillingPatientInsuranceCoverInformationCoverExceptionSearchFieldchange: function (field, newValue, oldValue, eOpts) {

        if (newValue) { return; }

        var cover_grid = field.up('fieldset').down('grid'),
            cover_grid_store = cover_grid.getStore(),
            cover_grid_records = cover_grid_store.getRange();

        for (var c = 0; c < cover_grid_records.length; c++) {
            cover_grid_records[c].set({
                exception_copay: 0.00,
                exception_isDollar: true,
                exception: false
            });
        }
    },

    //***********************
    //*  NEEDS VALIDATION   *
    //***********************




    //Grid Functions (Elegibility Btn) has its own controller
    onBillingPatientInsuranceCoverInformationCoverGridValidateEdit: function (plugin, context) {

        if (context.field = 'isDollar') return;

        var cover_record = context.record,
            prev_copay = cover_record.get('copay'),
            copay = context.value;

        cover_record.set({
            copay: copay,
            update_date: new Date(),
            update_uid: app.user.id
        });
    },

    onBillingPatientInsuranceCoverInformationCoverExceptionSearchFieldRender: function(field){

        field.store.on('beforeload', function (store) {

            var insurance_id = field.up('form').getForm().findField('insurance_id').getValue();

            store.getProxy().extraParams = { insurance_id: insurance_id  };

        }, this);
    },

    onBillingPatientInsuranceCoverInformationCoverExceptionSearchFieldSelect: function (field, selected_cover) {

        if (field.value.length === 0) return;

        var me = this,
            cover_form = field.up('form'),
            insurance_form = cover_form.getForm(),
            insurance_form_record = insurance_form.getRecord();

        me.getBillingPatientInsuranceCoverInformationDeductibleField().setValue(selected_cover[0].get('deductible'));
        me.getBillingPatientInsuranceCoverInformationCoverExceptionSearchField().setValue(selected_cover[0].get('cover'));

        var cover_grid = field.up('fieldset').down('grid'),
            cover_grid_store = cover_grid.getStore(),
            cover_grid_records = cover_grid_store.getRange();

        BillingCover.getBillingCoverExceptionByCoverId(selected_cover[0].get('id'), function (response) {

            for (var c = 0; c < cover_grid_records.length; c++) {
                cover_grid_records[c].set({
                    exception_copay: 0.00,
                    exception_isDollar: true,
                    exception: false
                });
            }


            for (var r = 0; r < response.length; r++) {

                for (var i = 0; i < cover_grid_records.length; i++) {

                    if (cover_grid_records[i].get('service_type_id') === response[r].service_type_id ) {
                        cover_grid_records[i].set({
                            exception_copay: response[r].copay,
                            exception_isDollar: response[r].isDollar,
                            exception: true,
                            update_date: new Date(),
                            update_uid: app.user.id
                        });
                    }
                }
            }

        });
    },

    onPatientInsurancesPanelSaveBtnClick: function (btn) {
        var me = this,
            insurance_panel = me.getPatientInsurancesPanel(),
            insuranceItems = insurance_panel.items;

        insuranceItems.each(function (form_panel) {

            var form = form_panel.getForm(),
                values = form.getValues(),
                record = form.getRecord();

            if (!form.isValid()) return;

            record.set(values);

            /**
             *  Instruccion en Comentario, si no, el Grid de Cubierta no ejecuta el save. CARLI
             *  if (Ext.Object.isEmpty(record.getChanges())) return;
             */

            record.save({

                callback: function () {

                    app.msg(_('sweet'), _('record_saved'));

                    var cover_grid_store = form_panel.down('grid').getStore(),
                        cover_grid_records = cover_grid_store.getRange();

                    for (var i = 0; i < cover_grid_records.length; i++) {

                        cover_grid_records[i].set({
                            patient_insurance_id: record.get('id')
                        });

                    }

                    cover_grid_store.sync({
                        success: function () {
                        }
                    });
                }

            });
        });
    }



});