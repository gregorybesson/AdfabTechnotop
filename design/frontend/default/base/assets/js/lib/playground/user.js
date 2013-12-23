/**
 * Copyright (C) 2013 - Adfab - nicolas labbé 
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

/** User Object
 * @class
 * @name PG.User
 */
var user = {
    
    env: { },
    id: null,
    uid: null,
    data: { },
    urls: {
        current: window.location.href,
        prev: null
    },
    
    /**
     * Init the user object
     * @function
     * 
     * @name PG.User.init
     * 
     * @param {null}
     * @return {null}
     * 
     * @this {User}
     * 
     * @ignore
     * 
     * @since version 1.0.0
     */ 
    init: function ()
    {
        'use strict';
        
        PG.Util.GenerateUniqueId();
        PG.User.urls.prev = PG.Util.readCookie('prev-url');
        
        var authent = PG.Util.readCookie('authent'),
            lastSync = PG.Util.readCookie('last-sync'),
            today = new Date().getTime();
        
        // @TODO : UNCOMMENT THIS TO MAKE THE AUTHENT STAY FOR A DAY
        authent = null;
        
        if(!PG.Util.not_null(lastSync) || (parseInt(lastSync, 10) + parseInt(24 * 60 * 60, 10)) < today) {
            authent = null;
        }
        
        if(!PG.Util.not_null(authent)) {
            PG.User.loadAuthent()
            .then(function ()
            {
                PG.User.checkUser();
            });
        }else {
            PG.User.data = JSON.parse(PG.Util.readCookie('authent'));
            PG.User.checkUser();
        }
    },
    
    /**
     * check on cookie if user is logged
     * @function
     * 
     * @name PG.User.isLogged
     * 
     * @param {null} 
     * @return {Boolean} bool true or false
     * 
     * @this {User}
     * 
     * @example
     * PG.User.isLogged()
     * 
     * @since version 1.0.0
     */
    isLogged: function ()
    {
        'use strict';
        var l;
        if(PG.User.id === '' || PG.User.id === null) {
            // check
            PG.User.id = PG.Util.readCookie('login');
        }
        l = (PG.Util.not_null(PG.User.id)) ? true : false;
        PG.Util.log('isLogged : ' + l);
        return l;
    },
    
    /**
     * Call service to get json data of logout user method
     * put the return into PG.User.data
     * @function
     * 
     * @name PG.User.loadAuthent
     * 
     * @param {null}
     * @return {Object} promise
     * 
     * @this {User}
     * 
     * @ignore
     * 
     * @since version 1.0.0
     */
    loadAuthent: function ()
    {
        'use strict';
        
        var p = new PG.Promise();

        PG.App.call( PG.Config.env[PG.Config.mode].connect )
        .then(
            function (data)
            {
                PG.User.data = data;
                PG.Util.createCookie('last-sync', new Date().getTime());
                PG.Util.createCookie('authent', JSON.stringify(data));
                p.resolve();
            }
        );
        
        return p;
    },
    
    /**
     * Check if the user is logged, logout, or tried to login/logout 
     * @function
     * 
     * @name PG.User.checkUser
     * 
     * @param {null}
     * @return {null}
     * 
     * @this {User}
     * 
     * @ignore
     * 
     * @since version 1.0.0
     */
    checkUser: function ()
    {
        'use strict';
        
        if(PG.User.isLogged()
            && PG.Util.not_null(PG.Util.readCookie('logout-try'))) { // USER TRIED TO LOUGOUT
            if(PG.Util.not_null(PG.User.data.library.stories.logout_user)
                && PG.Util.not_null(PG.User.data.library.stories.logout_user.events.after)
                    && PG.User.checkStory(PG.User.data.library.stories.logout_user.events.after)) {
                PG.User.logout();
            }
        }else if(!PG.User.isLogged()
            && PG.Util.not_null(PG.Util.readCookie('login-try'))) { // USER TRIED TO LOGIN
            if(PG.Util.not_null(PG.User.data.library.stories.login_user)
                && PG.Util.not_null(PG.User.data.library.stories.login_user.events.after)
                    && PG.User.checkStory(PG.User.data.library.stories.login_user.events.after)) {
                PG.User.login(PG.Util.readCookie('login-try'));
            }
        }else if(!PG.User.isLogged()
            && PG.Util.not_null(PG.Util.readCookie('login'))) { // USER IS ALREADY LOGGED
            PG.User.login(PG.Util.readCookie('login'));
        }else if(PG.User.isLogged()) { // USER IS LOGGED OUT
            PG.User.logout();
        }
        
        PG.Util.eraseCookie('logout-try');
        PG.Util.eraseCookie('login-try');
        
        PG.App.trackAreaEvent();
        PG.App.send(window.location.href);
    },
     
    /**
     * Set the login for current user if param {String} not null
     * call loadLogout to track logout since the user is logged
     * then return the login
     * @function
     * 
     * @name PG.User.login
     * 
     * @param {String} str (optional) if not set just return the login
     * @return {String} PG.User.data.id
     * 
     * @this {User}
     * 
     * @example
     * PG.User.login( {String} username )
     * 
     * @since version 1.0.0
     */
    login: function (str)
    {
        'use strict';
        
        PG.Util.log('Login "' + str + '"');
        if(PG.Util.not_null(str)) {
            PG.User.id = str;
            PG.Util.createCookie('login', str);
        }
        
        return PG.User.data.id;
    },
    
    /**
     * force logout user, then call loadLogin since user is logged out
     * @function
     * 
     * @name PG.User.logout
     * 
     * @param {null} 
     * @return {null}
     * 
     * @this {User}
     * 
     * @example
     * PG.User.logout()
     * 
     * @since version 1.0.0
     */
    logout: function ()
    {
        'use strict';
        
        PG.Util.log('User is out');
        PG.Util.eraseCookie('login');
        PG.User.id = null;
    },
    
    /**
     * Check evidences for current user.evidences object
     * @function
     * 
     * @name PG.User.checkStory
     * 
     * @param {Object} story
     * @return {Boolean} result true | false
     * 
     * @this {User}
     * 
     * @ignore
     * 
     * @since version 1.0.0
     */
    checkStory: function (story)
    {
        'use strict';
        
        var use = false;
        if(PG.Util.not_null(story)) {
            // check condition url + xpath
            if(PG.Util.not_null(story.url)
                && PG.Util.not_null(story.xpath)
                    && PG.Util.matchUrl(story.url)
                        && PG.Util.checkXpath(story.xpath)) {
                use = true;
            }else if(PG.Util.not_null(story.url)
                && PG.Util.matchUrl(story.url)
                    && !PG.Util.not_null(story.xpath)) {
                // check condition url
                use = true;
            }else if(PG.Util.not_null(story.xpath)
                && PG.Util.checkXpath(story.xpath)
                    && !PG.Util.not_null(story.url)) {
                // check condition xpath
                use = true;
            }
        }
        return use;
    },
    
    /**
     * return json story
     * @function
     * 
     * @name PG.User.getStory
     * 
     * @param {String} url
     * @param {Object} story
     * @return {Boolean} result true | false
     * 
     * @this {User}
     * 
     * @ignore
     * 
     * @since version 1.0.0
     */
    getStory: function (url, action, obj, checkEvt)
    {
        'use strict';
        
        var json = {
            user: {
                anonymous: PG.User.uid
            },
            objects: {},
            action: action,
            url: url,
            apiKey: PG.Settings.apiKey
        };
        
        if(PG.User.isLogged()) {
            json.user.login = PG.User.id;
        }
        
        if(PG.Util.not_null(obj)) {
        
            json.objects = {
                id: obj.id
            };
            
            if(PG.Util.not_null(obj.properties)
                &&obj.properties.length > 0
                    && PG.Util.not_null(obj.properties[0].xpath)
                        && PG.Util.not_null(obj.properties[0].name)
                            && PG.Util.not_null(PG.Util.getObjectFromXpath(obj.properties[0].xpath)[0])) {
                json.objects.properties = {
                    name: obj.properties[0].name,
                    value: PG.Util.getValueFromObject(
                        PG.Util.getObjectFromXpath(obj.properties[0].xpath)[0]
                    )
                };
            }
        }
        return json;
    },
    
    /**
     * Call this method when user quit the current to check if try to logout/login
     * make cookies 'login-try' and 'logout-try'
     * @function
     * 
     * @name PG.User.quit
     * 
     * @param {null}
     * @return {null}
     * 
     * @this {User}
     * 
     * @ignore
     * 
     * @since version 1.0.0
     */
    quit: function ()
    {
        'use strict';
        
        var it,
            id = '',
            s;
        
        PG.Util.createCookie('prev-url', window.location.href);
        
        if(PG.User.isLogged()) {
            // test logout
            if(PG.Util.not_null(PG.User.data.library.stories.logout_user)
                && PG.Util.not_null(PG.User.data.library.stories.logout_user.events.before)) {
                if(PG.User.checkStory(PG.User.data.library.stories.logout_user.events.before)) {
                    s = PG.User.getStory(
                        window.location.href,
                        'logout_user',
                        PG.User.data.library.stories.logout_user.objects
                    );
                    PG.Util.createCookie('logout-try', 'true');
                }
            }
        }else {
            // test login
            if(PG.Util.not_null(PG.User.data.library.stories.login_user)
                && PG.Util.not_null(PG.User.data.library.stories.login_user.events.before)) {
                if(PG.User.checkStory(PG.User.data.library.stories.login_user.events.before)) {
                    s = PG.User.getStory(
                        window.location.href,
                        'login_user',
                        PG.User.data.library.stories.login_user.objects
                    );
                    if(PG.Util.not_null(s.objects.properties.value)) {
                        PG.Util.createCookie('login-try', s.objects.properties.value);
                    }
                }
            }
        }
    }
};

// put the user into Adfab.Playground.User
try {
    addToNamespace('User', user);
}catch(e) {
   throw new Error( "Cannot extends 'User' to 'Adfab.playground.User'" );
}