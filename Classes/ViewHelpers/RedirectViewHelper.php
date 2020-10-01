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

use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3\CMS\Fluid\ViewHelpers\Uri\PageViewHelper;

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
        $uri = parent::renderStatic($arguments, $renderChildrenClosure, $renderingContext);

        header("HTTP/1.1 303 See other");
        header("Location: $uri");
        exit();
    }
}
