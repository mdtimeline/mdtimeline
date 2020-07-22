Ext.define('App.ux.combo.ActiveSpecialties', {
	extend: 'Ext.form.ComboBox',
	xtype: 'activespecialtiescombo',
	displayField: 'title',
	valueField: 'id',
	editable: false,
	emptyText: _('select'),
	queryMode: 'local',
	store: Ext.create('App.store.administration.Specialties',{
		pageSize: 500,
		autoLoad: true
	}),
	initComponent: function(){
		this.store.clearFilter(true);
		this.store.filter([
			{
				property: 'active',
				value: true
			}
		]);
		this.callParent();
	}

});