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

Ext.define('App.model.patient.ProgressNotesHistory', {
	extend: 'Ext.data.Model',
	fields: [
		{
			name: 'service_date',
			type: 'date'
		},
		{
			name: 'brief_description',
			type: 'string'
		},
		{
			name: 'subjective',
			type: 'string'
		},
		{
			name: 'objective',
			type: 'string'
		},
		{
			name: 'assessment',
			type: 'string'
		},
		{
			name: 'plan',
			type: 'string'
		},
		{
			name: 'addenda',
			type: 'string'
		},
		{
			name: 'instructions',
			type: 'string'
		},
		{
			name: 'specialty_id',
			type: 'int'
		},
		{
			name: 'provider_uid',
			type: 'int'
		},
		{
			name: 'provider_title',
			type: 'string'
		},
		{
			name: 'provider_lname',
			type: 'string'
		},
		{
			name: 'provider_fname',
			type: 'string'
		},
		{
			name: 'provider_mname',
			type: 'string'
		},
		{
			name: 'provider_npi',
			type: 'string'
		},
		{
			name: 'provider',
			type: 'string',
			convert: function(v, record){
				return Ext.String.format(
					'{0}, {1} {2} - {3}',
					record.get('provider_lname'),
					record.get('provider_fname'),
					record.get('provider_mname'),
					record.get('provider_npi')
				)
			}
		},
		{
			name: 'progress',
			type: 'string',
			convert: function(v, record){
				var my_encounter_style = record.get('provider_uid') === app.user.id ? 'background-color:#ffff003d;' : '',
				str = '<div style="width:90%; display: inline-block; font-size: 125%; padding: 5px 0 5px 5px; color: black; ' + my_encounter_style +'">';
				str += '<b>' + _('provider') + ':</b> ' + Ext.String.htmlDecode(record.get('provider')) + '<br>';
				str += '<b>' + _('service_date') + ':</b> ' + Ext.Date.format(record.get('service_date'), 'F j, Y, g:i a (l)') + '<br>';
				str += '--------------------- <br>';
				str += '<b>' + _('chief_complaint') + ':</b> ' + Ext.String.htmlDecode(record.get('brief_description')) + '<br>';
				str += '--------------------- <br>';
				str += '<b>' + _('subjective') + ':</b> ' + Ext.String.htmlDecode(record.get('subjective')) + '<br>';
				str += '<b>' + _('objective') + ':</b> ' + Ext.String.htmlDecode(record.get('objective')) + '<br>';
				str += '<b>' + _('assessment') + ':</b> ' + Ext.String.htmlDecode(record.get('assessment')) + '<br>';
				str += '<b>' + _('plan') + ':</b> ' +  Ext.String.htmlDecode(record.get('plan')) + '<br>';

				if(record.get('addenda') !== 'NONE'){
					str += '<b>' + _('addenda') + ':</b> ' +  Ext.String.htmlDecode(record.get('addenda')) + '<br>';
				}
				// str += '<b>' + _('instructions') + ':</b> ' + Ext.String.htmlDecode(record.data.instructions) + '<br>';
				return str + '</div>';
			}
		}
	],
	proxy: {
		type: 'direct',
		api: {
			read: 'Encounter.getSoapHistory'
		}
	}
});