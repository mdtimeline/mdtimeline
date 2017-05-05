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

Ext.define('App.view.patient.windows.SocialPsychologicalBehavioral', {
	extend: 'App.ux.window.Window',
	title: _('social_psychological_behavioral_eval'),
	itemId: 'SocialPsychologicalBehavioralWindow',
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
				bodyPadding: 30,
				itemId: 'SocialPsychologicalBehavioralForm',
				autoScroll: true,
				items: [
					{
						xtype: 'fieldset',
						title: 'Finantial Resource Strain',
						items: [
							{
								xtype: 'radiogroup',
								fieldLabel: 'How hard is it for you to pay for the very basics like food, housing, medical care, and heating? [76513-1]',
								columns: 1,
								vertical: true,
								labelAlign: 'top',
								items: [
									{ boxLabel: 'Very hard - [LA15832-1]',      name: 'pay_basics', inputValue: 1 },
									{ boxLabel: 'Hard - [LA14745-6]',           name: 'pay_basics', inputValue: 2 },
									{ boxLabel: 'Somewhat hard - [LA22683-9]',  name: 'pay_basics', inputValue: 3 },
									{ boxLabel: 'Not very hard - [LA22682-1]',  name: 'pay_basics', inputValue: 4 }
								]

							}
						]
					},
					{
						xtype: 'fieldset',
						title: 'Education',
						items: [
							{
								xtype: 'combobox',
								anchor: '50%',
								fieldLabel: 'What is the highest grade or level of school you have completed or the highest degree you have received? [63504-5]',
								labelAlign: 'top',
								name: 'edu_highest_grade',
								displayField: 'option',
								valueField: 'value',
								editable : false,
								store: Ext.create('Ext.data.Store',{
									fields: ['option', 'value'],
									data: [
										{ 'option': 'Never attended/kindergarten only - [LA15606-9]', value: 0 },
										{ 'option': '1st grade - [LA15607-7]', value: 1 },
										{ 'option': '2nd grade - [LA15608-5]', value: 2 },
										{ 'option': '3rd grade - [LA15609-3]', value: 3 },
										{ 'option': '4th grade - [LA15610-1]', value: 4 },
										{ 'option': '5th grade - [LA15611-9]', value: 5 },
										{ 'option': '6th grade - [LA15612-7]', value: 6 },
										{ 'option': '7th grade - [LA15613-5]', value: 7 },
										{ 'option': '8th grade - [LA15614-3]', value: 8 },
										{ 'option': '9th grade - [LA15615-0]', value: 9 },
										{ 'option': '10th grade - [LA15616-8]', value: 10 },
										{ 'option': '11th grade - [LA15617-6]', value: 11 },
										{ 'option': '12th grade, no diploma - [LA15618-4]', value: 12 },
										{ 'option': 'High school graduate - [LA15564-0]', value: 13 },
										{ 'option': 'GED or equivalent - [LA15619-2]', value: 14 },
										{ 'option': 'Some college, no degree - [LA15620-0]', value: 15 },
										{ 'option': 'Associate degree: occupational, technical, or vocational program - [LA15621-8]', value: 16 },
										{ 'option': 'Associate degree: academic program - [LA15622-6]', value: 17 },
										{ 'option': 'Bachelor\'s degree - [LA12460-4]', value: 18 },
										{ 'option': 'Master\'s degree - [LA12461-2]', value: 19 },
										{ 'option': 'Professional school degree - [LA15625-9]', value: 20 },
										{ 'option': 'Doctoral degree - [LA15626-7]', value: 21 },
										{ 'option': 'Refused - [LA4389-8]', value: 77 },
										{ 'option': 'Don\'t know - [LA12688-0]', value: 99 }
									]
								})
							},
						]
					},
					{
						xtype: 'fieldset',
						title: 'Stress',
						items: [
							{
								xtype: 'radiogroup',
								fieldLabel: 'Stress means a situation in which a person feels tense, restless, nervous, or anxious, or is unable to sleep at night because his/her mind is troubled all the time. Do you feel this kind of stress these days? [76542-0]',
								columns: 1,
								vertical: true,
								labelAlign: 'top',
								items: [
									{ boxLabel: 'Not at all - [LA6568-5]',      name: 'stress_level', inputValue: 1 },
									{ boxLabel: 'Only a little - [LA22687-0]',  name: 'stress_level', inputValue: 2 },
									{ boxLabel: 'To some extent - [LA22686-2]', name: 'stress_level', inputValue: 3 },
									{ boxLabel: 'Rather much - [LA22685-4]',    name: 'stress_level', inputValue: 4 },
									{ boxLabel: 'Very much - [LA13914-9]',      name: 'stress_level', inputValue: 5 }
								]
							}
						]
					},
					{
						xtype: 'fieldset',
						title: 'Depression - [PHQ-2] [55757-9]',
						items: [
							{
								xtype: 'radiogroup',
								fieldLabel: 'Little interest or pleasure in doing things? [44250-9]',
								columns: 1,
								vertical: true,
								labelAlign: 'top',
								items: [
									{ boxLabel: 'Not at all - [LA6568-5]',               name: 'interest_pleasure', inputValue: 0 },
									{ boxLabel: 'Several days - [LA6569-3]',             name: 'interest_pleasure', inputValue: 1 },
									{ boxLabel: 'More than half the days - [LA6570-1]',  name: 'interest_pleasure', inputValue: 2 },
									{ boxLabel: 'Nearly every day - [LA6571-9]',         name: 'interest_pleasure', inputValue: 3 }
								]
							},
							{
								xtype: 'radiogroup',
								fieldLabel: 'Feeling down, depressed, or hopeless? [44255-8]',
								columns: 1,
								vertical: true,
								labelAlign: 'top',
								items: [
									{ boxLabel: 'Not at all - [LA6568-5]',               name: 'feeling_down_depressed', inputValue: 0 },
									{ boxLabel: 'Several days - [LA6569-3]',             name: 'feeling_down_depressed', inputValue: 1 },
									{ boxLabel: 'More than half the days - [LA6570-1]',  name: 'feeling_down_depressed', inputValue: 2 },
									{ boxLabel: 'Nearly every day - [LA6571-9]',         name: 'feeling_down_depressed', inputValue: 3 }
								]
							},
							{
								xtype: 'displayfield',
								fieldLabel: 'Patient Health Questionnaire 2 item [PHQ-2] [55758-7]',
								labelAlign: 'top',
								name: 'patient_health_score'
							}
						]
					},
					{
						xtype: 'fieldset',
						title: 'Physical Avtivity',
						items: [
							{
								xtype: 'slider',
								anchor: '100%',
								fieldLabel: 'How many days of moderate to strenuous exercise, like a brisk walk, did you do in the last 7 days? [68515-6]',
								labelAlign: 'top',
								name: 'exercise_past_days_amount',
								value: null,
								minValue: 0,
								maxValue: 20
							},
							{
								xtype: 'slider',
								anchor: '100%',
								fieldLabel: 'On those days that you engage in moderate to strenuous exercise, how many minutes, on average, do you exercise? [68516-4]',
								labelAlign: 'top',
								name: 'exercise_past_days_minutes',
								value: null,
								minValue: 0,
								maxValue: 240
							}
						]
					},
					{
						xtype: 'fieldset',
						title: 'Alcohol Use - [AUDIT-C] [72109-2]',
						items: [
							{
								xtype: 'radiogroup',
								fieldLabel: 'How often do you have a drink containing alcohol? [68518-0]',
								columns: 1,
								vertical: true,
								labelAlign: 'top',
								items: [
									{ boxLabel: 'Never - [LA6270-8]',                   name: 'drink_often', inputValue: 0 },
									{ boxLabel: 'Monthly or less - [LA18926-8]',        name: 'drink_often', inputValue: 1 },
									{ boxLabel: '2-4 times a month - [LA18927-6]',      name: 'drink_often', inputValue: 2 },
									{ boxLabel: '2-3 times a week - [LA18928-4]',       name: 'drink_often', inputValue: 3 },
									{ boxLabel: '4 or more times a week - [LA18929-2]', name: 'drink_often', inputValue: 4 }
								]

							},
							{
								xtype: 'radiogroup',
								fieldLabel: 'How many standard drinks containing alcohol do you have on a typical day? [68519-8]',
								columns: 1,
								vertical: true,
								labelAlign: 'top',
								items: [
									{ boxLabel: '1 or 2 - [LA15694-5]',       name: 'drink_per_day', inputValue: 0 },
									{ boxLabel: '3 or 4 - [LA15695-2]',       name: 'drink_per_day', inputValue: 1 },
									{ boxLabel: '5 or 6 - [LA18930-0]',       name: 'drink_per_day', inputValue: 2 },
									{ boxLabel: '7 or 9 - [LA18931-8]',       name: 'drink_per_day', inputValue: 3 },
									{ boxLabel: '10 or more - [LA18932-6]',   name: 'drink_per_day', inputValue: 4 }
								]
							},
							{
								xtype: 'radiogroup',
								fieldLabel: 'How often do you have 6 or more drinks on 1 occasion? [68520-6]',
								columns: 1,
								vertical: true,
								labelAlign: 'top',
								items: [
									{ boxLabel: 'Never - [LA6270-8]',                   name: 'drink_more_than_6', inputValue: 0 },
									{ boxLabel: 'Less than monthly - [LA18933-4]',      name: 'drink_more_than_6', inputValue: 1 },
									{ boxLabel: 'Monthly - [LA18876-5]',                name: 'drink_more_than_6', inputValue: 2 },
									{ boxLabel: 'Weekly - [LA18891-4]',                 name: 'drink_more_than_6', inputValue: 3 },
									{ boxLabel: 'Daily or almost daily - [LA18934-2]',  name: 'drink_more_than_6', inputValue: 4 }
								]
							},
							{
								xtype: 'displayfield',
								fieldLabel: 'Total score [AUDIT-C] [75626-2]',
								labelAlign: 'top',
								name: 'patient_drink_score'
							}
						]
					},
					{
						xtype: 'fieldset',
						title: 'Social connection and isolation - [NHANES] [76506-5]',
						items: [
							{
								xtype: 'radiogroup',
								fieldLabel: 'Are you now married, widowed, divorced, separated, never married or living with a partner? [63503-7]',
								columns: 1,
								vertical: true,
								labelAlign: 'top',
								items: [
									{ boxLabel: 'Married - [LA48-4]',               name: 'marital_status', inputValue: 1 },
									{ boxLabel: 'Widowed - [LA49-2]',               name: 'marital_status', inputValue: 2 },
									{ boxLabel: 'Divorced - [LA51-8]',              name: 'marital_status', inputValue: 3 },
									{ boxLabel: 'Separated - [LA4288-2]',           name: 'marital_status', inputValue: 4 },
									{ boxLabel: 'Never married - [LA47-6]',         name: 'marital_status', inputValue: 5 },
									{ boxLabel: 'Living with partner - [LA15605-1]',name: 'marital_status', inputValue: 6 },
									{ boxLabel: 'Refused - [LA4389-8]',             name: 'marital_status', inputValue: 77 },
									{ boxLabel: 'Don\'t know - [LA12688-0]',        name: 'marital_status', inputValue: 99 }
								]
							},
							{
								xtype: 'slider',
								anchor: '100%',
								fieldLabel: 'In a typical week, how many times do you talk on the telephone with family, friends, or neighbors? [76508-1]',
								labelAlign: 'top',
								name: 'phone_family',
								value: null,
								minValue: 0,
								maxValue: 100
							},
							{
								xtype: 'slider',
								anchor: '100%',
								fieldLabel: 'How often do you get together with friends or relatives? [76509-9]',
								labelAlign: 'top',
								name: 'together_friends',
								value: null,
								minValue: 0,
								maxValue: 100
							},
							{
								xtype: 'slider',
								anchor: '100%',
								fieldLabel: 'How often do you attend church or religious services? [76510-7]',
								labelAlign: 'top',
								name: 'religious_services',
								value: null,
								minValue: 0,
								maxValue: 100
							},
							{
								xtype: 'radiogroup',
								fieldLabel: 'Do you belong to any clubs or organizations such as church groups unions, fraternal or athletic groups, or school groups? [76511-5]',
								columns: 1,
								vertical: true,
								labelAlign: 'top',
								items: [
									{ boxLabel: 'Yes - [LA33-6]',  name: 'belong_organizations', inputValue: 1 },
									{ boxLabel: 'No - [LA32-8]',   name: 'belong_organizations', inputValue: 0 }
								]
							},
							{
								xtype: 'displayfield',
								fieldLabel: 'Social isolation score [NHANES] [76512-3]',
								labelAlign: 'top',
								name: 'patient_isolation_score'
							}
						]
					},
					{
						xtype: 'fieldset',
						title: 'Humiliation, Afraid, Rape, and Kick [HARK] [76499-3]',
						items: [
							{
								xtype: 'radiogroup',
								fieldLabel: 'Within the last year, have you been humiliated or emotionally abused in other ways by your partner or ex-partner? [76500-8]',
								columns: 1,
								vertical: true,
								labelAlign: 'top',
								items: [
									{ boxLabel: 'Yes - [LA33-6]',  name: 'abused_by_partner', inputValue: 1 },
									{ boxLabel: 'No - [LA32-8]',   name: 'abused_by_partner', inputValue: 0 }
								]
							},
							{
								xtype: 'radiogroup',
								fieldLabel: 'Within the last year, have you been afraid of your partner or ex-partner? [76501-6]',
								columns: 1,
								vertical: true,
								labelAlign: 'top',
								items: [
									{ boxLabel: 'Yes - [LA33-6]',  name: 'afraid_of_partner', inputValue: 1 },
									{ boxLabel: 'No - [LA32-8]',   name: 'afraid_of_partner', inputValue: 0 }
								]
							},
							{
								xtype: 'radiogroup',
								fieldLabel: 'Within the last year, have you been raped or forced to have any kind of sexual activity by your partner or ex-partner? [76502-4]',
								columns: 1,
								vertical: true,
								labelAlign: 'top',
								items: [
									{ boxLabel: 'Yes - [LA33-6]',  name: 'raped_by_partner', inputValue: 1 },
									{ boxLabel: 'No - [LA32-8]',   name: 'raped_by_partner', inputValue: 0 }
								]
							},
							{
								xtype: 'radiogroup',
								fieldLabel: 'Within the last year, have you been kicked, hit, slapped, or otherwise physically hurt by your partner or ex-partner? [76503-2]',
								columns: 1,
								vertical: true,
								labelAlign: 'top',
								items: [
									{ boxLabel: 'Yes - [LA33-6]',  name: 'physically_hurt_by_partner', inputValue: 1 },
									{ boxLabel: 'No - [LA32-8]',   name: 'physically_hurt_by_partner', inputValue: 0 }
								]
							},
							{
								xtype: 'displayfield',
								fieldLabel: 'Total score [HARK] [76504-0]',
								labelAlign: 'top',
								name: 'patient_humiliation_score'
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
			itemId: 'SocialPsychologicalBehavioralPanelCancelBtn'
		},
		{
			text: _('save'),
			itemId: 'SocialPsychologicalBehavioralPanelSaveBtn'
		}
	]

});
