Ext.define('App.ux.combo.ActiveSpecialties', {
	extend: 'Ext.form.ComboBox',
	xtype: 'activespecialtiescombo',
	displayField: 'title',
	valueField: 'id',
	editable: false,
	emptyText: _('select'),
	queryMode: 'local',
	store: Ext.create('App.store.administration.Specialties',{
		filters: [
			{
				property:'active',
				value: true
			}
		],
		pageSize: 500,
		autoLoad: true
	})
});