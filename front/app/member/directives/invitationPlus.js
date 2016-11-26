/**
 * @ngdoc directive
 * @name member.directives:invitationPlus
 *
 * @restrict 'E'
 *
 * @description
 * This file defines the invitationPlus directive
 *
 *
 * @example
   <example>
     <file name="exemple.html">
        <div>
            <invitation-plus></invitation-plus>
        </div>
     </file>
   </example>
 */
define([], function () {
    'use strict';
    invitationPlus.$inject = ['invitationService', 'Notification'];

    function invitationPlus(invitationService, Notification) {
        return {
            restrict: 'E',
            scope: {
                typeInvitation: '@',
                collaboratorId: '=',
                token: '=',
                beforeSubmit: '&',
                onSuccess: '&'
            },
            templateUrl: 'app/member/views/directives/invitationPlus.html',
            controller: function ($scope) {

                /**
                 * @ngdoc method
                 * @name init
                 * @methodOf member.directives:invitationPlus
                 * @description
                 * Initialize scope
                 *
                 */
                $scope.init = function () {
                    $scope.invitation = {
                        typeInvitation: $scope.typeInvitation,
                        collaboratorId: $scope.collaboratorId,
                        firstName: '',
                        lastName: '',
                        email: ''
                    };
                };

                $scope.init();

                /**
                 * @ngdoc method
                 * @name sendInvitation
                 * @methodOf member.directives:invitationPlus
                 * @description
                 * Send invitation
                 *
                 * @param {object} form For of invatation to be validated
                 */
                $scope.sendInvitation = function (form) {
                    if (form.$valid) {
                        var promise = invitationService.publicPostInvitation($scope.token, $scope.invitation);
                        if ($scope.beforeSubmit) {
                            $scope.beforeSubmit();
                        }
                        promise.then(function (response) {
                            if (response.status == "warning") {
                                Notification.error({
                                    title: '<i class="fa fa-exclamation-circle" style="color: white"> Erreur</i>',
                                    message: response.message
                                });
                            }

                            $scope.init();
                            form.$submitted = false;
                            if ($scope.onSuccess) {
                                $scope.onSuccess();
                            }
                        });
                    }
                };

            }
        };
    }

    return invitationPlus;
});