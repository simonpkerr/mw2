(function () {
    'use strict';
    angular.module('mwApp.memoryWall', [
        //'mwApp.core',
        //'ngResource'
        // no need to declare here as already in app.module.js
    ]).controller('MemoryWall', MemoryWall);
    MemoryWall.$inject = ['$scope', '$routeParams', 'memoryWallService', 'memoryWallPrepService'];

    function MemoryWall($scope, $routeParams, memoryWallService, memoryWallPrepService) {
        //from here https://github.com/johnpapa/angularjs-styleguide#style-y034
        var vm = this;
        vm.wallData = {};
        vm.getWallData = getWallData;

        getWallData();

        function getWallData() {
            return memoryWallPrepService.get(function (data) {
                vm.wallData = data.wallData;
                return vm.wallData;
            });
        }
    }
})();
