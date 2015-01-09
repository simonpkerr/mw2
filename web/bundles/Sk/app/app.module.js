'use strict';

/**
 * @ngdoc Memory walls
 * @name publicApp
 * @description
 * # controls all aspects of memory walls
 *
 * Main module of the application.
 */
//var mwApp = angular.module('MWApp', [
//  'ngRoute',
//  'mwControllers',
//  'mwServices',
//  'mwDirectives'
//  
//]),
//  viewBase = '../bundles/Sk/public/app/';
//
//
//mwApp.config(['$routeProvider',
//  function($routeProvider) {
//    $routeProvider.
//      when('/index', {
//        templateUrl: viewBase + 'views/main.html',
//        controller: 'MainCtrl'
//      }).
//      otherwise({
//        redirectTo: '/index'
//      });
//  }
//]);

angular.module('mwApp', [
    /*shared widgets*/
    'mwApp.core',
    
    /*features*/
    'mwApp.memoryWall'
]);