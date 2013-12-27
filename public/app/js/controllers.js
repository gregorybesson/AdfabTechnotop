var technoTopControllers = angular.module('technoTopControllers', []);
 
technoTopControllers.controller('TechnoTopCtrl', function ($scope, $routeParams, $http) {
	$scope.techno = $routeParams.techno;
	$http.get('/techno/' + $routeParams.techno).success(function(data) {
	    $scope.technos = data.data;
	});
});