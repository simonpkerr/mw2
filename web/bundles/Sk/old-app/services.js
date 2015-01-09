'use strict';

var memoryWallService = angular.module('memoryWallService', ['ngResource']);

memoryWallService.factory('getMemoryWall', ['$resource',
    function($resource) {
        return $resource('/web/app_dev.php/api/memorywall', {
           query: {
               method: 'GET',
               isArray: true
           } 
        });
    }
]);
memoryWallService.factory('getMediaTypes', ['$resource',
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


