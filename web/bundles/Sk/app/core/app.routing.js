//https://github.com/johnpapa/angularjs-styleguide#style-y038
angular
    .module('mwApp')
    //.constant('VIEWBASE', '../bundles/Sk/public/app/')
    .config(config);

config.$inject = ['$routeProvider'];

function config($routeProvider) {
    var VIEW_BASE = '/web/bundles/Sk/app/';
    $routeProvider
        .when('/index' , {
            templateUrl: VIEW_BASE + 'memoryWall/main.html',
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
    return memoryWallService.memoryWall();
}


