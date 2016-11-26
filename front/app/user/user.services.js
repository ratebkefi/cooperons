/*global define, angular, list of services*/
define([
    './services/securityService',
    './services/userService'
], function (securityService, userService) {
    'use strict';

    var servicesModuleName = 'user.services';

    angular.module(servicesModuleName, [])
        /* Models */

        /* View handlers */
        .factory('securityService', securityService)
        .factory('userService', userService);

    return servicesModuleName;
});