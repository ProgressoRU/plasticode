<?php

namespace Plasticode;

use Plasticode\Util\Arrays;

class Collection implements \ArrayAccess, \Iterator, \Countable
{
    protected $data;
    
    protected function __construct(array $data)
    {
        $this->data = $data;
    }
    
    public static function make(array $data = null)
    {
        return new static($data ?? []);
    }
    
    public static function makeEmpty()
    {
        return self::make();
    }
    
    public function add($item)
    {
        $col = self::make([ $item ]);
        return $this->concat($col);
    }
    
    public function concat(Collection $other)
    {
        $data = array_merge($this->data, $other->toArray());
        return self::make($data);
    }
    
    public static function merge(...$collections)
    {
        $merged = self::makeEmpty();
        
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
    public function distinct($by = null)
    {
        $data = Arrays::distinctBy($this->data, $by ?? 'id');
        return self::make($data);
    }
    
    /**
     * Converts collection to associative array by column/property or callable.
     * Selector must be unique, otherwise only first element is taken, others are discarded.
     * 
     * @param mixed $by Column/property name or callable, returning generated column/property name. Default = 'id'.
     */
    public function toAssoc($by = null) : array
    {
        return Arrays::toAssocBy($this->data, $by ?? 'id');
    }
    
    public function toArray() : array
    {
        return $this->data;
    }
    
    /**
     * Groups collection by column/property or callable.
     * 
     * @param mixed $by Column/property name or callable, returning generated column/property name. Default = 'id'.
     * @return Returns associative array of collections.
     */
    public function group($by = null)
    {
        $groups = Arrays::groupBy($this->data, $by ?? 'id');
        
        foreach ($groups as $key => $group) {
            $result[$key] = self::make($group);
        }
        
        return $result;
    }
    
    /**
     * Flattens a collection of elements, arrays and collections one level
     */
    public function flatten()
    {
        $data = [];
        
        foreach ($this->data as $item) {
            if (is_array($item) || $item instanceof self) {
                foreach ($item as $subItem) {
                    $data[] = $subItem;
                }
            }
            else {
                $data[] = $item;
            }
        }
        
        return self::make($data);
    }
    
    /**
     * Skips $offset elements from the start and returns the remaining collection.
     */
    public function skip(int $offset)
    {
        $data = Arrays::skip($this->data, $offset);
        return self::make($data);
    }
    
    /**
     * Returns first $limit elements
     */
    public function take(int $limit)
    {
        $data = Arrays::take($this->data, $limit);
        return self::make($data);
    }
    
    /**
     * Skips $offset elements and takes $limit elements
     */
    public function slice(int $offset, int $limit)
    {
        $data = Arrays::slice($this->data, $offset, $limit);
        return self::make($data);
    }
    
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
    public function ids()
    {
        return $this->extract('id');
    }

    /**
     * Extracts non-null column/property values from collection.
     */
    public function extract($column)
    {
        $data = Arrays::extract($this->data, $column);
        return self::make($data);
    }
    
    public function any()
    {
        return !$this->empty();
    }

    public function empty()
    {
        return $this->count() == 0;
    }
    
    public function contains($value)
    {
        return in_array($value, $this->data);
    }

    /**
     * Filters collection by column/property value or callable, then returns first item or null.
     */
    public function first($by = null, $value = null)
    {
        return $by
            ? Arrays::firstBy($this->data, $by, $value)
            : Arrays::first($this->data);
    }

    /**
     * Filters collection by column/property value or callable, then returns last item or null.
     */
    public function last($by = null, $value = null)
    {
        return $by
            ? Arrays::lastBy($this->data, $by, $value)
            : Arrays::last($this->data);
    }
    
    /**
     * Filters collection by column/property value or callable.
     */
    public function where($by, $value = null)
    {
        $data = Arrays::filter($this->data, $by, $value);
        return self::make($data);
    }
    
    public function whereIn($column, $values)
    {
        if ($values instanceof Collection) {
            $values = $values->toArray();
        }
        
        $data = Arrays::filterIn($this->data, $column, $values);
        return self::make($data);
    }
    
    public function whereNotIn($column, $values)
    {
        if ($values instanceof Collection) {
            $values = $values->toArray();
        }
        
        $data = Arrays::filterNotIn($this->data, $column, $values);
        return self::make($data);
    }

    public function map($func)
    {
        $data = array_map($func, $this->data);
        return self::make($data);
    }
    
    public function apply($func)
    {
        array_walk($this->data, $func);
    }
    
    public function asc($column, $type = null)
    {
        return $this->orderBy($column, null, $type);
    }
    
    public function desc($column, $type = null)
    {
        $data = Arrays::orderByDesc($this->data, $column, $type);
        return self::make($data);
    }

    public function orderBy($column, $dir = null, $type = null)
    {
        $data = Arrays::orderBy($this->data, $column, $dir, $type);
        return self::make($data);
    }
    
    public function ascStr($column)
    {
        return $this->orderByStr($column);
    }
    
    public function descStr($column)
    {
        $data = Arrays::orderByStrDesc($this->data, $column);
        return self::make($data);
    }

    public function orderByStr($column, $dir = null)
    {
        $data = Arrays::orderByStr($this->data, $column, $dir);
        return self::make($data);
    }
    
    public function multiSort($sorts)
    {
        $data = Arrays::multiSort($this->data, $sorts);
        return self::make($data);
    }
    
    public function reverse()
    {
        $data = array_reverse($this->data);
        return self::make($data);
    }

    public function serialize()
    {
        return $this->map(function ($item) {
            return $item->serialize();
        });
    }
	
	// ArrayAccess
	
    public function offsetSet($offset, $value) {
        if (is_null($offset)) {
            throw new \InvalidArgumentException('$offset cannot be null.');
        } else {
            $this->data[$offset] = $value;
        }
    }

    public function offsetExists($offset) {
        return isset($this->data[$offset]);
    }

    public function offsetUnset($offset) {
        unset($this->data[$offset]);
    }

    public function offsetGet($offset) {
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
}
