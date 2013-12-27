// I bootstrap angular dynamically after the GCharts has been loaded
google.setOnLoadCallback(function() {
  angular.bootstrap(document.body, ['technoTopApp']);
});
google.load('visualization', '1', {packages: ['corechart']});


var technoTopApp = angular.module('technoTopApp', [
  'ngRoute',
  'technoTopControllers',
  'technoTopDirectives'
]);
 
technoTopApp.config(['$routeProvider', function($routeProvider) {
	$routeProvider
		.when('/techno/:techno', {templateUrl: 'partials/techno.html', controller: 'TechnoTopCtrl'})
		.otherwise({redirectTo: '/'});
}]);