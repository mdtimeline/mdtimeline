Ext.define('App.controller.patient.encounter.PhysicalExam', {
	extend: 'Ext.app.Controller',

	refs: [
		{
			ref: 'EncounterPhysicalExamForm',
			selector: '#EncounterPhysicalExamForm'
		}
	],

	init: function () {
		var me = this;

		me.control({
			'viewport': {
				encounterload: me.onEncounterLoad,
				encountersync: me.onEncounterSync
			}
		});

	},

	onEncounterSync: function (encounter_panel, store, form, encounter_record) {

		var me = this,
			physical_exam_form = me.getEncounterPhysicalExamForm().getForm(),
			physical_exam_record = physical_exam_form.getRecord(),
			physical_exam_values =  physical_exam_form.getValues();

		say('onEncounterSync');
		say(encounter_record);
		say(encounter_panel);
		say(physical_exam_form);
		say(physical_exam_record);
		say(physical_exam_values);
		say(physical_exam_record.get('id'));

		if(physical_exam_record.get('id') === undefined){
			physical_exam_record.set({
				exam_data: physical_exam_values,
				create_uid: app.user.id,
				create_date: app.getDate(),
				update_uid: app.user.id,
				update_date: app.getDate()
			});
		}else{
			physical_exam_record.set({
				exam_data: physical_exam_values,
				update_uid: app.user.id,
				update_date: app.getDate()
			});
		}

		if(!Ext.Object.isEmpty(physical_exam_record.getChanges())){
			physical_exam_record.store.sync();
		}

	},

	onEncounterLoad: function (encounter_record, encounter_panel) {

		var me = this,
			physical_exam_form = me.getEncounterPhysicalExamForm().getForm(),
			physical_exam_record = encounter_record.physicalexams().getAt(0);

		physical_exam_record.set({
			pid: encounter_record.get('pid')
		});

		say('onEncounterLoad --->>> physical_exam_record');
		say(physical_exam_record);

		physical_exam_form.loadRecord(physical_exam_record);
		physical_exam_form.setValues(physical_exam_record.get('exam_data'));

	}





});
