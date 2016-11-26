/**
 * @ngdoc directive
 * @name admin.directives:searchUsers
 *   
 * @description
 * Filter users
 *
 * @example
     <example>
             <div>
                <search-users></search-users>
             </div>
     </example>
 */
define([], function () {
    'use strict';
    searchUsers.$inject = ['$state'];

    function searchUsers($state) {
        return {
            restrict: 'E',
            templateUrl: 'app/admin/directives/cprSearchUsers/searchUsers.html',
            scope: {
                search: '=?'
            },
            controller: function ($scope, $state) {

                $scope.search == undefined ? $scope.search = '' : null;

                /**
                 * @ngdoc method
                 * @name searchUsers
                 * @methodOf admin.controllers:autoentrepreneurController
                 * @description
                 * Search User
                 */
                $scope.searchUsers = function () {
                    $state.go("admin.user", {searchValue: $scope.search}, {reload: true});
                };
            }
        };
    }

    return searchUsers;
});
