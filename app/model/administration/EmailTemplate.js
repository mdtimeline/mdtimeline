/**
 * Created with IntelliJ IDEA.
 * User: ernesto
 * Date: 11/4/13
 * Time: 6:28 PM
 * To change this template use File | Settings | File Templates.
 */
Ext.define('App.model.administration.EmailTemplate', {
	extend: 'Ext.data.Model',
	table: {
		name: 'email_templates'
	},
	fields: [
		{
			name: 'id',
			type: 'int'
		},
		{
			name: 'facility_id',
			type: 'int',
			index: true
		},
		{
			name: 'template_type',
			type: 'string',
			len: 40,
			index: true
		},
		{
			name: 'language',
			type: 'string',
			len: 5
		},
		{
			name: 'subject',
			type: 'string',
			len: 250
		},
		{
			name: 'from_email',
			type: 'string',
			len: 250
		},
		{
			name: 'from_name',
			type: 'string',
			len: 250
		},
		{
			name: 'body',
			type: 'string',
			dataType: 'mediumtext'
		},
		{
			name: 'active',
			type: 'bool',
			index: true
		}
	]
});
