(function () {
  'use strict';
  angular
  .module('mwApp.memoryWall')
  .directive('mwExploredItem', mwExploredItem);

  mwExploredItem.$inject = ['baseUrl', 'memoryWallService'];

  function mwExploredItem(baseUrl, memoryWallService) {
    var directive = {
      restrict: 'E',
      scope: {
        exploredItems: '=',
        exploreWall: '='
      },
      replace: true,
      link: link,
      templateUrl: baseUrl + 'memoryWall/exploredItem.html'
    };
    return directive;


    function link(scope, element, attrs) {

    }
}

})();
