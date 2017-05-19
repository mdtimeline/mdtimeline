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

Ext.define('App.controller.patient.Dictation', {
	extend: 'Ext.app.Controller',
	requires: [

	],
	refs: [
		{
			ref: 'DictationPanel',
			selector: '#DictationPanel'
		},
		{
			ref: 'DictationPanelSaveBtn',
			selector: '#DictationPanelSaveBtn'
		}
	],

	encounter: null,
	dictationStore: null,

	init: function(){
		var me = this;

		me.control({
			'viewport':{
				encounterload: me.onViewportEncounterLoad
			},
			'#DictationPanel':{
				activate: me.onDictationPanelActivate
			},
			'#DictationPanelSaveBtn':{
				click: me.onDictationPanelSaveBtnClick
			}
		});
	},

	onViewportEncounterLoad: function(encounter){
		this.encounter = encounter;
	},

	onDictationPanelActivate: function(){

		var me = this,
			form = me.getDictationPanel().getForm();

		me.encounter.dictation().load({
			callback: function(records){

				if(records.length > 0){
					form.loadRecord(records[0]);
					return;
				}

				form.loadRecord(
					Ext.create('App.model.patient.Dictation',{
						pid: me.encounter.get('pid'),
						eid: me.encounter.get('eid'),
						date: new Date(),
						uid: app.user.id
					})
				);
			}
		});
	},

	onDictationPanelSaveBtnClick: function(){

		var me = this,
			form = me.getDictationPanel().getForm(),
			record = form.getRecord(),
			values = form.getValues();

		if(!form.isValid()) return;


		say(record);
		say(values);

		record.set(values);

		record.save({
			callback: function(){
				app.msg(_('sweet'), _('record_saved'));
			}
		});

	},

});