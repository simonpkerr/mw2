(function () {
    'use strict';
    angular.module('mwApp.memoryWall')
        .factory('youTubePlayerService', youTubePlayerService);

    youTubePlayerService.$inject = ['$window', '$rootScope'];
    
    function youTubePlayerService($window, $rootScope) {
        var service = {
            getPlayer: getPlayer,            
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
            $window.onYouTubeIframeAPIReady = function() {
                $rootScope.$apply(function() {
                    service.isReady = true;
                    
                });
            };
        }
        
    }
})();

