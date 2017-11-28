
Ext.define('App.ux.grid.DeleteColumn', {
	extend: 'Ext.grid.column.Action',
	xtype: 'griddeletecolumn',
	icon: 'resources/images/icons/delete.png',  // Use a URL in the icon config
	tooltip: _('delete'),
	acl: '*',
	width: 30,
	handler: function(grid, rowIndex, colIndex, item, e, record) {
		var acl = Ext.isString(this.acl) ? a(this.acl) : eval(this.acl),
			eid = record.get('eid');

		if(eid !== app.patient.eid && !Ext.isEmpty(eid)){
			app.msg(_('oops'), _('remove_encounter_related_error'), true);
			return;
		}

		if(acl !== true) {
			app.msg(_('oops'), _('permission_denied'), true);
			return;
		}

		Ext.Msg.show({
			title:_('wait'),
			msg: _('delete_record_confirmation'),
			buttons: Ext.Msg.YESNO,
			icon: Ext.Msg.QUESTION,
			fn: function(btn){
				if(btn === 'yes'){
					var store = grid.store;
					store.remove(record);
					store.sync({
						callback: function () {
							app.msg(_('sweet'), _('record_removed'), 'yellow');
						}
					});
				}
			}
		});

	}
});
