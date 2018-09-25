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

Ext.define('App.controller.patient.FamilyHistory', {
	extend: 'Ext.app.Controller',
	requires: [

	],
	refs: [
		{
			ref: 'FamilyHistoryWindow',
			selector: 'familyhistorywindow'
		},
		{
			ref: 'FamilyHistoryGrid',
			selector: 'patientfamilyhistorypanel'
		},
		{
			ref: 'FamilyHistoryForm',
			selector: '#FamilyHistoryForm'
		},
		{
			ref: 'FamilyHistorySaveBtn',
			selector: '#familyHistorySaveBtn'
		},
		{
			ref: 'FamilyHistoryOtherConditionField',
			selector: '#FamilyHistoryOtherConditionField'
		},
		{
			ref: 'FamilyHistoryOtherReletionField',
			selector: '#FamilyHistoryOtherReletionField'
		},
		{
			ref: 'FamilyHistoryOtherNoteField',
			selector: '#FamilyHistoryOtherNoteField'
		}
	],

	init: function(){
		var me = this;
		me.control({
			'patientfamilyhistorypanel': {
				activate: me.onFamilyHistoryGridActivate
			},
			'#FamilyHistoryForm': {
				formitemsadded: me.onFamilyHistoryFormItemsAdded,
				beforerender: me.onFamilyHistoryFormBeforeRneder
			},
			'#FamilyHistoryGridAddBtn': {
				click: me.onFamilyHistoryGridAddBtnClick
			},
			'#FamilyHistoryWindowSaveBtn': {
				click: me.onFamilyHistoryWindowSaveBtnClick
			},
			'#FamilyHistoryWindowCancelBtn': {
				click: me.onFamilyHistoryWindowCancelBtnClick
			}
		});
	},

	onFamilyHistoryFormItemsAdded: function (form) {

		form.insert(0, {
			xtype: 'fieldset',
			title: _('other'),
			items: [
				{
					xtype: 'snomedlivesearch',
					itemId: 'FamilyHistoryOtherConditionField',
					fieldLabel: _('contition'),
					name: 'other_contition',
					hideLabel: false,
					anchor: '100%'
				},
				{
					xtype: 'gaiaehr.combo',
					itemId: 'FamilyHistoryOtherReletionField',
					fieldLabel: _('relation'),
					name: 'other_relation',
					action: 'relationcmb',
					width: 300,
					list: 109,
					allowBlank: false,
					loadStore: true,
					editable: false,
					resetable: true,
					value: ''
				},
				{
					xtype: 'textfield',
					itemId: 'FamilyHistoryOtherNoteField',
					fieldLabel: _('note'),
					name: 'other_note',
					anchor: '100%'
				}
			]
		});

	},

	onFamilyHistoryFormBeforeRneder: function (form) {


	},

    /**
     * This event is called from the view, and not from the controller itself.
     * @param grid
     * @param record
     */
    onDeactivateRecord: function(grid, record){
        var store,
            params = {
                id: record.data.id,
                pid: record.data.pid,
                eid: record.data.eid
            };

        Ext.Msg.show({
            title:_('removal'),
            msg: _('sure_for_removal'),
            buttons: Ext.Msg.YESNO,
            icon: Ext.Msg.QUESTION,
            fn: function(btn) {
                if (btn === 'yes') {
                    store = grid.getStore();
                    FamilyHistory.deleteFamilyHistory(params, function(response){
	                    store.load({
		                    filters: [
			                    {
				                    property: 'pid',
				                    value: app.patient.pid
			                    }
		                    ]
	                    });
                    });
                }
            }
        });
    },

	onFamilyHistoryGridActivate: function(grid){
		var store = grid.getStore();
		store.clearFilter(true);
		store.load({
			filters: [
				{
					property: 'pid',
					value: app.patient.pid
				}
			]
		});
	},

	onFamilyHistoryGridAddBtnClick:function(){
		this.showFamilyHistoryWindow();
		this.getFamilyHistoryForm().getForm().reset();
	},

	showFamilyHistoryWindow: function(){
		if(!this.getFamilyHistoryWindow()){
			Ext.create('App.view.patient.windows.FamilyHistory');
		}
		this.getFamilyHistoryWindow().show();
        this.getFamilyHistoryWindow().setAutoScroll(true);
	},

	onFamilyHistoryWindowSaveBtnClick:function(){
		var grid = this.getFamilyHistoryGrid(),
			form = this.getFamilyHistoryForm().getForm(),
			store = grid.getStore(),
			values = form.getValues(),
			histories = [],
			isValid =  true,
            foo,
            condition,
			relations;


		var other_condition = this.getFamilyHistoryOtherConditionField(),
			other_relation = this.getFamilyHistoryOtherReletionField(),
			other_note = this.getFamilyHistoryOtherNoteField();

		if(other_condition.getValue() && other_relation.getValue()){

			var condition_record = other_condition.findRecordByValue(other_condition.getValue()),
				relation_record = other_relation.findRecordByValue(other_relation.getValue());

			Ext.Array.push(histories, {
				pid: app.patient.pid,
				eid: app.patient.eid,
				relation: relation_record.get('option_name'),
				relation_code: relation_record.get('code'),
				relation_code_type: relation_record.get('code_type'),
				condition: condition_record.get('Term'),
				condition_code: condition_record.get('ConceptId'),
				condition_code_type: condition_record.get('CodeType'),
				notes: other_note.getValue(),
				create_uid: app.user.id,
				create_date: new Date()
			});
		}

		Ext.Object.each(values, function(key, value){

			if(value === '0~0' || value === '0~0~') return;

			foo = value.split('~');

			if(foo.length === 1) return;

            condition = foo[0].split(':');
            relations = foo[1].split(',');

			if(isValid && relations.length === 0){
				isValid = false;
			}

			relations.forEach(function (relation) {

				relation = relation.split(':');

				Ext.Array.push(histories, {
					pid: app.patient.pid,
					eid: app.patient.eid,
					relation: relation[2],
					relation_code: relation[1],
					relation_code_type: relation[0],
					condition: condition[2],
					condition_code: condition[1],
					condition_code_type: condition[0],
					notes: relation[3] || '',
					create_uid: app.user.id,
					create_date: new Date()
				});
			});
		});

		if(histories.length === 0){
			app.msg(_('oops'), _('no_history_selected'), true);
			return;
		}

		if(!isValid){
			app.msg(_('oops'), _('missing_required_information'), true);
			return;
		}

		store.add(histories);
		store.sync();
		this.getFamilyHistoryWindow().close();
	},

	onFamilyHistoryWindowCancelBtnClick:function(){
		this.getFamilyHistoryWindow().close();
	}

});
