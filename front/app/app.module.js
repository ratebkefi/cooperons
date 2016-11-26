/**
 * Inject all the external dependencies in our application
 * @returns {module}
 */
define([
    "app/core/core.module",
    "app/layout/layout.module",
    "app/admin/admin.module",
    "app/member/member.module",
    "app/corporate/corporate.module",
    "app/program/program.module",
    "app/party/party.module",
    "app/mandataire/mandataire.module",
    "app/autoEntrepreneur/autoEntrepreneur.module",
    "app/contract/contract.module",
    "app/public/public.module",
    "app/user/user.module",
    "app/prototype/prototype.module",
    "app/main.routes",
], function (core, layout, admin, member, corporate, program, party, mandataire, autoEntrepreneur, contract, _public, user, prototype, mainRoutes) {
    'use strict';

    var app = angular.module('app', [
        core,
        layout,
        admin,
        member,
        corporate,
        party,
        autoEntrepreneur,
        contract,
        program,
        mandataire,
        autoEntrepreneur,
        _public,
        user,
        prototype,
        mainRoutes
    ]);

    app.init = function () {
        angular.bootstrap(document, ['app']);
    };

    return app;
});