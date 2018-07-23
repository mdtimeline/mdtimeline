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

Ext.define('App.view.scanner.DocumentScanWindow', {
	extend: 'Ext.window.Window',
	xtype: 'documentscanwindow',
	itemId: 'DocumentScanWindow',
	width: 1000,
	height: 600,
	closeAction: 'hide',
	title: _('scanner'),
	layout: 'border',
	modal: true,
	maximizable: true,
	items: [
		{
			xtype: 'panel',
			region: 'center',
			split: true,
			itemId: 'DocumentScanDataViewPanel',
			autoScroll: true,
			items: [
				{
					xtype: 'dataview',
					itemId: 'DocumentScanThumbsDataView',
					cls: 'documents-data-view',
					store: Ext.create('Ext.data.Store', {
						model: 'App.model.patient.PatientDocuments'
					}),
					tpl: new Ext.XTemplate(
						'<tpl for=".">' +
						'<div style="margin-bottom:10px;" class="thumb-wrap">' +
						'<p>{docTypeCode} - {docType}</p>' +
						'<img src="{document}" style="{style}" />' +
						'</div>' +
						'</tpl>'
					),
					trackOver: true,
					overItemCls: 'x-item-over',
					itemSelector: 'div.thumb-wrap',
					emptyText: '<p style="padding: 10px;">No documents to display</p>',
				}
			],
			tbar: [
				'->',
				{
					xtype: 'button',
					text: _('remove_document'),
					disabled: true,
					icon: 'resources/images/icons/delete.png',
					itemId: 'DocumentScanRemoveDocumentBtn'
				}
			]
		},
		{
			xtype: 'form',
			region: 'east',
			width: 300,
			itemId: 'DocumentScanForm',
			autoScroll: true,
			frame: true,
			bodyPadding: 10,
			items: [
				{
					xtype: 'combobox',
					itemId: 'DocumentScanSourceCombo',
					editable: false,
					queryMode: 'local',
					displayField: 'name',
					valueField: 'id',
					fieldLabel: _('scanner'),
					anchor: '%100',
					allowBlank: false,
					name: 'scannerId',
					labelAlign: 'top',
					store: Ext.create('Ext.data.Store', {
						fields: [
							{
								name: 'id',
								type: 'string'
							},
							{
								name: 'name',
								type: 'string'
							}
						]
					})
				},
				{
					xtype: 'gaiaehr.combo',
					itemId: 'DocumentScanDocTypeCombo',
					fieldLabel: _('type'),
					labelAlign: 'top',
					list: 102,
					name: 'docTypeCode',
					allowBlank: false,
					editable: false,
					anchor: '%100',
				},
				{
					xtype: 'textfield',
					fieldLabel: _('title') + ' (' + _('optional') + ')',
					labelAlign: 'top',
					name: 'title',
					anchor: '%100',
				},
				{
					xtype: 'textareafield',
					name: 'note',
					fieldLabel: _('notes') + ' (' + _('optional') + ')',
					labelAlign: 'top',
					anchor: '%100',
				},
				{
					xtype: 'checkbox',
					name: 'duplex',
					anchor: '%100',
					boxLabel: _('duplex') + ' (' + _('double_sided') + ')'
				},
				{
					xtype: 'checkbox',
					name: 'landscape',
					anchor: '%100',
					boxLabel: _('landscape') + ' (' + _('orientation') + ')'
				},
				{
					xtype: 'checkbox',
					name: 'color',
					anchor: '%100',
					boxLabel: _('color')
				},
				{
					xtype: 'radiogroup',
					columns: 1,
					vertical: true,
					cls: 'high-resolution-radiogroup',
					items: [
						{ boxLabel: _('low_resolution'), name: 'resolution', inputValue: '0', checked: true },
						{ boxLabel: _('normal_resolution'), name: 'resolution', inputValue: '1'},
						{ boxLabel: _('high_resolution'), name: 'resolution', inputValue: '2' }
					]
				},
				{
					xtype: 'progressbar',
					itemId: 'DocumentScanProgressBar',
					margin: '25 0 5 0'
				},
				{
					xtype: 'button',
					text: _('start_scan'),
					scale: 'large',
					itemId: 'DocumentScanStartScanBtn',
					anchor: '%100',
					margin: '0 0 0 0'
				},
			]
		}
	],
	buttons: [
		{
			text: _('cancel'),
			itemId: 'DocumentScanCancelBtn'
		},
		{
			text: _('save'),
			itemId: 'DocumentScanSaveBtn'
		}
	]
});