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

Ext.define('App.view.patient.LegalLetters', {
	extend: 'Ext.grid.Panel',
	requires: [

	],
	xtype: 'patientlegalletters',
	title: _('legal_letters'),
	selType: 'checkboxmodel',
	features: [{
		ftype: 'grouping',
		groupHeaderTpl: '{columnName}: {name} ({rows.length} Item{[values.rows.length > 1 ? "s" : ""]})',
		// hideGroupedHeader: true,
		// startCollapsed: true
	}],
	initComponent: function (){
		var me = this;

		me.store = Ext.create('App.store.administration.LegalLetterSignatures', {
			pageSize: 100,
			remoteFilter: true,
			groupField: 'letter_title'
		});

		me.columns = [
			{
				text: _('letter'),
				dataIndex: 'letter_title',
				flex: 1
			},
			{
				text: _('letter_version'),
				dataIndex: 'letter_version',
				width: 120
			},
			{
				text: _('ip_address'),
				dataIndex: 'signature_ip',
				width: 120
			},
			{
				xtype: 'datecolumn',
				text: _('date_signed'),
				dataIndex: 'signature_date',
				flex: 1,
				format: g('date_time_display_format')
			},
			{
				text: _('signature'),
				dataIndex: 'signature',
				width: 200,
				renderer: function (v){
					if(v === ''){
						return '<span style="color: red;">SIGNATURE REQUIRED</span>'
					}else{
						return Ext.String.format('<img src="{0}" style="width: 100%" alt="Document Signature"/>', v);
					}
				}
			},

		];

		me.tbar = [
			'->',
			{
				xtype: 'button',
				text: 'Sign Document',
				iconCls: 'fal fa-signature',
				itemId: 'PatientLegalLettersSignDocumentBtn'
			}
		];
		me.bbar = {
			xtype: 'pagingtoolbar',
			pageSize: 100,
			store: me.store,
			displayInfo: true,
			plugins: new Ext.ux.SlidingPager()
		};

		me.callParent();

	}
});
