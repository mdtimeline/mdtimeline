/**
 * Created with IntelliJ IDEA.
 * User: ernesto
 * Date: 11/4/13
 * Time: 6:28 PM
 * To change this template use File | Settings | File Templates.
 */
Ext.define('App.model.patient.DocumentRelation', {
	extend: 'Ext.data.Model',
	table: {
		name: 'patient_documents_relations'
	},
	fields: [
		{
			name: 'id',
			type: 'int'
		},
		{
			name: 'document_id',
			type: 'int',
			index: true
		},
		{
			name: 'fk_table',
			type: 'string',
			len: 80,
			index: true
		},
		{
			name: 'fk_id',
			type: 'int',
			index: true
		}
	]
});