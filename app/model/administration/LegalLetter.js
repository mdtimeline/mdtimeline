/**
 * Created with IntelliJ IDEA.
 * User: ernesto
 * Date: 11/4/13
 * Time: 6:28 PM
 * To change this template use File | Settings | File Templates.
 */
Ext.define('App.model.administration.LegalLetter', {
	extend: 'Ext.data.Model',
	table: {
		name: 'legal_letters'
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
			name: 'workflow',
			type: 'string',
			len: 45,
			index: true
		},
		{
			name: 'title',
			type: 'string',
			len: 120
		},
		{
			name: 'document_code',
			type: 'string',
			len: 20
		},
		{
			name: 'content',
			type: 'string',
			dataType: 'TEXT'
		},
		{
			name: 'version',
			type: 'string',
			len: 20
		},
		{
			name: 'days_valid_for',
			type: 'int'
		},
		{
			name: 'active',
			type: 'bool',
			index: true
		},
		{
			name: 'create_uid',
			type: 'int'
		},
		{
			name: 'create_date',
			type: 'date'
		},
		{
			name: 'update_uid',
			type: 'int'
		},
		{
			name: 'update_date',
			type: 'date'
		},
		{
			name: 'facility',
			type: 'string',
			store: false
		}
	],
	proxy: {
		type: 'direct',
		api: {
			read: 'LegalLetters.getLegalLetters',
			create: 'LegalLetters.addLegalLetter',
			update: 'LegalLetters.updateLegalLetter'
		},
		reader: {
			root: 'data'
		},
		remoteGroup: false
	}
});
