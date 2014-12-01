'use strict';

/**
 * @ngdoc function
 * @name publicApp.controller:MainCtrl
 * @description
 * # MainCtrl
 * Controller of the publicApp
 */
var mwControllers = angular.module('mwControllers', []);

mwControllers.controller('MainCtrl', ['$scope', '$routeParams', 'mediaTypes',
  function($scope, $routeParams, mediaTypes) {
    mediaTypes.get(function(data) {
      $scope.mediaTypes = data.mediaTypes;
    });
  }
]);
