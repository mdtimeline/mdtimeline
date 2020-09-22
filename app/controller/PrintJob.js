Ext.define('App.controller.PrintJob', {
    extend: 'Ext.app.Controller',
    requires: [
        'App.ux.combo.Printers',
        'App.model.administration.PrinterCombo'
    ],
    refs: [
        {
            selector: '#ApplicationFooterTestDocumentViewerBtn',
            ref: 'ApplicationFooterTestDocumentViewerBtn'
        },
        {
            selector: '#PrintJobsWindow',
            ref: 'PrintJobsWindow'
        },
        {
            selector: '#PrintJobsWindowGird',
            ref: 'PrintJobsWindowGird'
        },
        {
            selector: '#PrintJobsWindowPrintBtn',
            ref: 'PrintJobsWindowPrintBtn'
        },
        {
            selector: '#PrintJobsWindowFromField',
            ref: 'PrintJobsWindowFromField'
        },
        {
            selector: '#PrintJobsWindowToField',
            ref: 'PrintJobsWindowToField'
        },
        {
            selector: '#PrintJobsWindowPriorityChkGroup',
            ref: 'PrintJobsWindowPriorityChkGroup'
        },
        {
            selector: '#PrintJobsWindowStatusChkGroup',
            ref: 'PrintJobsWindowStatusChkGroup'
        },
        {
            selector: '#PrintJobsWindowPrintersCombo',
            ref: 'PrintJobsWindowPrintersCombo'
        },
        {
            selector: '#PrintJobsWindowUserLiveSearch',
            ref: 'PrintJobsWindowUserLiveSearch'
        }
    ],

    init: function () {
        var me = this;

        me.control({
            'viewport': {
                printermsg: me.onApplicationPrinterMsg
            },
            '#ApplicationFooterPrintJobsBtn': {
                click: me.onApplicationFooterPrintJobsBtnClick
            },
            '#PrintJobsWindowGird': {
                itemdblclick: me.onPrintJobsWindowGridItemDblClick
            },
            '#PrintJobsWindowPrintBtn': {
                click: me.onPrintJobsWindowPrintBtnClick
            },
            '#PrintJobsWindowFromField': {
                change: me.onChangeFilters
            },
            '#PrintJobsWindowToField': {
                change: me.onChangeFilters
            },
            '#PrintJobsWindowPriorityChkGroup': {
                change: me.onChangeFilters
            },
            '#PrintJobsWindowStatusChkGroup': {
                change: me.onChangeFilters
            },
            '#ApplicationFooterTestDocumentViewerBtn': {
                click: me.onApplicationFooterTestDocumentViewerBtnClick
            },
            '#PrintJobsWindowUserLiveSearch': {
                beforerender: me.onPrintJobsWindowUserLiveSearchBeforeRender,
                select: me.onPrintJobsWindowUserLiveSearchSelect
            }
        });

        me.print_job_store = Ext.create('App.store.administration.PrintJob', {
            remoteFilter: true,
            remoteSort: true,
            autoSync: true,
            sorters: [
                {
                    property: 'priority',
                    direction: 'ASC'
                }
            ],
            pageSize: 50
        });

        me.print_task = setInterval(function () {
            me.checkJobsToPrint();
        }, 10000);
    },

    checkJobsToPrint: function () {
        var me = this,
            is_printjob_window_open = me.getPrintJobsWindow();

        if (is_printjob_window_open) {
            me.print_job_store.reload();
        } else {
            this.print_job_store.each(function (print_job_record) {
                if (print_job_record.get('print_status') === 'send') {
                    me.doPrintJob(print_job_record);
                }
            });

            this.print_job_store.clearFilter(true);
            this.print_job_store.filter([
                {
                    property: 'uid',
                    value: app.user.id
                },
                {
                    property: 'print_status',
                    value: 'send'
                },
                {
                    property: 'print_status',
                    value: 'printing'
                },
            ]);
        }
    },

    doPrintJob: function (print_job_record) {
        print_job_record.set({print_status: 'printing'});

        var me = this,
            printController = app.getController('Print'),
            printers = printController.printers,
            params = {
                id: print_job_record.get('document_id')
            };

        for (i = 0; i < printers.length; i++) {
            var printer_record = Ext.create('App.model.administration.PrinterCombo', printers[i]);

            //* Printer record id is a string and printer_id is an int
            if (printer_record.get('id') == print_job_record.get('printer_id')) {
                DocumentHandler.getPatientDocument(params, true, function (patient_document) {
                    if (patient_document === false) {
                        app.msg(_('error'), _('patient_document_not_found'), true);
                        return;
                    }

                    printController.doPrint(printer_record, patient_document.document, print_job_record.get('id'));
                });

                break;
            }
        }
    },

    onChangeFilters: function () {
        var me = this,
            from_date_field = me.getPrintJobsWindowFromField(),
            to_date_field = me.getPrintJobsWindowToField(),
            status_checkbox_group = me.getPrintJobsWindowStatusChkGroup(),
            priority_checkbox_group = me.getPrintJobsWindowPriorityChkGroup(),
            from_date = Ext.Date.format(from_date_field.getValue(), 'Y-m-d') + ' 00:00:00',
            to_date = Ext.Date.format(to_date_field.getValue(), 'Y-m-d') + ' 23:59:59',
            status_send = false,
            status_printing = false,
            status_done = false,
            status_waiting = false,
            status_failed = false,
            priority_high = false,
            priority_med = false,
            priority_low = false,
            selected_status_checkboxes = status_checkbox_group.getChecked(),
            selected_priority_checkboxes = priority_checkbox_group.getChecked(),
            user_id = me.getPrintJobsWindowUserLiveSearch().getValue(),
            filters = [];

        say(user_id);

        selected_status_checkboxes.forEach(function (checkbox) {
            switch (checkbox.getName()) {
                case 'status_sended':
                    status_send = true;
                    break;
                case 'status_printing':
                    status_printing = true;
                    break;
                case 'status_waiting':
                    status_waiting = true;
                    break;
                case 'status_done':
                    status_done = true;
                    break;
                case 'status_failed':
                    status_failed = true;
                    break;
            }
        });

        selected_priority_checkboxes.forEach(function (checkbox) {
            switch (checkbox.getName()) {
                case 'priority_high':
                    priority_high = true;
                    break;
                case 'priority_med':
                    priority_med = true;
                    break;
                case 'priority_low':
                    priority_low = true;
                    break;
            }
        });

        filters.push({property: 'uid', value: app.user.id});
        filters.push({property: 'created_at', operator: '>=', value: from_date});
        filters.push({property: 'created_at', operator: '<=', value: to_date});
        if (user_id !== null) filters.push({property: 'uid', value: user_id});
        if (status_send) filters.push({property: 'print_status', value: 'send'});
        if (status_printing) filters.push({property: 'print_status', value: 'printing'});
        if (status_waiting) filters.push({property: 'print_status', value: 'waiting'});
        if (status_failed) filters.push({property: 'print_status', value: 'failed'});
        if (status_done) filters.push({property: 'print_status', value: 'done'});
        if (priority_high) filters.push({property: 'priority', value: 1});
        if (priority_med) filters.push({property: 'priority', value: 2});
        if (priority_low) filters.push({property: 'priority', value: 3});

        me.print_job_store.clearFilter(true);
        me.print_job_store.filter(filters);
    },

    onApplicationPrinterMsg: function (msg, data) {
        var me = this,
            print_job_record = me.print_job_store.getById(data.job_id);

        if (print_job_record === null) return;

        print_job_record.set({print_status: data.print_status});
    },

    onPrintJobsWindowPrintBtnClick: function (btn) {
        var me = this,
            window = me.getPrintJobsWindow(),
            grid = me.getPrintJobsWindowGird(),
            printer_combobox = me.getPrintJobsWindowPrintersCombo(),
            printer_id = printer_combobox.getValue(),
            printer_record = printer_combobox.findRecordByValue(printer_id),
            selected_records = grid.getSelectionModel().getSelection();

        if (selected_records.length <= 0) {
            app.msg(_('error'), 'No print jobs selected.', true);
        }

        if (printer_record === false) {
            app.msg(_('error'), 'No printer selected.', true);
        }

        app.msg(_('sending') + '...', selected_records.length.toString() + ' print jobs sent to print.');

        selected_records.forEach(function (print_job_record) {
            print_job_record.set({
                print_status: 'send',
                printer_id: printer_record.get('id'),
                printer_type: printer_record.get('local') ? 'local' : 'remote'
            });

            me.doPrintJob(print_job_record);

            // if (print_job_record.get('print_status') === 'waiting') {
            //     print_job_record.set({
            //         print_status: 'send',
            //         printer_id: print_job_record.get('printer_id'),
            //         printer_type: printer_record.get('local')
            //     });
            //     me.doPrintJob(print_job_record);
            //     return;
            // }
            //
            // me.addPrintJob(print_job_record.get('document_id'), printer_record, true, print_job_record.get('priority'));
            // me.doPrintJob(print_job_record)
        });

        window.close();
    },

    addPrintJob: function (document_id, printer_record, print_now, priority) {
        var me = this;

        if (document_id === null || document_id === 0) {
            app.msg(_('error'), 'Print job could not be added. Document Missing.', true)
        }

        if (printer_record === null || printer_record.get('id') === null) {
            app.msg(_('error'), 'Print job could not be added. Printer Missing.', true)
        }

        if (priority === null || !Number.isInteger(priority)) {
            app.msg(_('error'), 'Print job could not be added. Priority Missing.', true)
        }

        me.print_job_store.add({
            uid: app.user.id,
            document_id: document_id,
            printer_id: printer_record.get('id'),
            printer_type: printer_record.get('local') ? 'local' : 'remote',
            print_status: print_now ? 'send' : 'waiting',
            priority: priority,
            created_at: Ext.Date.format(new Date(), 'Y-m-d H:i:s')
        });
    },

    onApplicationFooterPrintJobsBtnClick: function () {
        this.showPrintJobsWindow();

        this.onChangeFilters();
    },

    showPrintJobsWindow: function (init) {
        if (!this.getPrintJobsWindow()) {
            Ext.create('App.view.administration.PrintJobsWindow');
            var win = this.getPrintJobsWindow().show();
            this.getPrintJobsWindowGird().reconfigure(this.print_job_store);
            return win;
        } else {
            return this.getPrintJobsWindow().show();
        }
    },

    onApplicationFooterTestDocumentViewerBtnClick: function (btn) {
        var me = this,
            params = {
                body: 'test'
            };

        DocumentHandler.createTempDocument(params, function (response) {
            app.getController('DocumentViewer').doDocumentView(response.id, response.document_name);
        })
    },

    onPrintJobsWindowUserLiveSearchBeforeRender: function (cmb){
        var me = this,
            allow_to_see_other_print_jobs = true; //TODO: Check for permission

        if(!allow_to_see_other_print_jobs){
            cmb.setVisible(false);
        }
    },

    onPrintJobsWindowUserLiveSearchSelect: function (cmb,records){
        this.onChangeFilters();
    },

    onPrintJobsWindowGridItemDblClick: function (grid, record, item, index){
        var me = this,
            document_id = record.get('document_id');

        app.getController('DocumentViewer').doDocumentView(document_id);
    }
});
