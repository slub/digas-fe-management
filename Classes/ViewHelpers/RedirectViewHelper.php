<?php

namespace Slub\DigasFeManagement\ViewHelpers;

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

use TYPO3\CMS\Fluid\ViewHelpers\Uri\PageViewHelper;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;

/**
 * ViewHelper for redirect with parameters
 *
 * # Example: Basic example
 * # Look Uri/PageViewHelper for options
 * <code>
 * <digas:redirect pageUid="1" additionalParams="{foo: 'bar'}" />
 *
 * `/page/path/name.html?foo=bar`
 * </code>
 * <output>
 * redirects to page (with parameters)
 * </output>
 *
 * @package TYPO3
 */
class RedirectViewHelper extends PageViewHelper
{
    /**
     * @param array $arguments
     * @param \Closure $renderChildrenClosure
     * @param RenderingContextInterface $renderingContext
     * @return void
     */
    public static function renderStatic(array $arguments, \Closure $renderChildrenClosure, RenderingContextInterface $renderingContext)
    {
        // check if redirect contains kitodo gp-id and setCookie-param - redirect to set pageUid / set cookie for basket
        if (!empty($arguments['additionalParams']['setCookie']) && !empty($arguments['additionalParams']['gp-id'])) {
            // set cookie by request params
            RedirectViewHelper::setCookie($arguments['additionalParams']['gp-id']);

            // unset redirect parameters
            unset($arguments['additionalParams']['setCookie']);
            unset($arguments['additionalParams']['gp-id']);
        }

        $uri = parent::renderStatic($arguments, $renderChildrenClosure, $renderingContext);

        header("HTTP/1.1 303 See other");
        header("Location: $uri");
        exit();
    }

    /**
     * add gp-id to cookie 'dlf-requests'
     *
     * @param string $newKitodoRequestId
     * @return void
     */
    protected static function setCookie(string $newKitodoRequestId) {
        // get cookie 'dlf-requests'
        $kitodoRequestIds = $_COOKIE['dlf-requests'];
        $kitodoRequestIds = !empty($kitodoRequestIds) ? json_decode($kitodoRequestIds) : [];

        // add ID to cookie 'dlf-request' if ID is not already contained
        if (!in_array($newKitodoRequestId, $kitodoRequestIds)) {
            array_push($kitodoRequestIds, $newKitodoRequestId);
            setcookie('dlf-requests', json_encode($kitodoRequestIds), 0, '/');
        }
    }
}
