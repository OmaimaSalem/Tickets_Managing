<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Ticket Change Status Notification
    |--------------------------------------------------------------------------
    |
    | The following language lines are used during sending mail from system to owner
    | to notify him that his ticket has anew status.
    |
    */

    'subject' => 'Ticket Change Status',
    'ticketName' => 'The ticket: :ticket_name , has been changed to, :status .',
    'seeMore' => 'See More...',
    'footer' => 'Thank you for using our application!',
    'status' => [
        'open'        => 'open',
        'pending'     => 'pending',
        'in-progress' => 'in progress',
        'done'        => 'done',
    ]
];
