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
			}
		});
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

		say(store);
		say(selection);

		store.add({
			pid: app.patient.pid,
			eid: app.patient.eid,
			uid: app.user.id,
			title: selection[0].get('title'),
			url: selection[0].get('url'),
			snippet: selection[0].get('snippet'),
			organization_name: selection[0].get('organizationName')
		});

	}

});
