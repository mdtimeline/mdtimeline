Ext.define('App.controller.patient.encounter.Addenda', {
	extend: 'Ext.app.Controller',

	refs: [
		{
			ref: 'EncounterSoapAddendaFieldSet',
			selector: '#EncounterSoapAddendaFieldSet'
		},
		{
			ref: 'EncounterSoapAddendaGrid',
			selector: '#EncounterSoapAddendaGrid'
		},
		{
			ref: 'EncounterAddendumWindow',
			selector: '#EncounterAddendumWindow'
		},
		{
			ref: 'EncounterAddendumForm',
			selector: '#EncounterAddendumForm'
		}
	],

	init: function () {
		var me = this;

		me.control({
			'viewport': {
				encounterload: me.onEncounterLoad
			},
			'#EncounterMedicalToolbarAddendumAddBtn': {
				click: me.onEncounterMedicalToolbarAddendumAddBtnClick
			},
			'#EncounterAddendumFormCancelBtn': {
				click: me.onEncounterAddendumFormCancelBtnClick
			},
			'#EncounterAddendumFormSaveBtn': {
				click: me.onEncounterAddendumFormSaveBtnClick
			}
		});

	},

	onEncounterLoad: function (encounter, encounter_panel) {

		var me = this,
			addenda_fieldset = me.getEncounterSoapAddendaFieldSet(),
			addenda_grid = me.getEncounterSoapAddendaGrid();

		if (!addenda_fieldset || !addenda_grid) return;

		addenda_grid.store.load({
			filters: [
				{
					property: 'eid',
					value: encounter.get('eid')
				}
			],
			callback: function (addenda_records) {
				addenda_fieldset.setVisible(addenda_records.length > 0);
			}
		});
	},

	onEncounterMedicalToolbarAddendumAddBtnClick: function (btn) {
		var win = this.showEncounterAddendumWindow();


	},

	showEncounterAddendumWindow: function (){
		if(!this.getEncounterAddendumWindow()){
			Ext.create('App.view.patient.encounter.EncounterAddendumWindow');
		}
		return this.getEncounterAddendumWindow().show();
	},

	onEncounterAddendumFormCancelBtnClick: function (btn) {
		var win = btn.up('window'),
			form = win.down('form').getForm();

		form.reset();
		win.close();
	},

	onEncounterAddendumFormSaveBtnClick: function (btn) {

		var me = this,
			win = btn.up('window'),
			form = win.down('form').getForm(),
			values = form.getValues(),
			addenda_fieldset = me.getEncounterSoapAddendaFieldSet(),
			addenda_grid = me.getEncounterSoapAddendaGrid();

		if(!form.isValid()){
			return;
		}

		win.el.mask('Saving...');

		values.pid = app.patient.pid;
		values.eid = app.patient.eid;
		values.create_uid = app.user.id;
		values.update_uid = app.user.id;
		values.create_date = app.getDate();
		values.update_date = app.getDate();
		values.created_by_fname = app.user.fname;
		values.created_by_mname = app.user.mname;
		values.created_by_lname = app.user.lname;
		values.created_by = '';

		var addendum_record = Ext.create('App.model.patient.encounter.Addendum', values);

		addenda_fieldset.setVisible(true);
		addenda_grid.getStore().add(addendum_record);

		addenda_grid.getStore().sync({
			callback: function (){
				win.el.unmask();
				form.reset();
				win.close();

				app.msg(_('sweet'), 'Addendum Added');

				// TODO: whats next...

			}
		});

	}


});
