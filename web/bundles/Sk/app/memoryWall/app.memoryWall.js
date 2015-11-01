(function () {
    'use strict';
    angular.module('mwApp.memoryWall',[])
        .controller('MemoryWall', MemoryWall);

    MemoryWall.$inject = ['$scope', 'memoryWallPrepService', '$sce'];

    function MemoryWall($scope, memoryWallPrepService, $sce) {
      //from here https://github.com/johnpapa/angularjs-styleguide#style-y034
      var vm = this;
      vm.wallData = {};
      vm.decades = getDecades();
      vm.selectedDecade = 'any';
      vm.getWallData = getWallData;
      vm.getYouTubePlayer = getYouTubePlayer;

      getWallData();
      getYouTubePlayer();

      function getDecades(){
        var decades = [],
          year = new Date().getFullYear(),
          endDecade = year - (year % 10);
        for (var i = 1930; i <= endDecade; i+=10) {
          decades.push({
            label: 'THE ' + i + '\'S',
            id: i + 's'
          });
        }
        return decades;
      }

      function getWallData() {
        var decade = vm.selectedDecade || 'any';
        return memoryWallPrepService.memoryWall.get(
          {
            decade: decade
          },
          function (data) {
            vm.wallData = data.wallData;
            return vm.wallData;
          }
        );
      }

      function getYouTubePlayer() {
        return memoryWallPrepService.getYouTubePlayer();
      }
    }
})();
