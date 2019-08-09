/**
 * GaiaEHR (Electronic Health Records)
 * Copyright (C) 2013 Certun, LLC.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

Ext.define('App.controller.patient.Vitals', {
	extend: 'Ext.app.Controller',
	refs: [
		{
			ref: 'EncounterPanelVitalsPanel',
			selector: '#encounterPanel vitalspanel'
		},
		{
			ref: 'VitalsPanel',
			selector: 'vitalspanel'
		},
		{
			ref: 'VitalsBlocksPanel',
			selector: 'vitalspanel #vitalsBlocks'
		},
		{
			ref: 'VitalsBlocksPanel',
			selector: 'vitalspanel #vitalsBlocks'
		},
		{
			ref: 'VitalsHistoryGrid',
			selector: 'vitalspanel #historyGrid'
		},
		{
			ref: 'VitalsAddBtn',
			selector: 'vitalspanel #vitalAddBtn'
		},
		{
			ref: 'VitalSignBtn',
			selector: 'vitalspanel #vitalSignBtn'
		},
		//
		{
			ref: 'VitalTempFField',
			selector: '#vitalTempFField'
		},
		{
			ref: 'VitalTempCField',
			selector: '#vitalTempCField'
		},
		{
			ref: 'VitalHeightInField',
			selector: '#vitalHeightInField'
		},
		{
			ref: 'VitalHeightCmField',
			selector: '#vitalHeightCmField'
		},
		{
			ref: 'VitalWeightKgField',
			selector: '#vitalWeightKgField'
		},
		{
			ref: 'VitalWeightLbsField',
			selector: '#vitalWeightLbsField'
		},

		// blocks
		{
			ref: 'BpBlock',
			selector: 'vitalspanel #pulseBlock'
		},
		{
			ref: 'BpBlock',
			selector: 'vitalspanel #bpBlock'
		},
		{
			ref: 'TempBlock',
			selector: 'vitalspanel #tempBlock'
		},
		{
			ref: 'WeighBlock',
			selector: 'vitalspanel #weighBlock'
		},
		{
			ref: 'HeightBlock',
			selector: 'vitalspanel #heightBlock'
		},
		{
			ref: 'BmiBlock',
			selector: 'vitalspanel #bmiBlock'
		},
		{
			ref: 'NotesBlock',
			selector: 'vitalspanel #notesBlock'
		}
	],

	init: function(){
		var me = this;

		me.control({
			'viewport': {
				beforeencounterload: me.onAppBeforeEncounterLoad
			},
			'#PatientSummaryPanel vitalspanel': {
				activate: me.onPatientSummaryPanelVitalsPanelActivate
			},
			'vitalspanel #historyGrid': {
				selectionchange: me.onHistoryGridSelectionChange,
				beforeselect: me.onHistoryGridBeforeSelect,
				beforeedit: me.onHistoryGridBeforeEdit,
				validateedit: me.onHistoryGridValidEdit,
				edit: me.onHistoryGridEdit
			},
			'vitalspanel #vitalAddBtn': {
				click: me.onVitalAddBtnClick
			},
			'vitalspanel #vitalSignBtn': {
				click: me.onVitalSignBtnClick
			},


			/** conversions **/
			'#vitalTempFField':{
				keyup:me.onVitalTempFFieldKeyUp
			},
			'#vitalTempCField':{
				keyup:me.onVitalTempCFieldKeyUp
			},
			'#vitalHeightInField':{
				keyup:me.onVitalHeightInFieldKeyUp
			},
			'#vitalHeightCmField':{
				keyup:me.onVitalHeightCmFieldKeyUp
			},
			'#vitalWeightLbsField':{
				keyup:me.onVitalWeightLbsFieldKeyUp
			},
			'#vitalWeightKgField':{
				keyup:me.onVitalWeightKgFieldKeyUp
			}
		});
	},

	onPatientSummaryPanelVitalsPanelActivate: function (vitals_panel) {
		var grid = vitals_panel.down('grid'),
			sm = grid.getSelectionModel(),
			store  = grid.getStore();

		store.clearFilter(true);
		store.load({
			filters: [
				{
					property: 'pid',
					value: app.patient.pid
				}
			],
			callback: function (records) {
				if(records.length > 0){
					sm.select(records[0]);
				}
			}
		});

	},

	onRxOrdersDeleteActionHandler: function (grid, rowIndex, colIndex, item, e, record) {

		if(!a('remove_patient_vitals')){
			app.msg(_('oops'), _('not_authorized'), true);
			return;
		}

		var me = this,
			store = grid.getStore();

		if(record.get('eid') !== app.patient.eid){
			app.msg(_('oops'), _('remove_encounter_related_error'), true);
			return;
		}

		Ext.Msg.show({
			title: _('wait'),
			msg: ('<b>' + Ext.Date.format(record.get('date'), g('date_time_display_format')) + '</b><br><br>' + _('delete_this_record')),
			buttons: Ext.Msg.YESNO,
			icon: Ext.Msg.QUESTION,
			fn: function (btn1) {
				if(btn1 === 'yes'){
					Ext.Msg.show({
						title: _('wait'),
						msg: _('this_action_can_not_be_undone_continue'),
						buttons: Ext.Msg.YESNO,
						icon: Ext.Msg.QUESTION,
						fn: function (btn2) {
							if(btn2 === 'yes'){
								store.remove(record);
								store.sync({
									callback: function () {
										store.remove(record);
										store.sync({
											callback: function () {

											}
										});
									}
								});
							}
						}
					});
				}
			}
		});
	},

	onAppBeforeEncounterLoad: function(encounter_record, encounterPanel){
		if(encounterPanel.down('vitalspanel')){

			var me = this,
				grid = encounterPanel.down('vitalspanel').down('grid'),
				sm = grid.getSelectionModel(),
				vitals_store  = grid.getStore();

			vitals_store.clearFilter(true);
			vitals_store.load({
				filters: [
					{
						property: 'pid',
						value: encounter_record.get('pid')
					}
				],
				callback: function (vitals_records) {
					if(vitals_records.length > 0){
						sm.select(vitals_records[0]);
						app.fireEvent('patientvitalsload', me, encounter_record, vitals_records, vitals_store);
					}
				}
			});
		}
	},

	onHistoryGridSelectionChange: function(sm, records){
		var btn = sm.view.panel.down('#vitalSignBtn');

		this.doUpdateBlocks(sm.view.panel.up('vitalspanel'), records);
		if(records.length === 0 || records[0].data.auth_uid > 0){
			btn.disable();
		}else{
			btn.enable();
		}
	},

	onHistoryGridBeforeSelect: function(sm, record){
		var selected = sm.getSelection().length;

		if(selected > 0 && record.data.auth_uid > 0){
			app.msg(_('oops'),_('multi_select_signed_records_not_authorized'), true);
			return false;
		}

		return true;

	},

	onHistoryGridValidEdit: function(plugin, context){
		var me = this,
			form = plugin.editor.getForm(),
			w = me.isMetric() ? form.findField('weight_kg').getValue() : form.findField('weight_lbs').getValue(),
			h = me.isMetric() ? form.findField('height_cm').getValue() : form.findField('height_in').getValue(),
			bmi = me.bmi(w, h),
			bmiStatus = me.bmiStatus(bmi);

		context.record.set({
			bmi: bmi,
			bmi_status: bmiStatus
		});

		if(a('sign_enc') && context.record.get('auth_uid') === 0){
			context.record.set({
				authorized_by: app.getUserFullname(),
				auth_uid: app.user.id
			});
		}
	},

	onHistoryGridEdit: function(plugin, context){
		this.doUpdateBlocks(plugin.view.panel.up('vitalspanel'), [context.record])
	},

	onHistoryGridBeforeEdit: function(plugin, context){
		var auth_uid = context.record.get('auth_uid');
		if(auth_uid !== 0 && auth_uid !== app.user.id){
			app.msg(_('oops'), _('this_record_can_not_be_modified_because_it_has_been_signed_by') + ' ' + context.record.data.authorized_by, true);
			return false;
		}
		return true;
	},

	onVitalAddBtnClick: function(btn){
		var grid = btn.up('grid'),
			store = grid.getStore(),
			records;

		grid.editingPlugin.cancelEdit();
		records = store.add({
			pid: app.patient.pid,
			eid: app.patient.eid,
			uid: app.user.id,
			administer_by: app.getUserFullname(),
			date: new Date()
		});
		grid.editingPlugin.startEdit(records[0], 1);
	},

	onVitalSignBtnClick: function(){
		var me = this,
			grid = me.getVitalsHistoryGrid(),
			sm = grid.getSelectionModel(),
			records = sm.getSelection();

		app.fireEvent('beforevitalssigned', records);

		app.passwordVerificationWin(function(btn, password){

			if(btn === 'ok'){

				User.verifyUserPass(password, function(provider, response){
					if(response.result){

						for(var i = 0; i < records.length; i++){
							records[i].set({
								auth_uid: app.user.id
							});
						}

						records[0].store.sync({
							callback: function(){
								app.msg('Sweet!', _('vitals_signed'));
								app.fireEvent('vitalssigned', records);
							}
						});
					}else{
						Ext.Msg.show({
							title: 'Oops!',
							msg: _('incorrect_password'),
							buttons: Ext.Msg.OKCANCEL,
							icon: Ext.Msg.ERROR,
							fn: function(btn){
								if(btn === 'ok'){
									me.onVitalSignBtnClick();
								}
							}
						});
					}
				});
			}
		});

	},

	doUpdateBlocks: function(vitalspanel, records){
		var me = this,
			pulseBlock = vitalspanel.down('#pulseBlock'),
			bpBlock = vitalspanel.down('#bpBlock'),
			tempBlock = vitalspanel.down('#tempBlock'),
			weighBlock = vitalspanel.down('#weighBlock'),
			heightBlock = vitalspanel.down('#heightBlock'),
			bmiBlock = vitalspanel.down('#bmiBlock'),
			notesBlock = vitalspanel.down('#notesBlock');

		if(records.length > 0){
			pulseBlock.update(me.getBlockTemplate('pulse', records[0]));
			bpBlock.update(me.getBlockTemplate('bp', records[0]));
			if(me.isMetric()){
				tempBlock.update(me.getBlockTemplate('temp_c', records[0]));
				weighBlock.update(me.getBlockTemplate('weight_kg', records[0]));
				heightBlock.update(me.getBlockTemplate('height_cm', records[0]));
			}else{
				tempBlock.update(me.getBlockTemplate('temp_f', records[0]));
				weighBlock.update(me.getBlockTemplate('weight_lbs', records[0]));
				heightBlock.update(me.getBlockTemplate('height_in', records[0]));
			}
			bmiBlock.update(me.getBlockTemplate('bmi', records[0]));
			notesBlock.update(me.getBlockTemplate('other_notes', records[0]));
		}else{
			pulseBlock.update(me.getBlockTemplate('pulse', false));
			bpBlock.update(me.getBlockTemplate('bp', false));
			if(me.isMetric()){
				tempBlock.update(me.getBlockTemplate('temp_c', false));
				weighBlock.update(me.getBlockTemplate('weight_kg', false));
				heightBlock.update(me.getBlockTemplate('height_cm', false));
			}else{
				tempBlock.update(me.getBlockTemplate('temp_f', false));
				weighBlock.update(me.getBlockTemplate('weight_lbs', false));
				heightBlock.update(me.getBlockTemplate('height_in', false));
			}

			bmiBlock.update(me.getBlockTemplate('bmi', false));
			notesBlock.update(me.getBlockTemplate('other_notes', false));
		}
	},

	getBlockTemplate: function(property, record){
		var title = '',
			value = '',
			extra = '',
			symbol = '',
			align = 'center';

		if(record !== false){
			if(property === 'pulse'){
				title = _(property);
				value = record.data.pulse ?  + record.data.pulse : '--';
				extra = _('bpm');
			}else if(property === 'bp'){
				title = _(property);
				value = (record.data.bp_systolic + '/' + record.data.bp_diastolic);
				value = value === 'null/null' || value === '/' ? '--/--' : value;
				extra = _('systolic') + '/' + _('diastolic');

			}else if(property === 'temp_c' || property === 'temp_f'){
				title = _('temp');
				symbol = property === 'temp_c' ? '&deg;C' : '&deg;F';
				value = record.data[property] === null || record.data[property] === '' ? '--' : record.data[property] + symbol;
				extra = record.data.temp_location === '' ? '--' : record.data.temp_location;

			}else if(property === 'weight_lbs' || property === 'weight_kg'){
				title = _('weight');
				//				symbol = property == 'weight_lbs' ? ' lbs' : ' kg';
				value = record.data[property] === null || record.data[property] === '' ? '--' : record.data[property] + symbol;
				extra = property === 'weight_lbs' ? 'lbs/oz' : 'Kg';

			}else if(property === 'height_in' || property === 'height_cm'){
				title = _('height');
				symbol = property === 'height_in' ? ' in' : ' cm';
				value = record.data[property] === null || record.data[property] === '' ? '--' : record.data[property] + symbol;

			}else if(property === 'bmi'){
				title = _(property);
				value = record.data[property] === null || record.data[property] === '' ? '--' : this.decimalRound10(record.data[property], -1);
				extra = record.data.bmi_status === '' ? '--' : record.data.bmi_status;

			}else if(property === 'other_notes'){
				title = _('notes');
				value = record.data[property] === null || record.data[property] === '' ? '--' : record.data[property];
				align = 'left'
			}
		}else{
			if(property == 'temp_c' || property == 'temp_f'){
				title = _('temp');
			}else if(property == 'weight_lbs' || property == 'weight_kg'){
				title = _('weight');
			}else if(property == 'height_in' || property == 'height_cm'){
				title = _('height');
			}else if(property == 'other_notes'){
				title = _('notes');
				align = 'left'
			}else{
				title = _(property);
			}
			value = property == 'bp' ? '--/--' : '--';
			extra = '--';
		}

		return '<p class="title">' + title + '</p><p class="value" style="text-align: ' + align + '">' + value + '</p><p class="extra">' + extra + '</p>';
	},


	onVitalStoreWrite:function(store, operation, e){
		app.fireEvent('vitalwrite', store, operation, e);
	},

	onVitalTempFFieldKeyUp:function(field){
		field.up('form').getForm().getRecord().set({temp_c: this.fc(field.getValue())});
	},

	onVitalTempCFieldKeyUp:function(field){
		field.up('form').getForm().getRecord().set({temp_f: this.cf(field.getValue())});
	},

	onVitalHeightInFieldKeyUp:function(field){
		field.up('form').getForm().getRecord().set({height_cm: this.incm(field.getValue())});
	},

	onVitalHeightCmFieldKeyUp:function(field){
		field.up('form').getForm().getRecord().set({height_in: this.cmin(field.getValue())});
	},

	onVitalWeightLbsFieldKeyUp:function(field){
		field.up('form').getForm().getRecord().set({weight_kg: this.lbskg(field.getValue())});
	},

	onVitalWeightKgFieldKeyUp:function(field){
		field.up('form').getForm().getRecord().set({weight_lbs: this.kglbs(field.getValue())});
	},


	/** Conversions **/

	/**
	 * Convert Celsius to Fahrenheit
	 * @param v
	 */
	cf: function(v){
		return Ext.util.Format.round((9 * v / 5 + 32), 1);
	},

	/**
	 * Convert Fahrenheit to Celsius
	 * @param v
	 */
	fc: function(v){
		return Ext.util.Format.round(((v - 32) * 5 / 9), 1);
	},

	/**
	 * Convert Lbs to Kg
	 * @param v
	 */
	lbskg: function(v){
		var lbs = v[0] || 0,
			oz = v[1] || 0,
			kg = 0,
			res;
		if(lbs > 0) kg = kg + (lbs / 2.2046);
		if(oz > 0) kg = kg + (oz / 35.274);
		return Ext.util.Format.round(kg, 1);
	},

	/**
	 * Convert Kg to Lbs
	 * @param v
	 */
	kglbs: function(v){
		return Ext.util.Format.round((v * 2.2046), 1);
	},

	/**
	 * Convert Inches to Centimeter
	 * @param v
	 */
	incm: function(v){
		return Math.floor(v * 2.54);
	},

	/**
	 * Convert Centimeter to Inches
	 * @param v
	 */
	cmin: function(v){
		return  Ext.util.Format.round((v / 2.54), 0);
	},

	/**
	 * Get BMI from weight and height
	 * @param weight
	 * @param height
	 * @returns {*}
	 */
	bmi: function(weight, height){
		var bmi = '',
			foo = weight.split('/');

		if(foo.length > 1){
			weight = eval(foo[0]) + (foo[1] / 16);
		}

		if(weight > 0 && height > 0){
			if(!this.isMetric()){
				bmi = weight / (height * height) * 703;
			}else{
				bmi = weight / ((height / 100) * (height / 100));
			}
		}

		return bmi.toFixed ? bmi.toFixed(1) : bmi;
	},

	bmiStatus:function(bmi){
		var status = '';
		if(bmi == '') return '';
		if(bmi < 15){
			status = _('very_severely_underweight')
		}else if(bmi >= 15 && bmi < 16){
			status = _('severely_underweight')
		}else if(bmi >= 16 && bmi < 18.5){
			status = _('underweight')
		}else if(bmi >= 18.5 && bmi < 25){
			status = _('normal')
		}else if(bmi >= 25 && bmi < 30){
			status = _('overweight')
		}else if(bmi >= 30 && bmi < 35){
			status = _('obese_class_1')
		}else if(bmi >= 35 && bmi < 40){
			status = _('obese_class_2')
		}else if(bmi >= 40){
			status = _('obese_class_3')
		}
		return status;
	},

	/**
	 * return true if units of measurement is metric
	 */
	isMetric:function(){
		return g('units_of_measurement') === 'metric';
	},

    /*
    decimalAdjust:
        Method to correctly round numbers, with decimals.
        Thanks, to the excellentt guys a Mozilla Developer Network
        https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Math/round
     */
    decimalAdjust: function(type, value, exp) {
        // If the exp is undefined or zero...
        if (typeof exp === 'undefined' || +exp === 0) {
            return Math[type](value);
        }
        value = +value;
        exp = +exp;
        // If the value is not a number or the exp is not an integer...
        if (isNaN(value) || !(typeof exp === 'number' && exp % 1 === 0)) {
            return NaN;
        }
        // Shift
        value = value.toString().split('e');
        value = Math[type](+(value[0] + 'e' + (value[1] ? (+value[1] - exp) : -exp)));
        // Shift back
        value = value.toString().split('e');
        return +(value[0] + 'e' + (value[1] ? (+value[1] + exp) : exp));
    },

    /*
     decimalRound10:
        Method to correctly round numbers, with decimals.
        Thanks, to the excellentt guys a Mozilla Developer Network
        https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Math/round
     */
    decimalRound10: function(value, exp) {
        return this.decimalAdjust('round', value, exp);
    },
    floorRound10: function(value, exp) {
        return decimalAdjust('floor', value, exp);
    },
    ceilRound10: function(value, exp) {
        return decimalAdjust('ceil', value, exp);
    }

});
