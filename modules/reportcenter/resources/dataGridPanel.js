var dataGridStore = new Ext.create('Ext.data.Store', {
    storeId: 'reportStore',
    autoLoad  : false,
    remoteFilter: true,
    multiSortLimit: 10,
    /*groupingConfigStore*/
    fields: [
        /*fieldStore*/
    ],
    /*remoteSort*/
    proxy: {
        type: 'direct',
        api: {
            read: 'ReportGenerator.dispatchReportData'
        },
        reader: {
            type: 'json',
            root: 'data'
        }
    },
    /*dataStoreConfig*/
});

Ext.create('Ext.grid.Panel', {
    itemId: 'reportDataGrid',
    store: dataGridStore,
    region: 'center',
    rowLines: false,
    multiColumnSort: true,
    columnLines: true,
    /*dataGridConfig*/
    /*groupingConfigGrid*/
    columns: [
        /*fieldColumns*/
    ]
});
