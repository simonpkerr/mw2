(function () {
  'use strict';
  angular.module('mwApp.memoryWall')
  .factory('memoryWallService', memoryWallService);

  memoryWallService.$inject = ['$resource'];

  function memoryWallService($resource) {
    var service = {
      memoryWall: memoryWall,
      mediaTypes: mediaTypes,
      memoryWallItem: memoryWallItem
    };
    return service;

    function memoryWall() {

      return $resource('/web/app_dev.php/api/memorywalls/:decade',
        { decade: decade },
        {
          query: {
            method: 'GET',
            isArray: true
          }
        }
      );
    }

    function mediaTypes() {
      return $resource('/web/app_dev.php/api/mediatypes', {
        query: {
          method: 'GET',
          isArray: true
        }
      });
    }

    function memoryWallItem(item) {
      return $resource('/web/app_dev.php/api/memorywall/item/:provider/:id',
        {
          provider: item.provider,
          id: item.id
        },
        {
          query: {
            method: 'GET'
          }
        }
      );
    }

  }
})();

