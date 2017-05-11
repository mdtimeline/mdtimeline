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

Ext.define('App.controller.patient.SocialPsychologicalBehavioral', {
	extend: 'Ext.app.Controller',
	requires: [],
	refs: [
		{
			ref: 'SocialPsychologicalBehavioralPanel',
			selector: '#SocialPsychologicalBehavioralPanel'
		},
		{
			ref: 'SocialPsychologicalBehavioralWindow',
			selector: '#SocialPsychologicalBehavioralWindow'
		},
		{
			ref: 'SocialPsychologicalBehavioralForm',
			selector: '#SocialPsychologicalBehavioralForm'
		},
		{
			ref: 'SocialPsychologicalBehavioralGrid',
			selector: '#SocialPsychologicalBehavioralGrid'
		},
		{
			ref: 'SocialPsychologicalBehavioralChart',
			selector: '#SocialPsychologicalBehavioralChart'
		}
	],

	init: function () {
		var me = this;
		me.control({
			'#SocialPsychologicalBehavioralPanel': {
				activate: me.onSocialPsychologicalBehavioralPanelActivate
			},
			'#SocialPsychologicalBehavioralGrid': {
				itemdblclick: me.onSocialPsychologicalBehavioralGridItemDblClick
			},
			'#SocialPsychologicalBehavioralAddBtn': {
				click: me.onSocialPsychologicalBehavioralAddBtnClick
			},
			'#SocialPsychologicalBehavioralPanelSaveBtn': {
				click: me.onSocialPsychologicalBehavioralPanelSaveBtnClick
			},
			'#SocialPsychologicalBehavioralPanelCancelBtn': {
				click: me.onSocialPsychologicalBehavioralPanelCancelBtnClick
			}
		});

		//me.doTest();

	},

	doTest: function () {
		var me = this;

		me.showSocialPsychologicalBehavioralWindow();
		return;

		Ext.create('Ext.window.Window', {
			title: 'Hello',
			height: 500,
			width: 1200,
			layout: 'fit',
			items: [
				Ext.create('App.view.patient.SocialPsychologicalBehavioral')
			]
		}).show();

		Ext.Function.defer(function () {
			me.onSocialPsychologicalBehavioralPanelActivate();
		}, 3000, me);
	},

	onSocialPsychologicalBehavioralPanelActivate: function () {
		var store = this.getSocialPsychologicalBehavioralGrid().getStore();

		store.clearFilter();
		store.filter([
			{
				property: 'pid',
				value: app.patient.pid || 0 // TODO remove || 0 after development ...
			}
		]);
	},

	onSocialPsychologicalBehavioralAddBtnClick: function () {

		this.showSocialPsychologicalBehavioralWindow();

		var store = this.getSocialPsychologicalBehavioralGrid().getStore(),
			form = this.getSocialPsychologicalBehavioralForm().getForm();

		var records = store.add({
			pid: app.patient.pid,
			eid: app.patient.eid,
			create_uid: app.user.id,
			create_date: new Date()
		});

		form.reset();
		form.loadRecord(records[0]);
	},

	onSocialPsychologicalBehavioralGridItemDblClick: function (grid, psy_record) {
		this.showSocialPsychologicalBehavioralWindow();

		var form = this.getSocialPsychologicalBehavioralForm().getForm();

		form.reset();
		form.loadRecord(psy_record);
	},

	onSocialPsychologicalBehavioralPanelSaveBtnClick: function () {
		var me = this,
			form = me.getSocialPsychologicalBehavioralForm().getForm(),
			record = form.getRecord(),
			values = form.getValues();

		if(!form.isValid()) return;

		values.patient_health_score =
			values.interest_pleasure +
			values.feeling_down_depressed;

		values.patient_drink_score =
			values.drink_often +
			values.drink_per_day +
			values.drink_more_than_6;

		values.patient_isolation_score =
			values.marital_status +
			values.phone_family +
			values.together_friends +
			values.religious_services +
			values.belong_organizations;

		values.patient_humiliation_score =
			values.abused_by_partner +
			values.afraid_of_partner +
			values.raped_by_partner +
			values.physically_hurt_by_partner;

		record.set(values);

		if(Ext.Object.isEmpty(record.getChanges())) {
			me.getSocialPsychologicalBehavioralWindow().close();
			return;
		}

		record.store.sync({
			callback: function (){
				me.getSocialPsychologicalBehavioralWindow().close();
			}
		});
	},

	onSocialPsychologicalBehavioralPanelCancelBtnClick: function () {
		this.getSocialPsychologicalBehavioralWindow().close();
	},

	showSocialPsychologicalBehavioralWindow: function () {
		if(!this.getSocialPsychologicalBehavioralWindow()){
			Ext.create('App.view.patient.windows.SocialPsychologicalBehavioral');
		}
		return this.getSocialPsychologicalBehavioralWindow().show();
	}

});