'use strict';

/**
 * @ngdoc overview
 * @name publicApp
 * @description
 * # publicApp
 *
 * Main module of the application.
 */
var mwApp = angular.module('MWApp', [
  'ngRoute',
  'mwControllers',
  'mwServices'
  
]),
  //viewBase = '../web/bundles/Sk/public/app/';
  viewBase = '';


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
