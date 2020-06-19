Ext.define('App.ux.combo.ActiveSpecialties', {
	extend: 'Ext.form.ComboBox',
	xtype: 'activespecialtiescombo',
	displayField: 'title',
	valueField: 'id',
	editable: false,
	emptyText: _('select'),
	queryMode: 'local',
	initComponent: function(){

		this.store = Ext.create('App.store.administration.Specialties',{
			filters: [
				{
					property:'active',
					value: true
				}
			],
			sorters: [
				{
					property: 'title',
					direction: 'ASC'
				}
			],
			pageSize: 500,
			autoLoad: true,
			remoteFilter: true,
			remoteSort: false
		});

		this.callParent();
	}

});