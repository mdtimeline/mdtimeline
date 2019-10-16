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

Ext.define('App.view.patient.windows.ImmunizationRegisterResponseWindow', {
	extend: 'Ext.window.Window',
	requires: [
		'App.view.patient.Documents'
	],
	title: _('register_response'),
	layout: 'fit',
	height: 700,
	width: 1000,
	maximizable: true,
	hl7Printed: '',
	hl7Message: '',
	bodyPadding: 5,
	initComponent: function(){
		var me = this;

		me.items = [
			{
				xtype: 'tabpanel',
				items: [
					{
						xtype: 'panel',
						title: 'Response',
						layout: 'fit',
						autoScroll: true,
                        html: '<pre>' + me.hl7Printed + '</pre>',
					},
					{
						xtype: 'panel',
						title: 'Message',
						autoScroll: true,
						html: '<pre>' + me.hl7Message + '</pre>',
					}
				]
			}
		];

		me.callParent();
	}

});