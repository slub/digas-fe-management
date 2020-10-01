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
    protected $downloads;

    /**
     * @return \DateTime
     */
    public function getTstamp()
    {
        return $this->tstamp;
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
     * @return \Slub\DigasFeManagement\Domain\Model\User $feUser
     */
    public function getFeUser()
    {
        return $this->feUser;
    }

    /**
     * @return int
     */
    public function getDownloads()
    {
        return $this->downloads;
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
     *
     * @param \Slub\DigasFeManagement\Domain\Model\User|\In2code\Femanager\Domain\Model\User $feUser
     * @return void
     */
    public function setFeUser($feUser)
    {
        $this->feUser = $feUser;
    }

    /**
     * Sets the document
     *
     * @param int $document
     * @return void
     */
    public function setDocument($document)
    {
        $this->document = $document;
    }

    /**
     * @param int $downloads
     * @return void
     */
    public function setDownloads($downloads)
    {
        $this->downloads = $downloads;
    }
}
