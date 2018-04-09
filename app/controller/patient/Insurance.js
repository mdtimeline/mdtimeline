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

Ext.define('App.controller.patient.Insurance', {
	extend: 'Ext.app.Controller',
	requires: [

	],
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
		}
	],

	init: function(){
		var me = this;

		me.control({
			'viewport':{
				demographicsrecordload: me.onDemographicsRecordLoad
			},
			'#PatientInsurancesPanel':{
				beforeadd: me.onPatientInsurancesPanelBeforeAdd,
				newtabclick: me.onPatientInsurancesPanelNewTabClick
			},
			'#PatientInsuranceFormSubscribeRelationshipCmb':{
				select: me.onPatientInsuranceFormSubscribeRelationshipCmbSelect
			},
			'#InsuranceSameAsPatientField':{
				change: me.onInsuranceSameAsPatientFieldChange
			},
			'#InsuranceAddressSameAsPatientField':{
				change: me.onInsuranceAddressSameAsPatientFieldChange
			},
			'#PatientInsurancesPanelSaveBtn':{
				click: me.onPatientInsurancesPanelSaveBtnClick
			},
			'#PatientInsurancesPanelCancelBtn':{
				click: me.onPatientInsurancesPanelCancelBtnClick
			},
			'patientinsuranceform':{
                loadrecord: me.onPatientInsurancesFormLoadRecord
			}
		});
	},


	getActiveInsuranceFormPanel: function () {
		return this.getPatientInsurancesPanel().getActiveTab();
	},

	onPatientInsurancesPanelSaveBtnClick: function (btn) {
		var form = this.getActiveInsuranceFormPanel().getForm(),
			values = form.getValues(),
			record = form.getRecord();

		if(!form.isValid()) return;

		record.set(values);

		if(Ext.Object.isEmpty(record.getChanges())) return;

		record.save({
			callback: function () {
				app.msg(_('sweet'), _('record_saved'));
			}
		});

	},

	onPatientInsurancesPanelCancelBtnClick: function (btn) {
		var form_panel = this.getActiveInsuranceFormPanel(),
			form = form_panel.getForm(),
			record = form.getRecord();

		if(record.get('id')  > 0){
			form.reset();
			form.loadRecord(record);

			return;
		}

		this.getPatientInsurancesPanel().remove(form_panel);

	},

	onDemographicsRecordLoad: function (patient_record, patient_panel) {

		var me = this,
			insurance_panel = me.getPatientInsurancesPanel();

		patient_record.insurance().load({
			filters: [
				{
					property: 'pid',
					value: patient_record.get('pid')
				}
			],
			callback: function(records){
				// set the insurance panel
				insurance_panel.removeAll(true);
				for(var i = 0; i < records.length; i++){
					insurance_panel.add(
						Ext.widget('patientinsuranceform', {
							closable: false,
							insurance: records[i]
						})
					);
				}

				if(insurance_panel.items.length > 0) insurance_panel.setActiveTab(0);
			}
		});
	},

    onPatientInsurancesFormLoadRecord: function (form, insurance_record) {

        var grid = form.owner.down('grid'),
            patient_insurance_id = insurance_record.get('id');

        say('app.controller.patient.insurance.onPatientInsurancesFormLoadRecord');
        say(grid);
        say(patient_insurance_id);

        if(!grid) return;

        grid.getStore().load({
            filters: [
                {
                    property: 'patient_insurance_id',
                    value: patient_insurance_id
                }
            ]
        })

    },

	onPatientInsurancesPanelBeforeAdd: function (tapPanel, panel) {
			var me = this,
				record = panel.insurance || Ext.create('App.model.patient.Insurance', {pid: me.pid});

			panel.title = _('insurance') + ' (' + (record.get('insurance_type') ? record.get('insurance_type') : _('new')) + ')';

			me.insuranceFormLoadRecord(panel, record);
			if(record.get('image') !== '') panel.down('image').setSrc(record.get('image'));
	},

	onPatientInsurancesPanelNewTabClick: function (form) {

		var record = Ext.create('App.model.patient.Insurance',{
			pid: app.patient.pid,
			card_name_same_as_pateint: 1,
			create_uid: app.user.id,
			update_uid: app.user.id,
			create_date: new Date(),
			update_date: new Date()
		});

		this.insuranceFormLoadRecord(form, record);
	},

	insuranceFormLoadRecord: function(form, record){
		form.getForm().loadRecord(record);
		app.fireEvent('insurancerecordload', form, record);
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

	onPatientInsuranceFormSubscribeRelationshipCmbSelect: function(cmb, records){
		var form = cmb.up('form').getForm();

		// // SEL = Self
		if(records[0].get('option_value') !== 'SEL') return;

		form.findField('subscriber_title').setValue(app.patient.record.get('title'));
		form.findField('subscriber_given_name').setValue(app.patient.record.get('fname'));
		form.findField('subscriber_middle_name').setValue(app.patient.record.get('mname'));
		form.findField('subscriber_surname').setValue(app.patient.record.get('lname'));
		form.findField('subscriber_dob').setValue(app.patient.record.get('DOB'));
		form.findField('subscriber_sex').setValue(app.patient.record.get('sex'));
		form.findField('subscriber_phone').setValue(app.patient.record.get('phone_mobile') || app.patient.record.get('phone_home'));
		form.findField('subscriber_employer').setValue(app.patient.record.get('employer_name'));
		form.findField('subscriber_address_same_as_pateint').setValue(true);
	}

});