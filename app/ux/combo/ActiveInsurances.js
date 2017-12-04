Ext.define('App.ux.combo.ActiveInsurances', {
	extend: 'Ext.form.ComboBox',
	xtype: 'activeinsurancescombo',
	editable: false,
	displayField: 'option_name',
	valueField: 'option_value',
	emptyText: _('select'),
	initComponent: function(){
		var me = this;

		// *************************************************************************************
		// Structure, data for Insurance Payer Types
		// AJAX -> component_data.ejs.php
		// *************************************************************************************

		Ext.define('ActiveInsurancesComboModel', {
			extend: 'Ext.data.Model',
			fields: [
				{
					name: 'option_name',
					type: 'string'
				},
				{
					name: 'option_value',
					type: 'int'
				}
			],
			proxy: {
				type: 'direct',
				api: {
					read: 'CombosData.getActiveInsurances'
				}
			}
		});

		me.store = Ext.create('Ext.data.Store', {
			model: 'ActiveInsurancesComboModel',
			autoLoad: true
		});

		me.callParent();
	}
}); 