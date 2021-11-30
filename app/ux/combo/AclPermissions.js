Ext.define('App.ux.combo.AclPermissions', {
	extend: 'Ext.form.ComboBox',
	xtype: 'aclpermissions',
	typeAhead: true,
	typeAheadDelay: 50,
	queryMode: 'local',
	displayField: 'display_text',
	valueField: 'perm_key',
	emptyText: _('acl_permission'),
	initComponent: function () {
		var me = this;

		me.store = Ext.create('Ext.data.Store', {
			fields: [
				{name: 'perm_key', type: 'string'},
				{name: 'perm_name', type: 'string'},
				{name: 'perm_cat', type: 'string'},
				{
					name: 'display_text',
					type: 'string',
					convert: function (v,r){
						return Ext.String.format('{0}: {1}', r.get('perm_cat'), r.get('perm_name'));
					}
				}
			],
			sorters: [
				{
					property: 'perm_cat',
					direction: 'ASC'
				},
				{
					property: 'perm_name',
					direction: 'ASC'
				}
			],
			remoteSort: true,
			pageSize: 2000,
			autoLoad: true,
			proxy: {
				type: 'direct',
				api: {
					read: 'ACL.getAclPermissions'
				},
				reader: {
					type: 'json'
				}
			},
			listeners: {
				load: function () {
					this.insert(0, [{perm_key: '*', perm_name: 'No Restriction', perm_cat: 'NONE' }]);
				}
			}
		});

		me.callParent(arguments);
	}
});
