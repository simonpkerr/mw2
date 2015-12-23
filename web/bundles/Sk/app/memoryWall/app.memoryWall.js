(function () {
    'use strict';
    angular.module('mwApp.memoryWall',[])
        .controller('MemoryWall', MemoryWall);

    MemoryWall.$inject = ['$scope', 'memoryWallPrepService', '$sce', '$location', '$routeParams'];

    function MemoryWall($scope, memoryWallPrepService, $sce, $location, $routeParams) {
      //from here https://github.com/johnpapa/angularjs-styleguide#style-y034
      var vm = this;
      vm.wallData = {};
      vm.decades = getDecades();
      vm.selectedDecade = getSelectedDecade();
      vm.getWallData = getWallData;
      vm.getYouTubePlayer = getYouTubePlayer;

      getWallData();
      getYouTubePlayer();

      function getDecades(){
        var decades = [],
          year = new Date().getFullYear(),
          endDecade = year - (year % 10);
        for (var i = 1930; i <= endDecade; i += 10) {
          decades.push({
            label: 'THE ' + i + '\'S',
            id: i + 's'
          });
        }
        return decades;
      }

      function getSelectedDecade() {
        var decadeFromUrl = $routeParams.selectedDecade,
          decadeFromDecades;

        if (decadeFromUrl === undefined) {
          return undefined;
        } else {
          decadeFromDecades = $.grep(vm.decades, function (el) {
            return el.id === decadeFromUrl;
          });

          if (decadeFromDecades.length > 0) {
            return decadeFromDecades.pop();
          } else {
            return undefined;
          }
        }
      }

      function getWallData() {
        if (vm.selectedDecade === undefined) {
          $location.path('/index/' + vm.decades[Math.floor(Math.random() * vm.decades.length)].id);
          return;
        }

        //var decade = vm.selectedDecade || 'any';
        return memoryWallPrepService.memoryWall.get(
          {
            decade: vm.selectedDecade.id
          },
          function (data) {
            vm.wallData = data.wallData;
            //vm.selectedDecade.id = data.wallData.metaData.decade;
            //$location.path('/index/' + vm.selectedDecade);
            return vm.wallData;
          }
        );
      }

      function getYouTubePlayer() {
        return memoryWallPrepService.getYouTubePlayer();
      }
    }
})();
