(function () {
  'use strict';
  angular.module('mwApp.memoryWall')
  .directive('toggleText', toggleText);

  toggleText.$inject = ['baseUrl'];

  function toggleText (baseUrl){
    var directive = {
      scope: {
        toggleText: '@',
        toggleLimit: '='
        }, // {} = isolate, true = child, false/undefined = no change
        // controller: function($scope, $element, $attrs, $transclude) {},
        // require: 'ngModel', // Array = multiple requires, ? = optional, ^ = check parent elements
        restrict: 'A', // E = Element, A = Attribute, C = Class, M = Comment
        // template: '',
        templateUrl: baseUrl + 'memoryWall/toggleText.html',
        replace: true,
        //transclude: true,
        link: link
      };

      return directive;

      function link (scope, element, attrs, ctrl) {
        scope.appliedToggleLimit = scope.toggleLimit;
        scope.toggled = true;
        scope.toggleEnabled = true;
        scope.toggle = function () {
          scope.toggled = !scope.toggled;
          scope.appliedToggleLimit = scope.toggled ? scope.toggleLimit : scope.toggleText.length;
        };

        if (scope.toggleText.length <= scope.toggleLimit) {
          scope.toggleEnabled = false;
        }

      }
    }


  })();
