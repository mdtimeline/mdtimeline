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

Ext.define('App.view.administration.DocumentLocationWindow', {
	extend: 'Ext.window.Window',
	title: _('document_location'),
	itemId: 'DocumentLocationWindow',
	width: 500,
	modal: true,
	layout: 'fit',
	initComponent: function(){
		var me = this;

		me.items = [
			{
				xtype: 'form',
				itemId: 'DocumentLocationWindowForm',
				bodyPadding: 10,
				fieldDefaults: {
					labelAlign: 'top',
				},
				items: [
					{
						xtype: 'textfield',
						name: 'reference_number',
						allowBlank: false,
						fieldLabel: _('reference_number')
					},
					{
						xtype: 'textfield',
						name: 'description',
						fieldLabel: _('description'),
						anchor: '100%'
					},
					{
						xtype: 'textareafield',
						name: 'notes',
						fieldLabel: _('notes'),
						anchor: '100%'
					}
				]
			}
		];

		me.buttons =  [
			{
				text: _('cancel'),
				itemId: 'DocumentLocationWindowCancelBtn'
			},
			{
				text: _('save'),
				itemId: 'DocumentLocationWindowSaveBtn'
			}
		];

		me.callParent(arguments);
	}
});