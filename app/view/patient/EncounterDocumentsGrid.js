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

Ext.define('App.view.patient.EncounterDocumentsGrid', {
	extend: 'Ext.grid.Panel',
	requires: [
		'Ext.grid.feature.Grouping'
	],
	xtype: 'encounterdocumentsgrid',
	title: _('documents'),
	split: true,
	features: [
		{
			ftype: 'grouping',
			collapsible: false,
			groupHeaderTpl: '{name}\'s'
		}
	],
	selType: 'checkboxmodel',
	store: Ext.create('Ext.data.Store', {
		fields: ['id', 'record_id', 'description', 'document_type', 'controller', 'method'],
		proxy: {
			type: 'memory'
		},
		groupField: 'document_type',
		storeId: 'EncounterDocumentsGridStore'
	}),
	columns: [
		{
			header: _('description'),
			dataIndex: 'description',
			flex: 1
		}
	],
	tools: [
		{
			xtype: 'button',
			icon: 'resources/images/icons/preview.png',
			tooltip: _('view'),
			margin: '0 5 0 0',
			itemId: 'EncounterDocumentsViewBtn'
		},
		{
			xtype: 'button',
			icon: 'resources/images/icons/printer.png',
			tooltip: _('print'),
			itemId: 'EncounterDocumentsPrintBtn'
		}
	],
	loadDocs: function(eid){
		App.app.getController('patient.encounter.EncounterDocuments').loadDocumentsByEid(this, eid);
	}
});