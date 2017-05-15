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

Ext.define('App.view.patient.encounter.MedicationsAdministeredGrid', {
	extend: 'Ext.grid.Panel',
	requires: [
		'App.ux.grid.DeleteColumn'
	],
	xtype: 'medicationsadministeredgrid',
	itemId: 'MedicationsAdministeredGrid',
	frame: true,
	disableSelection: true,
	store: Ext.create('App.store.patient.MedicationsAdministered'),
	columns: [
		{
			xtype: 'actioncolumn',
			width: 25,
			groupable: false,
			items: [
				{
					icon: 'resources/images/icons/blueInfo.png',  // Use a URL in the icon config
					tooltip: 'Get Info',
					handler: function(grid, rowIndex, colIndex, item, e, record){
						App.app.getController('InfoButton').doGetInfo(
							record.data.rxcui,
							'RXCUI',
							record.data.description
						);
					}
				}
			]
		},
		{
			text: _('description'),
			flex: 1,
			dataIndex: 'description',
			renderer: function (v, meta, record) {
				if(!record.get('administered')) {
					meta.style = 'background-color: #FFCCCC';
				}
				return v;
			}
		},
		{
			text: _('instructions'),
			flex: 1,
			dataIndex: 'instructions',
			renderer: function (v, meta, record) {
				if(!record.get('administered')) {
					meta.style = 'background-color: #FFCCCC';
				}
				return v;
			}
		},
		{
			text: _('note'),
			flex: 2,
			dataIndex: 'note',
			renderer: function (v, meta, record) {
				if(!record.get('administered')) {
					meta.style = 'background-color: #FFCCCC';
				}
				return v;
			}
		},
		{
			text: _('administered_by'),
			dataIndex: 'administered_by',
			width: 150,
			renderer: function (v, meta, record) {
				if(!record.get('administered')) {
					meta.style = 'background-color: #FFCCCC';
				}
				return v;
			}
		},
		{
			text: _('adverse_reaction'),
			dataIndex: 'adverse_reaction_text',
			width: 150,
			renderer: function (v, meta, record) {
				if(!record.get('administered')) {
					meta.style = 'background-color: #FFCCCC';
				}
				return v;
			}
		}
	],
	tbar: [
		_('medications_administered'),
		'->',
		{
			text: _('medication'),
			itemId: 'MedicationsAdministeredGridAddBtn',
			action: 'encounterRecordAdd',
			iconCls: 'icoAdd'
		}
	]


});