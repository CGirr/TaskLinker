<?php

namespace App\Enum;

enum TaskStatus: string
{
    case ToDo = 'To Do';
    case Doing = 'Doing';
    case Done = 'Done';

    public function getLabel(): string
    {
        return match ($this) {
            self::ToDo => self::ToDo,
            self::Doing => self::Doing,
            self::Done => self::Done,
        };
    }
}
