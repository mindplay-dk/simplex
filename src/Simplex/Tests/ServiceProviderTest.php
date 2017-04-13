<?php

/*
 * This file is part of Simplex.
 *
 * Copyright (c) 2009 Fabien Potencier
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is furnished
 * to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

namespace Simplex\Tests;

use Interop\Provider\ServiceProviderInterface;
use Interop\Provider\ServiceRegistryInterface;
use Simplex\Container;
use Simplex\Tests\Fixtures\Service;

/**
 * @author Dominik Zogg <dominik.zogg@gmail.com>
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class ServiceProviderTest extends \PHPUnit_Framework_TestCase
{
    public function testProvider()
    {
        // create a source Container with some dependencies:

        $source = new Container();

        $this->assertInstanceOf(ServiceProviderInterface::class, $source);
        $this->assertInstanceOf(ServiceRegistryInterface::class, $source);

        $source->set("value", "VALUE");

        $source["service"] = function (Container $c) {
            $service = new Service();

            $service->value = $c["value"];

            return $service;
        };

        // create an empty target Container:

        $target = new Container();

        // import to target Container from source Container:

        $target->registerProvider($source);

        $this->assertSame("VALUE", $target["value"]);

        $this->assertInstanceOf(Service::class, $target["service"]);

        $this->assertSame($target["service"]->value, $target["value"]);

        // this last test is to make sure the implementation delegates, as opposed to copying the
        // factory-function itself, which would lead to the creation of two distinct instances!

        $this->assertSame($source["service"], $target["service"]);
    }
}
