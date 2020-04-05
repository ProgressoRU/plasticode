<?php

namespace Plasticode\Models;

use Plasticode\Hydrators\Interfaces\HydratorInterface;
use Plasticode\Models\Interfaces\SerializableInterface;
use Plasticode\ObjectProxy;
use Webmozart\Assert\Assert;

abstract class DbModel extends Model implements SerializableInterface
{
    private const NOT_HYDRATED = 1;
    private const BEING_HYDRATED = 2;
    private const HYDRATED = 3;

    protected static string $idField = 'id';

    protected int $hydratedState = self::NOT_HYDRATED;

    public static function idField() : string
    {
        return static::$idField;
    }

    /**
     * Static alias for new().
     * 
     * @param array|\ORM|null $obj
     */
    public static function create($obj = null) : self
    {
        return new static($obj);
    }

    /**
     * Returns the id of the model.
     * 
     * Use getId() instead of id when $idField is custom.
     * It is recommended to use getId() always for safer code.
     */
    public function getId() : ?int
    {
        $idField = self::idField();
        return $this->{$idField};
    }

    public function hasId() : bool
    {
        return $this->getId() > 0;
    }

    /**
     * Was model saved or not.
     */
    public function isPersisted() : bool
    {
        return $this->hasId();
    }

    public function isNotHydrated() : bool
    {
        return $this->hydratedState == self::NOT_HYDRATED;
    }

    /**
     * @param HydratorInterface|ObjectProxy|null $hydrator
     */
    public function hydrate($hydrator) : self
    {
        if (!$this->isNotHydrated()) {
            return $this;
        }

        $this->hydratedState = self::BEING_HYDRATED;

        if ($hydrator) {
            $hydrator->hydrate($this);
        }

        $this->hydratedState = self::HYDRATED;

        return $this;
    }

    public function failIfNotPersisted() : void
    {
        Assert::true(
            $this->isPersisted(),
            'Object must be persisted.'
        );
    }

    public function serialize() : array
    {
        return $this->toArray();
    }

    /**
     * Checks if two objects are equal.
     * 
     * Equal means:
     *  - Same class.
     *  - Same id.
     */
    public function equals(?self $model) : bool
    {
        return !is_null($model)
            && ($model->getId() === $this->getId())
            && (get_class($model) == static::class);
    }

    public function toString() : string
    {
        return '[' . $this->getId() . '] ' . static::class;
    }
}
