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

Ext.define('App.controller.patient.Medical', {
	extend: 'Ext.app.Controller',
	requires: [

	],
	refs: [
		{
			ref: 'MedicalWindow',
			selector: '#MedicalWindow'
		},
		{
			ref: 'EncounterMedicalToolbar',
			selector: '#EncounterMedicalToolbar'
		}
	],

	init: function(){
		var me = this;
		me.control({
			'viewport': {
				'navkey': me.onNavKey
			},
			'#MedicalWindow #immunization': {
				'show': me.onPanelShow
			},
			'#MedicalWindow #allergies': {
				'show': me.onPanelShow
			},
			'#MedicalWindow #activeproblems': {
				'show': me.onPanelShow
			},
            '#MedicalWindow #familyhistory': {
                'show': me.onPanelShow
            },
            '#MedicalWindow #patientprocedureshistorygrid': {
                'show': me.onPanelShow
            },
            '#MedicalWindow #advancedirectives': {
                'show': me.onPanelShow
            },
			'#MedicalWindow #medications': {
				'show': me.onPanelShow
			},
			'#MedicalWindow #laboratories': {
				'show': me.onPanelShow
			},
			'#MedicalWindow #socialhistory': {
				'show': me.onPanelShow
			},
			'#MedicalWindow #referrals': {
				'show': me.onPanelShow
			}
		});

		me.items_indexes = Ext.state.Manager.get('appmedicalwinodwtaborder') || {};
	},

	onMedicalPanelIndexChange: function (items_indexes) {
		this.items_indexes = items_indexes;
		Ext.state.Manager.set('appmedicalwinodwtaborder' , items_indexes);
	},

	onMedicalWindowTabPanelDrop: function (plugin, container, dragCmp, startIdx, idx, eOpts) {
		var tabs = container.items.items,
			items_indexes = {};

		tabs.forEach(function (tab, i) {
			items_indexes[tab.card.itemId] = i;
		});

		this.onMedicalPanelIndexChange(items_indexes);

		this.reorderEnvounterMedicalToolbar(startIdx, idx);

	},

	reorderEnvounterMedicalToolbar: function (startIdx, idx) {

		say('reorderEnvounterMedicalToolbar');
		say(startIdx);
		say(idx);

		var toobar = this.getEncounterMedicalToolbar(),
			removeBtn = toobar.remove(startIdx, false);

		toobar.insert(idx, removeBtn);
		toobar.doLayout();
	},

	getMedicalTabButtons: function () {

		var buttons = [],
			index,
			outofindex = 100;

		if(a('access_patient_immunizations')) {
			index = this.items_indexes['immunization'] !== undefined ? this.items_indexes['immunization'] : outofindex++;

			Ext.Array.insert(buttons, index, [
				{
					text: _('vaccs') + ' ',
					action: 'immunization',
					margin: '0 3 0 0',
					tooltip: _('vaccines_immunizations'),
					style: {
						backgroundColor: g('immunizations_tab_color'),
						backgroundImage: 'none'
					},
					acl: a('access_patient_immunizations')
				}
			]);
		}

		if(a('access_patient_allergies')) {
			index = this.items_indexes['allergies'] !== undefined ? this.items_indexes['allergies'] : outofindex++;
			Ext.Array.insert(buttons, index, [
				{
					text: _('al') + ' ',
					action: 'allergies',
					margin: '0 3 0 0',
					tooltip: _('allergies'),
					style: {
						backgroundColor: g('allergies_tab_color'),
						backgroundImage: 'none'
					},
					acl: a('access_patient_allergies')
				}
			]);
		}

		if(a('access_active_problems')) {
			index = this.items_indexes['activeproblems'] !== undefined ? this.items_indexes['activeproblems'] : outofindex++;
			Ext.Array.insert(buttons, index, [
				{
					text: _('act_prob') + ' ',
					action: 'activeproblems',
					margin: '0 3 0 0',
					tooltip: _('active_problems'),
					style: {
						backgroundColor: g('problems_tab_color'),
						backgroundImage: 'none'
					},
					acl: a('access_active_problems')
				}
			]);
		}

		if(a('access_family_history')) {
			index = this.items_indexes['familyhistory'] !== undefined ? this.items_indexes['familyhistory'] : outofindex++;
			Ext.Array.insert(buttons, index, [
				{
					text: _('fam_hx') + ' ',
					action: 'familyhistory',
					margin: '0 3 0 0',
					tooltip: _('family_history'),
					style: {
						backgroundColor: g('family_history_tab_color'),
						backgroundImage: 'none'
					},
					acl: a('access_family_history')
				}
			]);
		}

		if(a('access_procedures_history')) {
			index = this.items_indexes['procedureshistory'] !== undefined ? this.items_indexes['procedureshistory'] : outofindex++;
			Ext.Array.insert(buttons, index, [
				{
					text: _('proc_hx') + ' ',
					action: 'procedureshistory',
					margin: '0 3 0 0',
					tooltip: _('procedure_history'),
					style: {
						backgroundColor: g('procedure_history_tab_color'),
						backgroundImage: 'none'
					},
					acl: a('access_procedures_history')
				}
			]);
		}

		if(a('access_patient_advance_directive')) {
			index = this.items_indexes['advancedirectives'] !== undefined ? this.items_indexes['advancedirectives'] : outofindex++;
			Ext.Array.insert(buttons, index, [
				{
					text: _('adv_dir') + ' ',
					action: 'advancedirectives',
					margin: '0 3 0 0',
					tooltip: _('advance_directives'),
					style: {
						backgroundColor: g('advance_directive_tab_color'),
						backgroundImage: 'none'
					},
					acl: a('access_patient_advance_directive')
				}
			]);
		}

		if(a('access_patient_medications')) {
			index = this.items_indexes['medications'] !== undefined ? this.items_indexes['medications'] : outofindex++;
			Ext.Array.insert(buttons, index, [
				{
					text: _('meds') + ' ',
					action: 'medications',
					margin: '0 3 0 0',
					tooltip: _('medications'),
					style: {
						backgroundColor: g('medications_tab_color'),
						backgroundImage: 'none'
					},
					acl: a('access_patient_medications')
				}
			]);
		}

		if(a('access_patient_results')) {
			index = this.items_indexes['laboratories'] !== undefined ? this.items_indexes['laboratories'] : outofindex++;
			Ext.Array.insert(buttons, index, [
				{
					text: _('res') + ' ',
					action: 'laboratories',
					margin: '0 3 0 0',
					tooltip: _('results'),
					style: {
						backgroundColor: g('results_tab_color'),
						backgroundImage: 'none'
					},
					acl: a('access_patient_results')
				}
			]);
		}

		if(a('access_patient_social_history')) {
			index = this.items_indexes['social'] !== undefined ? this.items_indexes['social'] : outofindex++;
			Ext.Array.insert(buttons, index, [
				{
					text: _('soc_hx') + ' ',
					action: 'social',
					margin: '0 3 0 0',
					tooltip: _('social_history'),
					style: {
						backgroundColor: g('social_history_tab_color'),
						backgroundImage: 'none'
					},
					acl: a('access_patient_social_history')
				}
			]);
		}

		if(a('access_patient_functional_status')) {
			index = this.items_indexes['functionalstatus'] !== undefined ? this.items_indexes['functionalstatus'] : outofindex++;
			Ext.Array.insert(buttons, index, [
				{
					text: _('func_stat') + ' ',
					action: 'functionalstatus',
					margin: '0 3 0 0',
					tooltip: _('functional_status'),
					style: {
						backgroundColor: g('functional_status_tab_color'),
						backgroundImage: 'none'
					},
					acl: a('access_patient_functional_status')
				}
			]);
		}

		if(a('access_patient_referrals')) {
			index = this.items_indexes['referrals'] !== undefined ? this.items_indexes['referrals'] : outofindex++;
			Ext.Array.insert(buttons, index, [
				{
					text: _('refs') + ' ',
					action: 'referrals',
					margin: '0 3 0 0',
					tooltip: _('referrals'),
					style: {
						backgroundColor: g('referrals_tab_color'),
						backgroundImage: 'none'
					},
					acl: a('access_patient_referrals')
				}
			]);
		}

		if(a('access_patient_implantable_devices')) {
			index = this.items_indexes['ImplantableDeviceGrid'] !== undefined ? this.items_indexes['ImplantableDeviceGrid'] : outofindex++;
			Ext.Array.insert(buttons, index, [
				{
					text: _('imp_devs') + ' ',
					action: 'ImplantableDeviceGrid',
					margin: '0 3 0 0',
					tooltip: _('implantable_devices'),
					style: {
						backgroundColor: g('implantable_devices_tab_color'),
						backgroundImage: 'none'
					},
					acl: a('access_patient_implantable_devices')
				}
			]);
		}

		if(a('access_patient_pain_scales')){
			index = this.items_indexes['PainScaleGrid'] !== undefined ? this.items_indexes['PainScaleGrid'] : outofindex++;
			Ext.Array.insert(buttons, index, [
				{
					text: _('pain_scale') + ' ',
					action: 'PainScaleGrid',
					margin: '0 3 0 0',
					tooltip: _('pain_scale'),
					style: {
						backgroundColor: g('pain_scales_tab_color'),
						backgroundImage: 'none'
					},
					acl: a('access_patient_implantable_devices')
				}
			]);

		}

		if(a('access_patient_doctors_notes')) {
			index = this.items_indexes['DoctorsNotes'] !== undefined ? this.items_indexes['DoctorsNotes'] : outofindex++;
			Ext.Array.insert(buttons, index, [
				{
					text: _('doc_nt'),
					action: 'DoctorsNotes',
					margin: '0 3 0 0',
					tooltip: _('doctors_notes'),
					style: {
						backgroundColor: g('doctors_notes_tab_color'),
						backgroundImage: 'none'
					},
					acl: a('access_patient_doctors_notes')
				}
			]);
		}

		if(a('access_patient_lab_orders')) {
			index = this.items_indexes['LabOrders'] !== undefined ? this.items_indexes['LabOrders'] : outofindex++;
			Ext.Array.insert(buttons, index, [
				{
					text: _('lab_orders'),
					action: 'LabOrders',
					margin: '0 3 0 0',
					style: {
						backgroundColor: g('lab_orders_tab_color'),
						backgroundImage: 'none'
					},
					acl: a('access_patient_lab_orders')
				}
			]);
		}

		if(a('access_patient_rad_orders')) {
			index = this.items_indexes['RadOrders'] !== undefined ? this.items_indexes['RadOrders'] : outofindex++;
			Ext.Array.insert(buttons, index, [
				{
					text: _('xray_ct_orders'),
					action: 'RadOrders',
					margin: '0 3 0 0',
					style: {
						backgroundColor: g('rad_orders_tab_color'),
						backgroundImage: 'none'
					},
					acl: a('access_patient_rad_orders')
				}
			]);
		}

		if(a('access_patient_rx_orders')) {
			index = this.items_indexes['RxOrderGrid'] !== undefined ? this.items_indexes['RxOrderGrid'] : outofindex++;
			Ext.Array.insert(buttons, index, [
				{
					text: _('rx_orders'),
					action: 'RxOrderGrid',
					margin: '0 3 0 0',
					style: {
						backgroundColor: g('rx_orders_tab_color'),
						backgroundImage: 'none'
					},
					acl: a('access_patient_rx_orders')
				}
			]);
		}



		Ext.Array.push(buttons, ['-',
			{
				text: _('documents'),
				itemId: 'EncounterPatientDocumentsBtn',
				icon: 'resources/images/icons/icoDOC-16.png',
				//acl: a('access_patient_rx_orders')
			}
		]);

		return buttons;
	},

	getMedicalTabPanelItems: function () {
		var tapPanelItems = [],
			index = 0,
			outofindex = 100;

		if(a('access_patient_immunizations')){

			index = this.items_indexes['immunization'] !== undefined ? this.items_indexes['immunization'] : outofindex++;

			Ext.Array.insert(tapPanelItems, index, [{
				xtype:'patientimmunizationspanel',
				itemId: 'immunization',
				tabConfig: {
					tooltip: _('vaccines_immunizations'),
					style: {
						backgroundColor: g('immunizations_tab_color'),
						backgroundImage: 'none'
					}
				}
			}]);
		}

		if(a('access_patient_allergies')){

			index = this.items_indexes['allergies'] !== undefined ? this.items_indexes['allergies'] : outofindex++;

			Ext.Array.insert(tapPanelItems, index, [{
				xtype: 'patientallergiespanel',
				itemId: 'allergies',
				tabConfig: {
					tooltip: _('allergies'),
					style: {
						backgroundColor: g('allergies_tab_color'),
						backgroundImage: 'none'
					}
				}
			}]);
		}

		if(a('access_active_problems')){

			index = this.items_indexes['activeproblems'] !== undefined ? this.items_indexes['activeproblems'] : outofindex++;

			Ext.Array.insert(tapPanelItems, index, [{
				xtype: 'patientactiveproblemspanel',
				itemId: 'activeproblems',
				tabConfig: {
					tooltip: _('active_problems'),
					style: {
						backgroundColor: g('problems_tab_color'),
						backgroundImage: 'none'
					}
				}
			}]);
		}

		if(a('access_family_history')){

			index = this.items_indexes['familyhistory'] !== undefined ? this.items_indexes['familyhistory'] : outofindex++;

			Ext.Array.insert(tapPanelItems, index, [{
				xtype: 'patientfamilyhistorypanel',
				itemId: 'familyhistory',
				tabConfig: {
					tooltip: _('family_history'),
					style: {
						backgroundColor: g('family_history_tab_color'),
						backgroundImage: 'none'
					}
				}
			}]);
		}

		if(a('access_procedures_history')){

			index = this.items_indexes['procedureshistory'] !== undefined ? this.items_indexes['procedureshistory'] : outofindex++;

			Ext.Array.insert(tapPanelItems, index, [{
				xtype: 'patientprocedureshistorygrid',
				itemId: 'procedureshistory',
				tabConfig: {
					tooltip: _('procedure_history'),
					style: {
						backgroundColor: g('procedure_history_tab_color'),
						backgroundImage: 'none'
					}
				}
			}]);
		}

		if(a('access_patient_advance_directive')){

			index = this.items_indexes['advancedirectives'] !== undefined ? this.items_indexes['advancedirectives'] : outofindex++;

			Ext.Array.insert(tapPanelItems, index, [{
				xtype: 'patientadvancedirectivepanel',
				itemId: 'advancedirectives',
				tabConfig: {
					tooltip: _('advance_directives'),
					style: {
						backgroundColor: g('advance_directive_tab_color'),
						backgroundImage: 'none'
					}
				}
			}]);
		}

		if(a('access_patient_medications')){

			index = this.items_indexes['medications'] !== undefined ? this.items_indexes['medications'] : outofindex++;

			Ext.Array.insert(tapPanelItems, index, [{
				xtype:'patientmedicationspanel',
				itemId: 'medications',
				tabConfig: {
					tooltip: _('medications'),
					style: {
						backgroundColor: g('medications_tab_color'),
						backgroundImage: 'none'
					}
				}
			}]);
		}

		if(a('access_patient_results')){

			index = this.items_indexes['laboratories'] !== undefined ? this.items_indexes['laboratories'] : outofindex++;

			Ext.Array.insert(tapPanelItems, index, [{
				xtype:'patientresultspanel',
				itemId: 'laboratories',
				tabConfig: {
					tooltip: _('results'),
					style: {
						backgroundColor: g('results_tab_color'),
						backgroundImage: 'none'
					}
				}
			}]);
		}

		if(a('access_patient_social_history')){

			index = this.items_indexes['social'] !== undefined ? this.items_indexes['social'] : outofindex++;

			Ext.Array.insert(tapPanelItems, index, [{
				xtype: 'patientsocialpanel',
				itemId: 'social',
				tabConfig: {
					tooltip: _('social_history'),
					style: {
						backgroundColor: g('social_history_tab_color'),
						backgroundImage: 'none'
					}
				}
			}]);
		}

		if(a('access_patient_functional_status')){

			index = this.items_indexes['functionalstatus'] !== undefined ? this.items_indexes['functionalstatus'] : outofindex++;

			Ext.Array.insert(tapPanelItems, index, [{
				xtype: 'patientcognitiveandfunctionalstatuspanel',
				itemId: 'functionalstatus',
				tabConfig: {
					tooltip: _('functional_status'),
					style: {
						backgroundColor: g('functional_status_tab_color'),
						backgroundImage: 'none'
					}
				}
			}]);
		}

		if(a('access_patient_referrals')){

			index = this.items_indexes['referrals'] !== undefined ? this.items_indexes['referrals'] : outofindex++;

			Ext.Array.insert(tapPanelItems, index, [{
				xtype: 'patientreferralspanel',
				itemId: 'referrals',
				tabConfig: {
					tooltip: _('referrals'),
					style: {
						backgroundColor: g('referrals_tab_color'),
						backgroundImage: 'none'
					}
				}
			}]);
		}

		if(a('access_patient_implantable_devices')){

			index = this.items_indexes['ImplantableDeviceGrid'] !== undefined ? this.items_indexes['ImplantableDeviceGrid'] : outofindex++;

			Ext.Array.insert(tapPanelItems, index, [{
				xtype:'implantabledevicepanel',
				tabConfig: {
					tooltip: _('implantable_devices'),
					style: {
						backgroundColor: g('implantable_devices_tab_color'),
						backgroundImage: 'none'
					}
				}
			}]);
		}

		if(a('access_patient_pain_scales')){

			index = this.items_indexes['PainScaleGrid'] !== undefined ? this.items_indexes['PainScaleGrid'] : outofindex++;

			Ext.Array.insert(tapPanelItems, index, [{
				xtype:'painscalepanel',
				tabConfig: {
					tooltip: _('pain_scales'),
					style: {
						backgroundColor: g('pain_scales_tab_color'),
						backgroundImage: 'none'
					}
				}
			}]);
		}

		// if(a('access_patient_psy_behavioral')){
		// 	tapPanelItems = Ext.Array.push(tapPanelItems, {
		// 		xtype:'socialpsychologicalbehavioralpanel',
		// 		tabConfig: {
		// 			tooltip: _('social_psychological_behavioral'),
		// 			style: {
		// 				backgroundColor: g('psy_behavioral_tab_color'),
		// 				backgroundImage: 'none'
		// 			}
		// 		}
		// 	});
		// }

		if(a('access_patient_doctors_notes')){

			index = this.items_indexes['DoctorsNotes'] !== undefined ? this.items_indexes['DoctorsNotes'] : outofindex++;

			Ext.Array.insert(tapPanelItems, index, [{
				xtype: 'patientdoctorsnotepanel',
				tabConfig: {
					tooltip: _('doctors_notes'),
					style: {
						backgroundColor: g('doctors_notes_tab_color'),
						backgroundImage: 'none'
					}
				}
			}]);
		}

		if(a('access_patient_lab_orders')){

			index = this.items_indexes['LabOrders'] !== undefined ? this.items_indexes['LabOrders'] : outofindex++;

			Ext.Array.insert(tapPanelItems, index, [{
				xtype: 'patientlaborderspanel',
				tabConfig: {
					tooltip: _('laboratory_orders'),
					style: {
						backgroundColor: g('lab_orders_tab_color'),
						backgroundImage: 'none'
					}
				}
			}]);
		}

		if(a('access_patient_rad_orders')){

			index = this.items_indexes['RadOrders'] !== undefined ? this.items_indexes['RadOrders'] : outofindex++;

			Ext.Array.insert(tapPanelItems, index, [{
				xtype: 'patientradorderspanel',
				tabConfig: {
					tooltip: _('radiology_orders'),
					style: {
						backgroundColor: g('rad_orders_tab_color'),
						backgroundImage: 'none'
					}
				}
			}]);
		}

		if(a('access_patient_rx_orders')){

			index = this.items_indexes['RxOrderGrid'] !== undefined ? this.items_indexes['RxOrderGrid'] : outofindex++;

			Ext.Array.insert(tapPanelItems, index, [{
				xtype:'patientrxorderspanel',
				tabConfig: {
					tooltip: _('medication_orders'),
					style: {
						backgroundColor: g('rx_orders_tab_color'),
						backgroundImage: 'none'
					}
				}
			}]);
		}

		return tapPanelItems;
	},

	onNavKey: function(e, key){
		if(!app.patient.pid) {
			app.msg(_('oops'), _('patient_error'), true);
			return;
		}
		var win = this.getMedicalWindow().show();

		switch(key){
			case e.ONE:
				win.cardSwitch('immunization');
				break;
			case e.TWO:
				win.cardSwitch('allergies');
				break;
			case e.THREE:
				win.cardSwitch('activeproblems');
				break;
			case e.FOUR:
				win.cardSwitch('medications');
				break;
			case e.FIVE:
				win.cardSwitch('laboratories');
				break;
			case e.SIX:
				win.cardSwitch('socialhistory');
				break;
			case e.SEVEN:
				win.cardSwitch('referrals');
				break;
		}
	},

	onPanelShow:function(panel){
		this.setWindowTitle(panel.title);
	},

	setWindowTitle:function(title){
		this.getMedicalWindow().setTitle(
            app.patient.name +
            ' (' + title + ') ' +
            (app.patient.readOnly ? '-  <span style="color:red">[Read Mode]</span>' :'')
        );
	}


});
