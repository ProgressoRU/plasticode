<?php

namespace Plasticode\Parsing\LinkMappers;

use Plasticode\Parsing\Interfaces\TaggedLinkMapperInterface;
use Plasticode\Parsing\LinkMappers\Traits\Tagged;

abstract class TaggedEntityLinkMapper extends EntityLinkMapper implements TaggedLinkMapperInterface
{
    use Tagged;

    public function tag() : string
    {
        return $this->entity();
    }

    protected function renderSlug(string $slug, string $text) : ?string
    {
        return $this->renderPlaceholder($slug, $text);
    }
}
