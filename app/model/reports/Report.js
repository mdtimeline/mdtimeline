/**
 * Generated dynamically by Matcha::Connect
 * Create date: 2015-10-25 14:22:10
 */

Ext.define('App.model.reports.Report',{
    extend: 'Ext.data.Model',
    table: {
        name: 'reports'
    },
    fields: [
        {
            name: 'id',
            type: 'int'
        },
        {
            name: 'category',
            type: 'string'
        },
        {
            name: 'title',
            type: 'string'
        },
        {
            name: 'description',
            type: 'string'
        },
        {
            name: 'store_procedure_name',
            type: 'string'
        },
        {
            name: 'parameters',
            type: 'auto'
        },
        {
            name: 'group_fields',
            type: 'string'
        },
        {
            name: 'report_perm',
            type: 'string'
        },
        {
            name: 'columns',
            type: 'string'
        }
    ],
    proxy: {
        type: 'direct',
        api: {
            read: 'Reports.getReports',
            create: 'Reports.addReport',
            update: 'Reports.updateReport',
            destroy: 'Reports.deleteReport'
        },
	    reader:{
		    root: 'data'
	    }
    }
});
