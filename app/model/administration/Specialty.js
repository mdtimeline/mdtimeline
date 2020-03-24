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

Ext.define('App.model.administration.Specialty', {
	extend: 'Ext.data.Model',
	table: {
		name: 'specialties',
		comment: 'Providers Specialties'
	},
	fields: [
		{
			name: 'id',
			type: 'int'
		},
		{
			name: 'code',
			type: 'string',
			len: 5,
			index: true
		},
		{
			name: 'title',
			type: 'string',
			len: 100
		},
		{
			name: 'taxonomy',
			type: 'string',
			len: 15
		},
		{
			name: 'modality',
			type: 'string',
			len: 50
		},
        {
            name: 'medical_education',
            type: 'string'
        },
        {
            name: 'modality',
            type: 'string',
            len: 50
        },
		{
			name: 'isFda',
			type: 'bool'
		},

        {
            name: 'external_id',
            type: 'string',
            len: 25
        },
        {
            name: 'global_id',
            type: 'string',
            len: 50
        },

		{
			name: 'active',
			type: 'bool'
		},
		{
			name: 'create_uid',
			type: 'int'
		},
		{
			name: 'update_uid',
			type: 'int'
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
		},
        {
            name: 'text_details',
            type: 'string',
            convert: function(v, record){
                return record.data.id + ': ' + record.data.title;
            },
            store: false
        },
        {
            name: 'combo_text',
            type: 'string',
            convert: function(v, record){
                return record.data.id + ': ' + record.data.title + ' ' + (record.data.active ? ('(' + _('not_active') + ')') : '');
            },
            store: false
        }
	],
	proxy: {
		type: 'direct',
		api: {
			read: 'Specialties.getSpecialties',
			create: 'Specialties.addSpecialty',
			update: 'Specialties.updateSpecialty'
		},
		reader: {
			root: 'data'
		}
	}
});
