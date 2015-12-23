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
        item: '='
      },
      replace: true,
      link: link,
      templateUrl: baseUrl + 'memoryWall/amazon.html'
    };
    return directive;


    function link(scope, element, attrs) {
      //scope.template = '/web/bundles/Sk/app/memoryWall/' + scope.item.provider + '.html';

    }
}

})();
