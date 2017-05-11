/**
 * Created by JetBrains PhpStorm.
 * User: Ernesto J. Rodriguez (Certun)
 * File:
 * Date: 11/1/11
 * Time: 12:37 PM
 */
Ext.define('App.ux.form.fields.plugin.MacroRecorder', {
	extend: 'Ext.AbstractPlugin',
	alias: 'plugin.macrorecorder',

	init: function (field) {
		field.recording = false;
		field.enableKeyEvents = true;
		field.on('keydown', this.onKeyDown, this);
		field.on('keyup', this.onKeyUp, this);
	},
	
	metaKey: false,

	onKeyUp: function (field, e) {
		if(e.getKey() == 91){
			this.metaKey = false;
		}
	},

	onKeyDown: function (field, e) {

		e.preventDefault();

		var key = e.getKey(),
			char = String.fromCharCode(e.getKey()),
			value = [];

		if(key == 91 || key == 224) {
			this.metaKey = true;
		}
		if(key == 8){
			field.setValue('');
			return;
		}
		if(e.isSpecialKey() || char.search(/[A-Z0-9]/) === -1){
			return;
		}
		if(this.metaKey){
			value.push('META');
		}
		if(e.altKey){
			value.push('ALT');
		}
		if(e.ctrlKey && !this.metaKey){
			value.push('CTRL');
		}
		if(e.shiftKey){
			value.push('SHIFT');
		}
		if(value.length == 0){
			return;
		}

		value = value.sort();
		value.push(char);
		field.setValue(value.join('-'));
	}
});