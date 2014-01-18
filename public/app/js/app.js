// I bootstrap angular dynamically after the GCharts has been loaded
google.setOnLoadCallback(function() {
  angular.bootstrap(document.body, ['technoTopApp']);
});
google.load('visualization', '1', {packages: ['corechart']});

var technoTopApp = angular.module('technoTopApp', [
  'ngRoute',
  'ngResource',
  'ngTable',
  'technoTopControllers',
  'technoTopDirectives',
  'angular-google-analytics'
])
.config(['AnalyticsProvider', function(AnalyticsProvider) {
    // initial configuration
    AnalyticsProvider.setAccount('UA-47278386-1');

    // track all routes (or not)
    AnalyticsProvider.trackPages(true);

    //Optional set domain (Use 'none' for testing on localhost)
    //AnalyticsProvider.setDomainName('XXX');

    // url prefix (default is empty)
    // - for example: when an app doesn't run in the root directory
    //AnalyticsProvider.trackPrefix('my-application');

    // Use analytics.js instead of ga.jsgit diff
    AnalyticsProvider.useAnalytics(true);

    // Ignore first page view... helpful when using hashes and whenever your bounce rate looks obscenely low.
    AnalyticsProvider.ignoreFirstPageLoad(true);

    //Enabled eCommerce module for analytics.js
    AnalyticsProvider.useECommerce(false);

    //Enable enhanced link attribution
    AnalyticsProvider.useEnhancedLinkAttribution(true);

    //Enable analytics.js experiments
    //AnalyticsProvider.setExperimentId('12345');

    //Set custom cookie parameters for analytics.js
    //AnalyticsProvider.setCookieConfig({
    //  cookieDomain: 'foo.example.com',
    //  cookieName: 'myNewName',
    //  cookieExpires: 20000
    //});

    // change page event name
    AnalyticsProvider.setPageEvent('$stateChangeSuccess');
    }]);
 
technoTopApp.config(['$routeProvider', function($routeProvider) {
	$routeProvider
		.when('/techno/:techno', {templateUrl: 'partials/techno.html', controller: 'TechnoTopCtrl'})
		.when('/category/:id', {templateUrl: 'partials/category.html', controller: 'TechnoCategoryCtrl'})
		.otherwise({redirectTo: '/'});
}]);