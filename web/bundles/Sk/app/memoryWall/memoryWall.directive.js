angular.module('mwApp.memoryWall')
        .directive('mwItemDirective', memoryWallDirective);

function memoryWallDirective(){
    var directive = {
        restrict: 'E',
        scope: {
            item: '='
        },
        replace: true,
        templateUrl: templateUrl,
        link: link,
        controller: MemoryWall,
        controllerAs: 'vm'
    };
    return directive;
    
    function templateUrl(elem, attr) {
        return attr.provider + '.html';
        
    }
    
    function link(scope , element, attrs) {
        //TODO functionality
        //referencing 'vm' accesses the controller 
    }
};