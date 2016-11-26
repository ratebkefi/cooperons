/**
 * @ngdoc controller
 * @name member.controllers:homeController
 * @description
 * This file defines the member's home controller
 */
define([], function () {
    'use strict';

    /*@ngInject*/
    function homeController($scope, common, memberData) {

        common.setMenuTab('home');
        $scope.member = memberData.member;
        $scope.parties = memberData.parties;
    }

    return homeController;
});