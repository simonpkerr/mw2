/**
 *
 * @description 7digital directive gets release info by tag
 * then allows previews of each track to be played
 */
 (function () {
  'use strict';
  angular
  .module('mwApp.memoryWall')
  .directive('mwItemSevendigital', mwItemSevendigital);

  mwItemSevendigital.$inject = ['baseUrl'];

  function mwItemSevendigital(baseUrl) {
    var directive = {
      restrict: 'E',
      scope: {
        item: '='
      },
      replace: true,
      templateUrl: baseUrl + 'memoryWall/sevendigital.html',
      link: link

    };
    return directive;

    function link(scope, element, attrs) {
      scope.isOpen = false;
    }
  }

})();
