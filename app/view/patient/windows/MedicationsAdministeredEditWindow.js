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

Ext.define('App.view.patient.windows.MedicationsAdministeredEditWindow', {
	extend: 'Ext.window.Window',
	xtype: 'medicationsadministerededitwindow',
	itemId: 'MedicationsAdministeredEditWindow',
	modal: true,
	closeAction: 'hide',
	layout: 'fit',
	width: 750,
	title: _('medication_administered'),
	items: [
		{
			xtype: 'form',
			itemId: 'MedicationsAdministeredEditForm',
			bodyPadding: 10,
			items: [
				{
					xtype: 'displayfield',
					fieldLabel: _('medication'),
					name: 'description',
					anchor: '100%',
				},
				{
					xtype: 'displayfield',
					fieldLabel: _('instructions'),
					name: 'instructions',
					anchor: '100%',
				},
				{
					xtype: 'userlivetsearch',
					fieldLabel: _('administered_by'),
					hideLabel: false,
					name: 'administered_by',
					anchor: '100%',
					itemId: 'MedicationsAdministeredEditAdministerSearchField',
				},
				{
					xtype:'container',
					anchor: '100%',
					layout: {
						type: 'hbox',
						align: 'stretch'
					},
					items: [
						{
							xtype:'fieldset',
							title: _('administration'),
							flex: 1,
							margin: '0 5 0 0',
							defaults: {
								labelWidth: 120,
								anchor: '100%'
							},
							items: [
								{
									xtype: 'checkbox',
									fieldLabel: _('administered'),
									name: 'administered'
								},
								{
									xtype: 'mitos.datetime',
									fieldLabel: _('administered_date'),
									name: 'administered_date'
								},
								{
									xtype: 'textfield',
									fieldLabel: _('amount'),
									name: 'administered_amount'
								},
								{
									xtype: 'textfield',
									fieldLabel: _('units'),
									name: 'administered_units'
								},
								{
									xtype: 'gaiaehr.combo',
									fieldLabel: _('adverse_reaction'),
									listKey: 'med_adv_reactions',
									name: 'adverse_reaction_code',
									allowBlank: false,
									loadStore: true,
									queryMode: 'local',
									itemId: 'MedicationsAdministeredEditAdverseReactionField',
								}
							]
						},
						{
							xtype:'fieldset',
							title: _('substance_info'),
							flex: 1,
							margin: '0 0 0 5',
							defaults: {
								anchor: '100%'
							},
							items: [
								{
									xtype: 'displayfield',
									fieldLabel: _('rxnorm'),
									name: 'rxcui'
								},
								{
									xtype: 'textfield',
									fieldLabel: _('lot_number'),
									name: 'lot_number'
								},
								{
									xtype: 'datefield',
									fieldLabel: _('exp_date'),
									name: 'exp_date'
								},
								{
									xtype: 'textfield',
									fieldLabel: _('manufacturer'),
									name: 'manufacturer'
								}
							]
						},
					]
				},
				{
					xtype:'fieldset',
					title: _('notes'),
					anchor: '100%',
					layout: 'fit',
					items: [
						{
							xtype: 'textareafield',
							name: 'note',
							margin: '5 5 10 5'
						}
					]
				}

			]
		}
	],
	buttons: [
		{
			text: _('cancel'),
			itemId: 'MedicationsAdministeredEditCancelBtn'
		},
		{
			text: _('save'),
			itemId: 'MedicationsAdministeredEditSaveBtn'
		}
	]
});