<?php

namespace App\Enums;

enum UserRoles: int
{
    case ADMIN = 1;
    case OPERATOR = 2;
    case CLIENT = 3;
}