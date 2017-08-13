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

Ext.define('App.view.patient.windows.MiniMentalStateExam', {
	extend: 'App.ux.window.Window',
	title: _('mini_mental_state_exam'),
	itemId: 'MiniMentalStateExamWindow',
	closeAction: 'hide',
	modal: true,
	layout: 'fit',
	height: 600,
	width: 1000,
	initComponent: function(){
		var me = this;

		me.items = [
			{
				xtype:'form',
				bodyPadding: 10,
				itemId: 'MiniMentalStateExamForm',
				autoScroll: true,
				items: [
					{
						xtype: 'fieldset',
						title: _('orientation_time'),
						layout: 'hbox',
						anchor: '100%',
						padding: '0 10 10 10',
						items: [
							{
								xtype: 'displayfield',
								fieldLabel: _('description'),
								submitValue: false,
								labelAlign: 'top',
								flex: 1,
								value: _('orientation_time_description')
							},
							{
								xtype: 'numberfield',
								fieldLabel: _('score'),
								labelAlign: 'top',
								width: 100,
								margin: '0 10',
								name: 'orientation_time_score'
							},
							{
								xtype: 'textareafield',
								fieldLabel: _('notes'),
								labelAlign: 'top',
								flex: 2,
								name: 'orientation_time_notes'
							}
						]
					},
					{
						xtype: 'fieldset',
						title: _('orientation_place'),
						layout: 'hbox',
						anchor: '100%',
						padding: '0 10 10 10',
						items: [
							{
								xtype: 'displayfield',
								fieldLabel: _('description'),
								submitValue: false,
								labelAlign: 'top',
								flex: 1,
								value: _('orientation_place_description')
							},
							{
								xtype: 'numberfield',
								fieldLabel: _('score'),
								labelAlign: 'top',
								width: 100,
								margin: '0 10',
								name: 'orientation_place_score'
							},
							{
								xtype: 'textareafield',
								fieldLabel: _('notes'),
								labelAlign: 'top',
								flex: 2,
								name: 'orientation_place_notes'
							}
						]
					},
					{
						xtype: 'fieldset',
						title: _('registration'),
						layout: 'hbox',
						anchor: '100%',
						padding: '0 10 10 10',
						items: [
							{
								xtype: 'displayfield',
								fieldLabel: _('description'),
								submitValue: false,
								labelAlign: 'top',
								flex: 1,
								value: _('registration_score_description')
							},
							{
								xtype: 'numberfield',
								fieldLabel: _('score'),
								labelAlign: 'top',
								width: 100,
								margin: '0 10',
								name: 'registration_score'
							},
							{
								xtype: 'textareafield',
								fieldLabel: _('notes'),
								labelAlign: 'top',
								flex: 2,
								name: 'registration_notes'
							}
						]
					},
					{
						xtype: 'fieldset',
						title: _('attention_calculation'),
						layout: 'hbox',
						anchor: '100%',
						padding: '0 10 10 10',
						items: [
							{
								xtype: 'displayfield',
								fieldLabel: _('description'),
								submitValue: false,
								labelAlign: 'top',
								flex: 1,
								value: _('attention_calculation_description')
							},
							{
								xtype: 'numberfield',
								fieldLabel: _('score'),
								labelAlign: 'top',
								width: 100,
								margin: '0 10',
								name: 'attention_calculation_score'
							},
							{
								xtype: 'textareafield',
								fieldLabel: _('notes'),
								labelAlign: 'top',
								flex: 2,
								name: 'attention_calculation_notes'
							}
						]
					},
					{
						xtype: 'fieldset',
						title: _('recall'),
						layout: 'hbox',
						anchor: '100%',
						padding: '0 10 10 10',
						items: [
							{
								xtype: 'displayfield',
								fieldLabel: _('description'),
								submitValue: false,
								labelAlign: 'top',
								flex: 1,
								value: _('recall_description')
							},
							{
								xtype: 'numberfield',
								fieldLabel: _('score'),
								labelAlign: 'top',
								width: 100,
								margin: '0 10',
								name: 'recall_score'
							},
							{
								xtype: 'textareafield',
								fieldLabel: _('notes'),
								labelAlign: 'top',
								flex: 2,
								name: 'recall_notes'
							}
						]
					},
					{
						xtype: 'fieldset',
						title: _('language'),
						layout: 'hbox',
						anchor: '100%',
						padding: '0 10 10 10',
						items: [
							{
								xtype: 'displayfield',
								fieldLabel: _('description'),
								submitValue: false,
								labelAlign: 'top',
								flex: 1,
								value: _('language_description')
							},
							{
								xtype: 'numberfield',
								fieldLabel: _('score'),
								labelAlign: 'top',
								width: 100,
								margin: '0 10',
								name: 'language_score'
							},
							{
								xtype: 'textareafield',
								fieldLabel: _('notes'),
								labelAlign: 'top',
								flex: 2,
								name: 'language_notes'
							}
						]
					},
					{
						xtype: 'fieldset',
						title: _('repetition'),
						layout: 'hbox',
						anchor: '100%',
						padding: '0 10 10 10',
						items: [
							{
								xtype: 'displayfield',
								fieldLabel: _('description'),
								submitValue: false,
								labelAlign: 'top',
								flex: 1,
								value: _('repetition_description')
							},
							{
								xtype: 'numberfield',
								fieldLabel: _('score'),
								labelAlign: 'top',
								width: 100,
								margin: '0 10',
								name: 'repetition_score'
							},
							{
								xtype: 'textareafield',
								fieldLabel: _('notes'),
								labelAlign: 'top',
								flex: 2,
								name: 'repetition_notes'
							}
						]
					},
					{
						xtype: 'fieldset',
						title: _('complex_commands'),
						layout: 'hbox',
						anchor: '100%',
						padding: '0 10 10 10',
						items: [
							{
								xtype: 'displayfield',
								fieldLabel: _('description'),
								submitValue: false,
								labelAlign: 'top',
								flex: 1,
								value: _('complex_commands_description')
							},
							{
								xtype: 'numberfield',
								fieldLabel: _('score'),
								labelAlign: 'top',
								width: 100,
								margin: '0 10',
								name: 'complex_commands_score'
							},
							{
								xtype: 'textareafield',
								fieldLabel: _('notes'),
								labelAlign: 'top',
								flex: 2,
								name: 'complex_commands_notes'
							}
						]
					}

				]
			}
		];

		me.callParent(arguments);

	},
	buttons: [
		{
			text: _('cancel'),
			itemId: 'MiniMentalStateExamWindowCancelBtn'
		},
		{
			text: _('save'),
			itemId: 'MiniMentalStateExamWindowSaveBtn'
		}
	]

});
