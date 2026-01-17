<?php

namespace App\Enums;

enum UserRoleEnum :string 
{
    case ADMIN = "Super Admin";
    case USER = "Governmental Official";
    case POLICY_MAKER = "Policy Maker";

}