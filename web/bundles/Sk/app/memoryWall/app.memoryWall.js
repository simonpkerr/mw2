angular.module('mwApp.memoryWall', [
    'mwApp.core',
    'ngResource'
    //add services and directives?
])
.controller('MemoryWall', MemoryWall);
MemoryWall.$inject = ['$scope', '$routeParams', 'memoryWallService', 'memoryWallPrepService'];

function MemoryWall($scope, $routeParams, memoryWallService, memoryWallPrepService) {
    //from here https://github.com/johnpapa/angularjs-styleguide#style-y034
    var vm = this;
    vm.wallData = {};
    vm.getWallData = getWallData;
    
    getWallData();
    
    function getWallData() {
        return getMemoryWall.get(function(data) {
            vm.wallData = data.wallData;
            //?
            $scope.wallData = data.wallData;
            return vm.wallData;
        });
    };
    
    
    
}
