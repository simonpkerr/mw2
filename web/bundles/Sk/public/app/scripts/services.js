'use strict';
/* Services */
var mwServices = angular.module('mwServices', ['ngResource']);

mwServices.factory('mediaTypes', ['$resource',
  function($resource) {
    //../api/mediatypes
    return $resource('/mw/web/app_dev.php/api/mediatypes', {
        query: {
          method: 'GET',
          isArray: true
        }
      });
  }
]);