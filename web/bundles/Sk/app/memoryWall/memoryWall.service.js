angular.module('mwApp.memoryWall')
        .factory('memoryWallService', memoryWallService);

memoryWallService.$inject = ['$resource'];

function memoryWallService($resource) {
    var service = {
        getMemoryWall: getMemoryWall,
        getMediaTypes: getMediaTypes
    };
    return service;
    
    function getMemoryWall($resource) {
        return $resource('/web/app_dev.php/api/memorywall', {
            query: {
                method: 'GET',
                isArray: true
            }
        });
    }

    function getMediaTypes($resource) {
        //../api/mediatypes
        return $resource('/web/app_dev.php/api/mediatypes', {
            query: {
                method: 'GET',
                isArray: true
            }
        });
    }
}




