<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Validator\Tests\Mapping\Loader;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Mapping\Loader\LoaderInterface;

class FilesLoaderTest extends TestCase
{
    public function testCallsGetFileLoaderInstanceForeachPath()
    {
        $loader = $this->getFilesLoader($this->getFileLoader());
        $this->assertEquals(4, $loader->getTimesCalled());
    }

    public function testCallsActualFileLoaderForMetadata()
    {
        $fileLoader = $this->getFileLoader();
        $fileLoader->expects($this->exactly(4))
            ->method('loadClassMetadata');
        $loader = $this->getFilesLoader($fileLoader);
        $loader->loadClassMetadata(new ClassMetadata('Symfony\Component\Validator\Tests\Fixtures\Entity'));
    }

    public function getFilesLoader(LoaderInterface $loader)
    {
        return $this->getMockForAbstractClass('Symfony\Component\Validator\Tests\Fixtures\FilesLoader', array(array(
            DIR_DOMPDF.'/constraint-mapping.xml',
            DIR_DOMPDF.'/constraint-mapping.yaml',
            DIR_DOMPDF.'/constraint-mapping.test',
            DIR_DOMPDF.'/constraint-mapping.txt',
        ), $loader));
    }

    public function getFileLoader()
    {
        return $this->getMockBuilder('Symfony\Component\Validator\Mapping\Loader\LoaderInterface')->getMock();
    }
}
