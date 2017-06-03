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

Ext.define('App.view.scanner.Window', {
	extend: 'Ext.window.Window',
	xtype: 'scannerwindow',
	itemId: 'ScannerWindow',
	width: 900,
	height: 500,
	closeAction: 'hide',
	title: _('scanner'),
	layout: 'border',
	modal: true,
	items: [
		{
			xtype: 'panel',
			region: 'west',
			width: 150,
			split: true,
			itemId: 'ScannerImageDataViewPanel',
			autoScroll: true,
			items: [
				{
					xtype: 'dataview',
					itemId: 'ScannerImageThumbsDataView',
					store: Ext.create('Ext.data.Store', {
						fields: [
							{
								name: 'id',
								type: 'string'
							},
							{
								name: 'src',
								type: 'string'
							},
							{
								name: 'archived',
								type: 'bool'
							},
							{
								name: 'style',
								type: 'string'
							}
						]
					}),
					tpl: new Ext.XTemplate(
						'<tpl for=".">' +
						'<div style="margin-bottom:10px;" class="thumb-wrap">' +
						'<img width="100%" src="{src}" style="padding:10px;{style}" />' +
						'</div>' +
						'</tpl>'
					),
					trackOver: true,
					overItemCls: 'x-item-over',
					itemSelector: 'div.thumb-wrap',
					emptyText: 'No images to display',
				}
			],
			tbar: [
				{
					xtype: 'combobox',
					itemId: 'ScannerSourceCombo',
					editable: false,
					queryMode: 'local',
					displayField: 'name',
					valueField: 'id',
					flex: 1,
					margin: 1,
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
				}
			]
		},
		{
			xtype: 'panel',
			region: 'center',
			itemId: 'ScannerImageViewerPanel',
			layout: 'anchor',
			autoScroll: true,
			frame: true,
			items: [
				{
					xtype: 'image',
					anchor: '100%',
					style: 'background-color:white',
					itemId: 'ScannerImageViewer'
				}
			],
			tbar: [
				{
					text: _('scan'),
					itemId: 'ScannerImageScanBtn',
					scale: 'medium',
					width: 100
				},
				'-',
				{
					text: _('edit'),
					enableToggle: true,
					toggleGroup: 'ScannerImageEditGroup',
					itemId: 'ScannerImageEditBtn',
					scale: 'medium',
					width: 100
				},
				'->',
				{
					text: _('archive'),
					itemId: 'ScannerImageArchiveBtn',
					scale: 'medium',
					width: 150
				}
			]
		}
	],
	buttons: [
		{
			text: _('close'),
			itemId: 'ScannerImageCloseBtn'
		}
	]
});