Ext.define('App.ux.combo.Printers', {
	extend: 'Ext.form.ComboBox',
	xtype: 'printerscombo',
	editable: false,
	queryMode: 'local',
	displayField: 'name',
	valueField: 'id',
	emptyText: _('select'),
	width: 200,
	store: Ext.create('Ext.data.Store', {
		fields: [
			{ name: 'id', type: 'string' },
			{ name: 'name', type: 'string' },
			{ name: 'local', type: 'bool' }
		]
	})
});