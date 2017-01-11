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

    <script src='https://www.google.com/recaptcha/api.js?hl=en'></script>
    <script type="text/javascript">
        var app,
            acl = {},
            lang = {},
            globals = {},
	        ext = '<?php print EXTJS ?>',
	        version = '<?php print VERSION ?>',
	        site = '<?php print SITE ?>',
            localization = '<?php print site_default_localization ?>';
    </script>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta content="utf-8" http-equiv="encoding">
    <title>MD Timeline Logon Screen</title>
    <link rel="shortcut icon" href="favicon.ico">

    <script src="JSrouter.php?site=<?php print SITE ?>" charset="utf-8"></script>
    <script src="data/api.php?site=<?php print SITE ?>" charset="utf-8"></script>
    <script type="text/javascript">

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
                theme = Ext.state.Manager.get('mdtimeline_theme', g('application_theme')),
                link;

            if(theme == 'dark'){
                link  = document.createElement('link');
                link.rel  = 'stylesheet';
                link.type = 'text/css';
                link.href = 'resources/css/carbon/carbon.css';
                link.media = 'all';
                head.appendChild(link);
                link  = document.createElement('link');
                link.rel  = 'stylesheet';
                link.type = 'text/css';
                link.href = 'resources/css/carbon/style_newui.css';
                link.media = 'all';
                head.appendChild(link);
                link  = document.createElement('link');
                link.rel  = 'stylesheet';
                link.type = 'text/css';
                link.href = 'resources/css/carbon/custom_app.css';
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
                link.href = 'resources/css/style_newui.css';
                link.media = 'all';
                head.appendChild(link);
                link  = document.createElement('link');
                link.rel  = 'stylesheet';
                link.type = 'text/css';
                link.href = 'resources/css/custom_app.css';
                link.media = 'all';
                head.appendChild(link);
            }
        })();

        for(var x = 0; x < App.data.length; x++){
            Ext.direct.Manager.addProvider(App.data[x]);
        }

        Ext.onReady(function(){
            app = Ext.create('App.view.login.Login');
        });
    </script>
</head>
<body id="login">
<div id="msg-div"></div>
<div id="copyright" style=" margin:0; overflow: auto; width: 100%; bottom: 0; left:0; padding: 5px 10px; ">
	<div style="float: left">Copyright (C) 2016 MD Timeline (Electronic Health Records) |:|  Open Source Software operating under <a href="javascript:void(0)" onClick="Ext.getCmp('winCopyright').show();">GPLv3</a> |:| v<?php print VERSION ?></div>
    <div style="float: right;">by <a href="http://tranextgen.com/" target="_blank">The Right Answer, Inc.</a></div>
</body>
</html>
