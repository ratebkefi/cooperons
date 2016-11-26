/**
 *  This file defines the list of controllers and services of party module
 */
define([
    './party.controllers',
    './party.routes',
    './party.services'
], function (partyControllers, partyRoutes, partyServices) {
    'use strict';

    var moduleName = 'party';

    angular.module(moduleName, [partyControllers, partyRoutes, partyServices]);

    return moduleName;
});

