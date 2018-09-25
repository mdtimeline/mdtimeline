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

Ext.define('App.view.patient.encounter.CarePlanGoalsNewWindow', {
	extend: 'Ext.window.Window',
	requires: [
		'App.ux.LiveSnomedProcedureSearch'
	],
	xtype: 'careplangoalsnewwindow',
	title: _('new_goal'),
	closable: false,
	constrain: true,
	closeAction: 'hide',
	layout: 'fit',
	items: [
		{
			xtype: 'form',
			itemId: 'CarePlanGoalsNewForm',
			layout: {
				type: 'vbox',
				align: 'stretch'
			},
			bodyPadding: 10,
			items: [
				{
					xtype: 'fieldcontainer',
					layout: 'hbox',
					itemId: 'CarePlanGoalPlanDateContainer',
					fieldLabel: _('plan_date'),
					labelAlign: 'top',
					defaults: {
						margin: '0 5 0 0'
					},
					items: [
						{
							xtype: 'datefield',
							itemId: 'CarePlanGoalPlanDateField',
							allowBlank: false,
							format: 'Y-m-d',
							name: 'plan_date'
						},
						{
							xtype: 'button',
							text: '+1 Day',
							action: '1D'
						},
						{
							xtype: 'button',
							text: '+1 Week',
							action: '1W'
						},
						{
							xtype: 'button',
							text: '+2 Week',
							action: '2W'
						},
						{
							xtype: 'button',
							text: '+1 Month',
							action: '1M'
						},
						{
							xtype: 'button',
							text: '+3 Month',
							action: '3M'
						},
						{
							xtype: 'button',
							text: '+6 Month',
							action: '6M'
						},
						{
							xtype: 'button',
							text: '+1 Year',
							action: '1Y'
						}
					]
				},
				{
					xtype: 'snomedliveproceduresearch',
					itemId: 'CarePlanGoalSearchField',
					fieldLabel: _('goal'),
					displayField: 'Term',
					valueField: 'Term',
					labelAlign: 'top',
					allowBlank: false,
					hideLabel: false,
					name: 'goal'
				},
				{
					xtype: 'textareafield',
					fieldLabel: _('instructions'),
					labelAlign: 'top',
					name: 'instructions',
					flex: 1
				}
			]
		}
	],
	buttons: [
		{
			text: _('cancel'),
			itemId: 'CarePlanGoalsNewFormCancelBtn'
		},
		{
			text: _('save'),
			itemId: 'CarePlanGoalsNewFormSaveBtn'
		}
	]
});