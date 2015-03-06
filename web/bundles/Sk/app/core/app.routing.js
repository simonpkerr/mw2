(function () {
    'use strict';

//https://github.com/johnpapa/angularjs-styleguide#style-y038
    angular
        .module('mwApp')
        //.constant('VIEWBASE', '../bundles/Sk/public/app/')
        .config(config);

    config.$inject = ['$routeProvider', '$sceDelegateProvider'];

    function config($routeProvider, $sceDelegateProvider) {
        var VIEW_BASE = '/web/bundles/Sk/app/';
        $routeProvider
                .when('/index', {
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
                
        $sceDelegateProvider.resourceUrlWhitelist([
           'self',
           'http://upload.wikimedia.org/**',
           'https://www.youtube.com/**',
           'https://i.ytimg.com/**'
        ]);
    }

    memoryWallPrepService.$inject = ['memoryWallService', 'youTubeService'];
    function memoryWallPrepService(memoryWallService, youTubeService) {
        return {
            memoryWall: memoryWallService.memoryWall(),
            getYouTubePlayer: youTubeService.getPlayer
        };
    }


})();
