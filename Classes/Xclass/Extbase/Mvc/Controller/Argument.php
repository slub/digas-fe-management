<?php

namespace  Slub\DigasFeManagement\Xclass\Extbase\Mvc\Controller;

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

class Argument extends \TYPO3\CMS\Extbase\Mvc\Controller\Argument
{
    /**
     * Set data type for femanager workaround.
     * Workaround to avoid php7 warnings of wrong type hint.
     *
     * @param $dataType
     * @return $this
     */
    public function setDataType($dataType) {
        $this->dataType = $dataType;
        return $this;
    }
}
