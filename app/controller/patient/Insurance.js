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
            ref: 'PatientDemographicsPanel',
            selector: '#PatientDemographicsPanel'
        },
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
            ref: 'InsuranceAddressSameAsPatientBtn',
            selector: '#InsuranceAddressSameAsPatientBtn'
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
            ref: 'BillingPatientInsuranceCoverInformationMspInsuranceTypeField',
            selector: '#BillingPatientInsuranceCoverInformationMspInsuranceTypeField'
        },
        {
            ref: 'BillingPatientInsuranceCoverInformationDeductibleField',
            selector: '#BillingPatientInsuranceCoverInformationDeductibleField'
        },
        {
            ref: 'PatientInsurancesFormIsActiveBtn',
            selector: '#PatientInsurancesFormIsActiveBtn'
        },

        // insurance window
        {
            ref: 'PatientInsurancesWindow',
            selector: '#PatientInsurancesWindow'
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
            '#InsuranceAddressSameAsPatientBtn': {
                click: me.onInsuranceAddressSameAsPatientBtnClick
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
            },
            '#BillingPatientInsuranceCoverInformationMspInsuranceTypeField': {
                select: me.onBillingPatientInsuranceCoverInformationMspInsuranceTypeFieldSelect
            },

            '#PatientInsurancesWindow': {
                show: me.onPatientInsurancesWindowShow
            },
            '#PatientInsurancesWindowSaveBtn': {
                click: me.onPatientInsurancesWindowSaveBtnClick
            },
            '#PatientInsurancesWindowCancelBtn': {
                click: me.onPatientInsurancesWindowCancelBtnClick
            },

            '#InsuranceSubscriberAddressCopyBtn': {
                click: me.onInsuranceSubscriberAddressCopyBtnClick
            },

            '#PatientInsurancesFormIsActiveCkBox': {
                change: me.onPatientInsurancesFormIsActiveCkBoxChange
            },

            '#PatientInsuranceFormScannerBtn': {
                click: me.onPatientInsuranceFormScannerBtnClick
            },
            '#PatientInsuranceFormScannerOcrBtn': {
                click: me.onPatientInsuranceFormScannerOcrBtnClick
            }

        });

        me.scannerCtrl = me.getController('Scanner');

        me.doPatientInsurancesWindowCloseBuffered = Ext.Function.createBuffered(me.doPatientInsurancesWindowClose, 250, me);
    },

    onPatientInsuranceFormScannerBtnClick: function (btn){

        var me = this,
            insurance_img_container = btn.up('#PatientInsuranceFormCardContainer'),
            insurance_img = insurance_img_container.down('image'),
            textareafield = insurance_img_container.down('textareafield');

        // say('onPatientInsuranceFormScannerBtnClick');
        // say(insurance_img_container);
        // say(insurance_img);

        me.scannerCtrl.showBasicScanWindow([], function (scanned_images){

            var img = new Image();
            img.crossOrigin = "Anonymous";
            img.onload = function () {
                var imageData = me.trimImage(img);
                insurance_img.setSrc(imageData);
                insurance_img_container.doLayout();
            };
            img.src = 'data:image/jpeg;base64,' + scanned_images[0];
            textareafield.setValue('data:image/jpeg;base64,' + scanned_images[0]);

        });
    },

    onPatientInsuranceFormScannerOcrBtnClick: function (btn){
        var me = this,
            insurance_img_container = btn.up('#PatientInsuranceFormCardContainer'),
            insurance_img = insurance_img_container.down('image'),
            mask = new Ext.LoadMask({
                msg: 'Progress... 0%',
                target: insurance_img
            });

        mask.show();

        Tesseract.recognize(
            insurance_img.src,
            'eng',
            {
                logger: function (m){

                    say(m);

                    if(m.status === 'recognizing text'){
                        mask.msgTextEl.update(Ext.String.format('Progress... {0}%', Math.floor(m.progress * 100)));
                    }
                 }
            }
        ).then(function (data) {
            mask.hide();
            mask.destroy();
            me.onOcrComplete(data, insurance_img);
        });

    },

    onOcrComplete: function (data, insurance_img){
        say('onOcrComplete');
        say(data);
        say(insurance_img);



    },

    trimImage: function (imageObject) {

        var rbgThreshold = 255;
        var imgWidth = imageObject.width;
        var imgHeight = imageObject.height;
        var canvas = document.createElement('canvas');
        canvas.setAttribute("width", imgWidth);
        canvas.setAttribute("height", imgHeight);
        var context = canvas.getContext('2d');
        context.drawImage(imageObject, 0, 0);

        var imageData = context.getImageData(0, 0, imgWidth, imgHeight),
            data = imageData.data,
            getRBG = function(x, y) {
                var offset = imgWidth * y + x;
                return {
                    red:     data[offset * 4],
                    green:   data[offset * 4 + 1],
                    blue:    data[offset * 4 + 2],
                    opacity: data[offset * 4 + 3]
                };
            },
            isWhite = function (rgb) {
                // many images contain noise, as the white is not a pure #fff white
                return rgb.red > rbgThreshold && rgb.green > rbgThreshold && rgb.blue > rbgThreshold;
            },
            scanY = function (fromTop) {
                var offset = fromTop ? 1 : -1;

                // loop through each row
                for(var y = fromTop ? 0 : imgHeight - 1; fromTop ? (y < imgHeight) : (y > -1); y += offset) {

                    // loop through each column
                    for(var x = 0; x < imgWidth; x++) {
                        var rgb = getRBG(x, y);
                        if (!isWhite(rgb)) {
                            if (fromTop) {
                                return y;
                            } else {
                                return Math.min(y + 1, imgHeight);
                            }
                        }
                    }
                }
                return null; // all image is white
            },
            scanX = function (fromLeft) {
                var offset = fromLeft? 1 : -1;

                // loop through each column
                for(var x = fromLeft ? 0 : imgWidth - 1; fromLeft ? (x < imgWidth) : (x > -1); x += offset) {

                    // loop through each row
                    for(var y = 0; y < imgHeight; y++) {
                        var rgb = getRBG(x, y);
                        if (!isWhite(rgb)) {
                            if (fromLeft) {
                                return x;
                            } else {
                                return Math.min(x + 1, imgWidth);
                            }
                        }
                    }
                }
                return null; // all image is white
            };

        var cropTop = scanY(true),
            cropBottom = scanY(false),
            cropLeft = scanX(true),
            cropRight = scanX(false),
            cropWidth = cropRight - cropLeft,
            cropHeight = cropBottom - cropTop;

        canvas.setAttribute("width", cropWidth);
        canvas.setAttribute("height", cropHeight);
        // finally crop the guy

        context.drawImage(imageObject,
            cropLeft, cropTop, cropWidth, cropHeight,
            0, 0, cropWidth, cropHeight);

        return canvas.toDataURL();
    },


    onPatientInsurancesFormIsActiveCkBoxChange: function (field){

        var form = field.up('form').getForm(),
            values = form.getValues();


    },

    onInsuranceSubscriberAddressCopyBtnClick: function (btn){
        var form = btn.up('form').getForm(),
            values = form.getValues();

        form.setValues({
            physical_address: values.postal_address,
            physical_address_cont: values.postal_address_cont,
            physical_city: values.postal_city,
            physical_state: values.postal_state,
            physical_zip: values.postal_zip,
            physical_country: values.postal_country,
        });

        app.msg(_('info'), _('postal_address_copied'), 'blue');
    },

    onPatientInsurancesWindowSaveBtnClick: function(btn){

        var me = this,
            win = btn.up('window'),
            insurance_panel = btn.up('insurancestabpanel'),
            insuranceItems = insurance_panel.items;

        insuranceItems.each(function (form_panel) {

            var form = form_panel.getForm(),
                values = form.getValues(),
                record = form.getRecord();

            if (!form.isValid()) return;

            record.set(values);

            record.save({
                callback: function (saved_record) {
                    app.msg(_('sweet'), _('record_saved'));

                    var cover_grid_store = form_panel.down('grid').getStore(),
                        cover_grid_records = cover_grid_store.getRange();

                    for (var i = 0; i < cover_grid_records.length; i++) {
                        cover_grid_records[i].set({
                            patient_insurance_id: record.get('id')
                        });
                    }

                    if(Ext.Object.isEmpty(cover_grid_store.getModifiedRecords())){
                        me.updatePatientInsuranceForm(saved_record);
                        me.doPatientInsurancesWindowCloseBuffered(win);
                    }else{
                        cover_grid_store.sync({
                            success: function () {
                                me.updatePatientInsuranceForm(saved_record);
                                me.doPatientInsurancesWindowCloseBuffered(win);
                            }
                        });
                    }
                }
            });
        });
    },

    updatePatientInsuranceForm: function(){
        say('updatePatientInsuranceForm');

        this.patientInsurancePanelHandler(app.patient.record.insurance(), this.getPatientInsurancesPanel());
    },

    doPatientInsurancesWindowClose: function(win){
        win.close();
    },

    onPatientInsurancesWindowCancelBtnClick: function(btn){
        btn.up('window').close();
    },

    onPatientInsurancesWindowShow: function(win){
        var me = this,
            insurance_store = app.patient.record.insurance(),
            insurance_panel = win.down('insurancestabpanel');

        if(insurance_store.count() > 0){
            me.patientInsurancePanelHandler(insurance_store, insurance_panel);
        }else{
            insurance_store.load({
                filters: [
                    {
                        property: 'pid',
                        value: app.patient.record.get('pid')
                    }
                ],
                callback: function (records) {
                    me.patientInsurancePanelHandler(insurance_store, insurance_panel);
                }
            });
        }
    },

    showPatientInsurancesWindow: function(){
        if(!this.getPatientInsurancesWindow()){
            Ext.create('App.view.patient.windows.PatientInsurancesWindow');
        }
        return this.getPatientInsurancesWindow().show();
    },

    getActiveInsuranceFormPanel: function () {
        return this.getPatientInsurancesPanel().getActiveTab();
    },

    patientInsurancePanelHandler: function (insurance_store, insurance_panel) {

        say('patientInsurancePanelHandler');

        insurance_store.sort([
            {
                sorterFn: function (o1, o2) {
                    var getRank = function (o) {

                            var insurance_type = o.get('insurance_type'),
                                active = o.get('active');

                            if ((insurance_type === 'P') && (active)) {
                                return 1;
                            } else if ((insurance_type === 'C') && (active)) {
                                return 2;
                            } else if ((insurance_type === 'S') && (active)) {
                                return 3;
                            } else if ((insurance_type === 'T') && (active)) {
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

        var insurance_records = insurance_store.data.items,
            items = [], i;

        for (i = 0; i < insurance_records.length; i++) {
            items.push({
                xtype: 'patientinsuranceform',
                closable: false,
                insurance: insurance_records[i],
                action: insurance_records[i].get('insurance_type')
            });
        }

        insurance_panel.add(items);

        say(insurance_panel);

        if (insurance_panel.items.length > 0) insurance_panel.setActiveTab(0);

    },

    onDemographicsRecordLoad: function (patient_record, patient_panel) {

        var me = this,
            insurance_store = app.patient.record.insurance(),
            insurance_panel = patient_panel.ownerCt.down('insurancestabpanel');

        insurance_store.load({
            filters: [
                {
                    property: 'pid',
                    value: patient_record.get('pid')
                }
            ],
            callback: function (records) {
                me.patientInsurancePanelHandler(insurance_store, insurance_panel);
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

        if (record.get('active')) {
            panel.setIcon('modules/billing/resources/images/icoDotGreen.png');
        } else {
            panel.setIcon('modules/billing/resources/images/icoDotRed.png');
        }
        panel.title = record.get('ins_synonym') + ' (' + (record.get('insurance_type') ? record.get('insurance_type') : _('new')) + ')' ;
        panel.header.hide(true);

    },

    onPatientInsurancesPanelNewTabClick: function (form) {

        var me = this,
            insurance_panel = me.getPatientInsurancesPanel(),
            insuranceTabs = insurance_panel.items.length;

        /**
         * 01 = Self
         */

        var record = Ext.create('App.model.patient.Insurance', {
            code: insuranceTabs + '~' + app.patient.pubpid,
            pid: app.patient.pid,

            card_name_same_as_patient: true,
            card_first_name: app.patient.record.get('fname'),
            card_middle_name: app.patient.record.get('mname'),
            card_last_name: app.patient.record.get('lname'),

            subscriber_relationship: '01',
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

            active: true,
            create_uid: app.user.id,
            update_uid: app.user.id,
            create_date: new Date(),
            update_date: new Date()
        });

        // say('app.patient.record');
        // say(app.patient.record);

        this.insuranceFormLoadRecord(form, record);
    },

    onInsuranceAddressSameAsPatientBtnClick: function (field, value) {

        var insurance_form = field.up('form').getForm(),
            demographic_panel = this.getPatientDemographicsPanel(),
            demographic_form = demographic_panel.down('form').getForm(),
            demographic_values = demographic_form.getValues();

        insurance_form.setValues({
            subscriber_street: Ext.String.format('{0} {1}', demographic_values.postal_address,  demographic_values.postal_address_cont).trim(),
            subscriber_city: demographic_values.postal_city,
            subscriber_state: demographic_values.postal_state,
            subscriber_postal_code: demographic_values.postal_zip,
            subscriber_country: demographic_values.postal_country,
        });

        app.msg(_('info'), _('postal_address_copied'), 'blue');
    },

    onPatientInsuranceFormSubscribeRelationshipCmbSelect: function (cmb, records) {
        var form = cmb.up('form').getForm(),
            ins_record = form.getRecord();

        form.findField('subscriber_relationship').setValue(records[0].get('option_value'));

        /**
         * SEL = Self == 01
         */

        if (records[0].get('option_value') !== '01') return;

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
        var form_panel = this.getActiveInsuranceFormPanel();

        if (!form_panel) return;

        var form = form_panel.getForm(),
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

    onBillingPatientInsuranceCoverInformationMspInsuranceTypeFieldSelect: function (field, selected_mspType) {

        if (field.value.length === 0) return;

        var me = this,
            cover_form = field.up('form'),
            insurance_form = cover_form.getForm(),
            insurance_form_record = insurance_form.getRecord();

        insurance_form_record.set({
            msp_insurance_type: selected_mspType[0].get('code')
        });

    },

    //***********************
    //*  NEEDS VALIDATION   *
    //***********************


    //Grid Functions (Elegibility Btn) has its own controller

    onBillingPatientInsuranceCoverInformationCoverGridValidateEdit: function (plugin, context) {
        if (context.field === 'copay') {
            var cover_record = context.record,
                prev_copay = cover_record.get('copay'),
                copay = context.value;

            cover_record.set({
                copay: copay,
                update_date: new Date(),
                update_uid: app.user.id
            });
        }

        if (context.field === 'exception_copay') {
            var cover_record = context.record,
                prev_e_copay = cover_record.get('exception_copay'),
                e_copay = context.value;

            cover_record.set({
                exception_copay: e_copay,
                exception_isDollar: true,
                exception: true,
                update_date: new Date(),
                update_uid: app.user.id
            });
        }


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
            insurance_panel = btn.up('insurancestabpanel'),
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