<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Other Buyer Expenses Details (contract forms / PDF / show)
    |--------------------------------------------------------------------------
    |
    | When false, the section is not rendered in Global Contract Details,
    | create/edit/convert contract, contract show, or PDF. Existing values in
    | the database are left unchanged when saving contracts or global settings.
    |
    | Set SHOW_OTHER_BUYER_EXPENSES_SECTION=true in .env to show again.
    |
    */
    'show_other_buyer_expenses_section' => env('SHOW_OTHER_BUYER_EXPENSES_SECTION', false),

];
