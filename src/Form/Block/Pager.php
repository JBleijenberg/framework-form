<?php
/**
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the General Public License (GPL 3.0)
 * that is bundled with this package in the file LICENSE
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/GPL-3.0
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future.
 *
 * @author      Jeroen Bleijenberg
 *
 * @copyright   Copyright (c) 2017
 * @license     http://opensource.org/licenses/GPL-3.0 General Public License (GPL 3.0)
 */
namespace Jcode\Form\Block;

use Jcode\Application;
use Jcode\Db\Resource as DBResource;
use Jcode\Layout\Block\Template;

class Pager extends Template
{

    protected $resource;

    protected $limit = 25;

    protected $sortColumn;

    protected $sortOrder = 'ASC';

    protected $totalPages;

    protected $totalRows;

    protected $url;

    protected $pageParameter = 'p';

    protected $isSharedInstance = true;

    /**
     * Set pager limit
     *
     * @param int $limit
     * @return $this
     */
    public function setLimit($limit)
    {
        $this->limit = $limit;

        return $this;
    }

    /**
     * Get pager limit
     *
     * @return int
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * Set the resource to apply the pager on
     *
     * @param DBResource|Resource $resource
     * @return $this
     */
    public function setResource(DBResource $resource)
    {
        $this->resource = $resource;

        return $this;
    }

    /**
     * Get resource
     * @return DBResource|Resource
     */
    public function getResource() :DBResource
    {
        return $this->resource;
    }

    /**
     * Set sort order
     *
     * @param String $order
     * @return $this
     */
    public function setSortOrder(String $order)
    {
        if (in_array($order, ['ASC', 'DESC'])) {
            $this->sortOrder = $order;
        }

        return $this;
    }

    /**
     * Get sort order
     * @return String
     */
    public function getSortOrder() :?String
    {
        return $this->sortOrder;
    }

    /**
     * Set sort column
     *
     * @param String $sort
     * @return $this
     */
    public function setSort(String $sort)
    {
        $this->sortColumn = $sort;

        return $this;
    }

    /**
     * Get sort column
     * @return String
     */
    public function getSort() :?String
    {
        return $this->sortColumn;
    }

    public function load()
    {
        /** @var DBResource $resource */
        $resource   = $this->getResource();
        $controller = Application::registry('current_controller');
        $page       = $controller->getParam($this->getPageParameter()) - 1;
        $page       = ($page >= 0) ? $page : 0;
        $offset     = $page * $this->getLimit();

        $resource->addLimit($offset, $this->getLimit());

        if ($this->getSort() && $this->getSortOrder()) {
            $resource->addOrder($this->getSort(), $this->getSortOrder());
        }

        $resource->calculateFoundRows();

        $this->totalRows = $resource->getTotalRows();

        return $this;
    }

    public function getTotalPages()
    {
        if (!$this->totalPages) {
            $this->totalPages = ceil($this->totalRows / $this->getLimit());
        }

        return $this->totalPages;
    }

    public function getLastPage()
    {
        return $this->getTotalPages();
    }

    public function getFirstPage()
    {
        return 1;
    }

    public function getCurrentPage()
    {
        $controller = Application::registry('current_controller');

        $currentPage = ($controller->getParam($this->getPageParameter()) !== null)
            ? $controller->getParam($this->getPageParameter())
            : 1;

        return $currentPage;
    }

    /**
     * Get next page number
     * @return int|null
     * @internal param int $offset
     */
    public function getNextPage()
    {
        $nextPage = $this->getCurrentPage() + 1;

        if ($nextPage <= $this->getTotalPages()) {
            return $this->getUrl($this->getPagerUrl(), ['params' => [$this->getPageParameter() => $nextPage]]);
        }

        return null;
    }

    /**
     * Get previous page number
     * @return int|null
     * @internal param int $offset
     */
    public function getPreviousPage()
    {
        $previousPage = $this->getCurrentPage() - 1;

        if ($previousPage >= 1) {
            return $this->getUrl($this->getPagerUrl(), ['params' => [$this->getPageParameter() => $previousPage]]);
        }

        return null;
    }

    public function setPagerUrl(String $url)
    {
        $this->url = $url;

        return $this;
    }

    public function getPagerUrl() :String
    {
        return $this->url;
    }

    public function getPageParameter() :String
    {
        return $this->pageParameter;
    }

    public function setPageParameter(String $param)
    {
        $this->pageParameter = $param;
    }

    public function getPagesArray() :array
    {
        $array = [];

        foreach (range(1, $this->getTotalPages()) as $pageID) {
            $array[$pageID]['url'] = $this->getUrl($this->getPagerUrl(), ['params' => [$this->getPageParameter() => $pageID]]);

            switch (true) {
                case $pageID == $this->getCurrentPage():
                    $array[$pageID]['type'] = 'current';

                    break;
                case $pageID == 1:
                    $array[$pageID]['type'] = 'first';

                    break;
                case $pageID == $this->getTotalPages():
                    $array[$pageID]['type'] = 'last';

                    break;
                case $pageID < $this->getCurrentPage():
                    $array[$pageID]['type'] = 'previous';

                    break;
                case $pageID > $this->getCurrentPage():
                    $array[$pageID]['type'] = 'next';

                    break;
                default:
                    $array[$pageID]['type'] = null;
            }
        }

        return $array;
    }
}