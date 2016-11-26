/**
 * This file defines the list controllers of program module
 *
 * @param {controller} invitationParrainageController
 * @param {controller} createUserController
 * @param {controller} connectMemberController
 * @param {controller} activateAccountController
 * @returns {module}
 */
define([
    "./controllers/invitationController",
    "./controllers/createUserController",
    "./controllers/connectMemberController",
    "./controllers/activateAccountController"
], function (invitationController,
             createUserController,
             connectMemberController,
             activateAccountController) {

    'use strict';
    var controllersModuleName = "public.controllers";
    angular.module(controllersModuleName, [])
        .controller("public:invitationController", invitationController)
        .controller("public:createUserController", createUserController)
        .controller("public:connectMemberController", connectMemberController)
        .controller("public:activateAccountController", activateAccountController);

    return controllersModuleName;
});