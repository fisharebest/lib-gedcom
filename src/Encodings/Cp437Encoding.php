<?php
/**
 * @copyright 2018 Greg Roach <fisharebest@gmail.com>
 * @license   GPLv3+
 */
declare(strict_types=1);

namespace Fisharebest\LibGedcom\Encodings;

/**
 * Convert between Windows Code Page 437 and UTF-8.
 *
 * @link https://en.wikipedia.org/wiki/Code_page_437
 */
class Cp437Encoding extends AbstractEncodingLookup {
    const ENCODING_NAME = 'CP437';

    const TO_UTF8 = [
        "\x80" => "\u{00C7}", // (Ç)
        "\x81" => "\u{00FC}", // (ü)
        "\x82" => "\u{00E9}", // (é)
        "\x83" => "\u{00E2}", // (â)
        "\x84" => "\u{00E4}", // (ä)
        "\x85" => "\u{00E0}", // (à)
        "\x86" => "\u{00E5}", // (å)
        "\x87" => "\u{00E7}", // (ç)
        "\x88" => "\u{00EA}", // (ê)
        "\x89" => "\u{00EB}", // (ë)
        "\x8A" => "\u{00E8}", // (è)
        "\x8B" => "\u{00EF}", // (ï)
        "\x8C" => "\u{00EE}", // (î)
        "\x8D" => "\u{00EC}", // (ì)
        "\x8E" => "\u{00C4}", // (Ä)
        "\x8F" => "\u{00C5}", // (Å)
        "\x90" => "\u{00C9}", // (É)
        "\x91" => "\u{00E6}", // (æ)
        "\x92" => "\u{00C6}", // (Æ)
        "\x93" => "\u{00F4}", // (ô)
        "\x94" => "\u{00F6}", // (ö)
        "\x95" => "\u{00F2}", // (ò)
        "\x96" => "\u{00FB}", // (û)
        "\x97" => "\u{00F9}", // (ù)
        "\x98" => "\u{00FF}", // (ÿ)
        "\x99" => "\u{00D6}", // (Ö)
        "\x9A" => "\u{00DC}", // (Ü)
        "\x9B" => "\u{00A2}", // (¢)
        "\x9C" => "\u{00A3}", // (£)
        "\x9D" => "\u{00A5}", // (¥)
        "\x9E" => "\u{20A7}", // (₧)
        "\x9F" => "\u{0192}", // (ƒ)
        "\xA0" => "\u{00E1}", // (á)
        "\xA1" => "\u{00ED}", // (í)
        "\xA2" => "\u{00F3}", // (ó)
        "\xA3" => "\u{00FA}", // (ú)
        "\xA4" => "\u{00F1}", // (ñ)
        "\xA5" => "\u{00D1}", // (Ñ)
        "\xA6" => "\u{00AA}", // (ª)
        "\xA7" => "\u{00BA}", // (º)
        "\xA8" => "\u{00BF}", // (¿)
        "\xA9" => "\u{2310}", // (⌐)
        "\xAA" => "\u{00AC}", // (¬)
        "\xAB" => "\u{00BD}", // (½)
        "\xAC" => "\u{00BC}", // (¼)
        "\xAD" => "\u{00A1}", // (¡)
        "\xAE" => "\u{00AB}", // («)
        "\xAF" => "\u{00BB}", // (»)
        "\xB0" => "\u{2591}", // (░)
        "\xB1" => "\u{2592}", // (▒)
        "\xB2" => "\u{2593}", // (▓)
        "\xB3" => "\u{2502}", // (│)
        "\xB4" => "\u{2524}", // (┤)
        "\xB5" => "\u{2561}", // (╡)
        "\xB6" => "\u{2562}", // (╢)
        "\xB7" => "\u{2556}", // (╖)
        "\xB8" => "\u{2555}", // (╕)
        "\xB9" => "\u{2563}", // (╣)
        "\xBA" => "\u{2551}", // (║)
        "\xBB" => "\u{2557}", // (╗)
        "\xBC" => "\u{255D}", // (╝)
        "\xBD" => "\u{255C}", // (╜)
        "\xBE" => "\u{255B}", // (╛)
        "\xBF" => "\u{2510}", // (┐)
        "\xC0" => "\u{2514}", // (└)
        "\xC1" => "\u{2534}", // (┴)
        "\xC2" => "\u{252C}", // (┬)
        "\xC3" => "\u{251C}", // (├)
        "\xC4" => "\u{2500}", // (─)
        "\xC5" => "\u{253C}", // (┼)
        "\xC6" => "\u{255E}", // (╞)
        "\xC7" => "\u{255F}", // (╟)
        "\xC8" => "\u{255A}", // (╚)
        "\xC9" => "\u{2554}", // (╔)
        "\xCA" => "\u{2569}", // (╩)
        "\xCB" => "\u{2566}", // (╦)
        "\xCC" => "\u{2560}", // (╠)
        "\xCD" => "\u{2550}", // (═)
        "\xCE" => "\u{256C}", // (╬)
        "\xCF" => "\u{2567}", // (╧)
        "\xD0" => "\u{2568}", // (╨)
        "\xD1" => "\u{2564}", // (╤)
        "\xD2" => "\u{2565}", // (╥)
        "\xD3" => "\u{2559}", // (╙)
        "\xD4" => "\u{2558}", // (╘)
        "\xD5" => "\u{2552}", // (╒)
        "\xD6" => "\u{2553}", // (╓)
        "\xD7" => "\u{256B}", // (╫)
        "\xD8" => "\u{256A}", // (╪)
        "\xD9" => "\u{2518}", // (┘)
        "\xDA" => "\u{250C}", // (┌)
        "\xDB" => "\u{2588}", // (█)
        "\xDC" => "\u{2584}", // (▄)
        "\xDD" => "\u{258C}", // (▌)
        "\xDE" => "\u{2590}", // (▐)
        "\xDF" => "\u{2580}", // (▀)
        "\xE0" => "\u{03B1}", // (α)
        "\xE1" => "\u{00DF}", // (ß)
        "\xE2" => "\u{0393}", // (Γ)
        "\xE3" => "\u{03C0}", // (π)
        "\xE4" => "\u{03A3}", // (Σ)
        "\xE5" => "\u{03C3}", // (σ)
        "\xE6" => "\u{00B5}", // (µ)
        "\xE7" => "\u{03C4}", // (τ)
        "\xE8" => "\u{03A6}", // (Φ)
        "\xE9" => "\u{0398}", // (Θ)
        "\xEA" => "\u{03A9}", // (Ω)
        "\xEB" => "\u{03B4}", // (δ)
        "\xEC" => "\u{221E}", // (∞)
        "\xED" => "\u{03C6}", // (φ)
        "\xEE" => "\u{03B5}", // (ε)
        "\xEF" => "\u{2229}", // (∩)
        "\xF0" => "\u{2261}", // (≡)
        "\xF1" => "\u{00B1}", // (±)
        "\xF2" => "\u{2265}", // (≥)
        "\xF3" => "\u{2264}", // (≤)
        "\xF4" => "\u{2320}", // (⌠)
        "\xF5" => "\u{2321}", // (⌡)
        "\xF6" => "\u{00F7}", // (÷)
        "\xF7" => "\u{2248}", // (≈)
        "\xF8" => "\u{00B0}", // (°)
        "\xF9" => "\u{2219}", // (∙)
        "\xFA" => "\u{00B7}", // (·)
        "\xFB" => "\u{221A}", // (√)
        "\xFC" => "\u{207F}", // (ⁿ)
        "\xFD" => "\u{00B2}", // (²)
        "\xFE" => "\u{25A0}", // (■)
        "\xFF" => "\u{00A0}", // (non-breaking space)
    ];
}
