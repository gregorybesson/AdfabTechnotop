var technoTopControllers = angular.module('technoTopControllers', []);
 
technoTopControllers.controller('TechnoTopCtrl', function ($scope, $routeParams, $http) {
	$scope.techno = $routeParams.techno;
	
	$scope.chartTitle = "Techno usage";
	  $scope.chartWidth = 500;
	  $scope.chartHeight = 320;
	  $scope.chartData = [
	    ['Drupal',     11],
	    ['WordPress',      2],
	    ['Magento',  2],
	    ['ZF2', 2],
	    ['SF2',    7]
	  ];
	  
	  $scope.deleteRow = function (index) {
	    $scope.chartData.splice(index, 1);
	  };
	  $scope.addRow = function () {
	    $scope.chartData.push([]);
	  };
	  $scope.selectRow = function (index) {
	    $scope.selected = index;
	  };
	  $scope.rowClass = function (index) {
	    return ($scope.selected === index) ? "selected" : "";
	  };
	  
	  
	$http.get('/techno/' + $routeParams.techno).success(function(data) {
	    $scope.technos = data.data;
	    $scope.chartData = [
	                	    ['Drupal',     250],
	                	    ['WordPress',      2],
	                	    ['Magento',  2],
	                	    ['ZF2', 2],
	                	    ['SF2',    7]
	                	  ];
	});
});