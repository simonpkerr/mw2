'use strict';
//look here http://projectpoppycock.com/2014/03/05/mocking-resource-and-promises-in-angularjs-unit-tests/

describe('MemoryWall controller', function () {
    var vm,
            $q,
            $rootScope,
            $scope,
            $httpBackend,
            mockMemoryWallPrepService,
            memoryWallService,
            memoryWallRequest,
            wallDataFixtures =
            {
                wallData: {
                    metaData: {
                        decade: "1940s",
                        pageNumber: 1
                    },
                    providerData: [{
                            provider: "amazon",
                            id: "B004I8WHCO",
                            title: "Fantasia - [DVD] [1940]",
                            image: "FwAurdDHL._SL160_.jpg",
                            url: "http://www.amazon.co.uk",
                            price: ""
                        }]
                }
            };
    // load the controller's module
    beforeEach(module('mwApp'));
    beforeEach(module('mwApp.memoryWall'));

    // Initialize the controller and a mock scope
    beforeEach(inject(function (_$q_, _$rootScope_) {
        $q = _$q_;
        $rootScope = _$rootScope_;
    }));

    beforeEach(inject(function (_$httpBackend_, $controller, _memoryWallService_) {
        $scope = $rootScope.$new();
        $httpBackend = _$httpBackend_;
        memoryWallService = _memoryWallService_;
        memoryWallRequest = new RegExp('/web/app_dev.php/api/memorywall.*');
        $httpBackend.expectGET(memoryWallRequest)
            .respond(200, wallDataFixtures);

        spyOn(memoryWallService, 'memoryWall').andCallThrough();
        
        mockMemoryWallPrepService = {
            memoryWall: memoryWallService.memoryWall(),
            getYouTubePlayer: function(){
                return function(){
                    return "mock youtube function";
                };
            }
        };
        
        vm = $controller('MemoryWall', {
            '$scope': $scope,
            'memoryWallPrepService': mockMemoryWallPrepService
        });
    }));

    it('should call the memoryWall service on init', function() {
        expect(memoryWallService.memoryWall).toHaveBeenCalled();
    });

    it('should load data from the api on load', function () {
        expect(vm.wallData.hasOwnProperty("providerData")).toBe(false);
        $httpBackend.flush();
        expect(vm.wallData).toEqual(wallDataFixtures.wallData);
    });
});
