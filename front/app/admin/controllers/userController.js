/**
 * @ngdoc controller
 * @name admin.controllers:userController
 * @description
 * This file defines the user controller for admin space
 */
define([], function () {
    'use strict';

    userController.$inject = ['$scope', '$state', 'common', 'securityService', 'userService'];

    function userController($scope, $state, common, securityService, userService) {

        $scope.search = $state.params.searchValue;


        /**
         * @ngdoc method
         * @name loadUsers
         * @methodOf admin.controllers:userController
         * @description
         * Search User
         */
        $scope.loadUsers = function () {
            userService.getUsers($scope.search).then(function (response) {
                $scope.users = response.users;
            });
        };

        /**
         * @ngdoc method
         * @name connectAsMember
         * @methodOf admin.controllers:userController
         * @description
         * Connect administrator as a member
         *
         * @param {object} user A user to connect
         */
        $scope.connectAsMember = function (user) {
            securityService.connectUser(user.id)
                .then(function (data) {
                    common.saveMemberSession(data.token, data.roles);
                    common.openTab('member.home');
                });
        };

        /**
         * Init searchUsers
         */
        $scope.loadUsers();
    }

    return userController;
});