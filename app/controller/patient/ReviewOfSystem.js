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

Ext.define('App.controller.patient.ReviewOfSystem', {
	extend: 'Ext.app.Controller',
	requires: [
		'App.model.administration.ReviewOfSystemSettings'
	],
	refs: [
		{
			ref:'ReviewOfSystemSetupBtn',
			selector: '#ReviewOfSystemSetupBtn'
		},
		{
			ref:'ReviewOfSystemForm',
			selector: '#ReviewOfSystemForm'
		}
	],

	reviewOfSystemSetupWindow: undefined,
	reviewOfSystemSetup: {
		id: null,
		user_id: 0,
		settings_data: {}
	},

	init: function(){
		var me = this;

		me.control({
			'viewport': {
				render: me.onViewportRender
			},
			'#ReviewOfSystemForm': {
				beforerender: me.onReviewOfSystemFormBeforeRender
			},
			'#ReviewOfSystemSetupBtn': {
				click: me.onReviewOfSystemSetupBtnClick
			},
			'#ReviewOfSystemSetupCancelBtn': {
				click: me.onReviewOfSystemSetupCancelBtnClick
			},
			'#ReviewOfSystemSetupSaveBtn': {
				click: me.onReviewOfSystemSetupSaveBtnClick
			}
		});



	},

	onViewportRender: function(){
		var me = this;

		if(a('access_review_of_systems')) {
			Encounter.getReviewOfSystemSettingsByUserId(app.user.id, function (settings) {
				if(settings !== false){
					me.reviewOfSystemSetup = settings;
				}
			});
		}
	},

	onReviewOfSystemSetupCancelBtnClick: function(){
		this.reviewOfSystemSetupWindow.close();
	},

	onReviewOfSystemSetupSaveBtnClick: function(){
		var me = this,
			values = me.reviewOfSystemSetupWindow.down('form').getForm().getValues();


		me.reviewOfSystemSetup.user_id = app.user.id;
		Ext.apply(me.reviewOfSystemSetup.settings_data, values);

		Encounter.saveReviewOfSystemSettings(me.reviewOfSystemSetup, function (settings) {
			if(settings !== false){
				me.reviewOfSystemSetup = settings;
				me.setReviewOfSystemFormField();
				me.reviewOfSystemSetupWindow.close();
			}
		});
	},

	onReviewOfSystemSetupBtnClick: function(btn){
		this.showReviewOfSystemSetup(btn.up('form'));
	},

	onReviewOfSystemFormBeforeRender: function(){
		this.setReviewOfSystemFormField();
	},

	setReviewOfSystemFormField: function(){
		var me = this,
			fields = this.getReviewOfSystemForm().getForm().getFields();

		fields.each(function (field) {
			if(me.reviewOfSystemSetup.settings_data[field.name] !== undefined){
				field.setVisible(eval(me.reviewOfSystemSetup.settings_data[field.name]));
			}
		});
	},

	getReviewOfSystemSetupFields: function(form){
		var me = this,
			fieldsets = [];

		form.items.each(function (fieldset) {

			var fieldset_buff = {
				xtype: 'fieldset',
				title: fieldset.title,
				items: []
			};

			fieldset.items.each(function (switchfield) {
				fieldset_buff.items.push({
					xtype: 'checkbox',
					boxLabel: switchfield.fieldLabel,
					checked: true,
					name: switchfield.name
				});
			});

			fieldsets.push(fieldset_buff);
		});

		return fieldsets;

	},

	showReviewOfSystemSetup: function (form) {

		if(this.reviewOfSystemSetupWindow){
			return this.reviewOfSystemSetupWindow.show();
		}

		var items = this.getReviewOfSystemSetupFields(form);

		this.reviewOfSystemSetupWindow = Ext.create('Ext.window.Window', {
			title: 'Review Of System Setup',
			height: 600,
			width: 400,
			layout: 'fit',
			bodyPadding: 5,
			closeAction: 'hide',
			items: [
				{
					xtype: 'form',
					bodyPadding: 5,
					autoScroll: true,
					items: items
				}
			],
			buttons: [
				{
					text: _('cancel'),
					itemId: 'ReviewOfSystemSetupCancelBtn'
				},
				{
					text: _('save'),
					itemId: 'ReviewOfSystemSetupSaveBtn'
				}
			]
		}).show();

		this.reviewOfSystemSetupWindow.down('form').getForm().setValues(this.reviewOfSystemSetup.settings_data);


		return this.reviewOfSystemSetup;

	}

});