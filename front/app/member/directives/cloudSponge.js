/**
 * @ngdoc directive
 * @name member.directives:cloudSponge 
 * 
 * @restrict 'E' 
 *
 * @description
 * Invtation by cloudSponge
 * 
 *
 * @example
   <example>
     <file name="exemple.html">
        <div>
            <cloud-sponge></cloud-sponge>
        </div>
     </file>
   </example>
 */
define([], function () {
    'use strict';
    cloudSponge.$inject = ['$state', 'invitationService', 'Notification'];
  
    function cloudSponge($state, invitationService, Notification) {
        return {
            restrict: 'E',
            scope: {
                token: '=',
                member: '='
            },
            templateUrl: 'app/member/views/directives/cloudSponge.html',
            controller: function ($scope, $rootScope) {

                $scope.contactList = {emails: ''};
 
                /**
                * @ngdoc method
                * @name cloudSpongeLaunch
                * @methodOf member.directives:cloudSponge 
                * @description
                * Launch cloud Sponge by messaging type (gmail,yahoo,windowslive)
                * 
                * @param {string} type Type to launching
                *  
                */
                $scope.cloudSpongeLaunch = function (type) {
                    return cloudsponge.launch(type);
                };
 
                /**
                * @ngdoc method
                * @name loadContacts
                * @methodOf member.directives:cloudSponge 
                * @description
                * Load Contacts
                *  
                * @return {object} Contacts data
                */
                $scope.loadContacts = function () {
                    window.csPageOptions = {
                        sources: ['gmail', 'yahoo', 'windowslive'],
                        ignoreMultipleEmails: true,
                        skipSourceMenu: true,
                        mobile_render: true,
                        inlineOauth: 'mobile',
                        afterSubmitContacts: function (contacts) {
                            $scope.$apply(function () {
                                var formattedContacts = [];
                                angular.forEach(contacts, function (value, key) {
                                    formattedContacts.push(value.first_name + ' ' + value.last_name + '<' + value.email[0].address + '>');
                                });
                                $scope.contactList.emails = formattedContacts.join(', ');
                            });
                        }
                    };

                };
                $scope.loadContacts();

                // Asynchronously include the widget library. 
                (function (u) {
                    var d = document, s = 'script', a = d.createElement(s), m = d.getElementsByTagName(s)[0];
                    a.async = 1;
                    a.src = u;
                    m.parentNode.insertBefore(a, m);
                })('//api.cloudsponge.com/widget/daa4e6890ba0ef05029ce667958684434839b023.js');
 
                /**
                * @ngdoc method
                * @name sendInvitation
                * @methodOf member.directives:cloudSponge 
                * @description
                * Send invitation
                *  
                * @param {object} form For of invatation to be validated
                */
                $scope.sendInvitation = function (form) {
                    if (form.$valid) {
                        var invitation = {emailsFilleuls: $scope.contactList.emails};
                        invitationService.publicPostInvitation($scope.token, invitation).then(function (response) {
                            if (response && response.status == "warning") {
                                Notification.error({
                                    title: '<i class="fa fa-exclamation-circle" style="color: white">Erreur !</i>',
                                    message: response.message
                                })
                            }
                            else {
                                if ($scope.member.isUser) {
                                    $state.go("member.home");
                                } else if ($scope.member.isPreProd) {
                                    $state.go("public.createUser", {token: $scope.token});
                                } else {
                                    $state.go("public.connectMember", {token: $scope.token});
                                }
                            }
                            $scope.contactList = {emails: ''};
                            form.$submitted = false;
                        });
                    } else {
                        return false;
                    }
                };

            }
        };
    }

    return cloudSponge;
});