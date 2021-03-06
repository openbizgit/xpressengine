<?php
/**
 * CacheDecorator
 *
 * PHP version 5
 *
 * @category  Menu
 * @package   Xpressengine\Menu
 * @author    XE Developers <developers@xpressengine.com>
 * @copyright 2015 Copyright (C) NAVER Corp. <http://www.navercorp.com>
 * @license   http://www.gnu.org/licenses/old-licenses/lgpl-2.1.html LGPL-2.1
 * @link      https://xpressengine.io
 */
namespace Xpressengine\Menu\Repositories;

use Xpressengine\Menu\MenuRepository;
use Xpressengine\Menu\Models\Menu;
use Xpressengine\Menu\Models\MenuItem;
use Xpressengine\Support\CacheInterface;

/**
 * Class CacheDecorator
 *
 * @category  Menu
 * @package   Xpressengine\Menu
 * @author    XE Developers <developers@xpressengine.com>
 * @copyright 2015 Copyright (C) NAVER Corp. <http://www.navercorp.com>
 * @license   http://www.gnu.org/licenses/old-licenses/lgpl-2.1.html LGPL-2.1
 * @link      https://xpressengine.io
 */
class CacheDecorator implements MenuRepository
{
    /**
     * MenuRepository instance
     *
     * @var MenuRepository
     */
    protected $repo;

    /**
     * Cache instance
     *
     * @var CacheInterface
     */
    protected $cache;

    /**
     * Prefix for cache key
     *
     * @var string
     */
    protected $prefix = 'menu';

    /**
     * CacheDecorator constructor.
     *
     * @param MenuRepository $repo  MenuRepository instance
     * @param CacheInterface $cache Cache instance
     */
    public function __construct(MenuRepository $repo, CacheInterface $cache)
    {
        $this->repo = $repo;
        $this->cache = $cache;
    }

    /**
     * Find a menu
     *
     * @param string $id   menu identifier
     * @param array  $with relation
     * @return Menu
     */
    public function find($id, $with = [])
    {
        $key = $this->getCacheKey($id);
        $subKey = $this->getWithKey($with);

        $data = $this->cache->get($key);

        if (!$data || !isset($data[$subKey])) {
            if ($menu = $this->repo->find($id, $with)) {
                $data = $data ?: [];
                $data[$subKey] = $menu;
                $this->cache->put($key, $data);
            }
        } else {
            $menu = $data[$subKey];
        }

        return $menu;
    }

    /**
     * Get all menu
     *
     * @param string $siteKey site key
     * @param array  $with    relation
     * @return Menu[]
     */
    public function all($siteKey, $with = [])
    {
        $key = $this->getCacheKey($siteKey . '_all');
        $subKey = $this->getWithKey($with);

        $data = $this->cache->get($key);

        if (!$data || !isset($data[$subKey])) {
            $menus = $this->repo->all($siteKey, $with);
            if (!empty($menus)) {
                $data = $data ?: [];
                $data[$subKey] = $menus;
                $this->cache->put($key, $data);
            }
        } else {
            $menus = $data[$subKey];
        }

        return $menus;
    }

    /**
     * Find a menu item
     *
     * @param string $id   menu item identifier
     * @param array  $with relation
     * @return MenuItem
     */
    public function findItem($id, $with = [])
    {
        $key = $this->getItemCacheKey($id);
        $subKey = $this->getWithKey($with);

        $data = $this->cache->get($key);

        if (!$data || !isset($data[$subKey])) {
            if ($item = $this->repo->findItem($id, $with)) {
                $data = $data ?: [];
                $data[$subKey] = $item;
                $this->cache->put($key, $data);
            }
        } else {
            $item = $data[$subKey];
        }

        return $item;
    }

    /**
     * Get menu items by identifier list
     *
     * @param array $ids  menu item identifier
     * @param array $with relation
     * @return MenuItem[]
     */
    public function fetchInItem(array $ids, $with = [])
    {
        return $this->repo->fetchInItem($ids, $with);
    }

    /**
     * Insert menu
     *
     * @param Menu $menu menu instance
     * @return Menu
     */
    public function insert(Menu $menu)
    {
        $this->cache->forget($this->getCacheKey($menu->siteKey . '_all'));

        return $this->repo->insert($menu);
    }

    /**
     * Update menu
     *
     * @param Menu $menu menu instance
     * @return Menu
     */
    public function update(Menu $menu)
    {
        $this->cache->forget($this->getCacheKey($menu->siteKey . '_all'));
        $this->cache->forget($this->getCacheKey($menu->getKey()));

        return $this->repo->update($menu);
    }

    /**
     * Delete menu
     *
     * @param Menu $menu menu instance
     * @return bool
     */
    public function delete(Menu $menu)
    {
        $this->cache->forget($this->getCacheKey($menu->siteKey . '_all'));
        $this->cache->forget($this->getCacheKey($menu->getKey()));

        return $this->repo->delete($menu);
    }

    /**
     * Increment item count
     *
     * @param Menu $menu   menu instance
     * @param int  $amount amount
     * @return bool
     */
    public function increment(Menu $menu, $amount = 1)
    {
        $this->cache->forget($this->getCacheKey($menu->siteKey . '_all'));
        $this->cache->forget($this->getCacheKey($menu->getKey()));

        return $this->repo->increment($menu, $amount);
    }

    /**
     * Insert menu item
     *
     * @param MenuItem $item menu item instance
     * @return MenuItem
     */
    public function insertItem(MenuItem $item)
    {
        return $this->repo->insertItem($item);
    }

    /**
     * Update menu item
     *
     * @param MenuItem $item menu item instance
     * @return MenuItem
     */
    public function updateItem(MenuItem $item)
    {
        $this->cache->forget($this->getItemCacheKey($item->getKey()));

        return $this->repo->updateItem($item);
    }

    /**
     * Delete menu item
     *
     * @param MenuItem $item menu item instance
     * @return bool
     */
    public function deleteItem(MenuItem $item)
    {
        $this->cache->forget($this->getItemCacheKey($item->getKey()));

        return $this->repo->deleteItem($item);
    }

    /**
     * Create new menu model
     *
     * @return Menu
     */
    public function createModel()
    {
        return $this->repo->createModel();
    }

    /**
     * Create new menu item model
     *
     * @param Menu $menu menu instance
     * @return MenuItem
     */
    public function createItemModel(Menu $menu = null)
    {
        return $this->repo->createItemModel($menu);
    }

    /**
     * String for cache key
     *
     * @param string $keyword keyword
     * @return string
     */
    protected function getCacheKey($keyword)
    {
        return $this->prefix . '@' . $keyword;
    }

    /**
     * String for item cache key
     *
     * @param string $keyword keyword
     * @return string
     */
    protected function getItemCacheKey($keyword)
    {
        return $this->prefix . 'item@' . $keyword;
    }

    /**
     * Get sub key by relationship
     *
     * @param array $with relationships
     * @return string
     */
    private function getWithKey($with = [])
    {
        $with = !is_array($with) ? [$with] : $with;

        if (empty($with)) {
            return '_alone';
        }

        return implode('.', $with);
    }
}
