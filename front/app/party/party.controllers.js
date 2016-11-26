/**
 * This file defines the list controllers of party module
 * 
 * @param {controller} partyController
 * @param {controller} collaboratorController
 * @param {controller} habilitationController
 * @returns {controller}
 */
define([
    "./controllers/partyController",
    "./controllers/collaboratorController",
    "./controllers/habilitationController"
], function (PartyController, CollaboratorController, HabilitationController) {
    'use strict';

    var controllersModuleName = "party.controllers";

    angular.module(controllersModuleName, [])
        .controller("party:PartyController", PartyController)
        .controller("party:CollaboratorController", CollaboratorController)
        .controller("party:HabilitationController", HabilitationController);

    return controllersModuleName;
});