<?php

namespace App\Enums;

enum UserRoles: string
{
    case Administrator = 'active';
    case Manager = 'manager';
    case User = 'user';
}