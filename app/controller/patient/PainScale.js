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

Ext.define('App.controller.patient.PainScale', {
	extend: 'Ext.app.Controller',
	requires: [

	],
	refs: [
		{
			ref: 'PainScaleGrid',
			selector: '#PainScaleGrid'
		},
		{
			ref: 'PainScaleGridAddBtn',
			selector: '#PainScaleGridAddBtn'
		},
		{
			ref: 'PainScaleReviewBtn',
			selector: '#PainScaleReviewBtn'
		}
	],

	init: function(){
		var me = this;
		me.control({
			'#PainScaleGrid':{
				activate: me.onPainScaleGridActivate,
				validateedit: me.onPainScaleGridValidateEdit,
				beforeedit: me.onPainScaleGridBeforeEdit
			},
			'#PainScaleGridAddBtn':{
				click: me.onPainScaleGridAddBtnClick
			},
			'#PainScaleReviewBtn':{
				click: me.onPainScaleReviewBtnClick
			}
		});
	},

	onPainScaleGridActivate: function(grid){
		var store = grid.getStore();
		store.clearFilter(true);
		store.filter([
			{
				property: 'pid',
				value: app.patient.pid
			}
		]);
	},

	onPainScaleGridAddBtnClick: function(btn){
		var grid = btn.up('grid'),
			store = grid.getStore();

		grid.editingPlugin.cancelEdit();
		var records = store.insert(0, {
			pid: app.patient.pid,
			eid: app.patient.eid,
			create_date: new Date(),
			created_uid: app.user.id,
			update_date: new Date(),
			update_uid: app.user.id,
			pain_scale: 0
		});
		grid.editingPlugin.startEdit(records[0], 0);
	},

	onPainScaleReviewBtnClick: function(btn){
		var encounter = this.getController('patient.encounter.Encounter').getEncounterRecord();
		encounter.set({review_pain_scales: true});
		encounter.save({
			success: function(){
				app.msg(_('sweet'), _('items_to_review_save_and_review'));
			},
			failure: function(){
				app.msg(_('oops'), _('items_to_review_entry_error'));
			}
		});
	},

	onPainScaleGridValidateEdit: function (plugin, context) {
		var me = this,
			form = plugin.editor.getForm(),
			anatomical_region_field = form.findField('anatomical_region_code_text'),
			anatomical_region_record = anatomical_region_field.findRecordByValue(anatomical_region_field.getValue()),
			values = form.getValues(),
			record = form.getRecord(),
			store = this.getPainScaleGrid().getStore();

		if(anatomical_region_record){
			values.anatomical_region_code = anatomical_region_record.get('ConceptId');
			values.anatomical_region_code_type = anatomical_region_record.get('CodeType');
			values.update_date = new Date();
		}

		record.set(values);

	},

	onPainScaleGridBeforeEdit:function(plugin, context){
		if(!a('edit_patient_pain_scales')) return false;
	}

});