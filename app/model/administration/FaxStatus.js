/**
 * mdTimeLine PHR Messenger Moudle
 * Copyright (C) 2016 TRA, Inc.
 */

Ext.define('App.administration.model.FaxStatus', {
	extend: 'Ext.data.Model',
	table: {
		name: 'fax_status'
	},
	fields: [
		{
			type: 'int',
			name: 'id'
		},
		{
			type: 'string',
			name: 'sid',
			len: 80
		},
		{
			type: 'string',
			name: 'fax_status',
			len: 80
		},
		{
			type: 'date',
			name: 'status_timestamp',
			dateFormat: 'Y-m-d H:i:s'
		},
		{
			type: 'date',
			name: 'sent_timestamp',
			dateFormat: 'Y-m-d H:i:s'
		}
	]
});
