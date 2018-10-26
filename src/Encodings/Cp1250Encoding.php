<?php
/**
 * @copyright 2018 Greg Roach <fisharebest@gmail.com>
 * @license   GPLv3+
 */
declare(strict_types=1);

namespace Fisharebest\LibGedcom\Encodings;

/**
 * Convert between Windows Code Page 1250 and UTF-8.
 *
 * @link https://en.wikipedia.org/wiki/Windows-1250
 */
class Cp1250Encoding extends AbstractEncodingLookup {
    const ENCODING_NAME = 'Code Page 1250';

    const TO_UTF8 = [
        "\x80" => "\u{20AC}", // (€)
        "\x81" => '',
        "\x82" => "\u{201A}", // (‚)
        "\x83" => '',
        "\x84" => "\u{201E}", // („)
        "\x85" => "\u{2026}", // (…)
        "\x86" => "\u{2020}", // (†)
        "\x87" => "\u{2021}", // (‡)
        "\x88" => '',
        "\x89" => "\u{2030}", // (‰)
        "\x8A" => "\u{0160}", // (Š)
        "\x8B" => "\u{2039}", // (‹)
        "\x8C" => "\u{015A}", // (Ś)
        "\x8D" => "\u{0164}", // (Ť)
        "\x8E" => "\u{017D}", // (Ž)
        "\x8F" => "\u{0179}", // (Ź)
        "\x90" => '',
        "\x91" => "\u{2018}", // (‘)
        "\x92" => "\u{2019}", // (’)
        "\x93" => "\u{201C}", // (“)
        "\x94" => "\u{201D}", // (”)
        "\x95" => "\u{2022}", // (•)
        "\x96" => "\u{2013}", // (–)
        "\x97" => "\u{2014}", // (—)
        "\x98" => '',
        "\x99" => "\u{2122}", // (™)
        "\x9A" => "\u{0161}", // (š)
        "\x9B" => "\u{203A}", // (›)
        "\x9C" => "\u{015B}", // (ś)
        "\x9D" => "\u{0165}", // (ť)
        "\x9E" => "\u{017E}", // (ž)
        "\x9F" => "\u{017A}", // (ź)
        "\xA0" => "\u{00A0}", // (non-breaking space)
        "\xA1" => "\u{02C7}", // (ˇ)
        "\xA2" => "\u{02D8}", // (˘)
        "\xA3" => "\u{0141}", // (Ł)
        "\xA4" => "\u{00A4}", // (¤)
        "\xA5" => "\u{0104}", // (Ą)
        "\xA6" => "\u{00A6}", // (¦)
        "\xA7" => "\u{00A7}", // (§)
        "\xA8" => "\u{00A8}", // (¨)
        "\xA9" => "\u{00A9}", // (©)
        "\xAA" => "\u{015E}", // (Ş)
        "\xAB" => "\u{00AB}", // («)
        "\xAC" => "\u{00AC}", // (¬)
        "\xAD" => "\u{00AD}", // (soft hyphen)
        "\xAE" => "\u{00AE}", // (®)
        "\xAF" => "\u{017B}", // (Ż)
        "\xB0" => "\u{00B0}", // (°)
        "\xB1" => "\u{00B1}", // (±)
        "\xB2" => "\u{02DB}", // (˛)
        "\xB3" => "\u{0142}", // (ł)
        "\xB4" => "\u{00B4}", // (´)
        "\xB5" => "\u{00B5}", // (µ)
        "\xB6" => "\u{00B6}", // (¶)
        "\xB7" => "\u{00B7}", // (·)
        "\xB8" => "\u{00B8}", // (¸)
        "\xB9" => "\u{0105}", // (ą)
        "\xBA" => "\u{015F}", // (ş)
        "\xBB" => "\u{00BB}", // (»)
        "\xBC" => "\u{013D}", // (Ľ)
        "\xBD" => "\u{02DD}", // (˝)
        "\xBE" => "\u{013E}", // (ľ)
        "\xBF" => "\u{017C}", // (ż)
        "\xC0" => "\u{0154}", // (Ŕ)
        "\xC1" => "\u{00C1}", // (Á)
        "\xC2" => "\u{00C2}", // (Â)
        "\xC3" => "\u{0102}", // (Ă)
        "\xC4" => "\u{00C4}", // (Ä)
        "\xC5" => "\u{0139}", // (Ĺ)
        "\xC6" => "\u{0106}", // (Ć)
        "\xC7" => "\u{00C7}", // (Ç)
        "\xC8" => "\u{010C}", // (Č)
        "\xC9" => "\u{00C9}", // (É)
        "\xCA" => "\u{0118}", // (Ę)
        "\xCB" => "\u{00CB}", // (Ë)
        "\xCC" => "\u{011A}", // (Ě)
        "\xCD" => "\u{00CD}", // (Í)
        "\xCE" => "\u{00CE}", // (Î)
        "\xCF" => "\u{010E}", // (Ď)
        "\xD0" => "\u{0110}", // (Đ)
        "\xD1" => "\u{0143}", // (Ń)
        "\xD2" => "\u{0147}", // (Ň)
        "\xD3" => "\u{00D3}", // (Ó)
        "\xD4" => "\u{00D4}", // (Ô)
        "\xD5" => "\u{0150}", // (Ő)
        "\xD6" => "\u{00D6}", // (Ö)
        "\xD7" => "\u{00D7}", // (×)
        "\xD8" => "\u{0158}", // (Ř)
        "\xD9" => "\u{016E}", // (Ů)
        "\xDA" => "\u{00DA}", // (Ú)
        "\xDB" => "\u{0170}", // (Ű)
        "\xDC" => "\u{00DC}", // (Ü)
        "\xDD" => "\u{00DD}", // (Ý)
        "\xDE" => "\u{0162}", // (Ţ)
        "\xDF" => "\u{00DF}", // (ß)
        "\xE0" => "\u{0155}", // (ŕ)
        "\xE1" => "\u{00E1}", // (á)
        "\xE2" => "\u{00E2}", // (â)
        "\xE3" => "\u{0103}", // (ă)
        "\xE4" => "\u{00E4}", // (ä)
        "\xE5" => "\u{013A}", // (ĺ)
        "\xE6" => "\u{0107}", // (ć)
        "\xE7" => "\u{00E7}", // (ç)
        "\xE8" => "\u{010D}", // (č)
        "\xE9" => "\u{00E9}", // (é)
        "\xEA" => "\u{0119}", // (ę)
        "\xEB" => "\u{00EB}", // (ë)
        "\xEC" => "\u{011B}", // (ě)
        "\xED" => "\u{00ED}", // (í)
        "\xEE" => "\u{00EE}", // (î)
        "\xEF" => "\u{010F}", // (ď)
        "\xF0" => "\u{0111}", // (đ)
        "\xF1" => "\u{0144}", // (ń)
        "\xF2" => "\u{0148}", // (ň)
        "\xF3" => "\u{00F3}", // (ó)
        "\xF4" => "\u{00F4}", // (ô)
        "\xF5" => "\u{0151}", // (ő)
        "\xF6" => "\u{00F6}", // (ö)
        "\xF7" => "\u{00F7}", // (÷)
        "\xF8" => "\u{0159}", // (ř)
        "\xF9" => "\u{016F}", // (ů)
        "\xFA" => "\u{00FA}", // (ú)
        "\xFB" => "\u{0171}", // (ű)
        "\xFC" => "\u{00FC}", // (ü)
        "\xFD" => "\u{00FD}", // (ý)
        "\xFE" => "\u{0163}", // (ţ)
        "\xFF" => "\u{02D9}", // (˙)
    ];
}
