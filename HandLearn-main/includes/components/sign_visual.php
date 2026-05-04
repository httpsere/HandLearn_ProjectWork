<?php
/**
 * HandLearn - Sign visual component
 *
 * Genera una rappresentazione grafica coerente per un segno LIS
 * (mano stilizzata + lettera/parola). Sostituisce le emoji come
 * placeholder didattico finché non saranno disponibili
 * video/illustrazioni reali.
 *
 *   render_sign_visual('A', [
 *      'size'  => 'lg',     // sm | md | lg
 *      'color' => 'violet', // primary|amber|emerald|sky|violet|pink|rose
 *      'label' => true,
 *   ]);
 */

if (!function_exists('hl_pick_sign_color')) {
    function hl_pick_sign_color(string $key): string {
        $palette = ['primary','amber','emerald','sky','violet','pink','rose'];
        $hash    = abs(crc32(strtolower($key)));
        return $palette[$hash % count($palette)];
    }
}

if (!function_exists('hl_hand_svg')) {
    /**
     * Restituisce un SVG di mano stilizzata.
     * $variant cambia leggermente la posizione delle dita per evitare
     * che tutti i segni sembrino identici.
     */
    function hl_hand_svg(int $variant = 0): string {
        $variants = [
            // 0 - mano aperta
            '<g fill="currentColor" stroke="rgba(0,0,0,.08)" stroke-width="1">
              <rect x="22" y="20" width="10" height="46" rx="5"/>
              <rect x="36" y="14" width="10" height="52" rx="5"/>
              <rect x="50" y="10" width="10" height="56" rx="5"/>
              <rect x="64" y="18" width="10" height="48" rx="5"/>
              <rect x="6"  y="32" width="10" height="34" rx="5" transform="rotate(-30 11 49)"/>
              <path d="M8 60 Q8 88 44 88 H 70 Q 88 88 88 68 V 50 Q 88 42 76 42 V 60 Z"/>
              </g>',
            // 1 - pugno chiuso (numero / lettera S)
            '<g fill="currentColor" stroke="rgba(0,0,0,.08)" stroke-width="1">
              <rect x="22" y="38" width="10" height="22" rx="5"/>
              <rect x="36" y="34" width="10" height="26" rx="5"/>
              <rect x="50" y="34" width="10" height="26" rx="5"/>
              <rect x="64" y="38" width="10" height="22" rx="5"/>
              <rect x="14" y="46" width="14" height="14" rx="6"/>
              <path d="M8 58 Q8 88 44 88 H 70 Q 88 88 88 68 V 50 Q 88 42 76 42 V 58 Z"/>
              </g>',
            // 2 - V (indice + medio)
            '<g fill="currentColor" stroke="rgba(0,0,0,.08)" stroke-width="1">
              <rect x="32" y="6"  width="10" height="56" rx="5" transform="rotate(-12 37 34)"/>
              <rect x="50" y="6"  width="10" height="56" rx="5" transform="rotate(12 55 34)"/>
              <rect x="22" y="42" width="10" height="20" rx="5"/>
              <rect x="64" y="42" width="10" height="20" rx="5"/>
              <path d="M8 60 Q8 88 44 88 H 70 Q 88 88 88 68 V 50 Q 88 42 76 42 V 60 Z"/>
              </g>',
            // 3 - puntare con indice
            '<g fill="currentColor" stroke="rgba(0,0,0,.08)" stroke-width="1">
              <rect x="34" y="2"  width="12" height="60" rx="6"/>
              <rect x="50" y="38" width="10" height="22" rx="5"/>
              <rect x="64" y="38" width="10" height="22" rx="5"/>
              <rect x="20" y="46" width="14" height="14" rx="6"/>
              <path d="M8 58 Q8 88 44 88 H 70 Q 88 88 88 68 V 50 Q 88 42 76 42 V 58 Z"/>
              </g>',
            // 4 - tre dita
            '<g fill="currentColor" stroke="rgba(0,0,0,.08)" stroke-width="1">
              <rect x="30" y="6"  width="10" height="56" rx="5"/>
              <rect x="44" y="2"  width="10" height="60" rx="5"/>
              <rect x="58" y="6"  width="10" height="56" rx="5"/>
              <rect x="14" y="46" width="14" height="14" rx="6"/>
              <rect x="72" y="40" width="10" height="22" rx="5"/>
              <path d="M8 60 Q8 88 44 88 H 70 Q 88 88 88 68 V 50 Q 88 42 76 42 V 60 Z"/>
              </g>',
        ];
        return $variants[$variant % count($variants)];
    }
}

if (!function_exists('render_sign_visual')) {
    function render_sign_visual(string $name, array $opts = []): string {
        $size   = $opts['size']  ?? 'md';
        $color  = $opts['color'] ?? hl_pick_sign_color($name);
        $label  = $opts['label'] ?? true;
        $variant = abs(crc32($name)) % 5;

        $extra = '';
        if ($size === 'lg') $extra = ' sign-large';

        $svg  = '<svg viewBox="0 0 96 96" aria-hidden="true">';
        $svg .= hl_hand_svg($variant);
        $svg .= '</svg>';

        $html  = '<div class="sign sign-' . htmlspecialchars($color) . $extra . '">';
        $html .= $svg;
        if ($label) {
            $html .= '<span class="label">' . htmlspecialchars($name) . '</span>';
        }
        $html .= '</div>';
        return $html;
    }
}

if (!function_exists('hl_icon')) {
    /**
     * Mini libreria di icone SVG inline (Lucide-style, semplificate)
     * — usate al posto delle emoji nelle card/feature/navbar.
     */
    function hl_icon(string $name, int $size = 20, string $extra = ''): string {
        $stroke = 'stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" fill="none"';
        $paths = [
            'book'       => '<path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>',
            'gamepad'    => '<line x1="6" y1="11" x2="10" y2="11"/><line x1="8" y1="9" x2="8" y2="13"/><line x1="15" y1="12" x2="15.01" y2="12"/><line x1="18" y1="10" x2="18.01" y2="10"/><rect x="2" y="6" width="20" height="12" rx="2"/>',
            'target'     => '<circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="6"/><circle cx="12" cy="12" r="2"/>',
            'dictionary' => '<path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/>',
            'home'       => '<path d="M3 12l9-9 9 9"/><path d="M5 10v10a1 1 0 0 0 1 1h3v-6h6v6h3a1 1 0 0 0 1-1V10"/>',
            'user'       => '<path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>',
            'logout'     => '<path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/>',
            'login'      => '<path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/>',
            'check'      => '<polyline points="20 6 9 17 4 12"/>',
            'check-circle'=>'<path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>',
            'x'          => '<line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>',
            'arrow-right'=> '<line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/>',
            'arrow-left' => '<line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 19"/>',
            'play'       => '<polygon points="5 3 19 12 5 21 5 3" fill="currentColor" stroke="none"/>',
            'pause'      => '<rect x="6" y="4" width="4" height="16"/><rect x="14" y="4" width="4" height="16"/>',
            'rotate'     => '<polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/>',
            'search'     => '<circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>',
            'mail'       => '<path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/>',
            'lock'       => '<rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>',
            'eye'        => '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>',
            'heart'      => '<path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z" fill="currentColor" stroke="none"/>',
            'star'       => '<polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" fill="currentColor" stroke="none"/>',
            'trophy'     => '<path d="M8 21h8M12 17v4M17 4h4v3a4 4 0 0 1-4 4M7 4H3v3a4 4 0 0 0 4 4M7 4h10v8a5 5 0 0 1-10 0V4z"/>',
            'flame'      => '<path d="M8.5 14.5A2.5 2.5 0 0 0 11 12c0-1.38-.5-2-1-3-1.072-2.143-.224-4.054 2-6 .5 2.5 2 4.9 4 6.5 2 1.6 3 3.5 3 5.5a7 7 0 1 1-14 0c0-1.153.433-2.294 1-3a2.5 2.5 0 0 0 2.5 2.5z"/>',
            'sparkles'   => '<path d="M12 3v3M12 18v3M3 12h3M18 12h3M5.6 5.6l2.1 2.1M16.3 16.3l2.1 2.1M5.6 18.4l2.1-2.1M16.3 7.7l2.1-2.1"/>',
            'menu'       => '<line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="18" x2="21" y2="18"/>',
            'camera'     => '<path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/>',
            'mic'        => '<path d="M12 1a3 3 0 0 0-3 3v8a3 3 0 0 0 6 0V4a3 3 0 0 0-3-3z"/><path d="M19 10v2a7 7 0 0 1-14 0v-2"/><line x1="12" y1="19" x2="12" y2="23"/>',
            'globe'      => '<circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>',
            'graduation' => '<path d="M22 10v6"/><path d="M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/>',
            'shield'     => '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>',
            'zap'        => '<polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2" fill="currentColor" stroke="none"/>',
            'puzzle'     => '<path d="M19.439 7.85c-.049.322.059.648.289.878l1.568 1.568c.47.47.706 1.087.706 1.704s-.235 1.233-.706 1.704l-1.611 1.611a.98.98 0 0 1-.837.276c-.47-.07-.802-.48-.968-.925-.15-.405-.527-.701-.97-.741h-.026c-.514 0-.969.395-1.054.917l-.013.085-.001 1.677c0 1.097-.707 2.165-1.736 2.527-.319.112-.65.166-.984.166s-.665-.054-.984-.166c-1.029-.362-1.736-1.43-1.736-2.527v-1.677l-.013-.085c-.085-.522-.54-.917-1.054-.917h-.026c-.443.04-.82.336-.97.741-.166.445-.498.855-.968.925a.98.98 0 0 1-.837-.276l-1.611-1.611c-.47-.47-.706-1.087-.706-1.704s.235-1.233.706-1.704l1.568-1.568c.23-.23.338-.557.289-.878-.049-.322-.305-.581-.626-.622-.625-.07-1.232-.327-1.685-.78-.453-.453-.71-1.06-.78-1.685-.041-.321-.3-.577-.622-.626a4.84 4.84 0 0 1 0-9.554c.322-.049.581.305.622.626.07.625.327 1.232.78 1.685.453.453 1.06.71 1.685.78.321.041.577.3.626.622Z"/>',
        ];
        $svg = $paths[$name] ?? $paths['sparkles'];
        $cls = $extra ? ' class="' . htmlspecialchars($extra) . '"' : '';
        return "<svg width=\"$size\" height=\"$size\" viewBox=\"0 0 24 24\" $stroke$cls>$svg</svg>";
    }
}
