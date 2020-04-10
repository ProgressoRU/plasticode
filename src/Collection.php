<?php

namespace Plasticode;

use Plasticode\Interfaces\ArrayableInterface;
use Plasticode\Util\Arrays;

class Collection implements \ArrayAccess, \Iterator, \Countable, \JsonSerializable, ArrayableInterface
{
    /**
     * Empty collection
     */
    private static ?Collection $empty = null;

    protected array $data;

    protected function __construct(?array $data)
    {
        $this->data = $data ?? [];
    }

    /**
     * Creates collection from array.
     */
    public static function make(?array $data = null) : self
    {
        return new static($data);
    }

    /**
     * Creates collection from arrayable (including other Colection).
     */
    public static function from(ArrayableInterface $arrayable) : self
    {
        return self::make($arrayable->toArray());
    }

    public static function empty() : Collection
    {
        if (is_null(self::$empty)) {
            self::$empty = Collection::make();
        }

        return self::$empty;
    }

    public function add($item) : self
    {
        $col = self::make([$item]);
        return $this->concat($col);
    }

    /**
     * Concats the collection of the same type.
     */
    public function concat(self $other) : self
    {
        $data = array_merge($this->data, $other->toArray());
        return self::make($data);
    }

    /**
     * Merges several heterogenous collections.
     */
    public static function merge(Collection ...$collections) : Collection
    {
        $merged = Collection::empty();

        foreach ($collections as $collection) {
            $merged = $merged->concat($collection);
        }

        return $merged;
    }

    /**
     * Returns distinct values grouped by selector ('id' by default).
     * 
     * @param mixed $by Column/property name or callable, returning generated column/property name. Default = 'id'.
     */
    public function distinct($by = null) : self
    {
        $data = Arrays::distinctBy($this->data, $by ?? 'id');
        return self::make($data);
    }

    /**
     * Converts collection to associative array by column/property or callable.
     * Selector must be unique, otherwise only first element is taken, others are discarded.
     * 
     * @param mixed $by Column/property name or callable, returning generated column/property name. Default = 'id'.
     * @return array<string, mixed>
     */
    public function toAssoc($by = null) : array
    {
        return Arrays::toAssocBy($this->data, $by ?? 'id');
    }

    /**
     * Groups collection by column/property or \Closure.
     * 
     * @param mixed $by Column/property name or \Closure, returning generated column/property name. Default = 'id'.
     * @return array<string, self>
     */
    public function group($by = null) : array
    {
        $result = [];

        $groups = Arrays::groupBy($this->data, $by ?? 'id');

        foreach ($groups as $key => $group) {
            $result[$key] = self::make($group);
        }

        return $result;
    }

    /**
     * Flattens a collection of elements, arrays and collections one level.
     * 
     * Does not make collection distinct!
     */
    public function flatten() : Collection
    {
        $data = [];

        foreach ($this->data as $item) {
            if (is_array($item) || $item instanceof Collection) {
                foreach ($item as $subItem) {
                    $data[] = $subItem;
                }
            } else {
                $data[] = $item;
            }
        }

        return Collection::make($data);
    }

    /**
     * Skips $offset elements from the start and returns the remaining collection.
     */
    public function skip(int $offset) : self
    {
        $data = Arrays::skip($this->data, $offset);
        return self::make($data);
    }

    /**
     * Returns first $limit elements.
     */
    public function take(int $limit) : self
    {
        $data = Arrays::take($this->data, $limit);
        return self::make($data);
    }

    /**
     * Skips $offset elements and takes $limit elements.
     * Negative $offset is counted from the end backwards.
     */
    public function slice(int $offset, int $limit = null) : self
    {
        $data = Arrays::slice($this->data, $offset, $limit);
        return self::make($data);
    }

    /**
     * Removes $limit elements from the end of collection (backward skip).
     */
    public function trimEnd(int $limit = null) : self
    {
        $data = Arrays::trimEnd($this->data, $limit);
        return self::make($data);
    }

    /**
     * Return random item.
     * 
     * @return mixed
     */
    public function random()
    {
        $count = $this->count();

        if ($count === 0) {
            return null;
        }

        $offset = rand(0, $count - 1);

        return $this->slice($offset, 1)->first();
    }

    /**
     * Extracts non-null 'id' column/property values.
     */
    public function ids() : Collection
    {
        $data = Arrays::extractIds($this->data);
        return Collection::make($data);
    }

    /**
     * Extracts non-null column/property values from collection.
     */
    public function extract($column) : Collection
    {
        $data = Arrays::extract($this->data, $column);
        return Collection::make($data);
    }

    /**
     * Is there any value in this collection?
     *
     * @param string|\Closure|null $by
     * @param mixed $value
     * @return boolean
     */
    public function any($by = null, $value = null) : bool
    {
        if ($by !== null) {
            return $this
                ->where($by, $value)
                ->any();
        }

        return !$this->isEmpty();
    }

    public function isEmpty() : bool
    {
        return $this->count() == 0;
    }

    public function contains($value) : bool
    {
        return in_array($value, $this->data);
    }

    /**
     * Filters collection by column/property value or callable, then returns first item or null.
     * 
     * @param string|\Closure|null $by
     * @param mixed $value
     * @return mixed
     */
    public function first($by = null, $value = null)
    {
        return $by
            ? Arrays::firstBy($this->data, $by, $value)
            : Arrays::first($this->data);
    }

    /**
     * Filters collection by column/property value or callable,
     * then returns last item or null.
     * 
     * @param string|\Closure|null $by
     */
    public function last($by = null, $value = null)
    {
        return $by
            ? Arrays::lastBy($this->data, $by, $value)
            : Arrays::last($this->data);
    }

    /**
     * Filters collection by property value or closure.
     * 
     * @param string|\Closure $by
     */
    public function where($by, $value = null) : self
    {
        $data = Arrays::filter($this->data, $by, $value);
        return self::make($data);
    }

    public function whereIn($column, $values) : self
    {
        if ($values instanceof Collection) {
            $values = $values->toArray();
        }
        
        $data = Arrays::filterIn($this->data, $column, $values);
        return self::make($data);
    }

    public function whereNotIn($column, $values) : self
    {
        if ($values instanceof Collection) {
            $values = $values->toArray();
        }
        
        $data = Arrays::filterNotIn($this->data, $column, $values);
        return self::make($data);
    }

    public function map(\Closure $func) : Collection
    {
        $data = array_map($func, $this->data);
        return Collection::make($data);
    }

    /**
     * Applies closure to all collection's items.
     */
    public function apply(\Closure $func) : void
    {
        array_walk($this->data, $func);
    }

    /**
     * Sorts collection ascending using property or closure.
     *
     * @param string|\Closure $by
     */
    public function asc($by, ?string $type = null) : self
    {
        return $this->orderBy($by, null, $type);
    }

    /**
     * Sorts collection descending using property or closure.
     *
     * @param string|\Closure $by
     */
    public function desc($by, ?string $type = null) : self
    {
        $data = Arrays::orderByDesc($this->data, $by, $type);
        return self::make($data);
    }

    /**
     * Sorts collection by column or closure.
     * Ascending by default.
     *
     * @param string|\Closure $by
     */
    public function orderBy($by, ?string $dir = null, ?string $type = null) : self
    {
        $data = Arrays::orderBy($this->data, $by, $dir, $type);
        return self::make($data);
    }

    /**
     * Sorts collection ascending by column or closure as strings.
     *
     * @param string|\Closure $by
     */
    public function ascStr($by) : self
    {
        return $this->orderByStr($by);
    }

    /**
     * Sorts collection descending by column or closure as strings.
     *
     * @param string|\Closure $by
     */
    public function descStr($by) : self
    {
        $data = Arrays::orderByStrDesc($this->data, $by);
        return self::make($data);
    }

    /**
     * Sorts collection by column or closure as strings.
     * Ascending by default.
     *
     * @param string|\Closure $by
     */
    public function orderByStr($by, ?string $dir = null) : self
    {
        $data = Arrays::orderByStr($this->data, $by, $dir);
        return self::make($data);
    }

    /**
     * Sorts collection using sort steps.
     *
     * @param SortStep[] $steps
     */
    public function multiSort(array $steps) : self
    {
        $data = Arrays::multiSort($this->data, $steps);
        return self::make($data);
    }

    /**
     * Sorts collection using comparer function
     * which receives two items to compare.
     */
    public function orderByComparer(\Closure $comparer) : self
    {
        $data = $this->data; // cloning
        usort($data, $comparer);

        return self::make($data);
    }

    /**
     * Reorders collection's items in reverse.
     */
    public function reverse() : self
    {
        $data = array_reverse($this->data);
        return self::make($data);
    }

    /**
     * Removes nulls, empty strings and 0s.
     */
    public function clean() : self
    {
        $data = Arrays::clean($this->data);
        return self::make($data);
    }

    // ArrayAccess

    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            throw new \InvalidArgumentException('$offset cannot be null.');
        } else {
            $this->data[$offset] = $value;
        }
    }

    public function offsetExists($offset)
    {
        return isset($this->data[$offset]);
    }

    public function offsetUnset($offset)
    {
        unset($this->data[$offset]);
    }

    public function offsetGet($offset)
    {
        return isset($this->data[$offset])
            ? $this->data[$offset]
            : null;
    }

    // Iterator

    public function rewind()
    {
        reset($this->data);
    }

    public function current()
    {
        return current($this->data);
    }

    public function key() 
    {
        return key($this->data);
    }

    public function next() 
    {
        return next($this->data);
    }

    public function valid()
    {
        $key = key($this->data);
        return ($key !== null && $key !== false);
    }

    // Countable

    public function count()
    {
        return count($this->data);
    }

    // JsonSerializable

    public function jsonSerialize()
    {
        return $this->toArray();
    }

    // ArrayableInterface

    public function toArray() : array
    {
        return $this->data;
    }
}
