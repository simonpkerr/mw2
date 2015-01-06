'use strict';

/**
 * @ngdoc Memory walls
 * @name publicApp
 * @description
 * # controls all aspects of memory walls
 *
 * Main module of the application.
 */
var mwApp = angular.module('MWApp', [
  'ngRoute',
  'mwControllers',
  'mwServices'
  
]),
  viewBase = '../bundles/Sk/public/app/';
  //viewBase = '';


mwApp.config(['$routeProvider',
  function($routeProvider) {
    $routeProvider.
      when('/index', {
        templateUrl: viewBase + 'views/main.html',
        controller: 'MainCtrl'
      }).
      otherwise({
        redirectTo: '/index'
      });
  }
]);
