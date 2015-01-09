'use strict';

/**
 * @ngdoc function
 * @name publicApp.controller:MainCtrl
 * @description
 * # MainCtrl
 * Controller of the publicApp
 */
var mwControllers = angular.module('mwControllers', []);

mwControllers.controller('MainCtrl', ['$scope', '$routeParams', 'memoryWall',
  function($scope, $routeParams, memoryWall) {
    memoryWall.get(function(data) {
      $scope.wallData = data.wallData;
    });
  }
]);
