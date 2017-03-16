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

Ext.define('App.controller.patient.EducationResources', {
	extend: 'Ext.app.Controller',
	requires: [

	],
	refs: [
		{
			ref: 'EducationResourcesGrid',
			selector: '#EducationResourcesGrid'
		},
		{
			ref: 'EducationResourcesGridAddBtn',
			selector: '#EducationResourcesGridAddBtn'
		},
		{
			ref: 'EducationResourcesGridLanguageField',
			selector: '#EducationResourcesGridLanguageField'
		},
		{
			ref: 'EducationResourcesGridSearchField',
			selector: '#EducationResourcesGridSearchField'
		},
		{
			ref: 'EducationResourcesGridFindEncounterRelatedBtn',
			selector: '#EducationResourcesGridFindEncounterRelatedBtn'
		},
		{
			ref: 'EducationResourcesPreviewWindow',
			selector: '#EducationResourcesPreviewWindow'
		},
		{
			ref: 'EducationResourcesPreviewWindowGrid',
			selector: '#EducationResourcesPreviewWindowGrid'
		}
	],

	init: function(){
		var me = this;
		me.control({
			'viewport':{
				beforeencounterload: me.onAppEncounterLoad,
				encountersync: me.onAppEncounterSync
			},
			'#EducationResourcesGrid':{
				itemdblclick: me.onEducationResourcesGridItemDblClick
			},
			'#EducationResourcesGridLanguageField':{
				change: me.onEducationResourcesGridLanguageFieldChange
			},
			'#EducationResourcesGridSearchField':{
				select: me.onEducationResourcesGridSearchFieldSelect
			},
			'#EducationResourcesGridFindEncounterRelatedBtn':{
				click: me.onEducationResourcesGridFindEncounterRelatedBtnClick
			},
			'#EducationResourcesPreviewWindowCancelBtn':{
				click: me.onEducationResourcesPreviewWindowCancelBtnClick
			},
			'#EducationResourcesPreviewWindowSelectBtn':{
				click: me.onEducationResourcesPreviewWindowSelectBtnClick
			},
			'#EducationResourcesPreviewWindow':{
				close: me.onEducationResourcesPreviewWindowClose
			},
			'#EducationResourcesPreviewWindowGrid':{
				itemdblclick: me.onEducationResourcesPreviewWindowGridItemDblClick
			}
		});
	},

	onEducationResourcesGridFindEncounterRelatedBtnClick: function () {

		var me = this,
			language_field_value = me.getEducationResourcesGridLanguageField().getValue(),
			params = {
				eid: app.patient.eid,
				language: 'en'
			};

		if(language_field_value == 'patient'){
			if (app.patient.record && app.patient.record.get('language') == 'spa') {
				params.language = 'es';
			}
		}else {
			if(language_field_value == 'spa' || language_field_value == 'es' || language_field_value == 'esp'){
				params.language = 'es';
			}
		}

		me.getEducationResourcesGrid().el.mask(_('searching'));

		EducationResources.findEncounterEducationResources(params, function (response) {

			me.getEducationResourcesGrid().el.unmask();

			if(response.length > 0){
				me.showEducationResourcesFinderPreview();
				me.getEducationResourcesPreviewWindowGrid().getStore().loadRawData(response);
			}else {
				app.msg(_('info'), _('no_education_resources_found'));
			}
		});
	},

	onEducationResourcesPreviewWindowClose: function () {
		this.getEducationResourcesPreviewWindowGrid().getStore().removeAll();
	},

	onEducationResourcesPreviewWindowCancelBtnClick: function () {
		this.getEducationResourcesPreviewWindow().close();
	},

	onEducationResourcesPreviewWindowSelectBtnClick: function () {

		var me = this,
			selection = this.getEducationResourcesPreviewWindowGrid().getSelectionModel().getSelection(),
			store = this.getEducationResourcesGrid().getStore();

		selection.forEach(function (record) {
			var data = Ext.clone(record.data);
			data.pid = app.patient.pid;
			data.eid = app.patient.eid;
			data.uid = app.user.id;
			store.add(data);
		});

		me.getEducationResourcesPreviewWindow().close();
	},

	onEducationResourcesPreviewWindowGridItemDblClick: function (grid, document_record) {
		window.open(document_record.get('url'), '_blank');
	},

	onEducationResourcesGridItemDblClick: function (grid, document_record) {
		window.open(document_record.get('url'), '_blank');
	},

	onAppEncounterLoad: function(encounter){
		this.getEducationResourcesGrid().reconfigure(encounter.educationresources());
		encounter.educationresources().load();
	},

	onAppEncounterSync: function(encounter){
		encounter.encounter.educationresources().sync();
	},

	onEducationResourcesGridLanguageFieldChange: function (cmb, value) {
		this.getEducationResourcesGridSearchField().language = value;
	},

	onEducationResourcesGridSearchFieldSelect: function (field, selection) {
		var store = this.getEducationResourcesGrid().getStore();

		store.add({
			pid: app.patient.pid,
			eid: app.patient.eid,
			uid: app.user.id,
			title: selection[0].get('title'),
			url: selection[0].get('url'),
			snippet: selection[0].get('snippet'),
			organization_name: selection[0].get('organizationName')
		});

	},

	showEducationResourcesFinderPreview: function () {

		if(!this.getEducationResourcesPreviewWindow()){
			Ext.create('Ext.window.Window',{
				title: _('select_education_resources'),
				modal: true,
				layout: 'fit',
				itemId: 'EducationResourcesPreviewWindow',
				bodyPadding: 5,
				closeAction: 'hide',
				items: [
					{
						xtype:'grid',
						width: 700,
						height: 200,
						selType: 'checkboxmodel',
						itemId: 'EducationResourcesPreviewWindowGrid',
						store: Ext.create('App.store.patient.EducationResources'),
						columns:[
							{
								text: _('title'),
								dataIndex: 'title',
								flex: 1
							},
							{
								text: _('organization'),
								dataIndex: 'organization_name',
								flex: 1
							}
						]
					}
				],
				buttons: [
					{
						text: _('cancel'),
						itemId: 'EducationResourcesPreviewWindowCancelBtn'
					},
					{
						text: _('select'),
						itemId: 'EducationResourcesPreviewWindowSelectBtn'
					}
				]
			});
		}
		return this.getEducationResourcesPreviewWindow().show();
	}

});
