(function () {
  'use strict';
  angular
  .module('mwApp.memoryWall')
  .directive('mwItemAmazon', mwItemAmazon);

  mwItemAmazon.$inject = ['baseUrl'];

  function mwItemAmazon(baseUrl) {
    var directive = {
      restrict: 'E',
      scope: {
        item: '=',
        itemIndex: '='
      },
      require: '^mwItem',
      replace: true,
      link: link,
      templateUrl: baseUrl + 'memoryWall/amazon.html'
    };
    return directive;

    function link(scope, element, attrs, controller) {
      scope.exploreWall = controller.exploreWall;

    }


  }
})();
