(function () {
  'use strict';
  angular
  .module('mwApp.memoryWall')
  .directive('mwDecadeSelect', mwDecadeSelect);

  mwDecadeSelect.$inject = ['baseUrl', '$timeout'];

  function mwDecadeSelect(baseUrl, $timeout) {
    var select,
    slider,
    link = {
      post: post
    },
    sliderHtml = '<div id="decade-slider" class="site-header__slider"></div>',
    directive = {
      restrict: 'E',
      scope: false,
      replace: true,
      templateUrl: baseUrl + 'memoryWall/mwDecadeSelect.html',
      link: link
    };
    return directive;

    function post (scope, element, attrs) {
      select = $('select[name="selectedDecade"]', element).change(function() {
        slider.slider('value', this.selectedIndex );
      });
      slider = $(sliderHtml).insertAfter(element).slider({
        min: 0,
        max: scope.vm.decades.length,
        range: 'min',
        value: select[0].selectedIndex,
        slide: function ( event, ui ) {

          select[0].selectedIndex = ui.value;
          scope.vm.selectedDecade = scope.vm.decades[ui.value - 1];
        }
      });

      $timeout(function(){
        select.change();
      });
    }

  }

})();
