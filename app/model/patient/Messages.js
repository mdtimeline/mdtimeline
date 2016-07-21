/**
 GaiaEHR (Electronic Health Records)
 Copyright (C) 2013 Certun, LLC.

 This program is free software: you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation, either version 3 of the License, or
 (at your option) any later version.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License
 along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

Ext.define('App.model.patient.Messages', {
	extend: 'Ext.data.Model',
	fields: [
		{
			name: 'id',
			type: 'int'
		},
		{
			name: 'date',
			type: 'string',
			comment: 'Date of message'
		},
		{
			name: 'body',
			type: 'string',
			dataType: 'text',
			comment: 'Message'
		},
		{
			name: 'pid',
			type: 'int',
			comment: 'Patient ID'
		},
		{
			name: 'patient_name',
			type: 'string',
			comment: 'Patient Name'
		},
		{
			name: 'from_user',
			type: 'string',
			comment: 'Message is from user'
		},
		{
			name: 'to_user',
			type: 'string',
			comment: 'Message to user'
		},
		{
			name: 'subject',
			type: 'string',
			comment: 'Subject of the message'
		},
		{
			name: 'facility_id',
			type: 'string',
			comment: 'Facility'
		},
		{
			name: 'authorized',
			type: 'string',
			comment: 'Authorized?'
		},
		{
			name: 'to_id',
			type: 'int',
			comment: 'To'
		},
		{
			name: 'from_id',
			type: 'int',
			comment: 'From'
		},
		{
			name: 'message_status',
			type: 'string',
			comment: 'Message Status'
		},
		{
			name: 'note_type',
			type: 'string',
			comment: 'Message Type'
		},
		{
			name: 'to_deleted',
			type: 'bool',
			comment: 'Deleted to the user'
		},
		{
			name: 'from_deleted',
			type: 'bool',
			comment: 'Deleted from the source'
		}
	],
	proxy: {
		type: 'direct',
		api: {
			read: 'Messages.getMessages',
			create: 'Messages.sendNewMessage',
			update: 'Messages.replyMessage',
			destroy: 'Messages.deleteMessage'
		},
		reader: {
			type: 'json',
			root: 'data',
			totalProperty: 'totals'
		}
	}
});
