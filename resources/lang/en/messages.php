<?php

/**
 * Application Messages
 */

return [

    // General
    'nothingToUpdate' => 'You must provide at least one field to update the record.',
    'notANumber' => ':key must be a number. Current value :value',
    'emailSent' => 'The email has been sent.',

    // Auth
    'notAdmin' => "Sorry, you don't have privileges to view this page.",
    'authFailed' => "Sorry, your username and password don't match our records.",

    // Users
    'userNotFound' => "Sorry, we could not find that user.",
    'userRoleNotFound' => "Sorry, we could not find that user role.",
    'userExists' => "Sorry, a user with the email :email already exists.",
    'userUpdated' => "Success! User :email has been updated.",
    'userCreated' => "Success! User :email has been created.",
    'userDeleted' => "Success! User has been deleted.",
    'userDeleteYoureADummy' => "Oh no! You almost deleted yourself. Maybe don't do that?",

    // Haulers
    'haulerInvalidEmail' => 'Email must be comma-delimited string or an array.',
    'haulerValidationErrors' => 'The following fields are required to create a new Hauler: :fields',
    'haulerNotFound' => 'Unable to locate a Hauler with matching id: :id',
    'haulerCreated' => 'The Hauler was successfully created.',
    'haulerUpdated' => 'The changes to the Hauler were successfully saved.',
    'haulerExists' => 'A Hauler with that name already exists in that city.',
    'haulerDeleted' => 'The Hauler has been deleted.',
    'haulerArchived' => 'Tha Hauler has been archived.',
    'haulerUnArchived' => 'Tha Hauler has been un-archived.',
    'haulerNoneFound' => 'No matching Haulers were found.',

    // Leads
    'invalidEmailAddress' => 'The email address :email is not a valid email.',
    'leadValidationErrors' => 'The following fields are required to create a new Lead: :fields',
    'leadExists' => 'A lead already exists for that address.',
    'leadNotFound' => 'Unable to locate a Lead with matching id: :id',
    'leadCreated' => 'The Lead was successfully created.',
    'leadUpdated' => 'The changes to the Lead were successfully saved.',
    'leadDeleted' => 'The Lead has been deleted.',
    'leadArchived' => 'The Lead has been archived.',
    'leadUnArchived' => 'The Lead has been un-archived.',
    'leadNoHaulers' => 'No Haulers were selected to send bid requests to.',
    'leadBidsSent' => 'Your bids have been sent to the haulers.',
    'leadConverted' => 'The lead was successfully converted to a Client.',
    'leadRebid' => 'The Lead/Client have entered re-bidding process.',

    // Clients
    'clientValidationErrors' => 'The following fields are required to create a new Client: :fields',
    'clientExists' => 'A Client already exists for that address.',
    'clientNotFound' => 'Unable to locate a Client with matching id: :id',
    'clientCreated' => 'The Client was successfully created.',
    'clientUpdated' => 'The changes to the Client were successfully saved.',
    'clientDeleted' => 'The Client has been deleted.',
    'clientArchived' => 'The Client has been archived.',
    'clientUnArchived' => 'The Client has been un-archived.',

    // Bids
    'bidValidationErrors' => 'The following fields are required to create a new Bid: :fields',
    'bidExists' => 'A Bid already exists for that lead/hauler.',
    'bidNotFound' => 'Unable to locate a Bid with matching id: :id',
    'bidCreated' => 'The Bid was successfully created.',
    'bidUpdated' => 'The changes to the Bid were successfully saved.',
    'bidDeleted' => 'The Bid has been deleted.',
    'bidInvalidStatus' => ':status is not a valid Bid Status.',
    'bidAccepted' => 'The bid was accepted and all other bids for this lead have been closed.',
    'bidRescinded' => 'The bid was rescinded and all other bids for this lead have been re-opened.',
    'bidsNotFound' => 'Unable to locate any bids for that lead.',
    'emailReminder' => 'Reminder - you have a new business opportunity',
    'emailFinalReminder' => 'Final Reminder - you have a new business opportunity',

    // Service Areas
    'serviceAreaValidationErrors' => 'The following fields are required to create a new Service Area: :fields',
    'serviceAreaExists' => 'A Service Area already exists with that name.',
    'serviceAreaNotFound' => 'Unable to locate a Service Area with matching id: :id',
    'serviceAreaCreated' => 'The Service Area was successfully created.',
    'serviceAreaUpdated' => 'The changes to the Service Area were successfully saved.',
    'serviceAreaDeleted' => 'The Service Area has been deleted.',
];
