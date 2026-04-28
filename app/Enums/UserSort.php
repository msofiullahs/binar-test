<?php

namespace App\Enums;

enum UserSort: string
{
    case Created = 'created_at';
    case Name = 'name';
    case ID = 'id';
}