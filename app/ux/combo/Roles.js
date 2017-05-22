Ext.define('App.ux.combo.Roles', {
	extend: 'Ext.form.ComboBox',
	alias: 'widget.mitos.rolescombo',
	editable: false,
	queryMode: 'local',
	valueField: 'id',
	displayField: 'role_name',
	emptyText: _('select'),
	includeAllOption: false,
	initComponent: function () {
		var me = this;

		me.store = Ext.create('Ext.data.Store', {
			autoLoad: true,
			fields: [
				{name: 'id', type: 'int'},
				{name: 'role_name', type: 'string'}
			],
			proxy: {
				type: 'direct',
				api: {
					read: 'CombosData.getRoles'
				},
				extraParams: {
					includeAllOption: me.includeAllOption
				}
			}
		});

		me.callParent(arguments);
	}
});