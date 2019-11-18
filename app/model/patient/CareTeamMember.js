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

Ext.define('App.model.patient.CareTeamMember', {
	extend: 'Ext.data.Model',
	table: {
		name: 'patient_care_team_members'
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
			name: 'npi',
			type: 'string',
			len: 10,
			index: true
		},
		{
			name: 'is_primary',
			type: 'bool',
			index: true
		},
		{
			name: 'fname',
			type: 'string',
			store: false
		},
		{
			name: 'mname',
			type: 'string',
			store: false
		},
		{
			name: 'lname',
			type: 'string',
			store: false
		},
		{
			name: 'create_uid',
			type: 'int'
		},
		{
			name: 'create_date',
			type: 'date',
			dateFormat: 'Y-m-d H:i:s'
		}
	],
	proxy: {
		type: 'direct',
		api: {
			read: 'CareTeamMember.getCareTeamMembers',
			create: 'CareTeamMember.addCareTeamMember',
			update: 'CareTeamMember.updateCareTeamMember',
			destroy: 'CareTeamMember.destroyCareTeamMember'
		}
	}
});

