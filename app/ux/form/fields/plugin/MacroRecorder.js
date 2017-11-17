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

	isFKey: function (key) {
		return key >= 112 && key <= 123;
	},

	onKeyUp: function (field, e) {
		if(e.getKey() == 91){
			this.metaKey = false;
		}
	},

	onKeyDown: function (field, e) {

		e.preventDefault();

		var key = e.getKey(),
			char = String.fromCharCode(e.getKey()),
			isFKey = this.isFKey(key),
			value = [];

		say(key);
		say(char);

		if(key == 91 || key == 224) {
			this.metaKey = true;
		}
		if(key == 8){
			field.setValue('');
			return;
		}

		if(!isFKey && (e.isSpecialKey() || char.search(/[A-Z0-9]/) === -1)){
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

		if(value.length === 0 && !isFKey){
			return;
		}

		value = value.sort();

		if(isFKey){
			switch (key){
				case 112:
					value.push('F1');
					break;
				case 113:
					value.push('F2');
					break;
				case 114:
					value.push('F3');
					break;
				case 115:
					value.push('F4');
					break;
				case 116:
					value.push('F5');
					break;
				case 117:
					value.push('F6');
					break;
				case 118:
					value.push('F7');
					break;
				case 119:
					value.push('F8');
					break;
				case 120:
					value.push('F9');
					break;
				case 121:
					value.push('F10');
					break;
				case 122:
					value.push('F11');
					break;
				case 123:
					value.push('F12');
					break;
			}
		}else {
			value.push(char);
		}

		field.setValue(value.join('-'));
	}
});