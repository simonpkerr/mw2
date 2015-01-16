(function () {
    'use strict';
    angular
        .module('mwApp.memoryWall')
        .directive('mwItem', memoryWallItemDirective);

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
            //controller: 'MemoryWall',
            //controllerAs: 'vm'
        };
        return directive;

        //function templateUrl(elem, attr) {
            //the actual item data doesn't seem to be available inside the templateUrl
            //return '/web/bundles/Sk/app/memoryWall/amazon.html';
        //} 

        function link(scope, element, attrs) {
            scope.template = '/web/bundles/Sk/app/memoryWall/' + scope.item.provider + '.html';
            
            //TODO functionality
            //referencing 'vm' accesses the controller 
            //console.log(attrs);
        }
    }
})();
