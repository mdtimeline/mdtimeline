Ext.define('App.ux.combo.ComboOptionList', {
    extend: 'Ext.form.ComboBox',
    alias: 'widget.gaiaehr.listcombo',
    displayField: 'option_value',
    valueField: 'option_value',
    emptyText: _('select'),
    forceSelection: false,
    editable: false,

	/**
	 * List by Key
	 */
	listKey: null,

    /**
     * List ID
     */
    list: null,
    /**
     * Auto Load Store
     */
    loadStore: false,
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
                },
                {
                    name: 'code',
                    type: 'string'
                },
                {
                    name: 'code_type',
                    type: 'string'
                },
                {
                    name: 'color',
                    type: 'string'
                },
                {
                    name: 'extraListClass',
                    type: 'string'
                }
            ],
            proxy: {
                type: 'direct',
                api: {
                    read: 'CombosData.getOptionsByListId'
                },
                extraParams: {
                    list_id: me.list,
	                list_key: me.listKey
                }
            },
            idProperty: 'option_value'
        });

        me.store = Ext.create('Ext.data.Store', {
            model: model,
            autoLoad: me.loadStore
        });

        me.listConfig = {
            itemTpl: new Ext.XTemplate(
                '<tpl>' +
                '   <div style="white-space: nowrap;">{option_value} (<span style="font-weight: bold;">{option_name}</span>)</div>',
                '</tpl>'
            )
        };

        me.callParent(arguments);

    }
});