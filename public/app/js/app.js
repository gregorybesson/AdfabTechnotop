var technoTopApp = angular.module('technoTopApp', [
  'ngRoute',
  'technoTopControllers'
]);
 
technoTopApp.config(['$routeProvider', function($routeProvider) {
    $routeProvider
    		.when('/techno/:techno', {templateUrl: 'partials/techno.html', controller: 'TechnoTopCtrl'})
    		.otherwise({redirectTo: '/'});
  }]);