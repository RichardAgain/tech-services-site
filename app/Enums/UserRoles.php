<?php

namespace App\Enums;

enum UserRoles: int
{
    case ADMIN = 0;
    case OPERATOR = 1;
    case GUEST = 2;
}