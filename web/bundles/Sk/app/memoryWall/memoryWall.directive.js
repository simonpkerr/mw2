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
            template: '<ng-include src="template" />',
            link: link
            //,
            //controller: 'MemoryWall', controller already defined in app.routing.js
            //controllerAs: 'vm'
        };
        return directive;

        //function templateUrl(elem, attr) {
            //the actual item data doesn't seem to be available inside the templateUrl
            //return '/web/bundles/Sk/app/memoryWall/amazon.html';
        //} 

        function link(scope, element, attrs) {
            scope.template = '/web/bundles/Sk/app/memoryWall/' + scope.item.provider + '.html';
            
            scope.playYouTubeVideo = function(item) {
                console.log(item);
            };
            //TODO functionality
            //referencing 'vm' accesses the controller 
            //console.log(attrs);
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
