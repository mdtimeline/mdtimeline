<?php
/**
 * GaiaEHR
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

if(!defined('_GaiaEXEC')) die('No direct access allowed.');
?>
<!DOCTYPE html>
<html>
<head>

    <script type="text/javascript" src="lib/<?php print EXTJS ?>/ext-all.js" charset="utf-8"></script>

    <script type="text/javascript">
        var app,
            acl = {},
            lang = {},
            globals = {},
	        ext = '<?php print EXTJS ?>',
	        version = '<?php print VERSION ?>',
	        site = '<?php print SITE ?>',
            node_id = '<?php print (defined("node_id") ? node_id : "1") ?>',
            localization = '<?php print site_default_localization ?>';
    </script>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta content="utf-8" http-equiv="encoding">
    <title>MDTL Logon Screen</title>
    <link rel="shortcut icon" href="favicon.ico">

    <script src="JSrouter.php?site=<?php print SITE ?>&dc_=<?php print time() ?>" charset="utf-8"></script>
    <script src="data/api.php?site=<?php print SITE ?>&dc_=<?php print time() ?>" charset="utf-8"></script>
    <script type="text/javascript">

        Ext.override(Ext.util.Cookies, {
            set : function(name, value){
                var argv = arguments,
                    argc = arguments.length,
                    expires = (argc > 2) ? argv[2] : null,
                    path = (argc > 3) ? argv[3] : '/',
                    domain = (argc > 4) ? argv[4] : null,
                    secure = (argc > 5) ? argv[5] : true;

                document.cookie = name + "=" + escape(value) + ((expires === null) ? "" : ("; expires=" + expires.toGMTString())) + ((path === null) ? "" : ("; path=" + path)) + ((domain === null) ? "" : ("; domain=" + domain)) + ((secure === true) ? "; secure" : "");
            },
        });

	    if(Ext.supports.LocalStorage){
		    Ext.state.Manager.setProvider(new Ext.state.LocalStorageProvider());
	    }else{
		    Ext.state.Manager.setProvider(new Ext.state.CookieProvider({
			    secure: location.protocol === 'https:',
			    expires : new Date(Ext.Date.now() + (1000*60*60*24*90)) // 90 days
		    }));
	    }

        window.i18n = window._ = function(key){
            return window.lang[key] || '*'+key+'*';
        };

        window.say = function(args){
	        console.log(args);
        };

        window.g = function(global){
	        return window.globals[global] || false;
        };

        window.a = function(acl){
	        return window.acl[acl] || false;
        };

        Ext.Loader.setConfig({
            enabled: true,
            disableCaching: true,
            paths: {
                'App': 'app'
            }
        });

        (function(){
            var head = document.getElementsByTagName('head')[0],
                theme = Ext.util.Cookies.get('mdtimeline_theme') || g('application_theme'),
                link;

            if(parseInt(g('recaptcha_enable')) === 1){
	            link  = document.createElement('script');
	            link.src  = 'https://www.google.com/recaptcha/api.js?hl=en';
	            head.appendChild(link);
            }

            if(theme === 'dark'){
                link  = document.createElement('link');
                link.rel  = 'stylesheet';
                link.type = 'text/css';
                link.href = 'resources/css/carbon/carbon.css';
                link.media = 'all';
                head.appendChild(link);
                link  = document.createElement('link');
                link.rel  = 'stylesheet';
                link.type = 'text/css';
                link.href = 'resources/css/carbon/style_newui.css?dc_=<?php print time() ?>';
                link.media = 'all';
                head.appendChild(link);
                link  = document.createElement('link');
                link.rel  = 'stylesheet';
                link.type = 'text/css';
                link.href = 'resources/css/carbon/custom_app.css?dc_=<?php print time() ?>';
                link.media = 'all';
                head.appendChild(link);

            }else{
                link  = document.createElement('link');
                link.rel  = 'stylesheet';
                link.type = 'text/css';
                link.href = 'resources/css/ext-all-gray.css';
                link.media = 'all';
                head.appendChild(link);
                link  = document.createElement('link');
                link.rel  = 'stylesheet';
                link.type = 'text/css';
                link.href = 'resources/css/style_newui.css?dc_=<?php print time() ?>';
                link.media = 'all';
                head.appendChild(link);
                link  = document.createElement('link');
                link.rel  = 'stylesheet';
                link.type = 'text/css';
                link.href = 'resources/css/custom_app.css?dc_=<?php print time() ?>';
                link.media = 'all';
                head.appendChild(link);
            }
        })();

        for(var x = 0; x < App.data.length; x++){
            Ext.direct.Manager.addProvider(App.data[x]);
        }

        Ext.onReady(function(){
            app = Ext.create('App.view.login.Login');

            let kairos = Ext.create('Modules.kairos.Main');
            if(kairos) {
            	kairos.init();
            }

        });
    </script>
</head>
<body id="login">
<div id="msg-div"></div>
<div id="copyright" style=" margin:0; overflow: auto; width: 100%; bottom: 0; left:0; padding: 5px 10px; ">
	<div style="float: left">Copyright &#169; <?php print date('Y') ?> MDTIMELINE GROUP, LLC |:| v<?php print VERSION ?> |:| NODE: <?php print (defined("node_id") ? node_id : "1") ?></div>
    <div style="float: right;">by <a href="http://mdtimeline.com/" target="_blank">MDTIMELINE GROUP, LLC.</a></div>
</body>

<!-- Global site tag (gtag.js) - Google Analytics -->
<?php if(preg_match('/tranextgen.com/', $_SERVER['HTTP_HOST'])) {  ?>
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-133663460-1"></script>
<script>
	window.dataLayer = window.dataLayer || [];
	function gtag(){dataLayer.push(arguments);}
	gtag('js', new Date());
	gtag('config', 'UA-133663460-1', {
		'page_path': (window.location.pathname + window.location.hash),
		'custom_map': {
			'dimension2': 'domain'
		}
	});
	gtag('event', 'client_domain', {
		'event_category': 'Client',
		'event_label' : window.location.hostname,
		'domain': window.location.hostname
	});

	gtag('event', 'client_version', {
		'event_category': 'Client',
		'event_label' : window.location.hostname + ' - ' + version
	});

	// gtag('event', 'logout', {
	// 	'event_category': window.location.hostname,
	// });

</script>
<?php } ?>
</html>
