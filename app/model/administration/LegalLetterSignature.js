/**
 * Created with IntelliJ IDEA.
 * User: ernesto
 * Date: 11/4/13
 * Time: 6:28 PM
 * To change this template use File | Settings | File Templates.
 */
Ext.define('App.model.administration.LegalLetterSignature', {
	extend: 'Ext.data.Model',
	table: {
		name: 'legal_letters_signatures'
	},
	fields: [
		{
			name: 'id',
			type: 'int'
		},
		{
			name: 'letter_id',
			type: 'int',
			index: true
		},
		{
			name: 'pid',
			type: 'int',
			index: true
		},
		{
			name: 'signature',
			type: 'string',
			dataType: 'TEXT'
		},
		{
			name: 'signature_ip',
			type: 'string',
			len: 80
		},
		{
			name: 'signature_date',
			type: 'date'
		},
		{
			name: 'signature_hash',
			type: 'string',
			len: 128
		},
		{
			name: 'letter_title',
			type: 'string',
			store: false
		},
		{
			name: 'letter_content',
			type: 'string',
			store: false
		},
		{
			name: 'letter_version',
			type: 'string',
			store: false
		},
		{
			name: 'signer_fname',
			type: 'string',
			store: false
		},
		{
			name: 'signer_mname',
			type: 'string',
			store: false
		},
		{
			name: 'signer_lname',
			type: 'string',
			store: false
		}
	],
	proxy: {
		type: 'direct',
		api: {
			read: 'LegalLetters.getLegalLetterSignatures',
			create: 'LegalLetters.addLegalLetterSignature'
		},
		reader: {
			root: 'data'
		},
		remoteGroup: false
	}
});
