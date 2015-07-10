(function () {
    'use strict';
    angular
    .module('mwApp.memoryWall')
    .directive('mwDecadeSelect', mwDecadeSelect);
    
    function mwDecadeSelect() {
        var select,
            slider,
            sliderHtml = '<div id="decade-slider" class="site-header__slider"></div>',
            slide = function ( event, ui ) {
                select[ 0 ].selectedIndex = ui.value - 1;
            },
            change = function(){
                slider.slider( "value", this.selectedIndex + 1 );
                console.log(this.selectedIndex);
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

        // function compile(element, attrs) {
        //     var modelAccessor = $parse(attrs.ngModel);
        //     var html = '<select id="'+ attrs.id +'-select"></select>'
        // }

        function link(scope, element, attrs) {
            select = $('select', element);
            slider = $(sliderHtml).insertAfter(element).slider({
               min: 1,
               max: 6,
               range: "min",
               value: select[ 0 ].selectedIndex + 1,
               slide: slide,
               change: change
            });
        }
    }

})();
