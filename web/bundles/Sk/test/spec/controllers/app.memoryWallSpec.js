'use strict';

describe('MemoryWall controller', function () {

  // load the controller's module
  beforeEach(module('mwApp'));
  beforeEach(module('mwApp.memoryWall'));
    
  var ctrl, scope, $httpBackend,
  wallFixtures = 
    {
        wallData:{ 
            metaData:{ 
                decade:"1940s",
                pageNumber:1
            },
            providerData:[{
                provider:"amazon",
                id:"B004I8WHCO",
                title:"Fantasia - [DVD] [1940]",
                image:"FwAurdDHL._SL160_.jpg",
                url:"http://www.amazon.co.uk",
                price:""
            }]
        }
    };

  // Initialize the controller and a mock scope
  beforeEach(inject(function (_$httpBackend_, $rootScope, $controller) {
    $httpBackend = _$httpBackend_;
    $httpBackend.expectGET('/web/app_dev.php/api/memorywall')
        .respond(wallFixtures);
    
    scope = $rootScope.$new();
    ctrl = $controller('MemoryWall', {
      $scope: scope
    });
  }));

  it('should load data from the api on load', function () {
    expect(scope.wallData).toBe({});
    $httpBackend.flush();
    expect(scope.wallData).toEqual(wallFixtures);
  });
});
