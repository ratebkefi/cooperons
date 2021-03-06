var apiMessages = {
    4000: 'Echec de validation du formulaire',
    4001: 'Aucun paramètre envoyer',
    4002: 'Adresse mail requis',
    4003: 'Adresse mail invalide',
    4004: 'Firstname is required',
    4005: 'Lastname is required',
    //4006: 'Your unique id for the member is required',
    4007: 'Opération non trouvé deans le programme',
    4008: 'Membre non trouvé dans le programme',
    4009: 'Opération  non permise',
    40010: 'Token required',
    40011: 'Invitation non trouvée dans le programme',
    40012: 'Promoteur non trouvé dans le programme',
    40013: 'Invitation already exists for member with id #ID#',
    40014: 'Operation code is required',
    40015: 'Membre déjà existant dans le programme',
    40016: 'Membre déjà existant dans le programme',
    40017: 'No default amount for Operation #OPERATION#',
    40018: 'Unregistered member',
    40019: 'Aucune invitation trouvée',
    40020: 'Member / Invitation token conflict',
    40021: 'Member / Invitation with email #EMAIL# already exists in program #PROGRAM#',
    40022: 'Invitation email template not found in program #PROGRAM#',
    40023: 'Forbidden operation',
    40024: 'Program not found',
    40025: 'Mandataire not found',
    40026: 'MinDeposit is required',
    40027: 'Gift Order not found',
    40028: 'Fichier non trouvé',
    40029: 'Entreprise non trouvée',
    40030: 'Settlement not found',
    40031: 'List of payments ids is required',
    40032: 'Program is not in prod status',
    40033: 'Program is not cancelled',
    40034: 'Program is not easy',
    40035: 'Can not edit program in «prod» status',
    40036: 'Payment not found',
    40037: 'Payment not in «standby» status',
    40038: 'Invalid settlement status(status available : {waiting, settled, to be completed...})',
    40039: 'Invalid payment status(status available : {standby, payed, to be complited...})',
    40040: 'Invalid payment mode(status available : {virement to be complited...})',
    40041: 'Invoice not found',
    40042: 'Participate to program not found',
    40043: 'ParticipateId is required',
    40044: 'Invitation introuvable',
    40045: 'Affaire introuvale',
    40046: 'Amount must be a positive number',
    40047: 'Cancel message is required',
    40048: 'Inpossible de changer le status d\'une affaire payée ou annulée',
    40049: 'Le document de présentation n\'est pas trouvé',
    40050: 'L\'email d\'invitation n\'est pas trouvé',
    40051: 'Un email avec le même code existe déjà',
    40052: 'Le code de l\'email est requis',
    40053: 'Document de présentation manquant',
    40054: 'Le programme a déjà une copie',
    40055: 'Le programme est déjà une copie',
    40056: 'Creating corporate is not finalized',
    40057: 'Le document doit être de format PDF',
    40058: 'Le fichier doit ête un image',
    40059: 'Credit operation not found in program',
    40060: "Wrong data format for PATCH method. Data format must be [{op: '', path: '', parameter1: '', ...}, ..]",
    40061: 'Wrong patch format',
    40062: 'Can not remove default email template',
    40063: 'Corporate with same TVA Intracomm is already existing',
    40064: 'Collaborator not found in corporate',
    40065: 'Accord document for corporate not found',
    40066: 'Attestation not found',
    40067: 'Can not terminate corporate',
    40068: 'Program not found in corporate',
    40069: 'you are already in college',
    40070: 'Email déjà existant',
    40071: 'College Not Found',
    40072: 'User already exists',
    40073: 'Mot de passe incorrect',
    40074: 'Contrat non trouvé',
    40075: 'Echec du transfert du contrat',
    40076: 'User not found',
    40080: 'Image très volumineuse',
    40080: 'Very large file size',
    40081: 'Wrong settlements parameters',
    40082: "Invalid contract filter( available filters = {'default:owner', 'affair:client', 'affair:owner', 'default:owner'})",
    40083: 'Recruitment settings not found',
    40084: 'Contract service type not found',
    40085: 'Vous ne pouvez pas résilier ce contrat: des recrutements sont en cours de validité.',

    401:   'Not Authorized',
    4011:  'Jeton de confirmation non valide',
    4031:  'Accès au programme non autorisé',
    4032:  'Accès à tous les règlements non permis',
    4033:  'Accès à tous les payements non permis',
    4034:  'Denied access to collaborators',
    4035:  'Denied access to corporate accord',
    4036:  'Denied access to Attestation',
    4037:  "You don't have permission to update college",
    5001:  "An intenal error has occured",
    5031:  "No payment server was found",

};


var excludedApiMessages = [
    400110,
];