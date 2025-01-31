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

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

/**
 * Class Statistic
 * @package Slub\DigasFeManagement\Domain\Model
 */
class Statistic extends AbstractEntity
{
    /**
     * @var \DateTime
     */
    protected $tstamp;

    /**
     * document
     *
     * @var \Slub\SlubWebDigas\Domain\Model\KitodoDocument
     */
    protected $document;

    /**
     * @var \Slub\DigasFeManagement\Domain\Model\User
     */
    protected $feUser;

    /**
     * @var int
     */
    protected $downloadPages;

    /**
     * @var int
     */
    protected $downloadWork;

    /**
     * @var int
     */
    protected $workViews;

    /**
     * @return \DateTime
     */
    public function getTstamp()
    {
        return $this->tstamp;
    }

    /**
     * @param \DateTime $tstamp
     * @return void
     */
    public function setTstamp($tstamp)
    {
        $this->tstamp = $tstamp;
    }

    /**
     * Returns the document
     *
     * @return \Slub\SlubWebDigas\Domain\Model\KitodoDocument $document
     */
    public function getDocument()
    {
        return $this->document;
    }

    /**
     * Sets the document
     *
     * @param \Slub\SlubWebDigas\Domain\Model\KitodoDocument $document
     * @return void
     */
    public function setDocument($document)
    {
        $this->document = $document;
    }

    /**
     * @return \Slub\DigasFeManagement\Domain\Model\User $feUser
     */
    public function getFeUser()
    {
        return $this->feUser;
    }

    /**
     *
     * @param \Slub\DigasFeManagement\Domain\Model\User|\In2code\Femanager\Domain\Model\User $feUser
     * @return void
     */
    public function setFeUser($feUser)
    {
        // @phpstan-ignore-next-line
        $this->feUser = $feUser;
    }

    /**
     * @return int
     */
    public function getDownloadPages()
    {
        return $this->downloadPages;
    }

    /**
     * @param int $downloadPages
     * @return void
     */
    public function setDownloadPages($downloadPages)
    {
        $this->downloadPages = $downloadPages;
    }

    /**
     * @return void
     */
    public function incDownloadPages()
    {
        $this->downloadPages++;
    }

    /**
     * @return int
     */
    public function getDownloadWork()
    {
        return $this->downloadWork;
    }

    /**
     * @param int $downloadWork
     * @return void
     */
    public function setDownloadWork($downloadWork)
    {
        $this->downloadWork = $downloadWork;
    }

    /**
     * @return int
     */
    public function getWorkViews()
    {
        return $this->workViews;
    }

    /**
     * @param int $workViews
     * @return void
     */
    public function setWorkViews($workViews)
    {
        $this->workViews = $workViews;
    }

}
