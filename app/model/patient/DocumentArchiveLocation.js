/**
 * Created with IntelliJ IDEA.
 * User: ernesto
 * Date: 11/4/13
 * Time: 6:28 PM
 * To change this template use File | Settings | File Templates.
 */
Ext.define('App.model.patient.DocumentArchiveLocation', {
	extend: 'Ext.data.Model',
	table: {
		name: 'documents_archive_locations'
	},
	fields: [
		{
			name: 'id',
			type: 'int'
		},
		{
			name: 'reference_number',
			type: 'string',
			index: true,
			len: 60
		},
		{
			name: 'description',
			type: 'string',
			len: 120
		},
		{
			name: 'cmb_text',
			type: 'string',
			convert: function (v,rec) {
				return Ext.String.format('{0} - {1}', rec.get('reference_number'), rec.get('description'));
			},
			store: false
		},
		{
			name: 'notes',
			type: 'string',
			len: 300
		},
		{
			name: 'create_uid',
			type: 'int'
		},
		{
			name: 'update_uid',
			type: 'int'
		},
		{
			name: 'create_date',
			type: 'date',
			dateFormat: 'Y-m-d H:i:s'
		},
		{
			name: 'update_date',
			type: 'date',
			dateFormat: 'Y-m-d H:i:s'
		},
		{
			name: 'active',
			type: 'bool'
		}
	],
	proxy: {
		type: 'direct',
		api: {
			read: 'DocumentArchiveLocation.getDocumentLocations',
			create: 'DocumentArchiveLocation.addDocumentLocation',
			update: 'DocumentArchiveLocation.updateDocumentLocation',
			destroy: 'DocumentArchiveLocation.destroyDocumentLocation'
		},
		reader: {
			root: 'data'
		},
		writer: {
			writeAllFields: true
		}
	}
});