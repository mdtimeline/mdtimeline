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

Ext.define('App.model.administration.EducationResource', {
	extend: 'Ext.data.Model',
	table: {
		name: 'education_resources'
	},
	fields: [
		{
			name: 'id',
			type: 'int'
		},
		{
			name: 'code',
			type: 'string',
			len: 15
		},
		{
			name: 'code_text',
			type: 'string',
			len: 300
		},
		{
			name: 'code_type',
			type: 'string',
			len: 10
		},
		{
			name: 'title',
			type: 'string',
			len: 180
		},
		{
			name: 'publication_date',
			type: 'date',
			dateFormat: 'Y-m-d H:i:s'
		},
		{
			name: 'publication_date_formatted',
			type: 'string',
			store: false,
			convert: function(val, rec){
				return Ext.Date.format(rec.get('publication_date'), g('date_display_format'));
			}
		},
		{
			name: 'version',
			type: 'string',
			len: 10
		},
		{
			name: 'path',
			type: 'string',
			len: 600
		},
		{
			name: 'activve',
			type: 'bool'
		}
	],
	proxy: {
		type: 'direct',
		api: {
			read: 'EducationResources.getEducationResources'
		}
	}
});