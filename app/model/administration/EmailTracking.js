/**
 * Created with IntelliJ IDEA.
 * User: ernesto
 * Date: 11/4/13
 * Time: 6:28 PM
 * To change this template use File | Settings | File Templates.
 */
Ext.define('App.model.administration.EmailTracking', {
	extend: 'Ext.data.Model',
	table: {
		name: 'email_tracking'
	},
	fields: [
		{
			name: 'source_tracking_id',
			type: 'string',
			len: 80
		},
		{
			name: 'pid',
			type: 'int',
			index: true
		},
		{
			name: 'eid',
			type: 'int',
			index: true
		},
		{
			name: 'reference_type',
			type: 'string',
			len: 45,
			index: true
		},
		{
			name: 'reference_id',
			type: 'int',
			index: true
		},
		{
			name: 'send_time',
			type: 'date',
			dateFormat: 'Y-m-d H:i:s'
		},
		{
			name: 'delivery_status',
			type: 'string',
			len: 45
		},
		{
			name: 'delivery_time',
			type: 'date',
			dateFormat: 'Y-m-d H:i:s'
		},
		{
			name: 'opened_status',
			type: 'string',
			len: 45
		},
		{
			name: 'opened_time',
			type: 'date',
			dateFormat: 'Y-m-d H:i:s'
		}
	]
});
