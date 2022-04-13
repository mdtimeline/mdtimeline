Ext.define('App.controller.Print', {
    extend: 'Ext.app.Controller',
    requires: [
        'App.ux.combo.Printers'
    ],
    refs: [],

    printers: [],

    browserHelperCtl: undefined,

    init: function () {
        var me = this;

        me.control({
            'viewport': {
                browserhelperopen: me.onBrowserHelperOpen
            },
            'printerscombo': {
                render: me.onPrintersComboBeforeRender
            }
        });

        me.printJobCtl = this.getController('PrintJob');

        me.loadRemotePrinters();

    },

    doPrint: function (printer_record, document_base64, job_id, callback) {
        var me = this;

        if (printer_record.get('local')) {
            if (!me.browserHelperCtl) {
                app.msg(_('oops'), _('no_browser_helper_found'), true);
                return;
            }

            job_id = job_id || "0";

            me.browserHelperCtl.send(JSON.stringify({
                action: 'print',
                printer: printer_record.get('id'),
                payload: document_base64,
                job_id: job_id
            }), function (response) {
                if (!response.success) {
                    app.msg(_('oops'), 'Document could not be printed', true);
                }
                if(callback) callback(response);
            });

        } else {
            Printer.doPrint(printer_record.get('id'), document_base64, job_id, function (response){
                if(callback) callback(response);
            });
        }
    },

    addPrintJob: function (document_id, printer_record, print_now, priority) {
        var me = this;
        me.printJobCtl.addPrintJob(document_id, printer_record, print_now, priority);
    },

    loadRemotePrinters: function () {
        var me = this;

        Printer.getPrinters(function (printers) {
            me.printers = Ext.Array.merge(me.printers, printers);
            var rendered_printer_combos = Ext.ComponentQuery.query('printerscombo[rendered]');
            rendered_printer_combos.forEach(function (rendered_printer_combo){
                rendered_printer_combo.store.loadData(me.printers);
                rendered_printer_combo.showPrintersByFacility();
                rendered_printer_combo.setValue(rendered_printer_combo.getValue());
            });
        });
    },

    onBrowserHelperOpen: function (ctl) {
        var me = this;

        me.browserHelperCtl = ctl;

        me.browserHelperCtl.send('{"action":"printer/list"}', function (printers) {
            //Add local property to each printer
            for (var i = 0; i < printers.length; i++) {
                printers[i].local = true;
            }

            me.printers = Ext.Array.merge(me.printers, printers);

            var rendered_printer_combos = Ext.ComponentQuery.query('printerscombo[rendered]');
            rendered_printer_combos.forEach(function (rendered_printer_combo){
                rendered_printer_combo.store.loadData(me.printers);
                rendered_printer_combo.showPrintersByFacility();
                rendered_printer_combo.setValue(rendered_printer_combo.getValue());
            });

        });

    },

    onPrintersComboBeforeRender: function (cmb) {
        say(this.printers);
        cmb.store.loadData(this.printers);

        //register combobox to change when facility changes
        app.on('appfacilitychanged', function () {
            cmb.showPrintersByFacility();
            // if (cmb.store.getCount() > 0) {
            //     cmb.setValue(cmb.store.first());
            // } else {
            //     cmb.setValue(null);
            // }
        });

        //filter printers by facility and local
        cmb.showPrintersByFacility();

        if (cmb.store.find('id', cmb.getValue()) === cmb.getValue()) {
            // fix to stateful not selecting
            // cmb.setValue(cmb.getValue());
        } else {
            // cmb.store.getCount() > 0 ? cmb.setValue(cmb.store.first()) : cmb.setValue(null);
        }

    },

    getPrinters: function () {
        var me = this;

        return me.printers;
    },

    promptPrint: function () {

    }


});
