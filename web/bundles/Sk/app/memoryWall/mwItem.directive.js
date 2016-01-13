(function () {
  'use strict';
  angular
  .module('mwApp.memoryWall')
  .directive('mwItem', mwItem);

  mwItem.$inject = ['baseUrl', 'memoryWallService', '$timeout'];

  function mwItem(baseUrl, memoryWallService, $timeout) {
    var
      directive = {
        restrict: 'E',
        scope: {
          item: '=',
          selectedDecade: '='
        },
        controller: controller,
        controllerAs: 'vm',
        bindToController: true,
        replace: true,
        link: link,
        templateUrl: baseUrl + 'memoryWall/item.html'
      };
    return directive;

    function controller() {

      var vm = this;
      vm.exploredItems = [];
      vm.selected = false;
      vm.exploreWall = function (provider, id, itemIndex) {
        itemIndex = itemIndex || 0;

        //if item exists already, don't load it again, just open the window
        if ($.grep(vm.exploredItems, function (el) {
          return el.providerData !== undefined && el.providerData.id === id;
        }).length > 0) {
          vm.selected = true;
          return;
        }

        memoryWallService.memoryWallItem().get(
          {
            decade: vm.selectedDecade.id,
            provider: provider,
            id: id
          },
          function (data) {
            //make the api call return a generic itemData object with provider specific data inside
            vm.exploredItems.splice(itemIndex + 1, 0, data.wallItem);

            //move the window to the top of the selected panel
            vm.selected = true;

          }
        );
      };

      vm.scrollToElement = function (element) {
        $timeout(function () {
          $('html, body').animate(
            {
              scrollTop: element.offset().top
            }, 400, 'swing');
        }, 500);
      };

    }

    function link(scope, element, attrs) {
      scope.$watch('vm.selected', function (newVal, oldVal) {
        if (newVal) {
          scope.vm.scrollToElement(element);
        }
      });

    }
  }
})();
