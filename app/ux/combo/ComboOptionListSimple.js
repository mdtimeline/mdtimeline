Ext.define('App.ux.combo.ComboOptionListSimple', {
    extend: 'Ext.form.ComboBox',
    alias: 'widget.gaiaehr.listcombosimple',
    displayField: 'option_name',
    valueField: 'option_value',
    emptyText: _('select'),
    forceSelection: false,
    editable: false,

    /**
     * List ID
     */
    list: null,
    /**
     * Auto Load Store
     */
    loadStore: true,
    /**
     * value data type
     */
    valueDataType: 'string',


    initComponent: function () {
        var me = this,
            model = me.id + 'ComboOptionModel';

        Ext.define(model, {
            extend: 'Ext.data.Model',
            fields: [
                {
                    name: 'option_name',
                    type: 'string'
                },
                {
                    name: 'option_value',
                    type: me.valueDataType
                }
            ],
            proxy: {
                type: 'direct',
                api: {
                    read: 'CombosData.getOptionsByListId'
                },
                extraParams: {
                    list_id: me.list
                }
            },
            idProperty: 'option_value'
        });

        me.store = Ext.create('Ext.data.Store', {
            model: model,
            autoLoad: me.loadStore
        });

        me.callParent(arguments);

    }
});
