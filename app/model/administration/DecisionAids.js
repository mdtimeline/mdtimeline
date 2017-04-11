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

Ext.define('App.model.administration.DecisionAids', {
	extend: 'Ext.data.Model',
	table: {
		name: 'decision_aids_instructions'
	},
	fields: [
		{
			name: 'id',
			type: 'int'
		},
		{
			name: 'trigger_code',
			type: 'string',
			len: 40,
			index: true
		},
		{
			name: 'instruction_code',
			type: 'string',
			len: 40
		},
		{
			name: 'instruction_code_type',
			type: 'string',
			len: 40
		},
		{
			name: 'instruction_code_description',
			type: 'string',
			len: 600
		},
		{
			name: 'active',
			type: 'bool',
			index: true
		}
	],
	proxy: {
		type: 'direct',
		api: {
			read: 'DecisionAids.getDecisionAids',
			create: 'DecisionAids.addDecisionAid',
			update: 'DecisionAids.updateDecisionAid',
			destroy: 'DecisionAids.destroyDecisionAid'
		}
	}
});