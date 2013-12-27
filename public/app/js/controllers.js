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

technoTopControllers.controller('CategoriesCtrl', function ($scope, $routeParams, $http) {
	$http.get('/category').success(function(data) {
	    $scope.categories = data.data;
	});
});

technoTopControllers.controller('TechnoCategoryCtrl', function ($scope, $routeParams, $http) {
	  
	$http.get('/category/' + $routeParams.id).success(function(data) {
	    $scope.technos = data.data;
	    $scope.chartTitle = "Category share";
		$scope.chartWidth = 500;
		$scope.chartHeight = 320;
		//$scope.chartData = data.data;
		//$scope.chartData = google.visualization.arrayToDataTable(data.data);

        var sampleData = [];
		angular.forEach(data.data, function(row) {
			console.log(row.techno + ' ' +row.count);
            sampleData.push({ 0:row.techno, 1:row.count });
        });
		$scope.chartData = sampleData;
	});
});