/**
 *  This file defines the list of controllers, , directives and services of member module
 * 
 * @param {type} memberControllers
 * @param {type} memberRoutes
 * @param {type} memberServices 
 * @returns {String}
 */
define([
    "./member.controllers",
    "./member.routes",
    "./member.services",
    "./member.directives" 
], function (memberControllers, memberRoutes, memberServices, memberDirectives) {
    "use stricts";
    angular.module("member",
            [
                memberControllers,
                memberRoutes,  
                memberServices,
                memberDirectives
            ]);
    return "member";
});

