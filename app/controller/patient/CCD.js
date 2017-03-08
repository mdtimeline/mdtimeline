/**
 * mdTimeLine EHR (Electronic Health Records)
 * Copyright (C) 2017 mdTimeLine, Inc.
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

Ext.define('App.controller.patient.CCD', {
	extend: 'Ext.app.Controller',
	requires: [],
	refs: [
		{
			ref: 'PatientCcdPanel',
			selector: 'patientccdpanel'
		},
		{
			ref: 'PatientCcdPanelMiFrame',
			selector: 'patientccdpanel > miframe'
		},
		{
			ref: 'PatientCcdPanelEncounterCmb',
			selector: '#PatientCcdPanelEncounterCmb'
		},
		{
			ref: 'PatientCcdPanelExcludeCheckBoxGroup',
			selector: '#PatientCcdPanelExcludeCheckBoxGroup'
		}
	],

	init: function(){
		var me = this;
		me.control({
			'patientccdpanel': {
				activate: me.onPanelActivate
			},
			'#viewCcdBtn': {
				click: me.onViewCcdBtnClick
			},
			'#archiveCcdBtn': {
				click: me.onArchiveCcdBtnClick
			},
			'#exportCcdBtn': {
				click: me.onExportCcdBtnClick
			},
			'#importCcdBtn': {
				click: me.onImportCcdBtnClick
			},
			'#printCcdBtn': {
				click: me.onPrintCcdBtnClick
			},
            '#PatientCcdPanelEncounterCmb':{
			    select: me.onPatientCcdPanelEncounterCmbSelect
            }
		});

		me.importCtrl = this.getController('patient.CCDImport');
		me.disclosuresCtrl = this.getController('patient.Disclosures');
		me.logCtrl = this.getController('administration.AuditLog');
	},

	eid: null,

	loadPatientEncounters: function(){
		var me = this,
			cmb = me.getPatientCcdPanelEncounterCmb(),
			store = cmb.store;

		if(app.patient.pid == null){
			store.removeAll();
			cmb.reset();
		}else{
			store.load({
				filters: [
					{
						property: 'pid',
						value: app.patient.pid
					}
				]
			});
		}
	},

	onPanelActivate: function(panel){

		if(this.eid === null){
			panel.down('toolbar').down('#PatientCcdPanelEncounterCmb').setVisible(true);
			this.loadPatientEncounters();
		}else{
			panel.down('toolbar').down('#PatientCcdPanelEncounterCmb').setVisible(false);
		}

		this.onViewCcdBtnClick(panel.down('toolbar').down('button'));
	},

	onViewCcdBtnClick: function(btn){

		var eid = this.getEid(btn);

		btn.up('panel').query('miframe')[0].setSrc(
			'dataProvider/CCDDocument.php?' +
            'action=view' +
            '&site=' + window.site +
			'&pid=' + app.patient.pid +
			'&eid=' + eid +
			'&exclude=' + this.getExclusions(btn) +
			'&token=' + app.user.token
		);
        btn.up('panel').query('miframe')[0].el.unmask();

		this.logCtrl.addLog(
			app.patient.pid,
			app.user.id,
			eid,
			'encounters',
			'VIEW',
            eid == null ? 'Patient C-CDA VIEWED' : 'Encounter C-CDA VIEWED'
		);

        TransactionLog.saveExportLog({
            pid: app.patient.pid,
            uid: app.user.id,
            eid: eid,
            event: eid ? 'Patient_CCDA_VIEWED' : 'Encounter_CCDA_VIEWED'
        });
	},

	onArchiveCcdBtnClick: function(btn){

		var eid = this.getEid(btn);

		btn.up('panel').query('miframe')[0].setSrc(
			'dataProvider/CCDDocument.php?' +
            'action=archive&' +
            'site=' + window.site +
			'&pid=' + app.patient.pid +
			'&eid=' + eid +
			'&exclude=' + this.getExclusions(btn) +
			'&token=' + app.user.token
		);
        btn.up('panel').query('miframe')[0].el.unmask();

		this.logCtrl.addLog(
			app.patient.pid,
			app.user.id,
			eid,
			'encounters',
			'ARCHIVE',
            eid == null ? 'Patient C-CDA VIEWED' : 'Encounter C-CDA VIEWED'
		);

        TransactionLog.saveExportLog({
            pid: app.patient.pid,
            uid: app.user.id,
            eid: eid,
            event: eid ? 'Patient_CCDA_ARCHIVED' : 'Encounter_CCDA_ARCHIVED'
        });

	},

	onExportCcdBtnClick: function(btn){

		var eid = this.getEid(btn);

		btn.up('panel').query('miframe')[0].setSrc(
			'dataProvider/CCDDocument.php?action=export&site=' + window.site +
			'&pid=' + app.patient.pid +
			'&eid=' + eid +
			'&exclude=' + this.getExclusions(btn) +
			'&token=' + app.user.token
		);
        btn.up('panel').query('miframe')[0].el.unmask();

		this.logCtrl.addLog(
			app.patient.pid,
			app.user.id,
			eid,
			'encounters',
			'EXPORT',
            eid == null ? 'Patient C-CDA VIEWED' : 'Encounter C-CDA VIEWED'
		);

        TransactionLog.saveExportLog({
            pid: app.patient.pid,
            uid: app.user.id,
            eid: eid,
            event: eid ? 'Patient_CCDA_Exported' : 'Encounter_CCDA_Exported'
        });

		this.disclosuresCtrl.addRawDisclosure({
			pid: app.patient.pid,
			eid: eid,
			uid: app.user.id,
			date: Ext.Date.format(new Date(), 'Y-m-d H:i:s'),
			type: 'clinical_summary',
			recipient: 'patient',
			description: 'Clinical Summary Provided (Exported)',
			active: 1
		});
	},

	onPrintCcdBtnClick: function(btn){
		var cont = btn.up('panel').query('miframe')[0].frameElement.dom.contentWindow;
		cont.focus();
		cont.print();

		var eid = this.getEid(btn);

		this.logCtrl.addLog(
			app.patient.pid,
			app.user.id,
			eid,
			'encounters',
			'PRINT',
            eid == null ? 'Patient C-CDA VIEWED' : 'Encounter C-CDA VIEWED'
		);

        TransactionLog.saveExportLog({
            pid: app.patient.pid,
            uid: app.user.id,
            eid: eid,
            event: eid ? 'Patient_CCDA_PRINTED' : 'Encounter_CCDA_PRINTED'
        });

		this.disclosuresCtrl.addRawDisclosure({
			pid: app.patient.pid,
			eid: eid,
			uid: app.user.id,
			date: Ext.Date.format(new Date(), 'Y-m-d H:i:s'),
			type: 'clinical_summary',
			recipient: 'patient',
			description: 'Clinical Summary Provided (PRINTED)',
			active: 1
		});
	},

	onPatientCcdPanelEncounterCmbSelect: function(cmb, records){

		var eid = this.getEid(cmb);

		cmb.selectedRecord = records[0];

		cmb.up('panel').query('miframe')[0].setSrc(
			'dataProvider/CCDDocument.php?action=view&site=' + window.site +
			'&pid=' + app.patient.pid +
			'&eid=' + eid +
			'&exclude=' + this.getExclusions(cmb) +
			'&token=' + app.user.token
		);
		cmb.up('panel').query('miframe')[0].el.unmask();

		this.logCtrl.addLog(
			app.patient.pid,
			app.user.id,
			eid,
			'encounters',
			'VIEW',
			eid == null ? 'Patient C-CDA VIEWED' : 'Encounter C-CDA VIEWED'
		);

        TransactionLog.saveExportLog({
            pid: app.patient.pid,
            uid: app.user.id,
            eid: eid,
            event: eid ? 'Patient_CCDA_VIEWED' : 'Encounter_CCDA_VIEWED'
        });
	},

	getEid: function(cmp){
		var cmb = cmp.up('toolbar').query('#PatientCcdPanelEncounterCmb')[0];
		return cmb.selectedRecord ? cmb.selectedRecord.data.eid : this.eid;
	},

	cmbReset: function(cmp){
		var cmb = cmp.up('toolbar').query('#PatientCcdPanelEncounterCmb')[0];
		cmb.reset();
		delete cmb.selectedRecord;
	},

	getExclusions: function(cmp){
		var values = cmp.up('toolbar').query('#PatientCcdPanelExcludeCheckBoxGroup')[0].getValue(),
			excludes = values.exclude || [];
		return excludes.join ? excludes.join(',') : excludes;
	},

	onImportCcdBtnClick: function(btn){

		var me = this,
			win = Ext.create('App.ux.form.fields.UploadString');

		win.allowExtensions = ['xml','ccd','cda','ccda'];
		win.on('uploadready', function(comp, stringXml){
			me.getDocumentData(stringXml);
		});

		win.show();
	},

	getDocumentData: function(stringXml){
		var me = this;

		CCDDocumentParse.parseDocument(stringXml, function(ccdData){
			me.importCtrl.validatePosibleDuplicates = false;
			me.importCtrl.CcdImport(ccdData, app.patient.pid);
			me.importCtrl.validatePosibleDuplicates = true;
		});
	}

});
