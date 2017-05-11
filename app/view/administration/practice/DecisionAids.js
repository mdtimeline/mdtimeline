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

Ext.define('App.view.administration.practice.DecisionAids', {
	extend: 'Ext.grid.Panel',
	xtype: 'decisionaidspanel',
	requires: [
		'Ext.grid.plugin.RowEditing',
		'Ext.ux.SlidingPager'
	],
	title: _('patient_decision_aids'),

	initComponent: function(){
		var me = this;

		Ext.apply(me, {
			store: me.store = Ext.create('App.store.administration.DecisionAids', {
				autoSync: false
			}),
			columns: [
				{
					text: _('trigger_code'),
					dataIndex: 'trigger_code',
					sortable: true,
					width: 150,
					editor: {
						xtype: 'textfield',
						maxLength: 40
					}
				},
				{
					text: _('description_code'),
					dataIndex: 'instruction_code',
					width: 150,
					editor: {
						xtype: 'textfield',
						maxLength: 40
					}
				},
				{
					text: _('description_code_type'),
					dataIndex: 'instruction_code_type',
					width: 150,
					editor: {
						xtype: 'textfield',
						maxLength: 40
					}
				},
				{
					text: _('description'),
					dataIndex: 'instruction_code_description',
					flex: 1,
					editor: {
						xtype: 'textfield',
						maxLength: 600
					}
				},
				{
					text: _('active'),
					sortable: true,
					dataIndex: 'active',
					renderer: me.boolRenderer,
					editor: {
						xtype: 'checkboxfield'
					}
				}
			],
			plugins: [
				{
					ptype: 'rowediting',
					clicksToEdit: 1
				}
			],
			tbar: [
				'->',
				{
					xtype: 'button',
					text: _('decision_aid'),
					iconCls: 'icoAdd',
					itemId: 'DecisionAidsAddBtn'
				}
			],
			bbar: Ext.create('Ext.PagingToolbar', {
				pageSize: 10,
				store: me.store,
				displayInfo: true,
				plugins: Ext.create('Ext.ux.SlidingPager', {})
			})
		});

		me.callParent(arguments);

	}

});
