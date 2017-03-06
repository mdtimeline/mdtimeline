/**
 * mdTimeLine EHR (Electronic Health Records)
 * Copyright (C) 2017 mdTimeLine, Inc.
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

Ext.define('App.view.patient.CCD', {
	extend: 'Ext.panel.Panel',
	requires: [
		'Ext.ux.IFrame',
		'App.ux.ManagedIframe'
	],
	xtype: 'patientccdpanel',
	title: _('ccd'),
	columnLines: true,
	itemId: 'CcdPanel',
	layout: 'fit',
	items: [
		{
			xtype: 'miframe',
			style: 'background-color:white',
			autoMask: true,
			itemId: 'patientDocumentViewerFrame'
		}
	],
	tbar: [
        {
            xtype: 'fieldcontainer',
            layout: 'vbox',
            defaults: {
                margin: '0 5 5 5'
            },
            items:[
                {
                    xtype: 'patientEncounterCombo',
                    itemId: 'PatientCcdPanelEncounterCmb',
                    width: 300,
                    fieldLabel: _('filter_encounter'),
                    hideLabel: false,
                    labelAlign: 'top',
	                includeAllSelection: true
                }
            ]

        },
		'-',
		{
			xtype: 'checkboxgroup',
			fieldLabel: _('exclude'),
			columns: 5,
			vertical: true,
			labelWidth: 60,
			itemId: 'PatientCcdPanelExcludeCheckBoxGroup',
			flex: 1,
			items: [
				{boxLabel: _('procedures'), name: 'exclude', inputValue: 'procedures'},
				{boxLabel: _('vitals'), name: 'exclude', inputValue: 'vitals'},
				{boxLabel: _('immunizations'), name: 'exclude', inputValue: 'immunizations'},
				{boxLabel: _('medications'), name: 'exclude', inputValue: 'medications'},
				{boxLabel: _('meds_administered'), name: 'exclude', inputValue: 'administered'},
				{boxLabel: _('plan_of_care'), name: 'exclude', inputValue: 'planofcare'},
				{boxLabel: _('problems'), name: 'exclude', inputValue: 'problems'},
				{boxLabel: _('allergies'), name: 'exclude', inputValue: 'allergies'},
				{boxLabel: _('social'), name: 'exclude', inputValue: 'social'},
				{boxLabel: _('results'), name: 'exclude', inputValue: 'results'},
                {boxLabel: _('provider_information'), name: 'exclude', inputValue: 'provider_information'},
                {boxLabel: _('clinical_instructions'), name: 'exclude', inputValue: 'clinical_instructions'},
                {
                    xtype:'fieldset',
                    columnWidth: 1,
                    title: _('plan_of_care'),
                    defaultType: 'checkbox',
                    defaults: {anchor: '100%'},
                    layout: 'anchor',
                    items:[
                        {boxLabel: _('future_appointments'), name: 'exclude', inputValue: 'future_appointments'},
                        {boxLabel: _('referrals_other_providers'), name: 'exclude', inputValue: 'referrals_other_providers'}
                    ]
                }
			]
		},
		'-',
		{
			xtype: 'button',
			text: _('refresh'),
			margin: '0 0 5 0',
			itemId: 'viewCcdBtn',
			icon: 'resources/images/icons/refresh.png'
		},
		'-',
		{
			xtype: 'container',
			layout: 'vbox',
			items: [
				{
					xtype: 'button',
					text: _('upload'),
					margin: '0 0 5 0',
					itemId: 'importCcdBtn',
					icon: 'resources/images/icons/upload.png',
					width: 100
				},
				{
					xtype: 'button',
					text: _('download'),
					itemId: 'exportCcdBtn',
					icon: 'resources/images/icons/download.png',
					width: 100
				}
			]
		},
		'-',
		{
			xtype: 'container',
			layout: 'vbox',
			items: [
				{
					xtype: 'button',
					text: _('archive'),
					margin: '0 0 5 0',
					itemId: 'archiveCcdBtn',
					icon: 'resources/images/icons/archive_16.png',
					width: 100
				},
				{
					xtype: 'button',
					text: 'Print',
					iconCls: 'icon-print',
					itemId: 'printCcdBtn',
					width: 100
				}
			]
		}
	]

});
