<?php

namespace Plasticode\Repositories;

use Plasticode\Collection;
use Plasticode\Models\Menu;
use Plasticode\Repositories\Interfaces\MenuRepositoryInterface;

class MenuRepository extends IdiormRepository implements MenuRepositoryInterface
{
    public function getAll() : Collection
    {
        return Menu::getAll();
    }
}
