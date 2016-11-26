/*global angular*/
define([], function () {
    const cooperons = {
        events: {
            refreshInfoMandataire: 'refresh.infoMandataire',
            partyChanged: 'notification.party.changed',
            refreshParty: 'action.refresh.party',
        },
        contractTypes: {
            clients: 'default:owner',
            distributeurs: 'affair:client',
            prestataires: 'default:client',
            fournisseurs: 'affair:owner'
        },
        contractLabels: {
            clients: 'client',
            distributeurs: 'commercial',
            prestataires: 'prestataire',
            fournisseurs: 'fournisseur'
        },
        invitationTypes: {
            clients: 'client',
            distributeurs: 'partenaire distributeur',
            prestataires: 'prestataire',
            fournisseurs: 'partenaire fournisseur'
        },
        partyTabs: {
            users: 'Utilisateurs',
            college: 'College',
            clients: 'Clients',
            distributeurs: 'Distributeurs',
            prestataires: 'Prestataires',
            fournisseurs: 'Fournisseurs'
        }

    };

    return cooperons;
});