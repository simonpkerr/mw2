(function() {
    'use strict';
    describe('Amazon item directive', function() {
        var elm, scope;
        beforeEach(module('mwApp', 'mwApp.memoryWall'));
        beforeEach(module('mwTemplates'));
        
        beforeEach(inject(function($rootScope, $compile){
            elm = angular.element('<mw-item-amazon item="item"></mw-item-amazon>');
            scope = $rootScope;
            scope.item = {
                'provider': 'amazon',
                id: '1234',
                title: 'Mock title',
                image: 'sample-image.jpg',
                url: 'http://www.valid-url.com',
                price: ''
            };
            
            $compile(elm)(scope);
            scope.$digest();
        }));
        
        it('should create a container with a class of "amazon-item"', function() {
            expect(elm.hasClass('amazon-item')).toBe(true);
        });
        
        it('should link to the associated item', function() {
            expect($('a', elm).attr('href')).toEqual('http://www.valid-url.com');
        });
        
        
    });
})();



