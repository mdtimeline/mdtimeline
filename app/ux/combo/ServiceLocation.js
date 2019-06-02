/**
 * mdTimeLine EHR (Electronic Health Records)
 * Copyright (C) 2017 mdTimeLine, Inc.
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

Ext.define('App.ux.combo.ServiceLocation', {
	extend: 'Ext.form.ComboBox',
	xtype: 'servicelocationcombo',
	typeAhead: true,
	typeAheadDelay: 50,
	queryMode: 'local',
	displayField: 'valueFieldText',
	valueField: 'code',
	editable: false,
	initComponent: function () {
		var me = this;

		me.store = Ext.create('Ext.data.Store', {
			fields: [
				{name: 'code', type: 'string'},
				{name: 'displayName', type: 'string'},
				{
					name: 'valueFieldText', convert: function(v, r){
						return Ext.String.format('{0} - {1}', r.get('code'), r.get('displayName'));
					}
				}
			],
			autoLoad: true,
			proxy: {
				type: 'ajax',
				url: 'resources/code_sets/PH_HealthcareServiceLoc_NHSN.json',
				reader: {
					type: 'json'
				}
			}
		});

		me.callParent(arguments);
	}
});
