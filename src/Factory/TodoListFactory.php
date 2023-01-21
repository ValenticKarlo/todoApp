<?php

namespace App\Factory;

use App\Entity\TodoList;
use App\Repository\TodoListRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<TodoList>
 *
 * @method        TodoList|Proxy create(array|callable $attributes = [])
 * @method static TodoList|Proxy createOne(array $attributes = [])
 * @method static TodoList|Proxy find(object|array|mixed $criteria)
 * @method static TodoList|Proxy findOrCreate(array $attributes)
 * @method static TodoList|Proxy first(string $sortedField = 'id')
 * @method static TodoList|Proxy last(string $sortedField = 'id')
 * @method static TodoList|Proxy random(array $attributes = [])
 * @method static TodoList|Proxy randomOrCreate(array $attributes = [])
 * @method static TodoListRepository|RepositoryProxy repository()
 * @method static TodoList[]|Proxy[] all()
 * @method static TodoList[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static TodoList[]|Proxy[] createSequence(array|callable $sequence)
 * @method static TodoList[]|Proxy[] findBy(array $attributes)
 * @method static TodoList[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static TodoList[]|Proxy[] randomSet(int $number, array $attributes = [])
 */
final class TodoListFactory extends ModelFactory
{
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     *
     * @todo inject services if required
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    protected function getDefaults(): array
    {
        return [
            'createdAt' => \DateTimeImmutable::createFromMutable(self::faker()->dateTimeBetween('-100 days', '-1 minute')),
            'name' => self::faker()->sentence(4, true),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): self
    {
        return $this
            // ->afterInstantiate(function(TodoList $todoList): void {})
        ;
    }

    protected static function getClass(): string
    {
        return TodoList::class;
    }
}
