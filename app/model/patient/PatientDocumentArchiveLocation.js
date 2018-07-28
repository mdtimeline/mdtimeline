/**
 * Created with IntelliJ IDEA.
 * User: ernesto
 * Date: 11/4/13
 * Time: 6:28 PM
 * To change this template use File | Settings | File Templates.
 */
Ext.define('App.model.patient.PatientDocumentArchiveLocation', {
	extend: 'Ext.data.Model',
	table: {
		name: 'patient_documents_archive_locations'
	},
	fields: [
		{
			name: 'id',
			type: 'int'
		},
		{
			name: 'location_id',
			type: 'int',
			index: true
		},
		{
			name: 'document_id',
			type: 'int',
			index: true
		},
		{
			name: 'notes',
			type: 'string',
			len: 300
		},
		{
			name: 'create_uid',
			type: 'int',
			index: true
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
			name: 'scanned_date',
			type: 'date',
			dateFormat: 'Y-m-d H:i:s',
			store: false
		},
		{
			name: 'facility',
			type: 'string',
			store: false
		},
		{
			name: 'archive_reference_number',
			type: 'string',
			store: false
		},
		{
			name: 'archive_description',
			type: 'string',
			store: false
		},
		{
			name: 'department',
			type: 'string',
			store: false
		},
		{
			name: 'document_info',
			type: 'string',
			store: false
		},
		{
			name: 'archived_by_fname',
			type: 'string',
			store: false
		},
		{
			name: 'archived_by_mname',
			type: 'string',
			store: false
		},
		{
			name: 'archived_by_lname',
			type: 'string',
			store: false
		},
		{
			name: 'scanned_by_fname',
			type: 'string',
			store: false
		},
		{
			name: 'scanned_by_mname',
			type: 'string',
			store: false
		},
		{
			name: 'scanned_by_lname',
			type: 'string',
			store: false
		},
		{
			name: 'patient_record_number',
			type: 'string',
			store: false
		},
		{
			name: 'patient_fname',
			type: 'string',
			store: false
		},
		{
			name: 'patient_mname',
			type: 'string',
			store: false
		},
		{
			name: 'patient_lname',
			type: 'string',
			store: false
		}
	],
	proxy: {
		type: 'direct',
		api: {
			read: 'DocumentArchiveLocation.addDocumentLocationSearch',
			create: 'DocumentArchiveLocation.addPatientDocumentLocation',
			update: 'DocumentArchiveLocation.updatePatientDocumentLocation',
			destroy: 'DocumentArchiveLocation.destroyPatientDocumentLocation'
		},
		reader: {
			root: 'data'
		},
		writer: {
			writeAllFields: true
		}
	}
});