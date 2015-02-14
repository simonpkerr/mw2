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
//            scope.template = '/web/bundles/Sk/app/memoryWall/' + scope.item.provider + '.html';
            
        }
    }
    
})();
