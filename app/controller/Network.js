Ext.define('App.controller.Network', {
	extend: 'Ext.app.Controller',
	requires: [
	],
	refs: [
		{
			ref: 'AppNetworkStatusBox',
			selector: '#AppNetworkStatusBox'
		}
	],

	init: function(){
		var me = this;


		// say('new Network');

        me.Network = new Network();

        me.Network.latency.settings({
            endpoint: './classes/Latency.php',
            measures: 1,
            attempts: 1
        });


        me.Network.latency.on('end', function(average_latency, all_latencies) {
            me.onLatencyCheckEnd(average_latency, all_latencies);
        });

		me.control({
			'viewport': {
				render: me.onAppBeforeRender
			},
			'#AppNetworkStatusBox': {
				afterrender: me.onAppNetworkStatusBoxAfterRender
			}
		});


        window.addEventListener('online', function () {
            me.updateOnlineStatus();
        });
        window.addEventListener('offline', function () {
            me.updateOnlineStatus();
        });

    },

	onLatencyCheckEnd: function(average_latency){

		var bgcolor;

		if(average_latency < 50){
            bgcolor = 'green';
		}else if(average_latency < 75){
            bgcolor = 'yellow';
		}else if(average_latency < 100){
            bgcolor = 'orange';
		}else if(average_latency < 150){
            bgcolor = 'orange';
		}else {
            bgcolor = 'red';
		}

		// say('average_latency:' + average_latency);

        this.AppNetworkStatusBox.el.setStyle({
            backgroundColor: bgcolor,
            backgroundImage: 'none'
        });

	},

	startLatencyCheck: function(){
        performance.clearResourceTimings();
        this.Network.latency.start();
	},

    onAppBeforeRender: function(){
        var me = this;

        me.updateOnlineStatus();

        app.startLatencyCheck = function () {
            me.startLatencyCheck();
        };

        app.cron.addCronFn('app.startLatencyCheck()');
	},

    onAppNetworkStatusBoxAfterRender:function(comp){
		this.AppNetworkStatusBox = comp;
	},

    updateOnlineStatus: function () {
        app.fireEvent(navigator.onLine ? 'apponline' : 'appoffline', this);
        // say(navigator.onLine);

    },


});
