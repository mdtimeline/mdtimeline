
Ext.define('App.controller.patient.Insurance', {
	extend: 'Ext.app.Controller',
	requires: [

	],
	refs: [
		{
			ref: 'PatientInsuranceFormSubscribeRelationshipCmb',
			selector: 'PatientInsuranceFormSubscribeRelationshipCmb'
		}
	],

	init: function(){
		var me = this;

		me.control({
            '#PatientInsuranceFormSubscribeRelationshipCmb': {
                change: me.onPatientInsuranceFormSubscribeRelationshipCmbChange
            }
		});
	},

	onPatientInsuranceFormSubscribeRelationshipCmbChange: function(cmb, value){

		var me = this,
			subscriberFields = cmb.up('fieldset').query('[isFormField]'),
			disable = value == '01';

		for(var i = 0; i < subscriberFields.length; i++){
			if(subscriberFields[i].name == 'subscriber_relationship') continue;

			if(disable){
				subscriberFields[i].setDisabled(true);
				subscriberFields[i].reset();
				subscriberFields[i].allowBlank = true;
			}else{
				subscriberFields[i].setDisabled(false);

				if(
					subscriberFields[i].name == 'subscriber_given_name' ||
					subscriberFields[i].name == 'subscriber_surname' ||
					subscriberFields[i].name == 'subscriber_dob' ||
					subscriberFields[i].name == 'subscriber_sex' ||
					subscriberFields[i].name == 'subscriber_street' ||
					subscriberFields[i].name == 'subscriber_city' ||
					subscriberFields[i].name == 'subscriber_state' ||
					subscriberFields[i].name == 'subscriber_country' ||
					subscriberFields[i].name == 'subscriber_postal_code' ||
					subscriberFields[i].name == 'subscriber_employer'
				){
					subscriberFields[i].allowBlank = false;
				}

			}
		}

	}

});