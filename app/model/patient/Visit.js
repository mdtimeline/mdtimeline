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

Ext.define('App.model.patient.Visit', {
	extend: 'Ext.data.Model',
	table: {
		name: 'patient_visits'
	},
	fields: [
		{
			name: 'id',
			type: 'int'
		},
		{
			name: 'pid',
			type: 'int',
			index: true
		},
		{
			name: 'visit_number',
			type: 'string',
			len: 45,
			index: true
		},
		{
			name: 'facility_id',
			type: 'int',
			index: true
		},
		{
			name: 'attending_id',
			type: 'int',
			index: true
		},
		{
			name: 'admitting_id',
			type: 'int',
			index: true
		},
		{
			name: 'referring_id',
			type: 'int',
			index: true
		},
		{
			name: 'consulting1_id',
			type: 'int',
			index: true
		},
		{
			name: 'consulting2_id',
			type: 'int',
			index: true
		},
		{
			name: 'consulting3_id',
			type: 'int',
			index: true
		},
		{
			name: 'patient_class',
			type: 'string',
			len: 45
		},
		{
			name: 'admission_type',
			type: 'string',
			len: 45
		},
		{
			name: 'admit_source',
			type: 'string',
			len: 45
		},
		{
			name: 'hospital_service',
			type: 'string',
			len: 45
		},
		{
			name: 'assigned_location',
			type: 'string',
			len: 45
		},
		{
			name: 'assigned_zone',
			type: 'string',
			len: 45
		},
		{
			name: 'prior_location',
			type: 'string',
			len: 45
		},
		{
			name: 'prior_zone',
			type: 'string',
			len: 45
		},
		{
			name: 'admit_date',
			type: 'date',
			dateFormat: 'Y-m-d H:i:s'
		},
		{
			name: 'discharge_date',
			type: 'date',
			dateFormat: 'Y-m-d H:i:s'
		},
		{
			name: 'discharge_disposition',
			type: 'string',
			len: 45
		}
	],
	proxy: {
		type: 'direct',
		api: {
			read: 'Visits.getVisits',
			create: 'Visits.addVisit',
			update: 'Visits.updateVisit'
		}
	}
});