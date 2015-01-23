(function () {
    'use strict';
    angular
        .module('mwApp.memoryWall')
        .directive('mwItem', memoryWallItemDirective);
        //.directive('mwItemYoutube', youTube)

    function memoryWallItemDirective() {
        var directive = {
            restrict: 'E',
            scope: {
                item: '='
            },
            replace: true,
//          template: '<ng-include src="template" />',
            link: link,
            templateUrl: templateUrl// '/web/bundles/Sk/app/memoryWall/youtube.html'
            //,
            //controller: 'MemoryWall', controller already defined in app.routing.js
            //controllerAs: 'vm'
        };
        return directive;

        function templateUrl(elem, attr) {
            return '/web/bundles/Sk/app/memoryWall/' + attr.ngSwitchWhen + '.html';
        } 

        function link(scope, element, attrs) {
//            scope.template = '/web/bundles/Sk/app/memoryWall/' + scope.item.provider + '.html';
            
        }
    }
    
//    function youTube() {
//        var directive = {
//            restrict: 'E',
//            //require: 'MemoryWall',
//            scope: {
//                item: '='
//            },
//            replace: true,
//            transclude: true,
//            template: '<div class="youtube-item" ng-transclude></div>',
//            link: link
//        };
//        return directive;
//        
//        function link(scope, element, attrs) {
//            
//            scope.playYouTubeVideo = function(id) {
//                console.log(id);
//            }
//        }
//    }
})();
