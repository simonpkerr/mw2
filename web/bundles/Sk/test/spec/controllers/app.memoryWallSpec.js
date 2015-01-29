'use strict';
//look here http://projectpoppycock.com/2014/03/05/mocking-resource-and-promises-in-angularjs-unit-tests/

describe('MemoryWall controller', function () {
    var vm,
            $q,
            $rootScope,
            $scope,
            $httpBackend,
            mockMemoryWallPrepService,
            mockMemoryWallService,
            mockYouTubeService,
            wallFixtures =
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


//  module(function($provide) {
//     $provide.factory('memoryWallService', function(){
//         this.memoryWall = jasmine.createSpy('memoryWall').andCallFake(function(){
//             return 
//         });
//     }); 
//  });
//  


//    memoryWallPrepService = {
//        memoryWall: memoryWallService.memoryWall(),
//        getYouTubePlayer: youTubeService.getPlayer
//    };
//        


    // Initialize the controller and a mock scope
    beforeEach(inject(function (_$q_, _$rootScope_, _$controller_) {
//    $httpBackend = _$httpBackend_;
//    $httpBackend.expectGET('/web/app_dev.php/api/memorywall')
//        .respond(wallFixtures);
        $q = _$q_;
        $rootScope = _$rootScope_;
    }));

    beforeEach(inject(function ($controller) {
        $scope = $rootScope.$new();
        mockMemoryWallService = {
            query: function() {
                queryDeferred = $q.defer();
                return { 
                    $promise: queryDeferred.promise 
                };
            }
        };
        spyOn(mockMemoryWallService, 'query').andCallThrough();
        
        mockMemoryWallPrepService = {
            memoryWall: mockMemoryWallService.memoryWall()
        };
        
        vm = $controller('MemoryWall', {
            '$scope': $scope,
            'memoryWallPrepService': mockMemoryWallPrepService
        });
    }));

    it('should load data from the api on load', function () {
        expect(vm.wallData.hasOwnProperty("providerData")).toBe(false);
        $httpBackend.flush();
        expect(vm.wallData.hasOwnProperty("providerData")).toEqual(true);
    });
});
