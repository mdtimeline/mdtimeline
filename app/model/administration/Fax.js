/**
 * mdTimeLine PHR Messenger Moudle
 * Copyright (C) 2016 TRA, Inc.
 */

Ext.define('App.administration.model.Fax', {
	extend: 'Ext.data.Model',
	table: {
		name: 'fax'
	},
	fields: [
		{
			type: 'int',
			name: 'id'
		},
		{
			type: 'string',
			name: 'sid',
			len: 80,
			index: true
		},
		{
			type: 'int',
			name: 'pid'
		},
		{
			type: 'string',
			name: 'reference_type',
			len: 45,
			index: true
		},
		{
			type: 'int',
			name: 'reference_id',
			index: true
		},
		{
			type: 'int',
			name: 'sender_uid'
		},
		{
			type: 'string',
			name: 'to_phone_number',
			len: 80
		},
		{
			type: 'string',
			name: 'fax_pdf_path',
			dataType: 'text'
		},
		{
			type: 'date',
			name: 'create_date',
			dateFormat: 'Y-m-d H:i:s'
		}
	]
});
