<?php

namespace App\Enums;

enum UserRoles: string
{
    case Administrator = 'administrator';
    case Manager = 'manager';
    case User = 'user';
}