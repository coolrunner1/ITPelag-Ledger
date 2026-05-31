<?php

namespace App\Repositories;

use App\Models\Account;

class AccountRepository extends Repository
{
    function findAccount(int $id) {
        return Account::find($id);
    }
}
