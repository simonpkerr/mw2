'use strict';

var memoryWall = angular.module('memoryWall', ['ngResource']);

memoryWall.factory('getMemoryWall', ['$resource',
    function($resource) {
        return $resource('/web/app_dev.php/api/memorywall', {
           query: {
               method: 'GET',
               isArray: true
           } 
        });
    }
]);
memoryWall.factory('getMediaTypes', ['$resource',
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


