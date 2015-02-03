(function() {
    'use strict';
    describe('Youtube item directive', function() {
        var elm, scope;
        beforeEach(module('mwApp', 'mwApp.memoryWall'));
        beforeEach(module('mwTemplates'));
        
        beforeEach(inject(function($rootScope, $compile){
            elm = angular.element('<mw-item-youtube item="item"></mw-item-youtube>');
            scope = $rootScope;
            scope.item = {
                'provider': 'youtube',
                id: '1234',
                title: 'Youtube item title',
                image: 'sample-image.jpg',
                url: 'http://www.valid-url.com'
            };
            
            $compile(elm)(scope);
            scope.$digest();
        }));
    });
    
})();

