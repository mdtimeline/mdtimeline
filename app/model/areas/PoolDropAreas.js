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

Ext.define('App.model.areas.PoolDropAreas', {
	extend: 'Ext.data.Model',
	fields: [
		{
			name: 'id',
			type: 'int'
		},
		{
			name: 'fname',
			type: 'string'
		},
		{
			name: 'mname',
			type: 'string'
		},
		{
			name: 'lname',
			type: 'string'
		},
		{
			name: 'pid',
			type: 'int'
		},
		{
			name: 'pubpid',
			type: 'string'
		},
		{
			name: 'pic',
			type: 'string'
		},
		{
			name: 'appointment_id',
			type: 'int'
		},
		{
			name: 'zone',
			type: 'string'
		},
		{
			name: 'appointment_time',
			type: 'date',
			dateFormat: 'Y-m-d H:i:s'
		},
		{
			name: 'time_in',
			type: 'date',
			dateFormat: 'Y-m-d H:i:s'
		},
		{
			name: 'minutes',
			type: 'string',
			convert: function (v,r) {
				if(!r.get('time_in')) return 0;
				return (app.getDate().getTime() - r.get('time_in').getTime()) / 1000 / 60;
			}
		},
		{
			name: 'timer',
			type: 'string',
			convert: function (v,r) {
				var mitutes = r.get('minutes'),
					hours = (mitutes / 60),
					rhours = Math.floor(hours),
					minutes = (hours - rhours) * 60,
					rminutes = Math.round(minutes);
				return Ext.String.format('{0}:{1}', Ext.String.leftPad(rhours,2,'0'),  Ext.String.leftPad(rminutes,2,'0'));
			}
		},
		{
			name: 'alert_color',
			type: 'string',
			convert: function (v,r) {
				var min = r.get('minutes');

				if(min > 30){
					return 'lightcoral';
				}
				if(min > 20){
					return 'lightpink';
				}
				if(min > 10){
					return 'lightblue';
				}
				if(min > 5){
					return 'lightyellow';
				}

				return '';
			}
		}
	]
});