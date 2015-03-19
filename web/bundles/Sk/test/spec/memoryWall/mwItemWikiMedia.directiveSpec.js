(function() {
    'use strict';
    describe('Wikimedia item directive', function() {
        var elm, scope;
        beforeEach(module('mwApp', 'mwApp.memoryWall'));
        beforeEach(module('mwTemplates'));
        
        beforeEach(inject(function($rootScope, $compile){
            elm = angular.element('<mw-item-wikimedia item="item"></mw-item-wikimedia>');
            scope = $rootScope;
            scope.item = JSON.parse('{"provider":"wikimedia","id":253304,"title":"Victor22529A","image":"http:\/\/upload.wikimedia.org\/wikipedia\/commons\/e\/ea\/Victor22529A.jpg","thumbnail":"http:\/\/upload.wikimedia.org\/wikipedia\/commons\/thumb\/e\/ea\/Victor22529A.jpg\/300px-Victor22529A.jpg","url":"http:\/\/commons.wikimedia.org\/wiki\/File:Victor22529A.jpg","categories":["Files with no machine-readable author","Files with no machine-readable source","Location not applicable","Media missing infobox template","Music in 1930","Nat Shilkret","Nipper in logos","PD ineligible","Record labels","Victor Records"],"mediatype":"BITMAP"}');
            $compile(elm)(scope);
            scope.$digest();
        }));
        
        it('should create a container with a class of "wikimedia-item"', function() {
            expect(elm.hasClass('wikimedia-item')).toBe(true);
        });
        
        it('should create an image element using the image property if the media type is BITMAP', function() {
            expect($('img[ng-switch-when="BITMAP"]', elm).attr('ng-src')).toEqual('http://upload.wikimedia.org/wikipedia/commons/thumb/e/ea/Victor22529A.jpg/300px-Victor22529A.jpg');
        });
        
        it('should link to the associated page on wikimedia', function() {
            expect($('a.oj', elm).attr('href')).toEqual('http://commons.wikimedia.org/wiki/File:Victor22529A.jpg');
        });
        
        it('should show description and categories when the "show details" link is clicked', function() {
          expect($('div.info-window', elm).attr('class')).toContain('ng-hide');  
          $('a.cta', elm).click();            
          expect($('div.info-window', elm).attr('class')).not.toContain('ng-hide');
        });
        
        
        
    });
})();



