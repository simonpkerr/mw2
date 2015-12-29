(function () {
  'use strict';
  angular
  .module('mwApp.memoryWall')
  .directive('mwItemAmazon', mwItemAmazon);

  mwItemAmazon.$inject = ['baseUrl', 'memoryWallService'];

  function mwItemAmazon(baseUrl, memoryWallService) {
    var directive = {
      restrict: 'E',
      scope: {
        item: '='
        //,
        //exploreWall: '='
      },
      replace: true,
      link: link,
      templateUrl: baseUrl + 'memoryWall/amazon.html'
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
            scope.exploredItems.push(data.ItemLookupResponse);
            scope.selected = true;
          }
        );
      }

    }


  }
})();
