Ext.define('App.ux.combo.Printers', {
    extend: 'App.ux.combo.ComboResettable',
    xtype: 'printerscombo',
    editable: false,
    queryMode: 'local',
    displayField: 'display_value',
    valueField: 'id',
    emptyText: _('select'),
    width: 200,
    stateful: true,
    stateId: 'PrinterComboState',
    store: Ext.create('App.store.administration.PrinterCombo'),
    // store: Ext.create('Ext.data.Store', {
    //     fields: [
    //         {
    //             name: 'id',
    //             type: 'string'
    //         },
    //         {name: 'name', type: 'string'},
    //         {name: 'printer_description', type: 'string'},
    //         {name: 'local', type: 'bool'},
    //         {name: 'facility_id', type: 'int'},
    //         {name: 'active', type: 'bool'},
    //         {
    //             name: 'display_value',
    //             convert: function (v, r) {
    //                 if (r.get('local')) {
    //                     return r.get('name') + ' (local)';
    //                 }
    //                 return r.get('printer_description') + ' (remote)';
    //             }
    //         }
    //     ],
    //     remoteFilter: false
    // }),

    showAllPrinters: function () {
        this.store.clearFilter();
    },
    showRemotePrinters: function () {
        this.store.clearFilter(true);
        this.store.filter({
            property: 'local',
            value: false
        });
    },
    showLocalPrinters: function () {
        this.store.clearFilter(true);
        this.store.filter({
            property: 'local',
            value: true
        });
    },
    showPrintersByFacility: function () {
        this.store.clearFilter(true);
        this.store.filter(
            {
                filterFn: function (item) {
                    return (item.get("facility_id") === app.user.facility && item.get("active")) || item.get('local');
                }
            },
            {
                property: 'local',
                value: true
            });
    }

});