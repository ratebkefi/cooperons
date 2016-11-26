/**
 * @ngdoc controller
 * @name public.controllers:invitationController
 * @description
 * This file defines the invitation parrainage controller
 */
define([], function () {
    'use strict';

    invitationController.$inject = ['$rootScope', '$scope', '$state', '$window', 'invitationService', '$sce'];

    function invitationController($rootScope, $scope, $state, $window, invitationService, $sce) {

        var token = $state.params.token;
        invitationService.getInvitation(token).then(function (data) {
            $scope.participate = data.participate;
            $scope.program = data.program;
            $scope.invitationPlus = data.isInvitationPlus;
            $scope.member = data.participate.member;
            $scope.token = token;
            $scope.htmlOperations = $sce.trustAsHtml(data.htmlOperations.content);
            $scope.corporate = data.corporate;
            $scope.hasMailInvitation = $scope.participate.hasMailInvitation;
            $scope.member.college = data.college;
            $scope.delegate = data.delegate;
            $rootScope.spaceReady = true;
        }).catch(function () {
            common.dispatch();
        });

    }

    return invitationController;
});