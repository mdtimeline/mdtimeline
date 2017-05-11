/**
 * mdTImeLine EHR (Electronic Health Records)
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

Ext.define('App.model.patient.EducationResource', {
	extend: 'Ext.data.Model',
	requires: [

	],
	table: {
		name: 'patient_education_resources'
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
			name: 'uid',
			type: 'int'
		},
		{
			name: 'title',
			type: 'string',
			len: 300
		},
		{
			name: 'url',
			type: 'string',
			dataType: 'text'
		},
		{
			name: 'snippet',
			type: 'string',
			dataType: 'text'
		},
		{
			name: 'organization_name',
			type: 'string',
			dataType: 'text'
		}
	],
	proxy: {
		type: 'direct',
		api: {
			read: 'EducationResources.getPatientEducationResources',
			create: 'EducationResources.addPatientEducationResource',
			update: 'EducationResources.updatePatientEducationResource',
			destroy: 'EducationResources.destroyPatientEducationResource'
		},
		remoteGroup: false
	}
});
