/*global define, angular, list of services*/
define([
    "./directives/fileModel",
    "./directives/wizardSteps",
], function (fileModelDirective, wizardStepsDirective) {
    'use strict';
    var directiveModuleName = "program.directives";
    angular.module(directiveModuleName, [])
        .directive("fileModel", fileModelDirective)
        .directive("wizardSteps", wizardStepsDirective) ;
    return directiveModuleName;
});