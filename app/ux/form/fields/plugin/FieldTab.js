Ext.define('App.ux.form.fields.plugin.FieldTab', {
	extend: 'Ext.AbstractPlugin',
	alias: 'plugin.fieldtab',

	loop_around: true,
	SHIFT_DOWN: false,
	ALT_DOWN: false,
	CTRL_DOWN: false,

	keys_down_time: {},
	keys_up_time: {},
	is_buffing: false,
	start_index: -1,
	buff: '',

	properties: [
		'boxSizing',
		'width',  // on Chrome and IE, exclude the scrollbar, so the mirror div wraps exactly as the textarea does
		'height',
		'overflowX',
		'overflowY',  // copy the scrollbar for IE

		'borderTopWidth',
		'borderRightWidth',
		'borderBottomWidth',
		'borderLeftWidth',

		'paddingTop',
		'paddingRight',
		'paddingBottom',
		'paddingLeft',

		// https://developer.mozilla.org/en-US/docs/Web/CSS/font
		'fontStyle',
		'fontVariant',
		'fontWeight',
		'fontStretch',
		'fontSize',
		'lineHeight',
		'fontFamily',

		'textAlign',
		'textTransform',
		'textIndent',
		'textDecoration',  // might not make a difference, but better be safe

		'letterSpacing',
		'wordSpacing'
	],

	/**
	 *
	 * @param field
	 */
	init: function(field){
		var me = this;

		field.enableKeyEvents = true;

		me.last_key_time = new Date().getTime();
		me.working_textfield = null;

		field.on('render', function (f) {
			f.inputEl.on('keydown', me.onKeyDown, me);
			f.inputEl.on('keyup', me.onKeyUp, me);
		});
	},

	doPrevField: function(input){
		var me = this,
			cursor = me.getCursorPos(input);
		me.getPrevField(cursor, input);
	},

	doNextField: function(input){
		var me = this,
			cursor = me.getCursorPos(input);
		me.getNextField(cursor, input);
	},

	onKeyDown: function(e, t, eOpts){
		var key = e.getKey();

		if(!this.is_buffing){
			this.keys_down_time[key] = new Date().getTime();
		}

		if(key == e.SHIFT){
			e.preventDefault();
			this.SHIFT_DOWN = true;

		}else if(key == e.ALT){
			e.preventDefault();
			this.ALT_DOWN = true;

		}else if(key == e.CTRL){
			e.preventDefault();
			this.CTRL_DOWN = true;

		}else if(key == e.TAB){
			e.preventDefault();
		}
	},

	onKeyUp: function(e, t, eOpts){
		var key = e.getKey(),
			code = e.getCharCode();

		say('onKeyUp');

		if(!this.is_buffing){
			this.keys_up_time[key] = new Date().getTime();
		}

		if(this.enableDragonTypingFix){
			if(key != 144 && !e.isSpecialKey() && !e.isNavKeyPress()){
				if(this.isDragon(key)){
					//say('is dragon...');
					if(code == 190){
						code = 46;
					}
					this.is_buffing = true;
					this.buff += String.fromCharCode(code);
					if(this.start_index == -1) this.start_index = this.getCursorPos(t).start - 1;
					this.doTypingFixBuffer(key, t);
				}
			}
		}

		if(key == e.SHIFT){
			e.preventDefault();
			this.SHIFT_DOWN = false;

		}else if(key == e.ALT){
			e.preventDefault();
			this.ALT_DOWN = false;

		}else if(key == e.CTRL){
			e.preventDefault();
			this.CTRL_DOWN = false;

		}else if((key == e.TAB && this.SHIFT_DOWN) || (key == e.F5)){
			e.preventDefault();
			if(this.hasFields(t)) {
				this.doPrevField(t);
			}

		}else if(key == e.TAB || (key == e.F6)){
			e.preventDefault();
			if(this.hasFields(t)) {
				this.doNextField(t);
			}
		}
	},

	isDragon: function(key){
		//say((this.keys_up_time[key] - this.keys_down_time[key]));
		return this.is_buffing || (this.keys_up_time[key] - this.keys_down_time[key]) <= 15;
	},

	hasFields: function(input){
		return input.value.search(/\[.*?]/) != -1;
	},

	getPrevField: function(cursor, input){
		var str = input.value.substr(0, cursor.start),
			marches = str.match(/\[.*?]/g),
			start,
			end;

		if(marches == null || marches.length === 0){
			if(this.loop_around){
				this.getPrevField({start: input.value.length, end: input.value.length}, input);
			}
			return;
		}

		start = str.lastIndexOf(marches[marches.length - 1]);
		end = str.substr(start).search(/(](?=([^\]])))|(]$)/) + start + 1;

		this.setCursorPos(input, start, end);
		this.setScroll(input, start, end);
	},

	getNextField: function(cursor, input){
		var str = input.value.substr(cursor.end),
			start = str.search(/\[.*]/),
			end = str.substr(start).search(/(](?=([^\]])))|(]$)/) + 1;

		if(start == -1){
			if(this.loop_around){
				this.getNextField({start: 0, end: 0}, input);
			}
			return;
		}

		start = cursor.end + start;
		end = start + end;
		this.setCursorPos(input, start, end);
		this.setScroll(input, start, end);
	},

	setScroll: function(input, start, end){
		var coordinates = this.getCaretCoordinates(input, start);
		input.scrollTop = coordinates[3] - 600;
	},

	getCaretCoordinates: function (element, position) {

		var rect = element.getBoundingClientRect();

		var isFirefox = !(window.mozInnerScreenX == null);
		//var mirrorDivDisplayCheckbox = document.getElementById('mirrorDivDisplay');
		var mirrorDiv, computed, style;

		// mirrored div
		mirrorDiv = document.getElementById(element.nodeName + '--mirror-div');
		if (!mirrorDiv) {
			mirrorDiv = document.createElement('div');
			mirrorDiv.id = element.nodeName + '--mirror-div';
			document.body.appendChild(mirrorDiv);
		}

		style = mirrorDiv.style;
		computed = getComputedStyle(element);

		// default textarea styles
		style.whiteSpace = 'pre-wrap';
		if (element.nodeName !== 'INPUT')
			style.wordWrap = 'break-word';  // only for textarea-s

		// position off-screen
		style.position = 'absolute';  // required to return coordinates properly
		style.top = element.offsetTop + parseInt(computed.borderTopWidth) + 'px';
		style.left = "0";
		style.visibility = 'hidden';  // not 'display: none' because we want rendering

		// transfer the element's properties to the div
		this.properties.forEach(function (prop) {
			style[prop] = computed[prop];
		});

		if (isFirefox) {
			style.width = parseInt(computed.width) - 2 + 'px';  // Firefox adds 2 pixels to the padding - https://bugzilla.mozilla.org/show_bug.cgi?id=753662
			// Firefox lies about the overflow property for textareas: https://bugzilla.mozilla.org/show_bug.cgi?id=984275
			if (element.scrollHeight > parseInt(computed.height))
				style.overflowY = 'scroll';
		} else {
			style.overflow = 'hidden';  // for Chrome to not render a scrollbar; IE keeps overflowY = 'scroll'
		}

		mirrorDiv.textContent = element.value.substring(0, position);
		// the second special handling for input type="text" vs textarea: spaces need to be replaced with non-breaking spaces - http://stackoverflow.com/a/13402035/1269037
		if (element.nodeName === 'INPUT')
			mirrorDiv.textContent = mirrorDiv.textContent.replace(/\s/g, "\u00a0");

		var span = document.createElement('span');
		// Wrapping must be replicated *exactly*, including when a long word gets
		// onto the next line, with whitespace at the end of the line before (#7).
		// The  *only* reliable way to do that is to copy the *entire* rest of the
		// textarea's content into the <span> created at the caret position.
		// for inputs, just '.' would be enough, but why bother?
		span.textContent = element.value.substring(position) || '.';  // || because a completely empty faux span doesn't render at all
		span.style.backgroundColor = "lightgrey";
		mirrorDiv.appendChild(span);

		var left = span.offsetLeft + parseInt(computed['borderLeftWidth']) + rect.left - 20;
		var top = span.offsetTop + parseInt(computed['borderTopWidth']) + rect.top + 30;

		return [
			left,
			top - element.scrollTop,
			left,
			top
		];
	},

	getCursorPos: function(input){
		if("selectionStart" in input && document.activeElement == input){
			return {
				start: input.selectionStart,
				end: input.selectionEnd
			};
		}else if(input.createTextRange){
			var sel = document.selection.createRange();
			if(sel.parentElement() === input){
				var rng = input.createTextRange();
				rng.moveToBookmark(sel.getBookmark());
				for(var len = 0; rng.compareEndPoints("EndToStart", rng) > 0; rng.moveEnd("character", -1)){
					len++;
				}
				rng.setEndPoint("StartToStart", input.createTextRange());
				for(var pos = {
					start: 0,
					end: len
				}; rng.compareEndPoints("EndToStart", rng) > 0; rng.moveEnd("character", -1)){
					pos.start++;
					pos.end++;
				}
				return pos;
			}
		}
		return -1;
	},

	setCursorPos: function(input, start, end){
		if(arguments.length < 3) end = start;
		if("selectionStart" in input){
			setTimeout(function(){
				input.selectionStart = start;
				input.selectionEnd = end;
			}, 1);
		}else if(input.createTextRange){
			var rng = input.createTextRange();
			rng.moveStart("character", start);
			rng.collapse();
			rng.moveEnd("character", end - start);
			rng.select();
		}
	}

});