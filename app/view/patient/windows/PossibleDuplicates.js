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

Ext.define('App.view.patient.windows.PossibleDuplicates', {
	extend: 'App.ux.window.Window',
	title: _('possible_duplicates'),
	itemId: 'PossiblePatientDuplicatesWindow',
	closeAction: 'hide',
	bodyStyle: 'background-color:#fff',
	modal: true,
	closable: false,
	requires: [
		'Ext.toolbar.Paging',
		'Ext.ux.SlidingPager'
	],
	initComponent: function(){
		var me = this;

		me.items = [
			{
				xtype: 'grid',
				store: me.store = Ext.create('App.store.patient.PatientPossibleDuplicates'),
				width: 700,
				maxHeight: 700,
				frame: true,
				margin: 5,
				hideHeaders: true,
				columns: [
					{
						dataIndex: 'image',
						width: 65,
						renderer: function(v){
							var src =  v != '' ? v : app.patientImage;
							return '<img src="' + src + '" class="icon32Round" />';
						}
					},
					{
						dataIndex: 'fullname',
						flex: 1,
						renderer: function(v, meta, record){

                            //say(record);
							return '<table cellpadding="1" cellspacing="0" border="0" width="100%" style="font-size: 12px;">' +
								'<tbody>' +

								'<tr>' +
								'<td width="20%"><b>' + _('record_number') + ':</b></td>' +
								'<td>' + record.get('pubpid') +'</td>' +
								'</tr>' +

								'<tr>' +
								'<td><b>' + _('patient') + ':</b></td>' +
								'<td>' + record.get('name') + ' (' + record.get('sex') + ') ' + record.get('DOBFormatted') + '</td>' +
								'</tr>' +
								'</tr>' +
								'<tr>' +
								'<td><b>' + _('address') + ':</b></td>' +
								'<td>' + record.get('fulladdress') + '</td>' +
								'</tr>' +

								'<tr>' +
								'<td><b>' + _('phones') + ':</b></td>' +
								'<td>' + record.get('phones') + '</td>' +
								'</tr>' +

								'<tr>' +
								'<td><b>' + _('driver_lic') + ':</b></td>' +
								'<td>' + record.get('drivers_license') + '</td>' +
								'</tr>' +

								'<tr>' +
								'<td><b>' + _('employer_name') + ':</b></td>' +
								'<td>' + record.get('employer_name') + '</td>' +
								'</tr>' +
								'</tbody>' +
								'</table>';

						}
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

		me.buttons = [
			{
				text: _('cancel'),
				itemId: 'PossiblePatientDuplicatesCancelBtn'
			},
			'-',
			{
				text: _('continue'),
				itemId: 'PossiblePatientDuplicatesContinueBtn'
			}
		];

		me.callParent();
	}
});