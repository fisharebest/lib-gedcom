<?php
/**
 * @copyright 2017 Greg Roach <fisharebest@gmail.com>
 * @license   GPLv3+
 */
declare(strict_types=1);

namespace Fisharebest\LibGedcom\Encodings;

use Generator;

/**
 * Utilities to manage the various encodings.
 */
class EncodingHelper {
    /**
     * Magic strings that uniquely identify UTF encoding.
     *
     * @return AbstractEncodingUtf[]
     */
    public function utf16MagicStrings(): array {
        $utf8    = new Utf8Encoding;
        $utf16be = new Utf16BeEncoding;
        $utf16le = new Utf16LeEncoding;

        return [
            $utf8::BYTE_ORDER_MARK       => $utf8,
            $utf16be::BYTE_ORDER_MARK    => $utf16be,
            $utf16le::BYTE_ORDER_MARK    => $utf16le,
            $utf16be->fromUtf8('0 HEAD') => $utf16be,
            $utf16le->fromUtf8('0 HEAD') => $utf16le,
        ];
    }

    /**
     * What character sets might we find in a GEDCOM file, and what
     * encodings should we use to read them.
     *
     * @return Generator
     */
    public function characterSetsEncodings(): Generator {
        yield [[
            'ANSEL',
        ], new AnselEncoding];

        yield [[
            'ASCII',
        ], new AsciiEncoding];

        yield [[
            'UTF-8',
            'UNICODE',
        ], new Utf8Encoding];

        yield [[
            'IBMPC',
            'IBM', // Reunion
            'IBM-PC', // Cumberland Family Tree
            'OEM', // Généatique
        ], new Cp437Encoding];

        yield [[
            'MSDOS',
            'IBM DOS', // Reunion, EasyTree
            'MS-DOS', // AbrEdit, FTMwin
        ], new Cp850Encoding];

        yield [[
            'WINDOWS-1250', // GenoPro, Rodokmen Pro
        ], new Cp1250Encoding];

        yield [[
            'WINDOWS-1251', // Rodovid
        ], new Cp1251Encoding];

        yield [[
            'ANSI', // ANSI just means a windows code page.
            'WINDOWS', // Parentele
            'IBM WINDOWS', // EasyTree, Généalogie, Reunion, TribalPages
            'IBM_WINDOWS', // EasyTree
            'CP1252', // Lifelines
            'ISO-8859-1', // Cumberland Family Tree, Lifelines
            'ISO8859-1', // Scion Genealogist
            'ISO8859', // Genealogica Grafica
            'LATIN1', // GenealogyJ
        ], new Cp1252Encoding];

        yield [[
            'MACINTOSH',
            'ASCII/MACOS ROMAN' // GEDitCOM
        ], new MacintoshEncoding];
    }
}
