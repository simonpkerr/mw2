/**
 * 
 * @description 7digital directive gets release info by tag 
 * then allows previews of each track to be played
 */
(function () {
    'use strict';
    angular
        .module('mwApp.memoryWall')
        .directive('mwItemSevendigital', mwItemSevendigital);
    
    function mwItemSevendigital() {
        var directive = {
                restrict: 'E',
                scope: {
                    item: '='
                },
                replace: true,
                templateUrl: '/web/bundles/Sk/app/memoryWall/sevendigital.html',
                link: link

            };
        return directive;

        function link(scope, element, attrs) {
            scope.isOpen = false;
        }
    }

})();
