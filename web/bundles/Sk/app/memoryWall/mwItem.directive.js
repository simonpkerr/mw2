(function () {
  'use strict';
  angular
  .module('mwApp.memoryWall')
  .directive('mwItem', mwItem);

  mwItem.$inject = ['baseUrl', 'memoryWallService', '$timeout'];

  function mwItem(baseUrl, memoryWallService, $timeout) {
    var options = {
        items: 1,
        stagePadding: 50,
        margin: 10,
        pagination: true,
        nav: true,
        navText: ['<span class="icon-left-open"><span>', '<span class="icon-right-open"><span>']
      },
      directive = {
        restrict: 'E',
        scope: {
          item: '='
        },
        replace: true,
        link: link,
        templateUrl: baseUrl + 'memoryWall/item.html'
      };
    return directive;

    function link(scope, element, attrs) {
      var carousel = $('.owl-carousel', element);
      //carousel.owlCarousel();
      scope.exploredItems = [];
      scope.exploreWall = exploreWall;
      scope.selected = false;

      // scope.$watch('scope.exploredItems', function() {
      //   carousel.trigger('refresh.owl.carousel');
      // });
      //
      scope.setupCarousel = function (itemIndex) {
        if (scope.exploredItems.length === 1) {
          carousel.owlCarousel(options);

        } else {
          // carousel.trigger('destroy.owl.carousel').removeClass('owl-carousel owl-loaded');
          // carousel.find('.owl-stage-outer').children().unwrap();
          carousel.owlCarousel(options);
        }
      };


      function exploreWall (provider, id, itemIndex) {
        itemIndex = itemIndex || 0;

        if ($.grep(scope.exploredItems, function (el) {
          return el.data.id === id;
        }).length > 0) {
          scope.selected = true;
          return;
        }

        memoryWallService.memoryWallItem().get(
          {
            provider: provider,
            id: id
          },
          function (data) {
            //make the api call return a generic itemData object with provider specific data inside
            carousel.trigger('destroy.owl.carousel').removeClass('owl-loaded');
            $('.item', carousel).unwrap().unwrap().unwrap();
            scope.exploredItems.push(data.wallItem);

            //move the window to the top of the selected panel
            //setupCarousel(itemIndex);
            scope.selected = true;


          }
        );
      }
    }


  }
})();
