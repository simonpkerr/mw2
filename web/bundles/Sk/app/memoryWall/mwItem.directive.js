(function () {
  'use strict';
  angular
  .module('mwApp.memoryWall')
  .directive('mwItem', mwItem);

  mwItem.$inject = ['baseUrl', 'memoryWallService', '$timeout'];

  function mwItem(baseUrl, memoryWallService, $timeout) {
    var directive = {
      restrict: 'E',
      scope: {
        item: '='
      },
      replace: true,
      link: link,
      templateUrl: baseUrl + 'memoryWall/item.html'
    };
    return directive;

    function link(scope, element, attrs) {

      scope.exploredItems = [];
      scope.exploreWall = exploreWall;
      scope.selected = false;

      function exploreWall(item) {
        memoryWallService.memoryWallItem().get(
          {
            provider: item.provider,
            id: item.id
          },
          function (data) {
            //make the api call return a generic itemData object with provider specific data inside
            scope.exploredItems.push(data.wallItem);
            scope.selected = true;

            //move the window to the top of the selected panel
            // $timeout(function(){

            // });

          }
        );
      }
    }


  }
})();
