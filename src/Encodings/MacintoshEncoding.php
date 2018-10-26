<?php
/**
 * @copyright 2018 Greg Roach <fisharebest@gmail.com>
 * @license   GPLv3+
 */
declare(strict_types=1);

namespace Fisharebest\LibGedcom\Encodings;

/**
 * Convert between UTF-8 and Mac OS Roman encoding.
 */
class MacintoshEncoding extends AbstractEncodingLookup {
    const ENCODING_NAME = 'MacOS Roman';

    const TO_UTF8 = [
        "\x80" => "\u{00C4}", // (Ä)
        "\x81" => "\u{00C5}", // (Å)
        "\x82" => "\u{00C7}", // (Ç)
        "\x83" => "\u{00C9}", // (É)
        "\x84" => "\u{00D1}", // (Ñ)
        "\x85" => "\u{00D6}", // (Ö)
        "\x86" => "\u{00DC}", // (Ü)
        "\x87" => "\u{00E1}", // (á)
        "\x88" => "\u{00E0}", // (à)
        "\x89" => "\u{00E2}", // (â)
        "\x8A" => "\u{00E4}", // (ä)
        "\x8B" => "\u{00E3}", // (ã)
        "\x8C" => "\u{00E5}", // (å)
        "\x8D" => "\u{00E7}", // (ç)
        "\x8E" => "\u{00E9}", // (é)
        "\x8F" => "\u{00E8}", // (è)
        "\x90" => "\u{00EA}", // (ê)
        "\x91" => "\u{00EB}", // (ë)
        "\x92" => "\u{00ED}", // (í)
        "\x93" => "\u{00EC}", // (ì)
        "\x94" => "\u{00EE}", // (î)
        "\x95" => "\u{00EF}", // (ï)
        "\x96" => "\u{00F1}", // (ñ)
        "\x97" => "\u{00F3}", // (ó)
        "\x98" => "\u{00F2}", // (ò)
        "\x99" => "\u{00F4}", // (ô)
        "\x9A" => "\u{00F6}", // (ö)
        "\x9B" => "\u{00F5}", // (õ)
        "\x9C" => "\u{00FA}", // (ú)
        "\x9D" => "\u{00F9}", // (ù)
        "\x9E" => "\u{00FB}", // (û)
        "\x9F" => "\u{00FC}", // (ü)
        "\xA0" => "\u{2020}", // (†)
        "\xA1" => "\u{00B0}", // (°)
        "\xA2" => "\u{00A2}", // (¢)
        "\xA3" => "\u{00A3}", // (£)
        "\xA4" => "\u{00A7}", // (§)
        "\xA5" => "\u{2022}", // (•)
        "\xA6" => "\u{00B6}", // (¶)
        "\xA7" => "\u{00DF}", // (ß)
        "\xA8" => "\u{00AE}", // (®)
        "\xA9" => "\u{00A9}", // (©)
        "\xAA" => "\u{2122}", // (™)
        "\xAB" => "\u{00B4}", // (´)
        "\xAC" => "\u{00A8}", // (¨)
        "\xAD" => "\u{2260}", // (≠)
        "\xAE" => "\u{00C6}", // (Æ)
        "\xAF" => "\u{00D8}", // (Ø)
        "\xB0" => "\u{221E}", // (∞)
        "\xB1" => "\u{00B1}", // (±)
        "\xB2" => "\u{2264}", // (≤)
        "\xB3" => "\u{2265}", // (≥)
        "\xB4" => "\u{00A5}", // (¥)
        "\xB5" => "\u{00B5}", // (µ)
        "\xB6" => "\u{2202}", // (∂)
        "\xB7" => "\u{2211}", // (∑)
        "\xB8" => "\u{220F}", // (∏)
        "\xB9" => "\u{03C0}", // (π)
        "\xBA" => "\u{222B}", // (∫)
        "\xBB" => "\u{00AA}", // (ª)
        "\xBC" => "\u{00BA}", // (º)
        "\xBD" => "\u{03A9}", // (Ω)
        "\xBE" => "\u{00E6}", // (æ)
        "\xBF" => "\u{00F8}", // (ø)
        "\xC0" => "\u{00BF}", // (¿)
        "\xC1" => "\u{00A1}", // (¡)
        "\xC2" => "\u{00AC}", // (¬)
        "\xC3" => "\u{221A}", // (√)
        "\xC4" => "\u{0192}", // (ƒ)
        "\xC5" => "\u{2248}", // (≈)
        "\xC6" => "\u{2206}", // (∆)
        "\xC7" => "\u{00AB}", // («)
        "\xC8" => "\u{00BB}", // (»)
        "\xC9" => "\u{2026}", // (…)
        "\xCA" => "\u{00A0}", // (non-breaking space)
        "\xCB" => "\u{00C0}", // (À)
        "\xCC" => "\u{00C3}", // (Ã)
        "\xCD" => "\u{00D5}", // (Õ)
        "\xCE" => "\u{0152}", // (Œ)
        "\xCF" => "\u{0153}", // (œ)
        "\xD0" => "\u{2013}", // (–)
        "\xD1" => "\u{2014}", // (—)
        "\xD2" => "\u{201C}", // (“)
        "\xD3" => "\u{201D}", // (”)
        "\xD4" => "\u{2018}", // (‘)
        "\xD5" => "\u{2019}", // (’)
        "\xD6" => "\u{00F7}", // (÷)
        "\xD7" => "\u{25CA}", // (◊)
        "\xD8" => "\u{00FF}", // (ÿ)
        "\xD9" => "\u{0178}", // (Ÿ)
        "\xDA" => "\u{2044}", // (⁄)
        "\xDB" => "\u{20AC}", // (€)
        "\xDC" => "\u{2039}", // (‹)
        "\xDD" => "\u{203A}", // (›)
        "\xDE" => "\u{FB01}", // (ﬁ)
        "\xDF" => "\u{FB02}", // (ﬂ)
        "\xE0" => "\u{2021}", // (‡)
        "\xE1" => "\u{00B7}", // (·)
        "\xE2" => "\u{201A}", // (‚)
        "\xE3" => "\u{201E}", // („)
        "\xE4" => "\u{2030}", // (‰)
        "\xE5" => "\u{00C2}", // (Â)
        "\xE6" => "\u{00CA}", // (Ê)
        "\xE7" => "\u{00C1}", // (Á)
        "\xE8" => "\u{00CB}", // (Ë)
        "\xE9" => "\u{00C8}", // (È)
        "\xEA" => "\u{00CD}", // (Í)
        "\xEB" => "\u{00CE}", // (Î)
        "\xEC" => "\u{00CF}", // (Ï)
        "\xED" => "\u{00CC}", // (Ì)
        "\xEE" => "\u{00D3}", // (Ó)
        "\xEF" => "\u{00D4}", // (Ô)
        "\xF1" => "\u{00D2}", // (Ò)
        "\xF2" => "\u{00DA}", // (Ú)
        "\xF3" => "\u{00DB}", // (Û)
        "\xF4" => "\u{00D9}", // (Ù)
        "\xF5" => "\u{0131}", // (ı)
        "\xF6" => "\u{02C6}", // (ˆ)
        "\xF7" => "\u{02DC}", // (˜)
        "\xF8" => "\u{00AF}", // (¯)
        "\xF9" => "\u{02D8}", // (˘)
        "\xFA" => "\u{02D9}", // (˙)
        "\xFB" => "\u{02DA}", // (˚)
        "\xFC" => "\u{00B8}", // (¸)
        "\xFD" => "\u{02DD}", // (˝)
        "\xFE" => "\u{02DB}", // (˛)
        "\xFF" => "\u{02C7}", // (ˇ)
    ];
}
