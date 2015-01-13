angular.module('mwApp.memoryWall')
        .factory('memoryWallService', memoryWallService);

memoryWallService.$inject = ['$resource'];

function memoryWallService($resource) {
    var service = {
        memoryWall: memoryWall,
        mediaTypes: mediaTypes
    };
    return service;
    
    function memoryWall() {
        return $resource('/web/app_dev.php/api/memorywall', {
            query: {
                method: 'GET',
                isArray: true
            }
        });
    }

    function mediaTypes() {
        //../api/mediatypes
        return $resource('/web/app_dev.php/api/mediatypes', {
            query: {
                method: 'GET',
                isArray: true
            }
        });
    }
}




