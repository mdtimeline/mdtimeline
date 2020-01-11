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

Ext.define('App.view.patient.windows.NewEncounter', {
	extend: 'Ext.window.Window',
	requires: [
		'App.ux.LiveReferringPhysicianSearch'
	],
	itemId: 'EncounterDetailWindow',
	title: _('encounter'),
	closeAction: 'hide',
	closable: false,
	modal: true,
	width: 700,
	layout: 'fit',
	initComponent: function () {
		var me = this;

		me.store = Ext.create('App.store.patient.Encounters');

		Ext.apply(me, {
			items: [
				me.encForm = Ext.create('Ext.form.Panel', {
						itemId: 'EncounterDetailForm',
						border: false,
						bodyPadding: '10 10 0 10',
						items: [
							{
								xtype: 'fieldcontainer',
								layout: 'column',
								items: [
									{
										xtype: 'fieldcontainer',
										layout: 'anchor',
										items: [
											{
												xtype: 'mitos.datetime',
												fieldLabel: 'Date of Service',
												labelWidth: 100,
												width: 300,
												margin: '0 5 5 0',
												name: 'service_date'
											},
											{
												xtype: 'gaiaehr.combo',
												fieldLabel: 'Visit Category',
												labelWidth: 100,
												width: 300,
												margin: '0 5 5 0',
												name: 'visit_category',
												listKey: 'visit_cat',
												editable: false,
												loadStore: true,
												queryMode: 'local'
											},
											{
												xtype: 'textareafield',
												fieldLabel: 'Chief Complaint',
												emptyText: 'Chief Complaint (Please Type a Brief Description)',
												hideLabel: true,
												enableKeyEvents: true,
												width: 300,
												height: 157,
												margin: '0 10 0 0',
												grow: false,
												name: 'brief_description'
											}
										]
									}, {
										xtype: 'fieldcontainer',
										layout: 'anchor',
										items: [
											{
												xtype: 'activeproviderscombo',
												fieldLabel: 'Provider',
												labelWidth: 120,
												width: 350,
												allowBlank: false,
												name: 'provider_uid',
												itemId: 'EncounterProviderCmb',
												editable: false
											},
											{
												xtype: 'activeproviderscombo',
												fieldLabel: 'Technician',
												labelWidth: 120,
												width: 350,
												name: 'technician_uid',
												editable: false
											},
											{
												xtype: 'activefacilitiescombo',
												fieldLabel: 'Facility',
												labelWidth: 120,
												width: 350,
												margin: '0 5 5 0',
												name: 'facility',
												editable: false
											},
											{
												xtype: 'gaiaehr.combo',
												fieldLabel: 'Priority',
												labelWidth: 120,
												width: 350,
												margin: '0 5 5 0',
												name: 'priority',
												listKey: 'enc_prio',
												editable: false,
												loadStore: true,
												queryMode: 'local'
											},
											{
												xtype: 'gaiaehr.combo',
												fieldLabel: 'Patient Class',
												labelWidth: 120,
												width: 350,
												name: 'patient_class',
												listKey: 'pat_class',
												editable: false,
												loadStore: true,
												queryMode: 'local'
											},
											{
												xtype: 'referringphysicianlivetsearch',
												fieldLabel: 'Ref. Physician',
												hideLabel: false,
												labelWidth: 120,
												width: 350,
												name: 'referring_physician'
											},
											{
												xtype: 'fieldcontainer',
												layout: 'hbox',
												items: [
													{
														xtype: 'checkbox',
														itemId: 'EncounterCcdaAvailableField',
														fieldLabel: _('ccda_available'),
														labelWidth: 120,
														name: 'summary_care_provided'
													},
													{
														xtype: 'checkbox',
														fieldLabel: _('requested'),
														labelWidth: 80,
														labelAlign: 'right',
														name: 'summary_care_requested'
													}
												]
											},
											{
												xtype: 'mitos.datetime',
												fieldLabel: 'Onset Hosp. Date',
												labelWidth: 120,
												width: 350,
												margin: '0 5 5 0',
												name: 'onset_date'
											},
											{
												xtype: 'checkbox',
												fieldLabel: 'Is Private',
												labelWidth: 120,
												name: 'is_private',
												disabled: !a('allow_to_create_private_encounter'),
												tooltip: 'Private encounters can only be access by encounter\'s provider'
											}
										]
									}
								]
							}
						]

					}
				)
			],
			buttons: [
				{
					text: _('delete'),
					itemId: 'EncounterDeletetBtn',
					cls: 'toolWarning',
					hidden: true
				},
				{
					text: _('transfer'),
					itemId: 'EncounterTransferBtn',
					cls: 'toolWarning',
					acl: a('transfer_encounters')
				},
				'->',
				{
					text: _('save'),
					action: 'encounter',
					scope: me,
					disableOnCLick: true,
					handler: me.onFormSave
				},
				{
					text: _('cancel'),
					scope: me,
					handler: me.cancelNewEnc
				}
			],
			listeners: {
				show: me.checkValidation,
				hide: me.resetRecord
			}
		}, me);

		me.callParent(arguments);
	},

	checkValidation: function () {
		var me = this,
			form = me.down('form').getForm(),
			record = form.getRecord(),
			brief_description_field = form.findField('brief_description');

		if (app.patient.pid) {
			if (!record && a('add_encounters')) {

				me.loadRecord(
					Ext.create('App.model.patient.Encounter', {
						pid: app.patient.pid,
						service_date: Ext.Date.format(new Date(), 'Y-m-d H:i:s'),
						priority: 'Minimal',
						open_uid: app.user.id,
						facility: app.user.facility,
						billing_facility: app.user.facility,
						brief_description: g('default_chief_complaint')
					})
				);

				if (brief_description_field) brief_description_field.enable();


				Encounter.checkOpenEncountersByPid(app.patient.pid, function (provider, response) {
					if (response.result.encounter) {
						Ext.Msg.show({
							title: _('oops') + ' ' + _('open_encounters_found') + '...',
							msg: _('do_you_want_to') + ' <strong>' + _('continue_creating_the_new_encounters') + '</strong><br>"' + _('click_no_to_review_encounter_history') + '"',
							buttons: Ext.Msg.YESNO,
							icon: Ext.Msg.QUESTION,
							fn: function (btn) {
								if (btn != 'yes') {
									me.hide();
									form.reset();
								}
							}
						});
					}
				});
			} else if (record && a('edit_encounters')) {

				if (brief_description_field) brief_description_field.disable();

			} else {
				app.accessDenied();
			}
		} else {
			app.currPatientError();
		}
	},

	onFormSave: function (btn) {
		var me = this,
			form = me.encForm.getForm(),
			values = form.getValues(),
			record = form.getRecord(),
			isNew = record.data.eid === 0;

		if (form.isValid()) {
			if ((isNew && a('add_encounters') || (!isNew && a('edit_encounters')))) {
				record.set(values);

				say(values);

				record.save({
					callback: function (record) {
						if (isNew) {
							var data = record.data;
							app.patientButtonRemoveCls();
							app.patientBtn.addCls(data.priority);
							app.openEncounter(data.eid);
						}
						me.close();
					}
				});
			} else {
				btn.up('window').close();
				app.accessDenied();
			}
		}
	},

	loadRecord: function (record) {
		this.encForm.getForm().loadRecord(record);
	},

	resetRecord: function () {
		this.down('form').getForm().reset(true);
		delete this.down('form').getForm()._record;
	},

	cancelNewEnc: function () {
		this.close();
	}

});
