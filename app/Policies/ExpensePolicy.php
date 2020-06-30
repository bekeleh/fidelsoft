<?php

namespace App\Policies;

use App\Models\User;

class ExpensePolicy extends EntityPolicy
{
//    public static function create(User $user, $item)
//    {
//        if (!parent::create($user, $item)) {
//            return false;
//        }
//
//        return $user->hasFeature(FEATURE_EXPENSES);
//    }
}
