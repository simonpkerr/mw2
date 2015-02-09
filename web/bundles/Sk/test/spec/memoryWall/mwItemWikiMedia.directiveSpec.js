(function() {
    'use strict';
    describe('Wikimedia item directive', function() {
        var elm, scope;
        beforeEach(module('mwApp', 'mwApp.memoryWall'));
        beforeEach(module('mwTemplates'));
        
        beforeEach(inject(function($rootScope, $compile){
            elm = angular.element('<mw-item-wikimedia item="item"></mw-item-wikimedia>');
            scope = $rootScope;
            scope.item = {
                'provider': 'wikimedia',
                id: '10624400',
                title: 'File:Art Blakey al Capolinea.jpg',
                image: 'https://upload.wikimedia.org/wikipedia/commons/a/ad/Art_Blakey_al_Capolinea.jpg',
                url: 'https://commons.wikimedia.org/wiki/File:Art_Blakey_al_Capolinea.jpg',
                additional_info: {
                    categories: [
                        'Category:Art Blakey',
                        'Category:CC-Zero',
                        'Category:Music in 1980'
                    ]
                }
            };
            
            $compile(elm)(scope);
            scope.$digest();
        }));
        
        it('should create a container with a class of "wikimedia-item"', function() {
            expect(elm.hasClass('mwiki-item')).toBe(true);
        });
        
        it('should create an image element using the image property', function() {
            expect($('img', elm).attr('src')).toEqual('https://upload.wikimedia.org/wikipedia/commons/a/ad/Art_Blakey_al_Capolinea.jpg');
        });
        
        it('should link to the associated page on wikimedia', function() {
            expect($('a', elm).attr('href')).toEqual('https://commons.wikimedia.org/wiki/File:Art_Blakey_al_Capolinea.jpg');
        });
        
        it('should show any existing related categories when the "show info" link is pressed', function() {
            $('a.readmore', elm).click();
            expect($('div.info-panel', elm)).toContain('Art Blakey');
        });
        
        
        
    });
})();



