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

Ext.define('App.view.patient.SmokingStatus', {
	extend: 'Ext.grid.Panel',
	requires: [
		'Ext.grid.plugin.RowEditing',
		'App.store.patient.SmokeStatus',
		'App.ux.combo.SmokingStatus',
		'App.ux.grid.DeleteColumn'
	],
	xtype: 'patientsmokingstatusgrid',
	itemId: 'PatientSmokingStatusGrid',
	columnLines: true,
	store: Ext.create('App.store.patient.SmokeStatus', {
		remoteFilter: true
	}),
	plugins: [
		{
			ptype: 'rowediting'
		}
	],
	columns: [
		{
			xtype: 'griddeletecolumn',
			acl: a('delete_smoking_status'),
			width: 25
		},
		{
			text: _('status'),
			dataIndex: 'status',
			width: 250,
			renderer: function(v, meta, record){
				return v + ' (' + record.data.status_code +')';
			}
		},
		{
			text: _('counseling_given'),
			dataIndex: 'counseling',
			width: 120,
			editor: {
				xtype: 'checkbox'
			},
			renderer: function(v){
				return app.boolRenderer(v);
			}
		},
		{
			text: _('cessation_counseling_type'),
			flex: 1,
			dataIndex: 'counseling_text',
			editor:{
				xtype:'gaiaehr.combo',
				listKey: 'tobacco_use_cessation_counseling',
				displayField: 'option_name',
				valueField: 'option_name',
				itemId: 'PatientSmokingStatusGridCounselingField',
				loadStore: true
			}
		},
		{
			text: _('note'),
			dataIndex: 'note',
			flex: 1,
			editor: {
				xtype: 'textfield'
			}
		},
		{
			xtype: 'datecolumn',
			format: 'Y-m-d',
			text: _('start_date'),
			dataIndex: 'start_date',
			width: 120,
			editor: {
				xtype: 'datefield',
				format: g('date_display_format'),
				submitFormat: 'Y-m-d'
			}
		},
		{
			xtype: 'datecolumn',
			format: 'Y-m-d',
			text: _('end_date'),
			dataIndex: 'end_date',
			width: 120,
			editor: {
				xtype: 'datefield',
				format: g('date_display_format'),
				submitFormat: 'Y-m-d'
			}
		}
	],
	tbar: [
		{
			xtype: 'tbtext',
			text: _('smoking_status'),
			width: 100
		},
		{
			xtype: 'mitos.smokingstatuscombo',
			itemId: 'socialsmokingstatuscombo',
			width: 250
		}
	]
});
