/*
 * this is the main application file, which one that init project bind event, etc...
 * Copyright (C) 2013 - Adfab - nicolas labb�
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
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */
 
/**
 * @namespace reference to Adfab.Playground.App object
 */

/** App Object
 * @class
 * @name PG.App
 */
function App ()
{
    'use strict';
    
    return this;
}

/**
 * Start the App object by using init
 * This method create the namespace used all over the application
 * @function
 * 
 * @name PG.App.init
 * 
 * @param {null} no params
 * @return {null} no return
 * 
 * @this {App}
 * 
 * @ignore
 * 
 * @since version 1.0.0
 */
App.prototype.init = function ()
{
    'use strict';
    
    // check api key
    if(PG.Util.not_null(PG.Settings) && PG.Util.not_null(PG.Settings.apiKey)) {
        //PG.User.env = _plgd_settings;
        PG.Util.log('APIKEY FOUND = ' + PG.Settings.apiKey);
    }else {        
        PG.Util.log('NO APIKEY FOUND');
        return;
    }
    
    PG.App.easyXDM();
    PG.App.bindEvent();
    PG.User.init();
    
    PG.Util.fireEvt(
        'earsReady',
        window,
        {
            apiKey: PG.Settings.apiKey,
            uid: PG.User.uid,
            login: PG.User.id
		}
	);
};

/**
 * Bind the event to track user move
 * @function
 * 
 * @name PG.App.bindEvent
 * 
 * @param {null} no params
 * @return {null} no return
 * 
 * @this {App}
 * 
 * @ignore
 * 
 * @since version 1.0.0
 */
App.prototype.bindEvent = function ()
{
    'use strict';
    
    // AJAX
    var xhrListn = new Object();
        xhrListn.open = PG.Cache.xmlhttp.prototype.open;
        xhrListn.send = PG.Cache.xmlhttp.prototype.send;
    
    // Catch all the xhr open event
    PG.Cache.xmlhttp.prototype.open = function (a, b)
    {
        if (!a) var a='';
        if (!b) var b='';
        
        xhrListn.open.apply(this, arguments);
        xhrListn.method = a;  
        xhrListn.url = b;
        if (a.toLowerCase() == 'get') {
            xhrListn.data = b.split('?');
            xhrListn.data = xhrListn.data[1];
        }
    };
    
    // Catch all the xhr send event
    PG.Cache.xmlhttp.prototype.send = function (a, b)
    {
        if (!a) var a='';
        if (!b) var b='';
        xhrListn.send.apply(this, arguments);
        if(xhrListn.method.toLowerCase() == 'post') xhrListn.data = a;
    };
    
    // Catch all the load finished event
    PG.Cache.document.onreadystatechange = function (e)
    {
        if(PG.Util.not_null(e.data) && e.data != '') {
            //PG.App.send(e.data);
        }
    };
    
    PG.Cache.window.onbeforeunload = function (e)
    {
        PG.User.quit();
        return null;
    };
};

/**
 * Bind the event to track user click into an area
 * @function
 * 
 * @name PG.App.trackAreaEvent
 * 
 * @param {null} no params
 * @return {null} no return
 * 
 * @this {App}
 * 
 * @ignore
 * 
 * @since version 1.0.0
 */
App.prototype.trackAreaEvent = function ()
{
	'use strict';
	
    var i, story;
    
    function bindEvent (obj, story)
    {
    	var data = PG.User.getStory(window.location.href, story.action, story.objects),
    		areaObj = PG.Util.getObjectFromXpath(story.event.area.xpath),
    		realArea,
    		offset;
    	
    	if(typeof story.event.area.width === 'undefined' || !PG.Util.not_null(story.event.area.width)) {
    	    if(!PG.Util.not_null(areaObj[0])) {
    	        return;
    	    }
    		story.event.area.width = areaObj[0].offsetWidth;
    		story.event.area.height = areaObj[0].offsetHeight;
    	}
    	
    	if(typeof story.event.area.x === 'undefined' || !PG.Util.not_null(story.event.area.y)) {
    		story.event.area.x = 0;
    		story.event.area.y = 0;
    	}
    	
    	offset = PG.Util.getOffset(areaObj[0]);
    	
    	realArea = {
    			x: story.event.area.x + offset.x,
    			y: story.event.area.y + offset.y,
    			width: story.event.area.width,
    			height: story.event.area.height
    	}

    	if(typeof story.event.area.debug !== 'undefined' && story.event.area.debug === true) {
        	PG.Util.createShape(realArea)
    	}
    	
        obj.addEventListener(story.event.type, function (e)
        {
        	e.preventDefault();
    		
        	data.objects.x = e.pageX;
        	data.objects.y = e.pageY;
        	data.objects.text = window.getSelection().toString();
        	
        	if(PG.Util.not_null(story.event.area.text) && story.event.area.text !== '' && data.objects.text !== story.event.area.text) {
        		return;
        	}
        	
        	if(PG.Util.not_null(areaObj) && PG.Util.pointOnBox(data.objects, realArea)) {
        		
	            PG.App.sendToEXDM({
	                url: PG.Cache.protocol + PG.Config.env[PG.Config.mode].send,
	                method: 'POST',
	                data: data,
	                headers: {
	                    'Content-Type': 'application/json; charset=utf-8'
	                }
	            });
        	}
        	
        	return false;
        });
    }
    
    for(i in PG.User.data.library.stories) {
        story = PG.User.data.library.stories[i];
        
        if(PG.User.checkStory(story.conditions)
            && PG.Util.not_null(story.event)) {
            
            bindEvent(PG.Util.getObjectFromXpath(story.event.xpath)[0], story);
        }
    }
};

/**
 * Init easyXDM by calling this method
 * Using easyXDM as RPC mode
 * @function
 * 
 * @name PG.App.easyXDM
 * 
 * @param {null} no params
 * @return {null} no return
 * 
 * @this {App}
 * 
 * @ignore
 * 
 * @since version 1.0.0
 */
App.prototype.easyXDM = function ()
{
    'use strict';
    
    PG.App.rpc = new easyXDM.Rpc(
    {
        // fallback NameTransport
        local: PG.Cache.protocol + PG.Config.env[PG.Config.mode].nameTransport,
        // fallback swf
        swf: PG.Cache.protocol + PG.Config.env[PG.Config.mode].easySwf,
        // page to load
        remote: PG.Cache.protocol + PG.Config.env[PG.Config.mode].remote
    },
    {
        remote: {
            request: {}
        }
    });
};

/**
 * Send a request to easyXDM using RPC with no return
 * @function
 * 
 * @name PG.App.send
 * 
 * @param {string} url of the service
 * @return {Boolean} valid url
 * 
 * @this {App}
 * 
 * @example
 * PG.App.send( {String} url)
 * 
 * @since version 1.0.0
 */
App.prototype.send = function (url)
{
    'use strict';
    
    var broadcast = (PG.User.data.library.config.broadcast),
        userUrl = PG.Cache.protocol + PG.Config.env[PG.Config.mode].send,
        json, n, use, story;
    
    if(!PG.Util.isUrlValid(url)) {
        return false;
    }
    
    // check xpath and url
    for(n in PG.User.data.library.stories) {
        story = PG.User.data.library.stories[n];
        
        // get variable if send this url
        if(PG.User.checkStory(story.conditions) && typeof story.event === 'undefined') {
            json = PG.User.getStory(url, story.action, story.objects);
            
            PG.App.sendToEXDM({
                url: userUrl,
                method: 'POST',
                data: json,
                headers: {
                    'Content-Type': 'application/json; charset=utf-8'
                }
            });
        }
    }
    return true;
};

/**
 * Send a request to easyXDM using RPC with no return
 * @function
 * 
 * @name PG.App.send
 * 
 * @param {string} url of the service
 * @return {Boolean} valid url
 * 
 * @this {App}
 * 
 * @ignore
 * 
 * @since version 1.0.0
 */
App.prototype.sendToEXDM = function (config)
{
    'use strict';
    
    PG.App.rpc.request(
        config,
        function (rpcdata)
        {
            //PG.Util.log(rpcdata);
        },
        function (error)
        {
            throw 'Api Error, code : ' + error.code + ', msg : ' + error.message;
        }
    );
};

/**
 * Send a request to easyXDM using RPC waiting for a json return
 * @function
 * 
 * @name PG.App.call
 * 
 * @param {string} serviceName
 * @return {Object} promise
 * 
 * @this {App}
 * 
 * @throws {requestError} service cannot be called
 * 
 * @example
 * PG.App.call( {String} service )
 * .then( {Function} callback)
 * 
 * @since version 1.0.0
 */
App.prototype.call = function (s)
{
    'use strict';
    var promise = new PG.Promise();
    
    PG.Util.log('call service : ' + PG.Cache.protocol + s + '?apiKey=' + PG.Settings.apiKey);
    
    PG.App.rpc.request(
        {
            url: PG.Cache.protocol + s + '?apiKey=' + PG.Settings.apiKey
        },
        function (rpcdata)
        {
            promise.resolve(JSON.parse(rpcdata.data));
        },
        function (error)
        {
            throw 'Api Error, code : ' + error.code + ', msg : ' + error.message;
        }
    );
    
    return promise;
};

// create App instance
var app = new App();

// put the instance of App into the namespace Adfab.Playground.App
try {
    addToNamespace('App', app);
}catch(e) {
   throw new Error( "Cannot extends 'app' to 'Adfab.playground.App'" );
}

// Let's start over there
app.init();