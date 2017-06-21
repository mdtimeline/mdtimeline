Ext.define('App.controller.Main', {
	extend: 'Ext.app.Controller',

	refs: [
		{
			ref: 'viewport',
			selector: 'viewport'
		},
		{
			ref: 'ApplicationFacilityCombo',
			selector: '#ApplicationFacilityCombo'
		}
	],

	init: function(){
		var me = this;

		me.control({
			'viewport': {
				usersessionswitch: me.onApplicationUserSessionSwitch
			},
			'#ApplicationFacilityCombo': {
				select: me.onApplicationFacilityComboSelect,
				beforerender: me.onApplicationFacilityComboBeforeRender
			}
		});

	},

	onApplicationUserSessionSwitch: function (ctrl, session) {

		if(session.user.acl_roles[0] == app.user.acl_roles[0]){
			Ext.Object.each(session.user, function (key, value) {
				app.user[key] = value;
			});
			app.user.token = session.token;
			app.userSplitBtn.setText(app.user.title + ' ' + app.user.fname[0] + '.' + app.user.lname);
			ctrl.doApplicationUnLock();
			app.msg(
				_('sweet'),
				Ext.String.format(_('application_successfully_switched_to_x'), '<b>' + (app.user.title + ' ' + app.user.fname + ' ' + app.user.lname) + '</b>')
			);

		}else {
			window.onbeforeunload = null;
			window.location.reload();
		}
	},

	onApplicationFacilityComboSelect: function (cmb, records) {
		var me = this;
		Facilities.setFacility(records[0].data.option_value, function(provider, response){
			if(records[0].data.option_value == response.result){
				// set user global facility value
				app.user.facility = records[0].data.option_value;

				app.msg(_('sweet'), _('facility') + ' ' + records[0].data.option_name);
				app.setWindowTitle(records[0].data.option_name);
				app.nav['App_view_areas_PatientPoolDropZone'].reRenderPoolAreas();
				app.nav['App_view_areas_FloorPlan'].renderZones();
				app.getPatientsInPoolArea();
			}
		});
	},

	onApplicationFacilityComboBeforeRender: function (cmb) {
		cmb.getStore().on('load', this.onFacilityComboLoad, this);
	},

	onFacilityComboLoad:function(store, records){
		var rec = store.findRecord('option_value', app.user.facility);
		this.getApplicationFacilityCombo().setValue(rec);
		app.setWindowTitle(rec.data.option_name)
	},

	getCurrentFacility: function () {
		return this.getApplicationFacilityCombo().findRecordByValue(app.user.facility);
	}

});
