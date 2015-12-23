(function() {
    'use strict';
    describe('Youtube item directive', function() {
        var elm, scope, youTubeService;
        beforeEach(module('mwApp', 'mwApp.memoryWall'));
        beforeEach(module('mwTemplates'));

        beforeEach(inject(function($rootScope, $compile, _youTubeService_){
            youTubeService = _youTubeService_;
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

        it('should include the title', function() {
            expect($('.item-title', elm).text()).toEqual('Youtube item title');
        });

        it('should show a link to play the video', function() {
            expect($('a', elm).attr('ng-click')).toEqual('playYouTubeVideo()');
        });

        it('should wait until the youtube service is ready before trying to create a player', function() {
            spyOn(youTubeService, 'createPlayer').and.callThrough();
            youTubeService.isReady = false;
            $('a[ng-click]', elm).click();

            expect(youTubeService.createPlayer).not.toHaveBeenCalled();

        });

        it('should create a youtube player when the link is clicked for the first time', function() {
            youTubeService.isReady = true;
            spyOn(youTubeService, 'createPlayer').and.callFake(function() {
                return 'fake-player';
            });
            $('a[ng-click]', elm).click();

            expect(youTubeService.createPlayer).toHaveBeenCalled();
        });


    });

})();

