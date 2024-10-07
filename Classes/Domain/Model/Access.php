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
 * Class Access
 */
class Access extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{
    /**
     * dlfDocument
     *
     * @var \Slub\SlubWebDigas\Domain\Model\KitodoDocument
     */
    protected $dlfDocument;

    /**
     * recordId
     *
     * @var string
     */
    protected $recordId;

    /**
     * feUser
     *
     * @var int
     */
    protected $feUser;

    /**
     * hidden
     *
     * @var bool
     */
    protected $hidden;

    /**
     * startTime
     *
     * @var int
     */
    protected $startTime;

    /**
     * endTime
     *
     * @var int
     */
    protected $endTime;

    /**
     * startTimeString
     *
     * @var string
     */
    protected $startTimeString;

    /**
     * endTimeString
     *
     * @var string
     */
    protected $endTimeString;

    /**
     * @var int Timestamp of email notification to user for granted access
     */
    protected $accessGrantedNotification;

    /**
     * @var int Timestamp of email notification to user for expiration notice
     */
    protected $expireNotification;

    /**
     * rejected
     *
     * @var bool
     */
    protected $rejected;

    /**
     * rejectedReason
     *
     * @var string
     */
    protected $rejectedReason;

    /**
     * Returns the dlfDocument
     *
     * @return \Slub\SlubWebDigas\Domain\Model\KitodoDocument $dlfDocument
     */
    public function getDlfDocument()
    {
        return $this->dlfDocument;
    }

    /**
     * informUser
     *
     * @var bool
     */
    protected $informUser;

    /**
     * Sets the dlfDocument
     *
     * @param \Slub\SlubWebDigas\Domain\Model\KitodoDocument $dlfDocument
     * @return void
     */
    public function setDlfDocument(\Slub\SlubWebDigas\Domain\Model\KitodoDocument $dlfDocument)
    {
        $this->dlfDocument = $dlfDocument;
    }

    /**
     * Returns the recordId
     *
     * @return string $recordId
     */
    public function getRecordId()
    {
        return $this->recordId;
    }

    /**
     * Sets the recordId
     *
     * @param string $recordId
     * @return void
     */
    public function setRecordId($recordId)
    {
        $this->recordId = $recordId;
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

    /**
     * Returns the hidden
     *
     * @return bool $hidden
     */
    public function getHidden()
    {
        return $this->hidden;
    }

    /**
     * Sets hidden
     *
     * @param bool $hidden
     * @return void
     */
    public function setHidden($hidden)
    {
        $this->hidden = $hidden;
    }

    /**
     * Returns the start time
     *
     * @return int $startTime
     */
    public function getStartTime()
    {
        return $this->startTime;
    }

    /**
     * Sets the start time
     *
     * @param int $startTime
     * @return void
     */
    public function setStartTime(int $startTime)
    {
        $this->startTime = $startTime;
    }

    /**
     * Returns the end time
     *
     * @return int $endTime
     */
    public function getEndTime()
    {
        return $this->endTime;
    }

    /**
     * Sets the end time
     *
     * @param int $endTime
     * @return void
     */
    public function setEndTime(int $endTime)
    {
        $this->endTime = $endTime;
    }

    /**
     * Returns the start time as string
     *
     * @return string $startTimeString
     */
    public function getStartTimeString()
    {
        return $this->startTimeString;
    }

    /**
     * Sets the start time as string
     *
     * @param string $startTimeString
     * @return void
     */
    public function setStartTimeString(string $startTimeString)
    {
        $this->startTime = strtotime($startTimeString);
        $this->startTimeString = $startTimeString;
    }

    /**
     * Returns the end time as string
     *
     * @return int $endTimeString
     */
    public function getEndTimeString()
    {
        return $this->endTimeString;
    }

    /**
     * Sets the end time as string
     *
     * @param string $endTimeString
     * @return void
     */
    public function setEndTimeString(string $endTimeString)
    {
        $this->endTime = $endTimeString;
        $this->endTimeString = strtotime($endTimeString);
    }

    /**
     * Returns the accessGrantedNotification
     *
     * @return int $access_granted_notification
     */
    public function getAccessGrantedNotification()
    {
        return $this->accessGrantedNotification;
    }

    /**
     * Sets the accessGrantedNotification
     *
     * @param int $accessGrantedNotification
     * @return void
     */
    public function setAccessGrantedNotification($accessGrantedNotification)
    {
        $this->accessGrantedNotification = $accessGrantedNotification;
    }

    /**
     * Returns the expireNotification
     *
     * @return int $access_granted_notification
     */
    public function getExpireNotification()
    {
        return $this->expireNotification;
    }

    /**
     * Sets the expireNotification
     *
     * @param int $expireNotification
     * @return void
     */
    public function setExpireNotification($expireNotification)
    {
        $this->expireNotification = $expireNotification;
    }

    /**
     * Returns the rejected
     *
     * @return bool $rejected
     */
    public function getRejected()
    {
        return $this->rejected;
    }

    /**
     * Sets the rejected
     *
     * @param bool $rejected
     * @return void
     */
    public function setRejected($rejected)
    {
        $this->rejected = $rejected;
    }

    /**
     * Returns the rejectedReason
     *
     * @return string $rejectedReason
     */
    public function getRejectedReason()
    {
        return $this->rejectedReason;
    }

    /**
     * Sets the rejectedReason
     *
     * @param string $rejectedReason
     * @return void
     */
    public function setRejectedReason($rejectedReason)
    {
        $this->rejectedReason = $rejectedReason;
    }

    /**
     * Returns the informUser
     *
     * @return bool $informUser
     */
    public function getInformUser()
    {
        return $this->informUser;
    }

    /**
     * Sets the informUser
     *
     * @param bool $informUser
     * @return void
     */
    public function setInformUser($informUser)
    {
        $this->informUser = $informUser;
    }
}
