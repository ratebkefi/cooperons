/**
 * @ngdoc service
 * @name program.services:invitationService
 * @description
 * This file defines the invitationService
 */
define([], function () {
    'use strict';
    invitationService.$inject = ["$q", 'Restangular'];
 
    function invitationService($q, Restangular) {
        return {
 
            /**
            * @ngdoc method
            * @name postInvitation
            * @methodOf program.services:invitationService
            * @description
            * Create invitation
            *  
            * @param {object} invitation Invitation data  
            * 
            * @returns {object} Invitation data 
            */
            postInvitation: function (invitation) {
                return Restangular.all('invitations').post(invitation)
                    .then(function (response) {
                        return response.data;
                    }, function (fallback) {
                        return $q.reject(fallback);
                    });
            },
 
            /**
            * @ngdoc method
            * @name getInvitation
            * @methodOf program.services:invitationService
            * @description
            * Get invitation
            *  
            * @param {string} token Token for invitation
            * 
            * @returns {object} Invitation data 
            */
            getInvitation: function (token) {
                return Restangular.one('public').one('invitations', token).get()
                    .then(function (response) {
                        return response.data;
                    }, function (fallback) {
                        return $q.reject(fallback);
                    });
            },
 
            /**
            * @ngdoc method
            * @name publicPostInvitation
            * @methodOf program.services:invitationService
            * @description
            * Send mail invitation for cooperonsPlus
            *  
            * @param {string} token Token for invitation
            * @param {object} invitation Invitation data
            * 
            * @returns {object} Invitation data 
            */
            publicPostInvitation: function (token, invitation) {
                return Restangular.one('public').one('invitations', token).customPOST(invitation)
                    .then(function (response) {
                        return response;
                    }, function (fallback) {
                        return $q.reject(fallback.data);
                    });
            }
        };
    }

    return invitationService;
});