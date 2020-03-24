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

Ext.define('App.controller.patient.DecisionSupport', {
	extend: 'Ext.app.Controller',
	requires: [

	],
	refs: [
		{
			ref: 'DecisionSupportWarningPanel',
			selector: '#DecisionSupportWarningPanel'
		},
		{
			ref: 'MedicalDecisionSupportWarningPanel',
			selector: '#MedicalDecisionSupportWarningPanel'
		},
		{
			ref: 'MedicalDecisioMedicalWindownSupportWarningPanel',
			selector: '#MedicalWindow'
		}
	],

	init: function(){
		var me = this;
		me.control({
			'viewport':{
				beforeencounterload: me.onBeforeEncounterLoad
			},
			'#MedicalWindow':{
				show: me.onMedicalWindowShow
			},
			'#DecisionSupportWarningPanelCloseBtn':{
				click: me.DecisionSupportWarningPanelCloseBtnClick
			}
		});

	},

	DecisionSupportWarningPanelCloseBtnClick: function(btn){
		var warning = btn.up('decisionsupportwarningpanel');
		warning.collapse();
		warning.hide();
		warning.removeAll();
	},

	onBeforeEncounterLoad: function(){
		this.getDecisionSupportAlerts();
	},

	onMedicalWindowShow: function(){
		this.getDecisionSupportAlerts();
	},

	getDecisionSupportAlerts:function(){
        var btn,
            warning,
	        win_warning,
            i;

		warning = this.getDecisionSupportWarningPanel();
		win_warning = this.getMedicalDecisionSupportWarningPanel();

		if(!warning && !win_warning) return;

		if(warning){
			// warning.collapse();
			warning.hide();
			warning.removeAll();
		}

		if(win_warning) {
			// win_warning.collapse();
			win_warning.hide();
			win_warning.removeAll();
		}

		DecisionSupport.getAlerts({ pid: app.patient.pid, alertType: 'P' }, function(results){

			for(i=0; i < results.length; i++){

				var cls = 'decision-support-btn',
					reference_type = results[i].reference_type,
					icon = results[i].reference != '' ? 'resources/images/icons/icohelp.png' : null,
					tooltip = null;

				if(icon){
					if(reference_type == 'D'){
						icon = 'resources/images/icons/icohelpYellow.png';
						tooltip = 'Diagnostic and Therapeutic Reference';
					}else if(reference_type == 'E'){
						icon = 'resources/images/icons/icohelpPurple.png';
						tooltip = 'Evidence-Based CDS Intervention';
					}
				}

				btn = {
					xtype: 'button',
					margin: '2 5',
					icon: icon,
					text: results[i].description,
					result: results[i],
					cls: cls,
					tooltip: tooltip,
					handler: function(btn){
						if(btn.result.reference != ''){
							window.open(btn.result.reference, "_blank", "toolbar=no, scrollbars=yes, resizable=yes, top=10, left=10, width=1000, height=600");
						}else{
							app.msg(_('oops'), _('no_reference_provided'), true);
						}
					}
				};

				if(warning) {
					warning.add(btn);
				}
				if(win_warning) {
					win_warning.add(btn);
				}
			}

			if(results.length > 0){
				if(warning) {
					warning.show();
					// warning.expand();
				}
				if(win_warning) {
					win_warning.show();
					// win_warning.expand();
				}
			}

		});
	}

});
