/**
 * GaiaEHR (Electronic Health Records)
 * Copyright (C) 2012 Ernesto Rodriguez
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

Ext.define('App.view.dashboard.panel.NotSignedVitals', {
	extend: 'Ext.grid.Panel',
	itemId: 'DashboardNotSignedVitalsGrid',
	requires: [
		'Ext.ux.SlidingPager'
	],
	height: 220,
	selModel:{
		mode: 'MULTI',
		allowDeselect: true
	},

	initComponent: function(){
		var me = this;

		me.store = Ext.create('App.store.patient.Vitals', {
			remoteFilter: true,
			filters:[
				{
					property: 'auth_uid',
					value: 0
				}
			]
		});

		me.bbar = {
			xtype: 'pagingtoolbar',
			pageSize: 10,
			store: me.store,
			displayInfo: true,
			plugins: Ext.create('Ext.ux.SlidingPager'),
			items: [
				'-',
				{
					xtype: 'button',
					text: _('sign'),
					disabled: true,
					itemId: 'DashboardNotSignedVitalsSignBtn',
					icon: 'resources/images/icons/pen.png'
				}
			]
		};

		me.columns = [
			{
				xtype: 'datecolumn',
				text: _('date'),
				dataIndex: 'date'
			},
			{
				text: _('patient'),
				dataIndex: 'patient_lname',
				width: 200,
				renderer: function(v, met, rec){
					return Ext.String.format(
						'{0}, {1} {2} - {3}',
						(rec.get('patient_lname') || ''),
						(rec.get('patient_fname') || ''),
						(rec.get('patient_mname') || ''),
						(rec.get('patient_record_number') || rec.get('pid'))
					)
				}
			},
			{
				text: _('values'),
				dataIndex: 'code_text',
				flex: 1,
				renderer: function (v, meta, rec) {
					return Ext.String.format(
						'<b>BP:</b> {0}/{1}&nbsp;&nbsp;&nbsp;<b>PULSE:</b> {2}&nbsp;&nbsp;&nbsp;<b>TEMP:</b> {3}',
						(rec.get('bp_systolic') || 'NONE'),
						(rec.get('bp_diastolic') || 'NONE'),
						(rec.get('pulse') ? rec.get('pulse') + ' bpm' : 'NONE'),
						(rec.get('temp_f') ? rec.get('temp_f') + '&deg;' : 'NONE')
					)
				}
			},
			{
				text: _('administer_by'),
				dataIndex: 'administer_by',
				width: 200
			}
		];

		me.callParent(arguments);
	},

	signedRenderer: function(uid) {
		if(uid > 0) {
			return '<img style="padding-left: 13px" src="resources/images/icons/yes.png" />';
		} else {
			return '<img style="padding-left: 13px" src="resources/images/icons/no.png" />';
		}
	}
});
