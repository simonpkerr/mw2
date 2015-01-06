'use strict';
/* Services */
var mwServices = angular.module('mwServices', ['ngResource']);

mwServices.factory('memoryWall', ['$resource',
    function($resource) {
        return $resource('/web/app_dev.php/api/memorywall', {
           query: {
               method: 'GET',
               isArray: true
           } 
        });
    }
]);
mwServices.factory('mediaTypes', ['$resource',
  function($resource) {
    //../api/mediatypes
    return $resource('/web/app_dev.php/api/mediatypes', {
        query: {
          method: 'GET',
          isArray: true
        }
      });
  }
]);


