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
			'#ApplicationFacilityCombo': {
				select: me.onApplicationFacilityComboSelect,
				beforerender: me.onApplicationFacilityComboBeforeRender
			}
		});

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
