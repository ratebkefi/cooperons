/**
 * @ngdoc service
 * @name user.services:userService
 * @description
 * This file defines the userService
 */
define([], function () {
    'use strict';
    userService.$inject = ["$q", "Restangular"];
 
    function userService($q, Restangular) {
        return {
            
            baseUsers: Restangular.all('users'),

            basePublic: Restangular.all('public'),
 
            /**
            * @ngdoc method
            * @name connectUser
            * @methodOf user.services:userService
            * @description
            * Search Users
            *  
            * @param {string} searchValue Key to searching
            * 
            * @returns {object} Users data 
            */
            getUsers: function (searchValue) {
                return this.baseUsers.getList({key: searchValue})
                    .then(function (response) {
                        return response.data;
                    }, function (fallback) {
                        return $q.reject(fallback);
                    });
            },

            /**
            * @ngdoc method
            * @name getToken
            * @methodOf user.services:userService
            * @description
            * Retrieve token
            *    
            * @returns {token}
            */
            getToken: function (token) {
                return Restangular.all('public').one('tokens', token).get()
                    .then(function (response) {
                        return response.data;
                    }, function (fallback) {
                        return $q.reject(fallback);
                    });
            },
 
            /**
            * @ngdoc method
            * @name getUser
            * @methodOf user.services:userService
            * @description
             * Retrieve user
            * 
            * @returns {user} User 
            */
            getUser: function () {
                return Restangular.one('user').get()
                    .then(function (response) {
                        return response.data;
                    }, function (fallback) {
                        return $q.reject(fallback);
                    });
            },
 
            /**
            * @ngdoc method
            * @name putUser
            * @methodOf user.services:userService
            * @description
            * Update user's account
            *  
            * @param {object} data user account data
            * 
            * @returns {object} User data 
            */
            putUser: function (data) {
                return Restangular.one('user').customPUT(data)
                    .then(function (response) {
                        return response;
                    }, function (fallback) {
                        return $q.reject(fallback);
                    });
            },
 
            /**
            * @ngdoc method
            * @name postUser
            * @methodOf user.services:userService
            * @description
            * Create user
            * 
            * @param {string} token Token of user
            * @param {object} data user account data
            * 
            * @returns {object} User data 
            */
            postUser: function (token, data) {
                return this.basePublic.one('users', token).customPOST(data)
                    .then(function (response) {
                        return response.data;
                    }, function (response) {
                        return $q.reject(response);
                    });
            },
 
            /**
            * @ngdoc method
            * @name publicPatchUser
            * @methodOf user.services:userService
            * @description
            * Partial user account modification : public actions
            *  
            * @param {object} patch Data to patching
            * 
            * @returns {object} User data 
            */
            publicPatchUser: function (patch) {
                return Restangular.all('public').one('user').patch(JSON.stringify(patch))
                    .then(function (response) {
                        return response;
                    }, function (fallback) {
                        return $q.reject(fallback);
                    });
            },
 
            /**
            * @ngdoc method
            * @name patchUser
            * @methodOf user.services:userService
            * @description
            * Partial user account modification
            *  
            * @param {object} patch Data to patching
            * 
            * @returns {object} User data 
            */
            patchUser: function (patch) {
                return Restangular.one('user').patch(JSON.stringify(patch))
                    .then(function (response) {
                        return response;
                    }, function (fallback) {
                        return $q.reject(fallback);
                    });
            },
 
            /**
            * @ngdoc method
            * @name getUserMember
            * @methodOf user.services:userService
            * @description
            * Retrieve member by user id
            *  
            * @param {number} userId Id of user
            * 
            * @returns {object} Member data 
            */
            getUserMember: function (userId) {
                return Restangular.one('users', userId).one('member').get()
                    .then(function (response) {
                        return response.data;
                    }, function (fallback) {
                        return $q.reject(fallback);
                    });
            },
 
            /**
            * @ngdoc method
            * @name getUserAutoEntrepreneur
            * @methodOf user.services:userService
            * @description
            * Retrieve autoEntrepreneur
            *  
            * @param {number} userId Id of user
            * 
            * @returns {object} AutoEntrepreneur data 
            */
            getUserAutoEntrepreneur: function (userId) {
                return Restangular.one('users', userId).one('autoentrepreneur').get()
                    .then(function (response) {
                        return response.data;
                    }, function (fallback) {
                        return $q.reject(fallback);
                    });
            }
        };
    }

    return userService;
});