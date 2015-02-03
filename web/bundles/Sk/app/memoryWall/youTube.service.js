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

