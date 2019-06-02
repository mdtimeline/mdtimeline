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

Ext.define('App.view.patient.ProceduresHistoryGrid', {
	extend: 'Ext.grid.Panel',
	requires: [
		'Ext.grid.plugin.RowEditing',
		'App.ux.LiveSnomedProcedureSearch',
		'App.ux.LiveReferringPhysicianSearch',
		'App.ux.LiveSnomedBodySiteSearch'
	],
	xtype: 'patientprocedureshistorygrid',
	title: _('proc_hx'),
	columnLines: true,
	store: Ext.create('App.store.patient.ProcedureHistory', {
		remoteFilter: true,
	}),
	plugins: [
		{
			ptype: 'rowediting',
			clicksToEdit: 2
		}
	],
	columns: [
		{
			xtype: 'actioncolumn',
			width: 20,
			items: [
				{
					icon: 'resources/images/icons/cross.png',
					tooltip: _('remove'),
                    handler: function(grid, rowIndex, colIndex, item, e, record){
                        //App.app.getController('patient.FamilyHistory').onDeactivateRecord(grid, record);
                    }
				}
			]
		},
		{
			xtype: 'datecolumn',
			header: _('performed_date'),
			width: 150,
			dataIndex: 'performed_date',
			format: 'Y-m-d',
			editor: {
				xtype: 'datefield'
			}
		},
		{
			header: _('procedure'),
			width: 300,
			dataIndex: 'procedure',
			editor:{
				xtype: 'snomedliveproceduresearch',
				itemId: 'ProceduresHistoryGridProcedureField',
				valueField: 'Term',
			}
		},
		{
			header: _('body_site'),
			width: 200,
			dataIndex: 'target_site_code_text',
			editor:{
				xtype: 'snomedlivebodysitesearch',
				itemId: 'ProceduresHistoryGridTargetSiteField',
				valueField: 'Term',
			}
		},
		{
			header: _('performer'),
			width: 200,
			dataIndex: 'performer',
			editor:{
				xtype: 'referringphysicianlivetsearch',
				itemId: 'ProceduresHistoryGridPerformerField',
				valueField: 'fullname',
			}
		},
		{
			header: _('service_location'),
			width: 200,
			dataIndex: 'service_location',
			editor:{
				xtype: 'referringphysicianlivetsearch',
				itemId: 'ProceduresHistoryGridServiceLocationField',
				valueField: 'fullname',
			}
		},
		{
			header: _('notes'),
			flex: 1,
			dataIndex: 'notes',
            editor:{
                xtype: 'textfield'
            }
		}
	],
	tbar: [
		'->',
		{
			text: _('procedure'),
			iconCls: 'icoAdd',
			action: 'encounterRecordAdd',
			itemId:'ProceduresHistoryGridAddBtn'
		}
	]
});
