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

Ext.define('App.view.administration.ICD10', {
	extend: 'Ext.grid.Panel',
	requires:[
		//'Ext.grid.plugin.RowEditing',
		'App.store.administration.CPT'
	],
	xtype: 'icd10admingrid',
	title: _('icd10'),
	columns: [
		{
			width: 60,
			header: _('code'),
			dataIndex: 'dx_code'
		},
		{
			header: _('formatted_dx_code'),
			dataIndex: 'formatted_dx_code',
			width: 100,
			flex: 1
		},
		{
			header: _('valid_for_coding'),
			dataIndex: 'valid_for_coding',
			flex: 2
		},
		{
			header: _('short_desc'),
			dataIndex: 'short_desc',
			flex: 2
		},
		{
			header: _('long_desc'),
			dataIndex: 'long_desc',
			flex: 2
		},
		{
			width: 60,
			header: _('active'),
			dataIndex: 'active',
			renderer: function(v){
				return this.boolRenderer(v);
			}
		},
		{
			width: 100,
			header: _('revision'),
			dataIndex: 'revision'
		}
	],
	initComponent: function(){
		var me = this;

		me.store = Ext.create('App.store.administration.ICD10',{
			remoteSort: true
		});

		me.bbar = Ext.create('Ext.PagingToolbar', {
			store: me.store,
			displayInfo: true,
			emptyMsg: _('no_icd10s_to_display'),
			plugins: Ext.create('Ext.ux.SlidingPager'),
			items: [
			]
		});

		var currentTime = new Date();
		var revisionYears = [];

		// Push current year
		revisionYears.push({
			option: currentTime.getFullYear(),
			value: currentTime.getFullYear()
		});

		// If date greater than october 1st, add next year
		if (currentTime.getMonth() + 1 >= 10) {
			revisionYears.push({
				option: currentTime.getFullYear() + 1,
				value: currentTime.getFullYear() + 1
			});
		}

		// Push 2 years back from current date
		for(var i = 1; i < 3; i++) {
			revisionYears.push({
				option: currentTime.getFullYear() - i,
				value: currentTime.getFullYear() - i
			});
		}

		me.tbar = {
			width: '100',
			items: [
				{
					xtype: 'textfield',
					itemId: 'ICD10RevisionField',
					name: 'revision',
					fieldLabel: _('revision'),
					readOnly: false,
					//labelAlign: 'top',
					margin: '0 10 0 10',
					width: '60',
				},
				'-',
				{
					xtype: 'textfield',
					itemId: 'ICD10CodeField',
					name: 'code',
					fieldLabel: _('code'),
					readOnly: false,
					//labelAlign: 'top',
					margin: '0 10 0 10',
					width: '60',
				},
				'-',
				'->',
				{
					xtype: 'combobox',
					itemId: 'RevisionYearComboBox',
					name: 'revision_year',
					fieldLabel: _('revision_year'),
					readOnly: false,
					//labelAlign: 'top',
					margin: '0 5 0 0',
					width: '60',
					allowBlank: false,
					queryMode: 'local',
					displayField: 'option',
					valueField: 'value',
					//forceSelection: true,
					value: currentTime.getFullYear(),
					store: Ext.create('Ext.data.Store', {
						fields: [
							{ name:'option', type: 'string' },
							{ name:'value', type: 'string' }
						],
						sorters: [{
							property: 'option',
							direction: 'DESC'
						}],
						data : revisionYears
					})
				},
				'-',
				{
					xtype: 'button',
					text: _('update_icd10s'),
					iconCls: 'far fa-file-alt',
					itemId: 'ICD10UpdateCodesBtn'
				}
			]
		};

		me.callParent();
	}
});
