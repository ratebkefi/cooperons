/*global define, angular, list of services*/
define([
    "./directives/cprSearchUsers/cprSearchUsers"
], function (cprSearchUsersDirective) {
    'use strict';

    var directiveModuleName = "admin.directives";

    angular.module(directiveModuleName, [])
        .directive("cprSearchUsers", cprSearchUsersDirective) ;

    return directiveModuleName;
});