(function () {
    'use strict';
    angular.module('mwApp.memoryWall',[])
        .controller('MemoryWall', MemoryWall);
    
    MemoryWall.$inject = ['$scope', 'memoryWallPrepService', '$sce'];

    function MemoryWall($scope, memoryWallPrepService, $sce) {
        //from here https://github.com/johnpapa/angularjs-styleguide#style-y034
        var vm = this;
        vm.wallData = {};
        vm.getWallData = getWallData;
        vm.getYouTubePlayer = getYouTubePlayer;
                
        getWallData();
        getYouTubePlayer();

        function getWallData() {
            return memoryWallPrepService.memoryWall.get(function (data) {
                vm.wallData = data.wallData;
                return vm.wallData;
            });
        }
        
        function getYouTubePlayer() {
            return memoryWallPrepService.getYouTubePlayer();
        }
        
    }
})();
