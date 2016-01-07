<?php
/**
 * @copyright 2017 Greg Roach <fisharebest@gmail.com>
 * @license   GPLv3+
 */
declare(strict_types=1);

namespace Fisharebest\LibGedcom\Encodings;

use Normalizer;

/**
 * Convert between UTF-8 and ANSEL encoding.
 *
 * ANSEL is the common name for the MARC-21 encoding, also known as Z39.47, which
 * has a number of editions.  These are denoted by a year suffix.
 *
 * The GEDCOM 5.5.1 specification (1999-10-02) specifies the Z39.47-1985 edition.
 * It adds Es Zett (ß) at CF.  According to wikipedia, other non-standard characters
 * are also added.
 *
 * HEX Unicode Glyph Description
 * BE  25A1    □     Empty box
 * BF  25A0    ■     Black box
 * CD  0065    e     Midline e
 * CE  006F    o     Midline o
 * CF  00DF    ß     Es Zett
 * FC  0338    /     Combining slash
 *
 * @link https://en.wikipedia.org/wiki/ANSEL
 *
 * The MARC-21 specification has added a number of additional characters since
 * the 1985 edition.
 *
 * HEX Unicode Glyph Description
 * 88  0098          Start of string
 * 89  009C          String terminator
 * 8D  200D          Zero width joiner
 * 8E  200C          Zero width non-joiner
 * C7  00DF    ß     Es Zett
 * C8  20AC    €     Euro sign
 * E0  0309          Hook above
 * EF  0310          Chandrabindu
 * F2  0323          Low dot
 * F3  0324
 * F4  0325
 * F5  0333
 * F7  0332
 * F8  031C
 * F9  0328
 * FF  0338          Slash
 * @link http://memory.loc.gov/diglib/codetables/45.html
 *
 * Note that this means we can expect two different representations of Es Zett.
 *
 * There are two multi-part diacritics.  There are two ways to represent these.
 *
 * ANSEL       | UTF-8         | UTF-8 (prefered)
 * ------------+---------------+-----------------
 * FA x FB y   | x FE22 y FE23 | x 0360 y
 * EB x EC y   | y FE20 y FE21 | x 0361 y
 */
class AnselEncoding extends AbstractEncodingLookup {
    const ENCODING_NAME = 'ANSEL';

    const TO_UTF8 = [
        "\x80" => '',
        "\x81" => '',
        "\x82" => '',
        "\x83" => '',
        "\x84" => '',
        "\x85" => '',
        "\x86" => '',
        "\x87" => '',
        "\x88" => "\u{0098}", // Start of string
        "\x89" => "\u{009C}", // String terminator
        "\x8A" => '',
        "\x8B" => '',
        "\x8C" => '',
        "\x8D" => "\u{200D}", // Zero width joiner
        "\x8E" => "\u{200C}", // Zero width non-joiner
        "\x8F" => '',
        "\x90" => '',
        "\x91" => '',
        "\x92" => '',
        "\x93" => '',
        "\x94" => '',
        "\x95" => '',
        "\x96" => '',
        "\x97" => '',
        "\x98" => '',
        "\x99" => '',
        "\x9A" => '',
        "\x9B" => '',
        "\x9C" => '',
        "\x9D" => '',
        "\x9E" => '',
        "\x9F" => '',
        "\xA0" => '',
        "\xA1" => "\u{0141}", // (Ł)
        "\xA2" => "\u{00D8}", // (Ø)
        "\xA3" => "\u{0110}", // (Đ)
        "\xA4" => "\u{00DE}", // (Þ)
        "\xA5" => "\u{00C6}", // (Æ)
        "\xA6" => "\u{0152}", // (Œ)
        "\xA7" => "\u{02B9}", // (ʹ)
        "\xA8" => "\u{00B7}", // (·)
        "\xA9" => "\u{266D}", // (♭)
        "\xAA" => "\u{00AE}", // (®)
        "\xAB" => "\u{00B1}", // (±)
        "\xAC" => "\u{01A0}", // (Ơ)
        "\xAD" => "\u{01AF}", // (Ư)
        "\xAE" => "\u{02BC}", // (ʼ)
        "\xAF" => '',
        "\xB0" => "\u{02BB}", // (ʻ)
        "\xB1" => "\u{0142}", // (ł)
        "\xB2" => "\u{00F8}", // (ø)
        "\xB3" => "\u{0111}", // (đ)
        "\xB4" => "\u{00FE}", // (þ)
        "\xB5" => "\u{00E6}", // (æ)
        "\xB6" => "\u{0153}", // (œ)
        "\xB7" => "\u{02BA}", // (ʺ)
        "\xB8" => "\u{0131}", // (ı)
        "\xB9" => "\u{00A3}", // (£)
        "\xBA" => "\u{00F0}", // (ð)
        "\xBB" => '',
        "\xBC" => "\u{01A1}", // (ơ)
        "\xBD" => "\u{01B0}", // (ư)
        "\xBE" => '',
        "\xBF" => '',
        "\xC0" => "\u{00B0}", // (°)
        "\xC1" => "\u{2113}", // (ℓ)
        "\xC2" => "\u{2117}", // (℗)
        "\xC3" => "\u{00A9}", // (©)
        "\xC4" => "\u{266F}", // (♯)
        "\xC5" => "\u{00BF}", // (¿)
        "\xC6" => "\u{00A1}", // (¡)
        "\xC7" => "\u{00DF}", // (ß)
        "\xC8" => '',
        "\xC9" => '',
        "\xCA" => '',
        "\xCB" => '',
        "\xCC" => '',
        "\xCD" => '',
        "\xCE" => '',
        "\xCF" => '',
        "\xD0" => '',
        "\xD1" => '',
        "\xD2" => '',
        "\xD3" => '',
        "\xD4" => '',
        "\xD5" => '',
        "\xD6" => '',
        "\xD7" => '',
        "\xD8" => '',
        "\xD9" => '',
        "\xDA" => '',
        "\xDB" => '',
        "\xDC" => '',
        "\xDD" => '',
        "\xDE" => '',
        "\xDF" => '',
        "\xE0" => "\u{0309}", // (hook above)
        "\xE1" => "\u{0300}", // (grave)
        "\xE2" => "\u{0301}", // (acute)
        "\xE3" => "\u{0302}", // (circumflex)
        "\xE4" => "\u{0303}", // (tilde)
        "\xE5" => "\u{0304}", // (macron)
        "\xE6" => "\u{0306}", // (breve)
        "\xE7" => "\u{0307}", // (dot above)
        "\xE8" => "\u{0308}", // (diaeresis)
        "\xE9" => "\u{030C}", // (hacek)
        "\xEA" => "\u{030A}", // (circle above)
        "\xEB" => "\u{0361}", // (tie ligature)
        "\xEC" => '',
        "\xED" => "\u{0315}", // (high comma)
        "\xEE" => "\u{030B}", // (double acute)
        "\xEF" => "\u{0310}", // (chandrabindu)
        "\xF0" => "\u{0327}", // (cedilla)
        "\xF1" => "\u{0328}", // (ogonek)
        "\xF2" => "\u{0323}", // (low dot)
        "\xF3" => "\u{0324}", // ()
        "\xF4" => "\u{0325}", // ()
        "\xF5" => "\u{0333}", // ()
        "\xF6" => "\u{0332}", // (low line)
        "\xF7" => "\u{0326}", // ()
        "\xF8" => "\u{031C}", // ()
        "\xF9" => "\u{032E}", // ()
        "\xFA" => "\u{0360}", // ()
        "\xFB" => '',
        "\xFC" => '',
        "\xFD" => '',
        "\xFE" => "\u{0313}", // (high comma)
        "\xFF" => "\u{0338}", // (/)
    ];

    // ANSEL supports O and U with a horn diacritic, but not the combining diacritic.
    const HORN_CONVERT_STEP_1 = [
        "O\u{031B}" => "\x00O_WITH_HORN\x00",
        "U\u{031B}" => "\x00U_WITH_HORN\x00",
        "o\u{031B}" => "\x00o_WITH_HORN\x00",
        "u\u{031B}" => "\x00u_WITH_HORN\x00",
    ];
    const HORN_CONVERT_STEP_2 = [
        "\x00O_WITH_HORN\x00" => "\xAC",
        "\x00U_WITH_HORN\x00" => "\xAD",
        "\x00o_WITH_HORN\x00" => "\xBC",
        "\x00u_WITH_HORN\x00" => "\xBD",
    ];

    public function toUtf8(string $text): string {
        // ANSEL diacritics are prefixes.  UTF-8 diacritics are suffixes.
        $text = preg_replace('/([\xE0-\xFF]+)(.)/', '$2$1', $text);

        // Simple substitution creates denormalized UTF-8.
        $text = parent::toUtf8($text);

        // Convert combining diacritics to precomposed characters.
        $tmp = Normalizer::normalize($text, Normalizer::NFC);
        if ($tmp !== false) {
            $text = $tmp;
        }

        return $text;
    }

    public function fromUtf8(string $text): string {
        // Convert precomposed characters to combining diacritics.
        $text = Normalizer::normalize($text, Normalizer::NFD);

        // ANSEL supports letters with horns, but not the combining horn.
        $text = strtr($text, self::HORN_CONVERT_STEP_1);

        // Convert characters and combining diacritics separately.
        $text = parent::fromUtf8($text);

        // ANSEL supports two letters with horns, but not the combining horn.
        $text = strtr($text, self::HORN_CONVERT_STEP_2);

        // ANSEL diacritics are prefixes.  UTF-8 diacritics are suffixes.
        $text = preg_replace('/([^\xE0-\xFF])([\xE0-\xFF]+)/', '$2$1', $text);

        return $text;
    }
}
