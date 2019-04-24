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

Ext.define('App.ux.form.fields.Switch',{
    extend: 'Ext.slider.Single',

    xtype: 'switchfield',
    cls: 'switch-field-slider',
    onValue: 'true',
    offValue: 'false',
    neutralValue: null,
    width: 150,
    value: null,
    increment: 1,
    minValue: 0,
    maxValue: 2,
    animate: false,
    labelWidth: 150,
    mapSliderToValue: {},
    mapValueToSlider: {},
    boxLabel: '',
    boxLabelCls: Ext.baseCSSPrefix + 'form-cb-label',
    boxLabelAlign: 'after',

    initComponent: function () {
        var me = this;

        me.mapSliderToValue['0'] = me.offValue;
        me.mapSliderToValue['1'] = null;
        me.mapSliderToValue['2'] = me.onValue;

        me.mapValueToSlider[me.offValue] = 0;
        me.mapValueToSlider['null'] = 1;
        me.mapValueToSlider[me.onValue] = 2;

        if(me.boxLabel){
            me.on('render', me.doBoxLabel, me);
        }
        me.on('change', me.onSliderValueChange, me);

        me.callParent(arguments);
        me.onSliderValueChange(me, me.mapValueToSlider[me.value]);

    },

    doBoxLabel: function(){
        this.inputRow.appendChild({
            tag: 'td',
            width: this.labelWidth,
            html: Ext.String.format('<label class="{0} {0}-{1}" style="width:100%;">{2}</label>',
                this.boxLabelCls, this.boxLabelAlign, this.boxLabel)
        });
    },

    onSliderValueChange: function(slider, value){
        if(value === 0){
            this.addCls('switch-field-off');
            this.removeCls('switch-field-on');
        }else if(value === 2){
            this.addCls('switch-field-on');
            this.removeCls('switch-field-off');
        }else{
            this.removeCls('switch-field-off');
            this.removeCls('switch-field-on');
        }
    },

    getSubmitData: function() {
        var data = {};
        data[this.getName()] = this.getValue();
        return data;
    },

    getValue: function() {
        // just returns the value of the first thumb, which should be the only one in a single slider
        return this.mapSliderToValue[this.callParent([0])];
    },

    /**
     * Programmatically sets the value of the Slider. Ensures that the value is constrained within the minValue and
     * maxValue.
     * @param {Number} value The value to set the slider to. (This will be constrained within minValue and maxValue)
     * @param {Boolean} [animate] Turn on or off animation
     */
    setValue: function(value, animate) {

         var args = arguments,
            len  = args.length;

        if(len === 0 || args[0] === undefined || value === undefined){
            args[0] = 1;
        }

        var mapValue = this.mapValueToSlider[args[0]];

        if(mapValue !== undefined){
            args[0] = mapValue;
        }

        this.onSliderValueChange(this, args[0]);

        return this.callParent(args);
    },


});