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

Ext.define('App.model.patient.SocialPsychologicalBehavioral', {
	extend: 'Ext.data.Model',
	table: {
		name: 'patient_social_psycho_behavioral'
	},
	fields: [
		{
			name: 'id',
			type: 'int'
		},
		{
			name: 'eid',
			type: 'int',
			index: true
		},
		{
			name: 'pid',
			type: 'int',
			index: true
		},
		{
			name: 'pay_basics',
			type: 'int'
		},
		{
			name: 'edu_highest_grade',
			type: 'int'
		},
		{
			name: 'stress_level',
			type: 'int'
		},
		{
			name: 'stress_sleep',
			type: 'int'
		},
		{
			name: 'interest_pleasure',
			type: 'int'
		},
		{
			name: 'feeling_down_depressed',
			type: 'int'
		},
		{
			name: 'patient_health_score',
			type: 'int',
			comment: 'interest_pleasure + feeling_down_depressed'
		},
		{
			name: 'exercise_past_days_amount',
			type: 'int'
		},
		{
			name: 'exercise_past_days_minutes',
			type: 'int'
		},
		{
			name: 'drink_often',
			type: 'int'
		},
		{
			name: 'drink_per_day',
			type: 'int'
		},
		{
			name: 'drink_more_than_6',
			type: 'int'
		},
		{
			name: 'patient_drink_score',
			type: 'int',
			comment: 'drink_often + drink_per_day + patient_health_score'
		},
		{
			name: 'marital_status',
			type: 'int'
		},
		{
			name: 'phone_family',
			type: 'int'
		},
		{
			name: 'together_friends',
			type: 'int'
		},
		{
			name: 'religious_services',
			type: 'int'
		},
		{
			name: 'belong_organizations',
			type: 'int'
		},
		{
			name: 'patient_isolation_score',
			type: 'int',
			comment: 'marital_status + phone_family + together_friends + religious_services + belong_organizations'
		},
		{
			name: 'abused_by_partner',
			type: 'int'
		},
		{
			name: 'afraid_of_partner',
			type: 'int'
		},
		{
			name: 'raped_by_partner',
			type: 'int'
		},
		{
			name: 'physically_hurt_by_partner',
			type: 'int'
		},
		{
			name: 'patient_humiliation_score',
			type: 'int',
			comment: 'abused_by_partner + afraid_of_partner + raped_by_partner + physically_hurt_by_partner + patient_humiliation_score'
		},
		{
			name: 'create_uid',
			type: 'int',
			comment: 'user ID who created the record'
		},
		{
			name: 'update_uid',
			type: 'int',
			comment: 'user ID who updated the record'
		},
		{
			name: 'create_date',
			type: 'date',
			dateFormat: 'Y-m-d H:i:s'
		},
		{
			name: 'update_date',
			type: 'date',
			dateFormat: 'Y-m-d H:i:s'
		}
	],
	proxy: {
		type: 'direct',
		api: {
			read: 'SocialPsychologicalBehavioral.getSocialPsychologicalBehaviors',
			create: 'SocialPsychologicalBehavioral.addSocialPsychologicalBehavior',
			update: 'SocialPsychologicalBehavioral.updateSocialPsychologicalBehavior'
		}
	}
});