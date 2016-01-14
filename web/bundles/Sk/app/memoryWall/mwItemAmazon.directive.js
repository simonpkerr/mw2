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
      controller: controller,
      controllerAs: 'vm',
      bindToController: true,
      templateUrl: baseUrl + 'memoryWall/amazon.html'
    };
    return directive;

    function controller() {
      var vm = this;

      vm.getItemPrice = function (item) {
        if(item.price !== undefined) {
          return 'Buy this from Amazon for ' + item.price;
        } else {
          return 'Buy this from Amazon';
        }

      };
    }

    function link(scope, element, attrs, controller) {
      scope.vm.exploreWall = controller.exploreWall;
      controller.scrollToElement(element);

      //watch parent selectedItem
      scope.$watch('controller.selectedItem', function (newVal, oldVal) {
        if (scope.vm.item.providerData !== undefined && newVal === scope.vm.item.providerData.id) {
          controller.scrollToElement(element);
        }
      });
    }


  }
})();
