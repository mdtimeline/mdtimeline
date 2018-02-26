Ext.define('App.controller.Clock', {
    extend: 'Ext.app.Controller',

	refs:[
		{
			ref: 'ApplicationClockContainer',
			selector: '#ApplicationClockContainer'
		}
	],

	init: function() {
		var me = this;

		me.control({
			'#ApplicationClockContainer' :{
				render: me.initClock
			}
		});

		/**
		 * TaskScheduler
		 */
		me.cronTask = {
			scope: me,
			run: function(){
				me.clock.update(Ext.Date.format(me.date, 'F j, Y, g:i:s a'));
				me.date = Ext.Date.add(me.date, Ext.Date.SECOND, 1);
			},
			interval: 1000
		};
	},

	initClock: function(clock){
		this.clock = clock;
		this.date = new Date();
		Ext.TaskManager.start(this.cronTask);
	},

	updateClock:function(date){
    	say(date);
    	say(Ext.String.format('Y: {0} M: {1} D: {2} H: {3} M: {4} S: {5} ', date.year, date.mon, date.mday, date.hours, date.minutes, date.seconds));
		this.date = new Date(date.year, date.mon, date.mday, date.hours, date.minutes, date.seconds );
	},

	getTime: function () {
		return Ext.clone(this.date);
	},

	getDate: function () {
		return this.getTime();
	}

});
