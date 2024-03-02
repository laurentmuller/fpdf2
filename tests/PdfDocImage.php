<?php
/*
 * This file is part of the 'fpdf' package.
 *
 * For the license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author bibi.nu <bibi@bibi.nu>
 */

declare(strict_types=1);

namespace fpdf;

class PdfDocImage extends AbstractPdfDocTestCase
{
    public function testImageEmpty(): void
    {
        self::expectException(PdfException::class);
        $doc = $this->createDocument(false, false);
        $doc->image('');
        self::fail('A PDF exception must be raised.');
    }

    public function testImageEmptyType(): void
    {
        self::expectException(PdfException::class);
        $doc = $this->createDocument(false, false);
        $doc->image(__DIR__);
        self::fail('A PDF exception must be raised.');
    }

    public function testImageHeightLessZero(): void
    {
        $doc = $this->createDocument();
        $doc->image(__DIR__ . '/images/image.png', height: -10.0);
        self::assertSame(1, $doc->getPage());
    }

    public function testImageWidthLessZero(): void
    {
        $doc = $this->createDocument();
        $doc->image(__DIR__ . '/images/image.png', width: -10.0);
        self::assertSame(1, $doc->getPage());
    }

    public function testImageWrong(): void
    {
        self::expectException(PdfException::class);
        $doc = $this->createDocument(false, false);
        $doc->image(__FILE__);
        self::fail('A PDF exception must be raised.');
    }

    public function testImageWrongGif(): void
    {
        self::expectException(PdfException::class);
        $doc = $this->createDocument();
        $doc->image(__DIR__ . '/fake.txt', type: 'gif');
        self::fail('A PDF exception must be raised.');
    }

    public function testImageWrongJpeg(): void
    {
        self::expectException(PdfException::class);
        $doc = $this->createDocument();
        $doc->image(__DIR__ . '/fake.txt', type: 'jpg');
        self::fail('A PDF exception must be raised.');
    }

    public function testImageWrongPng(): void
    {
        self::expectException(PdfException::class);
        $doc = $this->createDocument();
        $doc->image(__DIR__ . '/fake.txt', type: 'png');
        self::fail('A PDF exception must be raised.');
    }
}
