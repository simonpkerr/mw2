(function () {
  'use strict';
  angular
  .module('mwApp.memoryWall')
  .directive('mwItemWikimedia', mwItemWikimedia);

  mwItemWikimedia.$inject = ['baseUrl'];

  function mwItemWikimedia(baseUrl) {
    var directive = {
      restrict: 'E',
      scope: {
        item: '='
      },
      replace: true,
      link: link,
      templateUrl: baseUrl + 'memoryWall/wikimedia.html'
    };
    return directive;

    function link(scope, element, attrs) {
      scope.isOpen = false;
    }
  }

})();
