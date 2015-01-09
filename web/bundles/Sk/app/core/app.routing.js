//https://github.com/johnpapa/angularjs-styleguide#style-y038
angular
    .module('mwApp')
    .config(['$routeProvider', config]);

function config($routeProvider) {
    $routeProvider
        .when('/index' , {
            templateUrl: 'memoryWall/main.html',
            controller: 'MemoryWall',
            controllerAs: 'vm',
            resolve: {
                memoryWallPrepService: memoryWallPrepService
            }
        }).
        otherwise({
            redirectTo: '/index'
        });
}

memoryWallPrepService.$inject = ['memoryWallService'];
function memoryWallPrepService(memoryWallService) {
    return memoryWallService.getMemoryWall(); //? should this be just .MemoryWall ?
}


