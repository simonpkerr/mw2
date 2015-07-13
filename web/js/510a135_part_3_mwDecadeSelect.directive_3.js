(function () {
    'use strict';
    angular
    .module('mwApp.memoryWall')
    .directive('mwDecadeSelect', mwDecadeSelect);
    
    function mwDecadeSelect() {
        var select,
            slider,
            link = {
                post: post
            },
            sliderHtml = '<div id="decade-slider" class="site-header__slider"></div>',
            slide = function ( event, ui ) {
                select[ 0 ].selectedIndex = ui.value - 1;
            },
            change = function(){
                slider.slider( "value", this.selectedIndex + 1 );
            },
            directive = {
                restrict: 'E',
                scope: {
                    decades: '=',
                    selectedDecade: '=',
                    searchEvent: '='
                },
                replace: true,
                templateUrl: '/web/bundles/Sk/app/memoryWall/mwDecadeSelect.html',
                link: link

            };
        return directive;

        function post (scope, element, attrs) {
            select = $('select[name="selectedDecade"]', element).change(change);
            slider = $(sliderHtml).insertAfter(element).slider({
               min: 1,
               max: scope.decades.length + 1,
               range: "min",
               value: select[ 0 ].selectedIndex + 1,
               slide: slide
            });
        }
        
    }

})();
