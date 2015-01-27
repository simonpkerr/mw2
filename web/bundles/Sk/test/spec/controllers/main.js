'use strict';

describe('MemoryWall controller', function () {

  // load the controller's module
  beforeEach(module('mwApp'));

  var MWCtrl,
    scope;

  // Initialize the controller and a mock scope
  beforeEach(inject(function ($controller, $rootScope) {
    scope = $rootScope.$new();
    MWCtrl = $controller('MemoryWall', {
      $scope: scope
    });
  }));

  it('should attach a list of awesomeThings to the scope', function () {
    expect(scope.awesomeThings.length).toBe(3);
  });
});
