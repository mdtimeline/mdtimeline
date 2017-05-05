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

Ext.define('App.view.patient.SocialPsychologicalBehavioral', {
	extend: 'Ext.panel.Panel',
	requires: [

	],
	itemId: 'SocialPsychologicalBehavioralPanel',
	xtype: 'socialpsychologicalbehavioralpanel',
	title: _('psychological_behavioral'),
	layout: 'border',
	tbar: [
		{
			text: _('spb'),
			iconCls: 'icoAdd',
			action: 'encounterRecordAdd',
			itemId: 'SocialPsychologicalBehavioralAddBtn'
		}
	],
	initComponent: function () {

		var me = this;

		var store = Ext.create('App.store.patient.SocialPsychologicalBehavioral', {
			remoteFilter: true
		});

		me.items = [
			{
				xtype: 'grid',
				store: store,
				region: 'west',
				width: 450,
				itemId: 'SocialPsychologicalBehavioralGrid',
				columns: [
					{
						xtype: 'datecolumn',
						text: _('observation_date'),
						dataIndex: 'create_date',
						format: 'F j, Y',
						width: 110
					},
					{
						text: _('pay_basic_needs'),
						dataIndex: 'pay_basics',
						width: 110,
						renderer: function (v) {
							var obj = {
								0: 'Very hard',
								1: 'Hard',
								2: 'Somewhat hard',
								3: '3rd grade',
								4: 'Not very hard'
							};

							if(obj[v]){
								return obj[v];
							}

							return v;
						}
					},
					{
						text: _('education'),
						dataIndex: 'edu_highest_grade',
						width: 100,
						renderer: function (v) {
							var obj = {
								0: 'Never attended/kindergarten only',
								1: '1st grade',
								2: '2nd grade',
								3: '3rd grade',
								4: '4th grade',
								5: '5th grade',
								6: '6th grade',
								7: '7th grade',
								8: '8th grade',
								9: '9th grade',
								10: '10th grade',
								11: '11th grade',
								12: '12th grade, no diploma',
								13: 'High school graduate',
								14: 'GED or equivalent',
								15: 'Some college, no degree',
								16: 'Associate degree: occupational, technical, or vocational program',
								17: 'Associate degree: academic program',
								18: 'Bachelor\'s degree',
								19: 'Master\'s degree',
								20: 'Professional school degree',
								21: 'Doctoral degree',
								77: 'Refused',
								99: 'Don\'t know'
							};

							if(obj[v]){
								return obj[v];
							}

							return v;
						}
					},
					{
						text: _('exercise'),
						dataIndex: 'exercise',
						flex: 1,
						renderer: function (v, meta, rec) {
							return Ext.String.format(
								'for {0} min(s) {1} in a week',
								rec.get('exercise_past_days_minutes'),
								rec.get('exercise_past_days_amount')
							);
						}
					}
				]
			},
			{
				xtype:'panel',
				layout: 'fit',
				region: 'center',
				items: [
					{
						xtype: 'chart',
						itemId: 'SocialPsychologicalBehavioralChart',
						animate: true,
						store: store,
						legend: {
							position: 'top'
						},
						axes: [
							{
								type: 'Numeric',
								position: 'left',
								fields: ['stress_level', 'patient_health_score', 'patient_drink_score', 'patient_isolation_score', 'patient_humiliation_score'],
								title: 'Scores',
								grid: true,
								minimum: 0
							},
							{
								type: 'Time',
								position: 'bottom',
								fields: ['create_date'],
								title: 'Date',
								dateFormat: 'M j, Y'
							}
						],
						series: [
							{
								type: 'line',
								highlight: {
									size: 7,
									radius: 7
								},
								axis: 'left',
								xField: 'create_date',
								yField: ['stress_level'],
								title: 'Stress Lvl',
								showInLegend: true,
								markerConfig: {
									type: 'circle',
									size: 4,
									radius: 4,
									'stroke-width': 0
								}
							},
							{
								type: 'line',
								highlight: {
									size: 7,
									radius: 7
								},
								axis: 'left',
								xField: 'create_date',
								yField: ['patient_health_score'],
								title: 'PHQ-2 Score',
								showInLegend: true,
								markerConfig: {
									type: 'circle',
									size: 4,
									radius: 4,
									'stroke-width': 0
								}
							},
							{
								type: 'line',
								highlight: {
									size: 7,
									radius: 7
								},
								axis: 'left',
								xField: 'create_date',
								yField: 'patient_drink_score',
								title: 'AUDIT-C Score',
								showInLegend: true,
								markerConfig: {
									type: 'circle',
									size: 4,
									radius: 4,
									'stroke-width': 0
								}
							},
							{
								type: 'line',
								highlight: {
									size: 7,
									radius: 7
								},
								axis: 'left',
								xField: 'create_date',
								yField: 'patient_isolation_score',
								title: 'NHANES Score',
								showInLegend: true,
								markerConfig: {
									type: 'circle',
									size: 4,
									radius: 4,
									'stroke-width': 0
								}
							},
							{
								type: 'line',
								highlight: {
									size: 7,
									radius: 7
								},
								axis: 'left',
								xField: 'create_date',
								yField: 'patient_humiliation_score',
								title: 'HARK Score',
								showInLegend: true,
								markerConfig: {
									type: 'circle',
									size: 4,
									radius: 4,
									'stroke-width': 0
								}
							}
						]
					}
				]
			}
		];

		me.callParent();
	}

});
