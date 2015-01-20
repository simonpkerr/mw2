(function () {
    'use strict';
    angular.module('mwApp.memoryWall')
        .factory('youTubePlayerService', youTubePlayerService);

    youTubePlayerService.$inject = ['$window', '$rootScope', '$document'];
    
    function youTubePlayerService($window, $rootScope, $document) {
        var service = {
            getPlayer: getPlayer,
            isReady: false
        };
        return service;
        
        //resolve this in the memory wall again
        function getPlayer() {
            return function(){
                var tag = $document.createElement('script');
                tag.src = 'https://www.youtube.com/iframe_api';
                var firstScriptTag = $document.getElementsByTagName('script')[0];
                firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);
            };
        }
        
        $window.onYouTubeIframeAPIReady = function() {
            $rootScope.$apply(function() {
                service.isReady = true;
            });
        };
    }
})();

