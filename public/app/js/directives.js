var technoTopDirectives = angular.module('technoTopDirectives', []);

technoTopDirectives.directive('pieChart', function ($timeout) {
  return {
    restrict: 'EA',
    scope: {
      title:    '@title',
      width:    '@width',
      height:   '@height',
      data:     '=data',
      selectFn: '&select'
    },
    link: function($scope, $elm, $attr) {
      
      // Create the data table and instantiate the chart
      var data = new google.visualization.DataTable();
      data.addColumn('string', 'Label');
      data.addColumn('number', 'Value');
      var chart = new google.visualization.PieChart($elm[0]);

      draw();
      
      // Watches, to refresh the chart when its data, title or dimensions change
      $scope.$watch('data', function() {
        draw();
      }, true); // true is for deep object equality checking
      $scope.$watch('title', function() {
        draw();
      });
      $scope.$watch('width', function() {
        draw();
      });
      $scope.$watch('height', function() {
        draw();
      });

      // Chart selection handler
      google.visualization.events.addListener(chart, 'select', function () {
        var selectedItem = chart.getSelection()[0];
        if (selectedItem) {
          $scope.$apply(function () {
            $scope.selectFn({selectedRowIndex: selectedItem.row});
            var value = data.getValue(selectedItem.row, 0);
            document.location.href= '/app/index.html#/techno/'+value;
          });
        }
      });
        
      function draw() {
        if (!draw.triggered) {
          draw.triggered = true;
          $timeout(function () {
            draw.triggered = false;
            var label, value;
            data.removeRows(0, data.getNumberOfRows());
            angular.forEach($scope.data, function(row) {
              label = row[0];
              value = parseFloat(row[1], 10);
              if (!isNaN(value)) {
                data.addRow([row[0], value]);
              }
            });
            var options = {'title': $scope.title,
                           'width': $scope.width,
                           'height': $scope.height};
            chart.draw(data, options);
            // No raw selected
            $scope.selectFn({selectedRowIndex: undefined});
          }, 0, true);
        }
      }
    }
  };
});