<?php

namespace App\Enum;

/**
 *
 */
enum EmployeeStatus: string
{
    case Cdi = 'CDI';
    case Cdd = 'CDD';
    case Interim = 'Intérimaire';
    case Freelance = 'Freelance';

    /**
     * @return string
     */
    public function getLabel(): string
    {
        return match ($this) {
            self::Cdi => 'CDI',
            self::Cdd => 'CDD',
            self::Interim => 'Interim',
            self::Freelance => 'Freelance',
        };
    }
}
