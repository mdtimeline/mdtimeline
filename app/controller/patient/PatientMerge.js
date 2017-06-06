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

Ext.define('App.controller.patient.PatientMerge', {
	extend: 'Ext.app.Controller',
	requires: [],
	refs: [
		{
			ref: 'PatientMergeWindow',
			selector: '#PatientMergeWindow'
		},
		{
			ref: 'PatientMergeBtn',
			selector: '#PatientMergeBtn'
		},
		{
			ref: 'PatientMergeRecordOneForm',
			selector: '#PatientMergeRecordOneForm'
		},
		{
			ref: 'PatientMergeRecordTwoForm',
			selector: '#PatientMergeRecordTwoForm'
		}
	],

	init: function () {
		var me = this;
		me.control({
			'#PatientMergeBtn': {
				click: me.onPatientMergeBtnClick
			},
			'button[toggleGroup=patientmergegroup]': {
				toggle: me.onPatientMergeGroupBtnToggle
			},
			'#PatientMergeCancelBtn': {
				click: me.onPatientMergeCancelBtnClick
			},
			'#PatientMergeMergeBtn': {
				click: me.onPatientMergeMergeBtnClick
			},
			'#PatientMergeRecordSerchField': {
				select: me.onPatientMergeRecordSerchFieldSelect
			}
		});

		me.bufferMergeForms = Ext.Function.createBuffered(me.setMergeForms, 100, me);
	},

	onPatientMergeBtnClick: function () {
		if(!a('allow_merge_patients')) return;

		this.showPatientMergeWindow();

		this.getPatientMergeRecordOneForm().getForm().loadRecord(app.patient.record);

	},

	onPatientMergeGroupBtnToggle: function (btn, pressed) {
		var form = btn.up('form');
		form.primaryRecord = pressed;

		this.bufferMergeForms();
	},

	setMergeForms: function () {
		var primary_form = this.getPatientMergeWindow().query('form[primaryRecord=true]')[0],
			secondary_form = this.getPatientMergeWindow().query('form[primaryRecord=false]')[0];

		primary_form.body.setStyle({backgroundColor: '#c3ffc3'});
		secondary_form.body.setStyle({backgroundColor: '#ffc3c3'});

		primary_form.setTitle(_('primary_record'));
		secondary_form.setTitle(_('merge_record'));

	},

	onPatientMergeCancelBtnClick: function () {
		this.getPatientMergeWindow().close();
	},

	onPatientMergeMergeBtnClick: function () {
		var me = this,
			primaryRecord = me.getPrimaryRecord(),
			secondaryRecord = me.getSecondaryRecord();

		if (!primaryRecord || !secondaryRecord) {
			app.msg(_('oops'), _('merge_record_missing'), true);
			return;
		}

		var primaryPid = primaryRecord.get('pid'),
			secondaryPid = secondaryRecord.get('pid');

		Ext.Msg.show({
			title: _('wait'),
			msg: Ext.String.format(_('merge_action_msg'), secondaryPid, primaryPid, secondaryPid),
			buttons: Ext.Msg.YESNO,
			icon: Ext.Msg.QUESTION,
			fn: function (btn) {

				if (btn !== 'yes') return;

				me.getPatientMergeWindow().el.mask(_('merging'));

				Merge.merge(primaryPid, secondaryPid, function (success) {

					me.getPatientMergeWindow().el.unmask();

					if (success !== true) {
						say(success);
						app.msg(_('oops'), _('error_merging_patient'), true);
						return;
					}

					me.getPatientMergeWindow().close();
					app.msg(_('sweet'), _('patients_merged'));

					app.setPatient(primaryPid, null, null, function () {
						app.openPatientSummary();
					});
				});
			}
		});
	},

	onPatientMergeRecordSerchFieldSelect: function (field, records) {
		var me = this;

		App.model.patient.Patient.load(records[0].get('pid'), {
			callback: function (merge_patient_record, operation, success) {
				me.getPatientMergeRecordTwoForm().getForm().loadRecord(merge_patient_record);
			}
		});
	},

	getPrimaryRecord: function () {

		var primary_form = this.getPatientMergeWindow().query('form[primaryRecord=true]');

		if (primary_form.length === 0) {
			app.msg(_('oops'), _('not_primary_record_selected'), true);
			return false;
		}

		if (primary_form.length > 1) {
			app.msg(_('oops'), _('multiple_primary_records_found'), true);
			return false;
		}

		return primary_form[0].getForm().getRecord() || false;

	},

	getSecondaryRecord: function () {
		var secondary_form = this.getPatientMergeWindow().query('form[primaryRecord=false]');
		return secondary_form[0].getForm().getRecord() || false;
	},

	showPatientMergeWindow: function () {
		if (!this.getPatientMergeWindow()) {
			Ext.create('App.view.patient.windows.PatientMerge');
		}
		return this.getPatientMergeWindow().show();
	}

});
