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

    mwItemYoutube.$inject = ['youTubeService', 'baseUrl'];

    function mwItemYoutube(youTubeService, baseUrl) {
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
                templateUrl: baseUrl + 'memoryWall/youtube.html',
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
