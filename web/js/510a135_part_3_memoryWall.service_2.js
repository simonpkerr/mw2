(function () {
    'use strict';
    angular.module('mwApp.memoryWall')
        .factory('memoryWallService', memoryWallService);

    memoryWallService.$inject = ['$resource'];

    function memoryWallService($resource) {
        var service = {
            memoryWall: memoryWall,
            mediaTypes: mediaTypes
        };
        return service;

        function memoryWall(decade) {
          
          return $resource('/web/app_dev.php/api/memorywalls/:decade',
          { decade: decade },
          {
            query: {
              method: 'GET',
              isArray: true
            }
          });
        }

        function mediaTypes() {
            return $resource('/web/app_dev.php/api/mediatypes', {
                query: {
                    method: 'GET',
                    isArray: true
                }
            });
        }
        
    }
})();

