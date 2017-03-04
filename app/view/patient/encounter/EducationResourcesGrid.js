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

Ext.define('App.view.patient.encounter.EducationResourcesGrid', {
	extend: 'Ext.grid.Panel',
	requires: [
		'App.ux.grid.DeleteColumn',
		'App.ux.LiveEducationResourceSearch'
	],
	xtype: 'educationresourcesgrid',
	itemId: 'EducationResourcesGrid',
	frame: true,
	initComponent: function(){
		var me = this;

		me.columns = [
			{
				xtype: 'griddeletecolumn',
				width: 25,
				acl: 'remove_encounter_education_resources'
			},
			{
				text: _('title'),
				dataIndex: 'title',
				width: 250
			},
			{
				text: _('snippet'),
				dataIndex: 'snippet',
				flex: 1
			}
		];

		me.callParent();
	},
	tbar: [
		_('education_resources'),
		{
			xtype: 'educationresourcelivetsearch',
			itemId: 'EducationResourcesGridSearchField',
			width: 400,
			margin: '0 10 0 0'
		},
		{
			xtype: 'combobox',
			width: 150,
			labelWidth: 50,
			fieldLabel: _('language'),
			queryMode: 'local',
			displayField: 'option',
			valueField: 'value',
			editable: false,
			value: 'patient',
			stateful: true,
			stateId: 'EducationResourcesGridLanguageField',
			itemId: 'EducationResourcesGridLanguageField',
			store: Ext.create('Ext.data.Store', {
				fields: ['option', 'value'],
				data : [
					{option: _('patient'), value: 'patient'},
					{option: _('english'), value: 'en'},
					{option: _('spanish'), value: 'es'}
				]
			})
		}
	]


});