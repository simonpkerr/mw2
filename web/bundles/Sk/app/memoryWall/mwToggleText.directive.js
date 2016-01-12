(function () {
  'use strict';
  angular.module('mwApp.memoryWall')
  .directive('toggleText', toggleText);

  toggleText.$inject = ['limitToFilter'];

  function toggleText (limitToFilter){
    var directive = {
      scope: {
        toggleText: '@',
        toggleLimit: '='
        }, // {} = isolate, true = child, false/undefined = no change
        // controller: function($scope, $element, $attrs, $transclude) {},
        // require: 'ngModel', // Array = multiple requires, ? = optional, ^ = check parent elements
        restrict: 'A', // E = Element, A = Attribute, C = Class, M = Comment
        template: '<div><p>{{ toggleText | limitTo:appliedToggleLimit }}</p><a ng-click="toggle()" href="#">show {{ toggled ? "more" : "less" }}</a></div>',
        // templateUrl: '',
        replace: true,
        //transclude: true,
        link: link
      };

      return directive;

      function link (scope, element, attrs, ctrl) {
        scope.appliedToggleLimit = scope.toggleLimit;
        scope.toggled = true;
        scope.toggle = function () {
          scope.toggled = !scope.toggled;
          scope.appliedToggleLimit = scope.toggled ? scope.toggleLimit : 9999999;
        };



      }
    }


  })();
