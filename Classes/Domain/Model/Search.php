<?php

namespace Slub\DigasFeManagement\Domain\Model;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2020 SLUB Dresden <typo3@slub-dresden.de>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 * Class Log
 */
class Search extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{
    /**
     * title
     *
     * @var string
     */
    protected $title = '';

    /**
     * searchParams
     *
     * @var string
     */
    protected $searchParams;

    /**
     * feUser
     *
     * @var int
     */
    protected $feUser;

    /**
     * crdate
     *
     * @var int
     */
    protected $crdate;

    /**
     * Returns the crdate
     *
     * @return int $crdate
     */
    public function getCrdate()
    {
        return $this->crdate;
    }

    /**
     * Sets the crdate
     *
     * @param int $crdate
     * @return void
     */
    public function setCrdate($crdate)
    {
        $this->crdate = $crdate;
    }

    /**
     * Returns the title
     *
     * @return string $title
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Sets the title
     *
     * @param string $title
     * @return void
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * Returns the searchParams
     *
     * @return array $searchParams
     */
    public function getSearchParams()
    {
        return json_decode($this->searchParams);
    }

    /**
     * Sets the searchParams
     *
     * @param array $searchParams
     * @return void
     */
    public function setSearchParams($searchParams)
    {
        $this->searchParams = json_encode($searchParams);
    }

    /**
     * Returns the feUser
     *
     * @return int $feUser
     */
    public function getFeUser()
    {
        return $this->feUser;
    }

    /**
     * Sets the feUser
     *
     * @param int $feUser
     * @return void
     */
    public function setFeUser($feUser)
    {
        $this->feUser = $feUser;
    }
}
