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

Ext.define('App.view.administration.FileSystems', {
	extend: 'App.ux.RenderPanel',
	requires: [

	],
	pageTitle: _('file_systems'),
	itemId: 'AdminFileSystemsPanel',
	pageLayout: 'fit',
	initComponent: function(){
		var me = this;

		var store = Ext.create('App.store.administration.FileSystems');

		me.pageBody = [
			{
				xtype: 'grid',
				store: store,
				itemId: 'FileSystemsGrid',
				plugins: [
					{
						ptype: 'rowediting'
					}
				],
				columns: [
					{
						text: _('id'),
						dataIndex: 'id'
					},
					{
						text: _('next_id'),
						dataIndex: 'next_id',
						editor: {
							xtype: 'textfield'
						}
					},
					{
						text: _('dir_path'),
						dataIndex: 'dir_path',
						flex: 2,
						editor: {
							xtype: 'textfield'
						}
					},
					{
						text: _('status'),
						dataIndex: 'status',
						editor: {
							xtype: 'combobox',
							displayField: 'option',
							valueField: 'value',
							editable: false,
							store: Ext.create('Ext.data.Store',{
								fields: ['option', 'value'],
								data: [
									{ option: 'ACTIVE', value: 'ACTIVE' },
									{ option: 'FULL', value: 'FULL' },
									{ option: 'ONLINE', value: 'ONLINE' },
									{ option: 'OFFLINE', value: 'OFFLINE' }
								]
							})
						}
					},
					{
						text: _('total_space'),
						dataIndex: 'total_space'
					},
					{
						text: _('free_space'),
						dataIndex: 'free_space'
					},
					{
						text: _('percent_used'),
						dataIndex: 'percent',
						renderer: function (v, meta, rec) {
							return Math.floor(Math.abs(((rec.get('free_space') / rec.get('total_space')) * 100) - 100)) + '% Used';
						}
					},
					{
						text: _('error'),
						dataIndex: 'error',
						flex: 1
					},
				],
				tbar: [
					{
						text: _('file_system'),
						itemId: 'FileSystemsAddBtn',
						iconCls: 'icoAdd'
					}
				],
				bbar: {
					xtype: 'pagingtoolbar',
					pageSize: 10,
					store: store,
					displayInfo: true,
					plugins: new Ext.ux.SlidingPager(),
					items: [
						'-',
						{
							text: _('analyze'),
							itemId: 'FileSystemsAnalyzeBtn'
						},
						'-'
					]
				}
			}
		];

		me.callParent(arguments);
	}

});
