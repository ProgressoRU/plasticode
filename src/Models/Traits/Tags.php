<?php

namespace Plasticode\Models\Traits;

use Plasticode\Query;
use Plasticode\Models\Tag;
use Plasticode\Models\TagLink;
use Plasticode\Util\Strings;

trait Tags
{
    protected static function getTagsEntityType()
    {
        return static::getTable();
    }
    
    /**
     * Returns tags as an array of TRIMMED strings
     */
    protected function getTags() : array
    {
        $tags = $this->{static::$tagsField};
        
		return Strings::explode($tags);
    }
    
	public function tagLinks()
	{
	    $tab = static::getTagsEntityType();
	    $tags = $this->getTags();
	    
		return array_map(function($t) use ($tab) {
			return new TagLink($t, $tab);
		}, $tags);
	}

	public static function getBaseByTag($tag) : Query
	{
	    return static::getByTag($tag, self::baseQuery());
	}

	public static function getByTag($tag, Query $baseQuery = null) : Query
	{
		$tag = Strings::normalize($tag);
		$ids = Tag::getIdsByTag(static::getTable(), $tag);

		if ($ids->empty()) {
			return Query::empty();
		}
		
		$query = $baseQuery ?? self::query();
		$query = $query->whereIn('id', $ids);

	    if (method_exists(static::class, 'tagsWhere')) {
	        $query = static::tagsWhere($query);
	    }
		    
        return $query;
	}
}
