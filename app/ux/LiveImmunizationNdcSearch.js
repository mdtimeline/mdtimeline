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
Ext.define('App.ux.LiveImmunizationNdcSearch', {
	extend: 'Ext.form.ComboBox',
	xtype: 'immunizationndclivesearch',
	hideLabel: true,
	displayField: 'UseUnitPropName',
	valueField: 'NDC11',
	emptyText: _('immunization_search') + '...',
	typeAhead: true,
	minChars: 1,
	initComponent: function(){
		var me = this;

		Ext.define('liveImmunizationNdcSearchModel', {
			extend: 'Ext.data.Model',
			fields: [
				{name: 'NDCInnerID', type: 'string'},
				{name: 'CVXCode', type: 'string'},
				{name: 'CVXShortDescription', type: 'string'},
				{name: 'UseUnitPropName', type: 'string'},
				{name: 'UseUnitGenericName', type: 'string'},
				{name: 'UseUnitLabelerName', type: 'string'},
				{name: 'NoInner', type: 'string'},
				{name: 'NDC11', type: 'string'},
				{name: 'status', type: 'string'},
				{name: 'update_date', type: 'date', dateFormat: 'Y-m-d H:i:s'}
			],
			idProperty: 'NDCInnerID',
			proxy: {
				type: 'direct',
				api: {
					read: 'Immunizations.getImmunizationNDCLiveSearch'
				},
				reader: {
					totalProperty: 'total',
					root: 'data'
				}
			}
		});

		me.store = Ext.create('Ext.data.Store', {
			model: 'liveImmunizationNdcSearchModel',
			pageSize: 10,
			autoLoad: false
		});

		Ext.apply(this, {
			store: me.store,
			listConfig: {
				loadingText: _('searching') + '...',
				getInnerTpl: function(){
					return '<div class="search-item">NDC - {NDC11}: <span style="font-weight: normal;" class="list-status-{status}">{UseUnitPropName} ({CVXShortDescription})</span></div>';
				}
			},
			pageSize: 10
		});

		me.callParent();
	}
});
