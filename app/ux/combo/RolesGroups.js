Ext.define('App.ux.combo.RolesGroups', {
	extend: 'Ext.form.ComboBox',
	alias: 'widget.mitos.rolesgroupscombo',
	editable: false,
	queryMode: 'local',
	valueField: 'id',
	displayField: 'title',
	emptyText: _('select'),
	includeAllOption: false,
	initComponent: function () {
		var me = this;

		me.store = Ext.create('Ext.data.Store', {
			autoLoad: true,
			fields: [
				{name: 'id', type: 'int'},
				{name: 'title', type: 'string'}
			],
			proxy: {
				type: 'direct',
				api: {
					read: 'CombosData.getRolesGroups'
				},
				extraParams: {
					includeAllOption: me.includeAllOption
				}
			}
		});

		me.callParent(arguments);
	}
});