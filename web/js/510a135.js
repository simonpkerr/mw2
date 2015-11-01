(function () {
    'use strict';
    angular.module('mwApp', [
        /*shared widgets*/
        'mwApp.core',
        
        /*features*/
        'mwApp.memoryWall'
    ]);
})();
(function () {
    'use strict';
    angular.module('mwApp.core', [
        'ngRoute',
        'ngResource',
        'ngSanitize'
    ]);    
})();
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
           'https://i.ytimg.com/**',
           'http://previews.7digital.com/**'
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

(function () {
    'use strict';
    angular.module('mwApp.memoryWall',[])
        .controller('MemoryWall', MemoryWall);
    
    MemoryWall.$inject = ['$scope', 'memoryWallPrepService', '$sce'];

    function MemoryWall($scope, memoryWallPrepService, $sce) {
      //from here https://github.com/johnpapa/angularjs-styleguide#style-y034
      var vm = this;
      vm.wallData = {};
      vm.decades = getDecades();
      vm.selectedDecade = 'any';
      vm.getWallData = getWallData;
      vm.getYouTubePlayer = getYouTubePlayer;

      getWallData();
      getYouTubePlayer();

      function getDecades(){
        var decades = [],
          year = new Date().getFullYear(),
          endDecade = year - (year % 10);
        for (var i = 1930; i <= endDecade; i+=10) {
          decades.push('THE ' + i + "'S");
        }
        return decades;
      }

      function getWallData() {
        var decade = vm.selectedDecade || 'any';
        return memoryWallPrepService.memoryWall.get(
          { 
            decade: decade 
          },
          function (data) {
            vm.wallData = data.wallData;
            return vm.wallData;
          }
        );
      }

      function getYouTubePlayer() {
        return memoryWallPrepService.getYouTubePlayer();
      }
    }
})();

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


(function () {
    'use strict';
    angular
    .module('mwApp.memoryWall')
    .directive('mwDecadeSelect', mwDecadeSelect);

    function mwDecadeSelect() {
        var select,
            slider,
            link = {
                post: post
            },
            sliderHtml = '<div id="decade-slider" class="site-header__slider"></div>',
            slide = function ( event, ui ) {
                select[ 0 ].selectedIndex = ui.value - 1;
            },
            change = function(){
                slider.slider('value', this.selectedIndex + 1 );
            },
            directive = {
                restrict: 'E',
                scope: false,
                // scope: {
                //     decades: '=',
                //     selectedDecade: '=',
                //     getWallData: '='
                // },
                replace: true,
                templateUrl: '/web/bundles/Sk/app/memoryWall/mwDecadeSelect.html',
                link: link

            };
        return directive;

        function post (scope, element, attrs) {
            select = $('select[name="selectedDecade"]', element).change(change);
            slider = $(sliderHtml).insertAfter(element).slider({
               min: 1,
               max: scope.vm.decades.length + 1,
               range: "min",
               value: select[ 0 ].selectedIndex + 1,
               slide: slide
            });
        }

    }

})();

(function () {
    'use strict';
    angular
        .module('mwApp.memoryWall')
        .directive('mwItemAmazon', mwItemAmazon);

    function mwItemAmazon() {
        var directive = {
            restrict: 'E',
            scope: {
                item: '='
            },
            replace: true,
            link: link,
            templateUrl: '/web/bundles/Sk/app/memoryWall/amazon.html'
        };
        return directive;


        function link(scope, element, attrs) {
//            scope.template = '/web/bundles/Sk/app/memoryWall/' + scope.item.provider + '.html';
            
        }
    }
    
})();

/**
 * 
 * @description 7digital directive gets release info by tag 
 * then allows previews of each track to be played
 */
(function () {
    'use strict';
    angular
        .module('mwApp.memoryWall')
        .directive('mwItemSevendigital', mwItemSevendigital);
    
    function mwItemSevendigital() {
        var directive = {
                restrict: 'E',
                scope: {
                    item: '='
                },
                replace: true,
                templateUrl: '/web/bundles/Sk/app/memoryWall/sevendigital.html',
                link: link

            };
        return directive;

        function link(scope, element, attrs) {
            scope.isOpen = false;
        }
    }

})();

(function () {
    'use strict';
    angular
        .module('mwApp.memoryWall')
        .directive('mwItemWikimedia', mwItemWikimedia);

    function mwItemWikimedia() {
        var directive = {
            restrict: 'E',
            scope: {
                item: '='
            },
            replace: true,
            link: link,
            templateUrl: '/web/bundles/Sk/app/memoryWall/wikimedia.html'
        };
        return directive;

        function link(scope, element, attrs) {
            scope.isOpen = false;
        }
    }
    
})();

/**
 * 
 * @description thanks to brandly https://github.com/brandly/angular-youtube-embed 
 * for the youtube embed directive, which this is a modified version of.
 */
(function () {
    'use strict';
    angular
        .module('mwApp.memoryWall')
        .directive('mwItemYoutube', mwItemYoutube);
    
    mwItemYoutube.$inject = ['youTubeService'];
    
    function mwItemYoutube(youTubeService) {
        var stateNames = {
                '-1': 'unstarted',
                0: 'ended',
                1: 'playing',
                2: 'paused',
                3: 'buffering',
                5: 'queued'
            },
            uniqueId = 1,
            eventPrefix = 'youtube.player.',
            directive = {
                restrict: 'E',
                scope: {
                    item:           '=',
                    videoId:        '=?',
                    player:         '=?',
                    playerHeight:   '=?',
                    playerWidth:    '=?'
                },
                replace: true,
                templateUrl: '/web/bundles/Sk/app/memoryWall/youtube.html',
                link: link

            };
        return directive;

        function link(scope, element, attrs) {
            scope.playerId = attrs.playerId || 'you-tube-player-' + uniqueId++;
            element.children('.youtube-player').attr('id', scope.playerId);
            scope.videoId = scope.videoId || scope.item.id;
            scope.playerHeight = scope.playerHeight || 390;
            scope.playerWidth = scope.playerWidth || 640;
            
            //scope.playerVars = scope.playerVars || {};
            
            function applyBroadcast() {
                var args = Array.prototype.slice.call(arguments);
                scope.$apply(function(){ 
                    scope.$emit.apply(scope, args);
                });
            }
            
            function onPlayerStateChange (event) {
                var state = stateNames[event.data];
                if (typeof state !== 'undefined') {
                    applyBroadcast(eventPrefix + state, scope.player, event);
                }
                scope.$apply(function () {
                    scope.player.currentState = state;
                });
            }
            
            function onPlayerReady (event) {
                applyBroadcast(eventPrefix + 'ready', scope.player, event);
            }
            
            function createPlayer() {
                //var playerVars = angular.copy(scope.playerVars);
                //playerVars.start = playerVars.start || null;
                var events = {
                        onReady: onPlayerReady,
                        onStateChange: onPlayerStateChange                         
                    },
                    player = youTubeService.createPlayer(scope, events);
                    
                return player;
            }
            
            function loadPlayer() {
                if(scope.videoId) {
                    if(scope.player && scope.player.d && typeof scope.player.destroy === 'function') {
                        scope.player.destroy();
                    }
                    
                    scope.player = createPlayer();
                }
            }
            
            function preparePlayer() {
                var stopWatchingReady = scope.$watch(
                    //watchExpression
                    function() {
                        return youTubeService.isReady && typeof scope.videoId !== 'undefined';
                    },
                    //listener
                    function (ready) {
                        if (ready) {
                            stopWatchingReady();
                            scope.$watch('videoId', function () {
                               loadPlayer();
                            });
                        }
                    }
                );
            }
            
            scope.playYouTubeVideo = function() {
                if(typeof scope.player !== 'object') {
                    preparePlayer();
                }
            };
            
            scope.$on('$destroy', function () {
                scope.player && scope.player.destroy();
            });
    

        }
    }

})();

(function () {
    'use strict';
    angular.module('mwApp.memoryWall')
            .factory('youTubeService', youTubeService);

    youTubeService.$inject = ['$window', '$rootScope'];

    function youTubeService($window, $rootScope) {
        var service = {
            getPlayer: getPlayer,
            createPlayer: createPlayer,
            isReady: false
        };
        onReady();

        return service;

        //resolve this in the memory wall again
        function getPlayer() {
            var tag = document.createElement('script');
            tag.src = 'https://www.youtube.com/iframe_api';
            var firstScriptTag = document.getElementsByTagName('script')[0];
            firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);
        }

        function onReady() {
            $window.onYouTubeIframeAPIReady = function () {
                $rootScope.$apply(function () {
                    service.isReady = true;

                });
            };
        }

        function createPlayer(scope, events) {
            var player = new YT.Player(scope.playerId, {
                height: scope.playerHeight,
                width: scope.playerWidth,
                videoId: scope.videoId,
                //playerVars: scope.playerVars,
                events: events
            });
            player.id = scope.playerId;
            return player;
        }

    }
})();

