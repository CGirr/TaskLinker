<?php

namespace App\Enum;

/**
 *
 */
enum TaskStatus: string
{
    case ToDo = 'To Do';
    case Doing = 'Doing';
    case Done = 'Done';

    /**
     * @return string
     */
    public function getLabel(): string
    {
        return match ($this) {
            self::ToDo => 'To Do',
            self::Doing => 'Doing',
            self::Done => 'Done',
        };
    }
}
