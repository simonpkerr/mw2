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
        itemIndex:      '=',
        videoId:        '=?',
        player:         '=?',
        playerHeight:   '=?',
        playerWidth:    '=?'
      },
      require: '^mwItem',
      replace: true,
      templateUrl: baseUrl + 'memoryWall/youtube.html',
      controller: controller,
      controllerAs: 'vm',
      bindToController: true,
      link: link

    };
    return directive;

    function controller () {
      var vm = this;
    }

    function link(scope, element, attrs, controller) {

      scope.vm.exploreWall = controller.exploreWall;
      controller.scrollToElement(element);

      scope.vm.playerId = attrs.playerId || 'you-tube-player-' + uniqueId++;
      element.find('.youtube-player').attr('id', scope.vm.playerId);
      scope.vm.videoId = scope.vm.videoId || scope.vm.item.providerData.id;
      scope.vm.playerHeight = scope.vm.playerHeight || 390;
      scope.vm.playerWidth = scope.vm.playerWidth || 640;

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
          applyBroadcast(eventPrefix + state, scope.vm.player, event);
        }
        scope.$apply(function () {
          scope.vm.player.currentState = state;
        });
      }

      function onPlayerReady (event) {
        applyBroadcast(eventPrefix + 'ready', scope.vm.player, event);
      }

      function createPlayer() {
        //var playerVars = angular.copy(scope.playerVars);
        //playerVars.start = playerVars.start || null;
        var events = {
          onReady: onPlayerReady,
          onStateChange: onPlayerStateChange
        },
        player = youTubeService.createPlayer(scope.vm, events);

        return player;
      }

      function loadPlayer() {
        if(scope.vm.videoId) {
          if(scope.vm.player && scope.vm.player.d && typeof scope.vm.player.destroy === 'function') {
            scope.vm.player.destroy();
          }

          scope.vm.player = createPlayer();
        }
      }

      function preparePlayer() {
        var stopWatchingReady = scope.$watch(
          //watchExpression
          function() {
            return youTubeService.isReady && typeof scope.vm.videoId !== 'undefined';
          },
          //listener
          function (ready) {
            if (ready) {
              stopWatchingReady();
              scope.$watch('scope.vm.videoId', function () {
               loadPlayer();
             });
            }
          }
        );
      }

      scope.vm.playYouTubeVideo = function() {
        if(typeof scope.vm.player !== 'object') {
          preparePlayer();
        }
      };

      scope.$on('$destroy', function () {
        scope.vm.player && scope.vm.player.destroy();
      });

      scope.vm.playYouTubeVideo();

    }
  }

})();
