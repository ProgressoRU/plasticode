<?php

namespace Plasticode\ViewModels;

/**
 * BB quote view model.
 */
class QuoteViewModel extends ViewModel
{
    private $text;
    private $author;
    private $url;
    private $chunks;

    /**
     * @param string $text
     * @param string|null $author
     * @param string|null $url
     * @param string[] $chunks
     */
    public function __construct(string $text, ?string $author, ?string $url, array $chunks)
    {
        parent::__construct();

        $this->text = $text;
        $this->author = $author;
        $this->url = $url;
        $this->chunks = $chunks;
    }

    /**
     * Text.
     *
     * @return string
     */
    public function text() : string
    {
        return $this->text;
    }

    /**
     * Author name.
     *
     * @return string|null
     */
    public function author() : ?string
    {
        return $this->author;
    }

    /**
     * Url.
     *
     * @return string|null
     */
    public function url() : ?string
    {
        return $this->url;
    }

    /**
     * Other chunks.
     *
     * @return string[]
     */
    public function chunks() : array
    {
        return $this->chunks;
    }
}
