Ext.define('App.ux.combo.Providers', {
	extend: 'Ext.form.ComboBox',
	alias: 'widget.mitos.providerscombo',
	editable: false,
	queryMode: 'local',
	displayField: 'name',
	valueField: 'id',
	emptyText: _('select'),

	initComponent: function(){
		var me = this;

		Ext.define('ProvidersComboBoxModel', {
			extend: 'Ext.data.Model',
			fields: [
				{
					name: 'id',
					type: 'string'
				},
				{
					name: 'fname',
					type: 'string'
				},
				{
					name: 'mname',
					type: 'string'
				},
				{
					name: 'lname',
					type: 'string'
				},
				{
					name: 'email',
					type: 'string'
				},
				{
					name: 'mobile',
					type: 'string'
				},
				{
					name: 'name',
					type: 'string',
					convert: function (v, r) {
						return Ext.String.format('{0}, {1} {2}',
								r.get('lname'), r.get('fname'), r.get('mname')
							);
					}
				},
			],
			proxy: {
				type: 'direct',
				api: {
					read: 'User.getProviders'
				}
			}
		});

		me.store = Ext.create('Ext.data.Store', {
			model: 'ProvidersComboBoxModel',
			autoLoad: true
		});

		me.callParent(arguments);
	}
});