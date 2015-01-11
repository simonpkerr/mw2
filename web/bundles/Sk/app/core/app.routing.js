//https://github.com/johnpapa/angularjs-styleguide#style-y038
angular
    .module('mwApp')
    .config(['$routeProvider', config])
    .constant('viewBase', '../bundles/Sk/public/app/');

function config($routeProvider) {
    $routeProvider
        .when('/index' , {
            templateUrl: viewBase + 'memoryWall/main.html',
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


