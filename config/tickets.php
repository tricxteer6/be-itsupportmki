<?php

return [
    /*
    | After the employee confirms completion, the ticket stays visible in admin
    | and employee portals for this many hours, then is hidden from those lists.
    | (Data remains in the database.)
    */
    'archive_hours_after_user_confirm' => (int) env('TICKET_ARCHIVE_HOURS_AFTER_USER_CONFIRM', 48),
];
