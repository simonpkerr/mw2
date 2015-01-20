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
            eventPrefix = 'youtube.player.',
            directive = {
                restrict: 'E',
                scope: {
                    videoId:        '=?',
                    player:         '=?',
                    playerVars:     '=?',
                    playerHeight:   '=?',
                    playerWidth:    '=?'
                },
                replace: true,
    //          template: '<ng-include src="template" />',
                link: link

            };
        return directive;

        function link(scope, element, attrs) {
            console.log(element);
        }
    }

})();
