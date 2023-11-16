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
			},
			'#AppCleatState': {
				click: me.onAppCleatStateClick
			}
		});

	},

	onAppCleatStateClick: function (btn){

		Ext.Msg.show({
			title: 'Wait!',
			msg: 'This action will reset the application state and refresh the application',
			buttons: Ext.Msg.YESNO,
			icon: Ext.Msg.QUESTION,
			fn: function (btn){
				if(btn === 'yes'){
					AppState.AppStateUnClearByUid(app.user.id, function (){
						window.document.location.reload();
					});
				}
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

				app.fireEvent('appfacilitychanged', me, app.user.facility, cmb);

				app.msg(_('sweet'), _('facility') + ' ' + records[0].data.option_name);
				app.setWindowTitle(records[0].data.option_name);

				me.getController('areas.PatientPoolAreas').reRenderPoolAreas();
				me.getController('areas.FloorPlan').renderZones();

				// app.nav['App_view_areas_PatientPoolAreas'].reRenderPoolAreas();
				// app.nav['App_view_areas_FloorPlan'].renderZones();
				app.getPatientsInPoolArea();
			}
		});
	},

	onApplicationFacilityComboBeforeRender: function (cmb) {
		cmb.setValue(app.user.facility);
		cmb.getStore().on('load', this.onFacilityComboLoad, this);
	},

	onFacilityComboLoad:function(store, records){
		var facility_record = this.getApplicationFacilityCombo().findRecordByValue(app.user.facility);
		app.setWindowTitle(facility_record.data.option_name)
	},

	getCurrentFacility: function () {
		return this.getApplicationFacilityCombo().findRecordByValue(app.user.facility);
	},

	getCurrentFacilityName: function () {
		return this.getApplicationFacilityCombo().findRecordByValue(app.user.facility).get('option_name');
	}

});
