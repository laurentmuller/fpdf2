<?php

/*
 * This file is part of the FPDF2 package.
 *
 * (c) bibi.nu <bibi@bibi.nu>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

use fpdf\Enums\PdfDirection;
use fpdf\Enums\PdfState;
use fpdf\PdfException;
use fpdf\PdfWriter;
use PHPUnit\Framework\TestCase;

final class PdfWriterTest extends TestCase
{
    private PdfWriter $writer;

    #[Override]
    protected function setUp(): void
    {
        $this->writer = new PdfWriter();
    }

    public function testBeginPageValid(): void
    {
        $this->writer->setCompression(false);
        $this->writer->beginPage(1);
        $this->writer->endPage();
        $this->writer->putStreamPage(1);
        self::assertSameBuffer(
            $this->writer,
            '3 0 obj',
            '<</Length 0>>',
            'stream',
            '',
            'endstream',
            'endobj',
        );
    }

    public function testExceptionOutClosed(): void
    {
        self::expectException(PdfException::class);
        self::expectExceptionMessage('Invalid call: Document closed.');
        $this->writer->close();
        $this->writer->out(1, 'fake');
    }

    public function testExceptionOutEndPage(): void
    {
        self::expectException(PdfException::class);
        self::expectExceptionMessage('Invalid call: End page.');
        $this->writer->beginPage(1);
        $this->writer->endPage();
        $this->writer->out(1, 'fake');
    }

    public function testExceptionOutNoPage(): void
    {
        self::expectException(PdfException::class);
        self::expectExceptionMessage('Invalid call: No page added.');
        $this->writer->out(1, 'fake');
    }

    public function testExceptionStateNoPageToEndPage(): void
    {
        self::expectException(PdfException::class);
        self::expectExceptionMessage('Unable to move state from "NO_PAGE" to "END_PAGE".');
        $this->writer->endPage();
    }

    public function testExceptionStatePageStartedToClosed(): void
    {
        self::expectException(PdfException::class);
        self::expectExceptionMessage('Unable to move state from "PAGE_STARTED" to "CLOSED".');
        $this->writer->beginPage(1);
        $this->writer->close();
    }

    public function testExceptionStatePageStartedToPageStarted(): void
    {
        self::expectException(PdfException::class);
        self::expectExceptionMessage('Unable to move state from "PAGE_STARTED" to "PAGE_STARTED".');
        $this->writer->beginPage(1);
        $this->writer->beginPage(2);
    }

    public function testFormatNumber(): void
    {
        $actual = PdfWriter::formatNumber(1);
        self::assertSame('1 0 R', $actual);
    }

    public function testInitialValues(): void
    {
        self::assertSame(2, $this->writer->getObjectNumber());
        self::assertSame('', $this->writer->getBuffer());
        self::assertSame([], $this->writer->getOffsets());
        self::assertSame(0, $this->writer->getOffset());
        self::assertFalse($this->writer->isClosed());
    }

    public function testOutf(): void
    {
        $this->writer->setCompression(false);
        $this->writer->beginPage(1);
        $this->writer->outf(1, '%d', 1);
        $this->writer->putStreamPage(1);
        self::assertSameBuffer(
            $this->writer,
            '3 0 obj',
            '<</Length 2>>',
            'stream',
            '1',
            '',
            'endstream',
            'endobj',
        );
    }

    public function testOutValid(): void
    {
        $this->writer->beginPage(1);
        $this->writer->out(1, 'content');
        self::assertFalse($this->writer->isClosed());
    }

    public function testPutBackedEnum(): void
    {
        $this->writer->put(PdfDirection::L2R);
        self::assertSameBuffer($this->writer, 'L2R');
    }

    public function testPutEndObj(): void
    {
        $this->writer->putEndObj();
        self::assertSameBuffer($this->writer, 'endobj');
    }

    public function testPutf(): void
    {
        $this->writer->putf('%d', 1);
        self::assertSameBuffer($this->writer, '1');
    }

    public function testPutNewObj(): void
    {
        $this->writer->putNewObj(1);
        self::assertSameBuffer($this->writer, '1 0 obj');
    }

    public function testPutStream(): void
    {
        $this->writer->putStream('data');
        self::assertSameBuffer($this->writer, 'stream', 'data', 'endstream');
    }

    public function testPutStreamObjectNoCompression(): void
    {
        $this->writer->setCompression(false);
        $this->writer->putStreamObject('data');
        self::assertSameBuffer(
            $this->writer,
            '3 0 obj',
            '<</Length 4>>',
            'stream',
            'data',
            'endstream',
            'endobj',
        );
    }

    public function testPutStreamObjectWithCompression(): void
    {
        $this->writer->putStreamObject('data');
        self::assertSameBuffer(
            $this->writer,
            '3 0 obj',
            '<</Filter /FlateDecode /Length 12>>',
            'stream',
            (string) \gzcompress('data'),
            'endstream',
            'endobj',
        );
    }

    public function testPutStreamPage(): void
    {
        $this->writer->setCompression(false);
        $this->writer->beginPage(1);
        $this->writer->putStreamPage(1);
        self::assertSameBuffer(
            $this->writer,
            '3 0 obj',
            '<</Length 0>>',
            'stream',
            '',
            'endstream',
            'endobj',
        );
    }

    public function testPutUnitEnum(): void
    {
        $this->writer->put(PdfState::CLOSED);
        self::assertSameBuffer($this->writer, 'CLOSED');
    }

    public function testUpdateAliasNumberPages(): void
    {
        $this->writer->setCompression(false);
        $this->writer->beginPage(1);
        $this->writer->out(1, '{pages}');
        $this->writer->updateAliasNumberPages(1, 10, '{pages}');
        $this->writer->putStreamPage(1);
        self::assertSameBuffer(
            $this->writer,
            '3 0 obj',
            '<</Length 3>>',
            'stream',
            '10',
            '',
            'endstream',
            'endobj',
        );
    }

    protected static function assertSameBuffer(PdfWriter $writer, string ...$values): void
    {
        $expected = \implode(PdfWriter::NEW_LINE, $values) . PdfWriter::NEW_LINE;
        self::assertSame($expected, $writer->getBuffer());
    }
}
