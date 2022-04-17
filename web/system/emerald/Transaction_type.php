<?php

namespace System\Emerald;

use Model\User_model;

class Transaction_type extends \ReflectionClass
{
    const ACTION_TYPES = [
        'add_balance' => 'adding balance',
        'buy_likes' => 'buying likes',
        'buy_boosterpack' => 'buying boosterpack',
    ];
}