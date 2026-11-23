<?php

namespace App\Support;

use RuntimeException;
use ZipArchive;

/**
 * Bina kisi package ke chhota sa .xlsx writer (sirf PHP ka ZipArchive chahiye).
 *
 * $xlsx = new SimpleXlsx('Members');
 * $xlsx->addRow(['Name', 'Mobile'], true);   // true = bold header
 * $xlsx->addRow(['Ramesh', '9876543210']);
 * return $xlsx->download('members.xlsx');
 *
 * Rows seedhe temp file me likhe jaate hain, isliye hazaron rows me bhi memory kam lagti hai.
 */
class SimpleXlsx
{
    private string $sheetName;
    private string $tmpSheet;
    /** @var resource */
    private $handle;
    private int $rowNo = 0;
    private array $colWidths = [];

    public function __construct(string $sheetName = 'Sheet1')
    {
        if (!class_exists(ZipArchive::class)) {
            throw new RuntimeException('PHP zip extension enable nahi hai (php-zip install karein).');
        }

        $this->sheetName = mb_substr(preg_replace('/[\\\\\/\?\*\[\]:]/', '', $sheetName) ?: 'Sheet1', 0, 31);
        $this->tmpSheet  = tempnam(sys_get_temp_dir(), 'xlsx_rows_');
        $this->handle    = fopen($this->tmpSheet, 'w');
    }

    public function setColumnWidths(array $widths): self
    {
        $this->colWidths = array_values($widths);

        return $this;
    }

    public function addRow(array $cells, bool $bold = false): self
    {
        $this->rowNo++;
        $xml = '<row r="' . $this->rowNo . '">';

        foreach (array_values($cells) as $i => $value) {
            $ref   = self::colLetter($i) . $this->rowNo;
            $style = $bold ? ' s="1"' : '';

            if (is_int($value) || is_float($value)) {
                $xml .= '<c r="' . $ref . '"' . $style . '><v>' . $value . '</v></c>';
            } else {
                // Mobile / pincode jaise numbers bhi text rahenge (0 aur E+ format ki dikkat nahi)
                $text = self::clean((string) $value);
                $xml .= '<c r="' . $ref . '" t="inlineStr"' . $style . '><is><t xml:space="preserve">' . $text . '</t></is></c>';
            }
        }

        fwrite($this->handle, $xml . '</row>');

        return $this;
    }

    /**
     * Poori .xlsx file ka path (temp) return karta hai.
     */
    public function save(): string
    {
        fclose($this->handle);

        $cols = '';
        if ($this->colWidths) {
            $cols = '<cols>';
            foreach ($this->colWidths as $i => $w) {
                $cols .= '<col min="' . ($i + 1) . '" max="' . ($i + 1) . '" width="' . (float) $w . '" customWidth="1"/>';
            }
            $cols .= '</cols>';
        }

        // Header row freeze
        $sheetHead = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
            . '<sheetViews><sheetView workbookViewId="0"><pane ySplit="1" topLeftCell="A2" activePane="bottomLeft" state="frozen"/></sheetView></sheetViews>'
            . $cols . '<sheetData>';
        $sheetFoot = '</sheetData></worksheet>';

        $sheetFile = tempnam(sys_get_temp_dir(), 'xlsx_sheet_');
        $out = fopen($sheetFile, 'w');
        fwrite($out, $sheetHead);
        $in = fopen($this->tmpSheet, 'r');
        stream_copy_to_stream($in, $out);
        fclose($in);
        fwrite($out, $sheetFoot);
        fclose($out);
        @unlink($this->tmpSheet);

        $xlsxFile = tempnam(sys_get_temp_dir(), 'xlsx_');
        $zip = new ZipArchive();
        if ($zip->open($xlsxFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new RuntimeException('Excel file nahi ban payi.');
        }

        $zip->addFromString('[Content_Types].xml',
            '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
            . '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
            . '<Default Extension="xml" ContentType="application/xml"/>'
            . '<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>'
            . '<Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>'
            . '<Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>'
            . '</Types>');

        $zip->addFromString('_rels/.rels',
            '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>'
            . '</Relationships>');

        $zip->addFromString('xl/workbook.xml',
            '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
            . '<sheets><sheet name="' . self::clean($this->sheetName) . '" sheetId="1" r:id="rId1"/></sheets>'
            . '</workbook>');

        $zip->addFromString('xl/_rels/workbook.xml.rels',
            '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>'
            . '<Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>'
            . '</Relationships>');

        // Style 0 = normal, style 1 = bold + light green header
        $zip->addFromString('xl/styles.xml',
            '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            . '<fonts count="2"><font><sz val="11"/><name val="Calibri"/></font><font><b/><sz val="11"/><name val="Calibri"/></font></fonts>'
            . '<fills count="3"><fill><patternFill patternType="none"/></fill><fill><patternFill patternType="gray125"/></fill>'
            . '<fill><patternFill patternType="solid"><fgColor rgb="FFE2EFDA"/><bgColor indexed="64"/></patternFill></fill></fills>'
            . '<borders count="1"><border><left/><right/><top/><bottom/><diagonal/></border></borders>'
            . '<cellStyleXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"/></cellStyleXfs>'
            . '<cellXfs count="2"><xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/>'
            . '<xf numFmtId="0" fontId="1" fillId="2" borderId="0" xfId="0" applyFont="1" applyFill="1"/></cellXfs>'
            . '<cellStyles count="1"><cellStyle name="Normal" xfId="0" builtinId="0"/></cellStyles>'
            . '</styleSheet>');

        $zip->addFile($sheetFile, 'xl/worksheets/sheet1.xml');
        $zip->close();
        @unlink($sheetFile);

        return $xlsxFile;
    }

    public function download(string $fileName)
    {
        return response()->download($this->save(), $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }

    private static function colLetter(int $index): string
    {
        $letter = '';
        for ($n = $index + 1; $n > 0; $n = intdiv($n - 1, 26)) {
            $letter = chr(65 + ($n - 1) % 26) . $letter;
        }

        return $letter;
    }

    /** XML-escape + invalid control characters hatao */
    private static function clean(string $value): string
    {
        $value = preg_replace('/[^\x{9}\x{A}\x{D}\x{20}-\x{D7FF}\x{E000}-\x{FFFD}\x{10000}-\x{10FFFF}]/u', '', $value) ?? '';

        return htmlspecialchars($value, ENT_XML1 | ENT_QUOTES, 'UTF-8');
    }
}
