<?php

namespace App\Factory;

use App\Entity\Task;
use App\Enum\TaskStatus;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Task>
 */
final class TaskFactory extends PersistentProxyObjectFactory
{
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     *
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @return string
     */
    public static function class(): string
    {
        return Task::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     */
    protected function defaults(): array|callable
    {
        return [
            'project' => ProjectFactory::random(),
            'status' => self::faker()->randomElement(TaskStatus::cases()),
            'title' => self::faker()->text(50),
            'description' => self::faker()->text(255),
            'date' => self::faker()->optional(0.6)->passthrough(
                \DateTimeImmutable::createFromMutable(self::faker()->dateTimeBetween('now', '+1 years'))
            ),
            'member' => self::faker()->optional(0.7)->passthrough(EmployeeFactory::random()),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(Task $task): void {})
        ;
    }
}
