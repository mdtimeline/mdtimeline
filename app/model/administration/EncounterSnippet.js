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

Ext.define('App.model.administration.EncounterSnippet', {
	extend: 'Ext.data.Model',
	table: {
		name: 'soap_snippets'
	},
	fields: [
		{
			name: 'id',
			type: 'string'
		},
		{
			name: 'uid',
			type: 'int',
			index: true
		},
		{
			name: 'specialty_id',
			type: 'int',
			index: true
		},
		{
			name: 'index',
			type: 'int'
		},
		{
			name: 'description',
			type: 'string',
			len: 80
		},
		{
			name: 'category',
			type: 'string',
			len: 50
		},
		{
			name: 'subjective',
			type: 'string',
			dataType: 'text'
		},
		{
			name: 'objective',
			type: 'string',
			dataType: 'text'
		},
		{
			name: 'assessment',
			type: 'string',
			dataType: 'text'
		},
		{
			name: 'instructions',
			type: 'string',
			dataType: 'text'
		},
		{
			name: 'diagnoses',
			type: 'string',
			dataType: 'text'
		}
	],
	proxy: {
		type: 'direct',
		api: {
			read: 'Snippets.getSoapSnippets',
			create: 'Snippets.addSoapSnippets',
			update: 'Snippets.updateSoapSnippets',
			destroy: 'Snippets.deleteSoapSnippets'
		}
	}
});