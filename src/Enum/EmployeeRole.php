<?php

namespace App\Enum;

use phpDocumentor\Reflection\Types\Self_;

/**
 *
 */
enum EmployeeRole: string
{
    case ADMIN = 'ROLE_ADMIN';
    case USER = 'ROLE_USER';

    /**
     * @return string
     */
    public function getLabel(): string
    {
        return match ($this) {
            self::ADMIN => 'Chef de projet',
            self::USER => 'Employé',
        };
    }
}
