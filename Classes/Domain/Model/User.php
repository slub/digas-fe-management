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
 * Class User
 *
 * @package Slub\DigasFeManagement\Domain\Model
 */
class User extends \In2code\Femanager\Domain\Model\User
{
    /**
     * companyType
     *
     * @var string
     */
    protected $companyType;

    /**
     * @var int
     */
    protected $deleted;

    /**
     * @var \DateTime
     */
    protected $inactivemessageTstamp;

    /**
     * Locale setting (sys_language_uid)
     *
     * @var int
     */
    protected $locale;

    /**
     * pwChangedOnConfirmation
     *
     * @var bool
     */
    protected $pwChangedOnConfirmation;

    /**
     * mustChangePassword
     *
     * @var bool
     */
    protected $mustChangePassword;

    /**
     * hidden
     *
     * @var bool
     */
    protected $hidden;

    /**
     * oldAccount
     *
     * @var int
     */
    protected $oldAccount;

    /**
     * tempUserOrderingParty
     *
     * @var string
     */
    protected $tempUserOrderingParty;

    /**
     * tempUserAreaLocation
     *
     * @var string
     */
    protected $tempUserAreaLocation;

    /**
     * tempUserPurpose
     *
     * @var string
     */
    protected $tempUserPurpose;

    /**
     * district
     *
     * @var string
     */
    protected $district;

    /**
     * Returns the companyType
     *
     * @return string $companyType
     */
    public function getCompanyType()
    {
        return $this->companyType;
    }

    /**
     * Sets the companyType
     *
     * @param string $companyType
     * @return void
     */
    public function setCompanyType($companyType)
    {
        $this->companyType = $companyType;
    }

    /**
     * Get the inactivemessageTstamp
     *
     * @return \DateTime
     */
    public function getInactivemessageTstamp()
    {
        return $this->inactivemessageTstamp;
    }

    /**
     * Sets the inactivemessageTstamp
     *
     * @param \DateTime $inactivemessageTstamp
     * @return void
     */
    public function setInactivemessageTstamp($inactivemessageTstamp)
    {
        $this->inactivemessageTstamp = $inactivemessageTstamp;
    }

    /**
     * Returns the locale
     *
     * @return int $locale
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * Sets the locale
     *
     * @param int $locale
     * @return void
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;
    }

    /**
     * Returns the mustChangePassword
     *
     * @return bool $mustChangePassword
     */
    public function getMustChangePassword()
    {
        return $this->mustChangePassword;
    }

    /**
     * Sets the mustChangePassword
     *
     * @param bool $mustChangePassword
     * @return void
     */
    public function setMustChangePassword($mustChangePassword)
    {
        $this->mustChangePassword = $mustChangePassword;
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
     * Sets the companyType
     *
     * @param bool $hidden
     * @return void
     */
    public function setHidden($hidden)
    {
        $this->hidden = $hidden;
    }

    /**
     * Sets the deleted
     *
     * @return int
     */
    public function getDeleted()
    {
        return $this->deleted;
    }

    /**
     * Sets the deleted flag
     *
     * @param int $deleted
     * @return void
     */
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;
    }

    /**
     * Sets the isOnline
     *
     * @param bool $isOnline
     * @return void
     */
    public function setIsOnline($isOnline)
    {
        $this->isOnline = $isOnline;
    }

    /**
     * Returns the oldAccount
     *
     * @return int $oldAccount
     */
    public function getOldAccount()
    {
        return $this->oldAccount;
    }

    /**
     * Sets the oldAccount
     *
     * @param int $oldAccount
     * @return void
     */
    public function setOldAccount($oldAccount)
    {
        $this->oldAccount = $oldAccount;
    }

    /**
     * savedSearches
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Slub\DigasFeManagement\Domain\Model\Search>
     * @TYPO3\CMS\Extbase\Annotation\ORM\Cascade("remove")
     */
    protected $savedSearches;


    /**
     * kitodoDocumentAccess
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Slub\DigasFeManagement\Domain\Model\Access>
     * @TYPO3\CMS\Extbase\Annotation\ORM\Cascade("remove")
     */
    protected $kitodoDocumentAccess;

    /**
     * __construct
     */
    public function __construct()
    {

        //Do not remove the next line: It would break the functionality
        $this->initStorageObjects();
    }

    /**
     * Initializes all ObjectStorage properties
     * Do not modify this method!
     * It will be rewritten on each save in the extension builder
     * You may modify the constructor of this class instead
     *
     * @return void
     */
    protected function initStorageObjects()
    {
        $this->savedSearches = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
        $this->kitodoDocumentAccess = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
    }

    /**
     * Adds a savedSearch
     *
     * @param \Slub\DigasFeManagement\Domain\Model\Search $savedSearch
     * @return void
     */
    public function addSavedSearch(\Slub\DigasFeManagement\Domain\Model\Search $savedSearch)
    {
        $this->savedSearches->attach($savedSearch);
    }

    /**
     * Removes a savedSearch
     *
     * @param \Slub\DigasFeManagement\Domain\Model\Search $savedSearchToRemove The savedSearch to be removed
     * @return void
     */
    public function removeSavedSearch(\Slub\DigasFeManagement\Domain\Model\Search $savedSearchToRemove)
    {
        $this->savedSearches->detach($savedSearchToRemove);
    }

    /**
     * Returns the savedSearches
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Slub\DigasFeManagement\Domain\Model\Search> $savedSearches
     */
    public function getSavedSearches()
    {
        return $this->savedSearches;
    }

    /**
     * Sets the savedSearches
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Slub\DigasFeManagement\Domain\Model\Search> $icons
     * @return void
     */
    public function setSavedSearches(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $savedSearches)
    {
        $this->savedSearches = $savedSearches;
    }

    /**
     * Returns the firstName and the lastName
     *
     * @return string
     */
    public function getFullName()
    {
        return $this->firstName . ' ' . $this->lastName;
    }

    /**
     * Sets the pwChangedOnConfirmation
     *
     * @param bool $pwChangedOnConfirmation
     * @return void
     */
    public function setPwChangedOnConfirmation(bool $pwChangedOnConfirmation)
    {
        $this->pwChangedOnConfirmation = $pwChangedOnConfirmation;
    }

    /**
     * Returns the pwChangedOnConfirmation
     *
     * @return bool $pwChangedOnConfirmation
     */
    public function getPwChangedOnConfirmation()
    {
        return $this->pwChangedOnConfirmation;
    }

    /**
     * Returns the tempUserOrderingParty
     *
     * @return string $tempUserOrderingParty
     */
    public function getTempUserOrderingParty()
    {
        return $this->tempUserOrderingParty;
    }

    /**
     * Sets the tempUserOrderingParty
     *
     * @param string $tempUserOrderingParty
     * @return void
     */
    public function setTempUserOrderingParty($tempUserOrderingParty)
    {
        $this->tempUserOrderingParty = $tempUserOrderingParty;
    }

    /**
     * Returns the tempUserAreaLocation
     *
     * @return string $tempUserAreaLocation
     */
    public function getTempUserAreaLocation()
    {
        return $this->tempUserAreaLocation;
    }

    /**
     * Sets the tempUserAreaLocation
     *
     * @param string $tempUserAreaLocation
     * @return void
     */
    public function setTempUserAreaLocation($tempUserAreaLocation)
    {
        $this->tempUserAreaLocation = $tempUserAreaLocation;
    }

    /**
     * Returns the tempUserPurpose
     *
     * @return string $tempUserPurpose
     */
    public function getTempUserPurpose()
    {
        return $this->tempUserPurpose;
    }

    /**
     * Sets the tempUserPurpose
     *
     * @param string $tempUserPurpose
     * @return void
     */
    public function setTempUserPurpose($tempUserPurpose)
    {
        $this->tempUserPurpose = $tempUserPurpose;
    }

    /**
     * Adds a kitodoDocumentAccess
     *
     * @param \Slub\DigasFeManagement\Domain\Model\Access $kitodoDocumentAccess
     * @return void
     */
    public function addKitodoDocumentAccess(\Slub\DigasFeManagement\Domain\Model\Access $kitodoDocumentAccess)
    {
        $this->kitodoDocumentAccess->attach($kitodoDocumentAccess);
    }

    /**
     * Removes a kitodoDocumentAccess
     *
     * @param \Slub\DigasFeManagement\Domain\Model\Access $kitodoDocumentAccessSearchToRemove The savedSearch to be removed
     * @return void
     */
    public function removeKitodoDocumentAccess(\Slub\DigasFeManagement\Domain\Model\Access $kitodoDocumentAccessSearchToRemove)
    {
        $this->kitodoDocumentAccess->detach($kitodoDocumentAccessSearchToRemove);
    }

    /**
     * Returns the kitodoDocumentAccess
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Slub\DigasFeManagement\Domain\Model\Access> $kitodoDocumentAccess
     */
    public function getKitodoDocumentAccess()
    {
        return $this->kitodoDocumentAccess;
    }

    /**
     * Sets the kitodoDocumentAccess
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Slub\DigasFeManagement\Domain\Model\Access> $icons
     * @return void
     */
    public function setKitodoDocumentAccess(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $kitodoDocumentAccess)
    {
        $this->kitodoDocumentAccess = $kitodoDocumentAccess;
    }

    /**
     * Returns the district
     *
     * @return string $district
     */
    public function getDistrict()
    {
        return $this->district;
    }

    /**
     * Sets the district
     *
     * @param string $district
     * @return void
     */
    public function setDistrict($district)
    {
        $this->district = $district;
    }
}
