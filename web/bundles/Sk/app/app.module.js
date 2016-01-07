(function () {
    'use strict';
    angular.module('mwApp', [
        /*shared widgets*/
        'mwApp.core',
        //'mwApp.filters',

        /*features*/
        'mwApp.memoryWall'
    ])
      .constant('baseUrl', 'app/');// '/web/bundles/Sk/app/';
})();
