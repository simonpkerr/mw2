/**
 * 
 * @description thanks to brandly https://github.com/brandly/angular-youtube-embed 
 * for the youtube embed directive, which this is a modified version of.
 */
(function () {
    'use strict';
    angular
        .module('mwApp.memoryWall')
        .directive('youtubePlayer', youTubePlayer);
    
    youTubePlayer.$inject = ['youTubePlayerService'];
    
    function youTubePlayer(youTubePlayerService) {
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
                    videoId:        '@',
                    player:         '=?',
                    playerHeight:   '=?',
                    playerWidth:    '=?'
                },
                //replace: true,
    //          template: '<ng-include src="template" />',
                link: link

            };
        return directive;

        function link(scope, element, attrs) {
            var playerId = attrs.playerId || element[0].id || 'you-tube-player-' + uniqueId++;
            element[0].id = playerId;
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
                var player = new YT.Player(playerId, {
                    height: scope.playerHeight,
                    width: scope.playerWidth,
                    videoId: scope.videoId,
                    //playerVars: scope.playerVars,
                    events: {
                        onReady: onPlayerReady,
                        onStateChange: onPlayerStateChange                         
                    }
                });
                player.id = playerId;
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
            
            var stopWatchingReady = scope.$watch(
                function() {
                    return youTubePlayerService.isReady && typeof scope.videoId !== 'undefined';
                },
                function (isReady) {
                    if (isReady) {
                        stopWatchingReady();
                        scope.$watch('videoId', function () {
                           loadPlayer();
                        });
                    }
                }
            );
            
            scope.$on('$destroy', function () {
                scope.player && scope.player.destroy();
            });
    

        }
    }

})();
