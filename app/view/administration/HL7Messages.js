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


Ext.define('App.view.administration.HL7Messages', {
	extend: 'Ext.window.Window',
	layout: 'fit',
	title: _('hl7_messages'),
	itemId: 'HL7MessagesWindow',
	width: 1000,
	height: 600,
	bodyStyle: 'background-color:white',
	defaults: {
		xtype: 'textareafield',
		labelAlign: 'top'
	},
	initComponent: function(){
		var me = this;

		me.store = Ext.create('App.store.administration.HL7Messages');

		me.items = [
			{
				xtype: 'grid',
				itemId: 'HL7MessagesGrid',
				store: me.store,
				columns: [
					{
						xtype:'datecolumn',
						text: _('date'),
						formar: g('date_time_display_format'),
						dataIndex: 'date_processed'
					},
					{
						text: _('type'),
						dataIndex: 'msg_type'
					},
					{
						text: _('foreign_facility'),
						dataIndex: 'foreign_facility',
						flex: 1
					},
					{
						text: _('foreign_application'),
						dataIndex: 'foreign_application',
						flex: 1
					},
					{
						text: _('foreign_address'),
						dataIndex: 'foreign_address'
					},

					{
						text: _('status'),
						dataIndex: 'status'
					}
				],
				bbar: {
					xtype: 'pagingtoolbar',
					pageSize: 10,
					store: me.store,
					displayInfo: true,
					plugins: Ext.create('Ext.ux.SlidingPager')
				}
			}
		];

		me.callParent();

	}

}); 