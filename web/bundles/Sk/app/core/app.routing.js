(function () {
    'use strict';

//https://github.com/johnpapa/angularjs-styleguide#style-y038
  angular
    .module('mwApp')
    //.constant('VIEWBASE', '../bundles/Sk/public/app/')
    .config(config);

  config.$inject = ['$routeProvider', '$sceDelegateProvider', '$locationProvider', 'baseUrl'];

  function config($routeProvider, $sceDelegateProvider, $locationProvider, baseUrl) {
    //var VIEW_BASE = 'app/';// '/web/bundles/Sk/app/';
    $routeProvider
      .when('/index', {
        templateUrl: baseUrl + 'memoryWall/main.html',
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
     'https://upload.wikimedia.org/**',
     'https://www.youtube.com/**',
     'https://i.ytimg.com/**',
     'http://previews.7digital.com/**'
    ]);

    $locationProvider.html5Mode(true);
  }

  memoryWallPrepService.$inject = ['memoryWallService', 'youTubeService'];
  function memoryWallPrepService (memoryWallService, youTubeService) {
    return {
      memoryWall: memoryWallService.memoryWall(),
      getYouTubePlayer: youTubeService.getPlayer
    };
  }


})();
