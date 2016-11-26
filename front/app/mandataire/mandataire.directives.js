/*global define, angular, list of services*/
define([  
    "./directives/infosMandataire" 
], function (infosMandataireDirective) {
    'use strict';
    var directiveModuleName = "mandataire.directives"; 
     angular.module(directiveModuleName, [])
            .directive("infosMandataire", infosMandataireDirective);
    return directiveModuleName;
});