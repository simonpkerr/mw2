(function () {
  'use strict';
  angular
  .module('mwApp.memoryWall')
  .directive('mwItem', mwItem);

  mwItem.$inject = ['baseUrl', 'memoryWallService', '$timeout'];

  function mwItem(baseUrl, memoryWallService, $timeout) {
    var
    // options = {
    //     items: 1,
    //     stagePadding: 50,
    //     margin: 10,
    //     pagination: true,
    //     nav: true,
    //     navText: ['<span class="icon-left-open"><span>', '<span class="icon-right-open"><span>']
    //   },
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

    }

    function link(scope, element, attrs) {
      scope.$watch('vm.selected', function (newVal, oldVal) {
        if (newVal) {
          $timeout(function () {
            window.scrollTo(0, element.offset().top);
          }, 500);

        }
      });

    }
  }
})();
