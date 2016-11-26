/**
 * @ngdoc service
 * @name user.services:securityService
 * @description
 * This file defines the securityService
 */
define([], function () {
    'use strict';
    securityService.$inject = ["$q", "Restangular"];
 
    function securityService($q, Restangular) {
        return {
 
            /**
            * @ngdoc method
            * @name login
            * @methodOf user.services:securityService
            * @description
            * Get authentication token
            *  
            * @param {object} user User data to login
            *  
            */
            login: function (user) {
                return Restangular.one('login_check').customPOST(user)
                    .then(function (response) {
                        return response;
                    }, function (fallback) {
                        return $q.reject(fallback);
                    });
            },
 
            /**
            * @ngdoc method
            * @name connectUser
            * @methodOf user.services:securityService
            * @description
            * Authenticate administrator as member
            *  
            * @param {number} userId Id of user
            *  
            */
            connectUser: function (userId) {
                return Restangular.one('users', userId).one('connect').get()
                    .then(function (response) {
                        return response.data;
                    }, function (fallback) {
                        return $q.reject(fallback);
                    });
            }
        };
    }

    return securityService;
});