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

Ext.define('App.model.patient.MiniMentalStateExam', {
	extend: 'Ext.data.Model',
	table: {
		name: 'patient_mini_mental_exams'
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
			name: 'orientation_time_score',
			type: 'int'
		},
		{
			name: 'orientation_place_score',
			type: 'int'
		},
		{
			name: 'registration_score',
			type: 'int'
		},
		{
			name: 'attention_calculation_score',
			type: 'int'
		},
		{
			name: 'recall_score',
			type: 'int'
		},
		{
			name: 'language_score',
			type: 'int'
		},
		{
			name: 'repetition_score',
			type: 'int'
		},
		{
			name: 'complex_commands_score',
			type: 'int'
		},
		{
			name: 'total_score',
			type: 'int'
		},
		{
			name: 'orientation_time_notes',
			type: 'string',
			dataType: 'text'
		},
		{
			name: 'orientation_place_notes',
			type: 'string',
			dataType: 'text'
		},
		{
			name: 'registration_notes',
			type: 'string',
			dataType: 'text'
		},
		{
			name: 'attention_calculation_notes',
			type: 'string',
			dataType: 'text'
		},
		{
			name: 'recall_notes',
			type: 'string',
			dataType: 'text'
		},
		{
			name: 'language_notes',
			type: 'string',
			dataType: 'text'
		},
		{
			name: 'repetition_notes',
			type: 'string',
			dataType: 'text'
		},
		{
			name: 'complex_commands_notes',
			type: 'string',
			dataType: 'text'
		},
		{
			name: 'assess_lvl',
			type: 'string',
			len: 10
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
			read: 'MiniMentalStateExam.getMiniMentalStateExams',
			create: 'MiniMentalStateExam.addMiniMentalStateExam',
			update: 'MiniMentalStateExam.updateMiniMentalStateExam'
		}
	}
});