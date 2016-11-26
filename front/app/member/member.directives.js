/*global define, angular, list of services*/
define([  
    "./directives/invitationPlus",
    "./directives/cloudSponge" 
], function (invitationPlusDirective, cloudSpongeDirective) {
    'use strict';
    var directiveModuleName = "member.directives"; 
     angular.module(directiveModuleName, [])
            .directive("invitationPlus", invitationPlusDirective)
            .directive("cloudSponge", cloudSpongeDirective);
    return directiveModuleName;
});