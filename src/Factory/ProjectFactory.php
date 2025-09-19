<?php

namespace App\Factory;

use App\Entity\Project;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Project>
 */
final class ProjectFactory extends PersistentProxyObjectFactory
{
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     *
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
        return Project::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     *
     */
    protected function defaults(): array|callable
    {
        return [
            'archived' => self::faker()->boolean(),
            'title' => self::faker()->text(50),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this
            ->afterInstantiate(function (Project $project): void {
                $members = EmployeeFactory::randomSet(self::faker()->numberBetween(1, 5));
                foreach ($members as $member) {
                    $project->addMember($member->_real());
                }
            });
    }
}
