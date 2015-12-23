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
      },
      replace: true,
      link: link,
      templateUrl: baseUrl + 'memoryWall/amazon.html'
    };
    return directive;


    function link(scope, element, attrs) {

      // scope.explore = function (item) {
      //   return memoryWallService.memoryWallItem.get(
      //     {
      //       provider: item.provider,
      //       id: item.id
      //     },
      //     function (data) {
      //       return data;
      //     }
      //   );

      // };

    }
}

})();
