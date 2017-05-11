Ext.define('App.controller.BrowserHelper', {
	extend: 'Ext.app.Controller',
	requires: [],
	refs: [

	],
	socket: undefined,
	host: 'local.tranextgen.com',
	port: 9595,
	connected: false,
	waiting: false,
	callback: undefined,

	reconnect_interval: 1000 * 5, // 5 seconds
	debug: false,
	initiated: false,

	init: function(){
		var me = this;

		me.connect();

	},

	connect: function () {

		var me = this;

		if(me.socket) return;

		me.socket = new WebSocket('wss://' + me.host +':' + me.port + '/');

		me.socket.onopen = function (event) {
			me.onOpen(event.data);
		};
		me.socket.onclose = function (event) {
			me.onClose(event.data);
		};
		me.socket.onmessage = function (event) {
			me.onMessage(event.data);
		};
		me.socket.onerror = function (event) {
			me.onError(event.data);
		};

	},

	reconnect: function () {
		var me = this;

		if(!me.initiated) return;

		this.log('reconnect');

		me.connected = false;
		delete me.socket;

		Ext.Function.defer(function () {
			me.connect();
		},me.reconnect_interval);
	},

	send: function (message, callback) {
		this.socket.send(message);
		this.callback = callback;
	},

	onOpen: function (data) {
		this.log('onOpen');
		this.log(data);
		this.initiated = true;
		this.connected = true;
		Ext.Function.defer(function () {
			app.fireEvent('browserhelperopen', this);
		},1000);
	},

	onClose: function (data) {
		this.log('onClose');
		this.log(data);
		this.reconnect();
		Ext.Function.defer(function () {
			app.fireEvent('browserhelperclose', this);
		},1000);
	},

	onMessage: function (data) {
		this.log('onMessage');
		this.log(data);

		data = JSON.parse(data);

		if(data.action && data.action == 'command'){
			this.log('fireEvent: ' + data.msg);
			app.fireEvent(data.msg, this);
		}else{

			// callback is defined
			if(this.callback){
				this.callback(data);
				this.callback = undefined;
			}
		}

	},

	onError: function (data) {
		this.log('onError');
		this.log(data);
		this.reconnect();
	},


	log: function (msg) {
		if(this.debug){
			say(msg);
		}
	},

	sendMessage: function(message, callback){

		if(this.connected){
			this.send(JSON.stringify(message), callback);
		}else {
			app.msg(_('oops'), _('not_conneted'), true);
		}
	}
});