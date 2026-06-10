<?php
/**
 * HandLearn - Sign visual component
 *
 * Usa immagini reali dei segni LIS:
 * assets/segni_immagini/A.png
 * assets/segni_immagini/B.png
 * assets/segni_immagini/C.png
 * ecc.
 */

if (!function_exists('hl_pick_sign_color')) {

    function hl_pick_sign_color(string $key): string {

        $palette = [
            'primary',
            'amber',
            'emerald',
            'sky',
            'violet',
            'pink',
            'rose'
        ];

        $hash = abs(crc32(strtolower($key)));

        return $palette[$hash % count($palette)];
    }
}

if (!function_exists('render_sign_visual')) {

    function render_sign_visual(string $name, array $opts = []): string {

        $size  = $opts['size']  ?? 'md';
        $color = $opts['color'] ?? hl_pick_sign_color($name);
        $label = $opts['label'] ?? true;

        // Classe dimensione
        $extra = '';

        if ($size === 'lg') {
            $extra = ' sign-large';
        }

        if ($size === 'sm') {
            $extra = ' sign-small';
        }

        // Nome file
        // Esempio:
        // A => A.png
        // CIAO => CIAO.png

        $filename = trim($name) . '.png';

        // Percorso pubblico immagine
        $imagePath = 'assets/segni_immagini/' . $filename;

        // Percorso fisico server
        $fullPath = __DIR__ . '/../../' . $imagePath;

        // Se immagine non esiste usa default
        if (!file_exists($fullPath)) {
            $imagePath = 'assets/default.svg';
        }

        $html  = '<div class="sign sign-' . htmlspecialchars($color) . $extra . '">';

        if (isset($opts['icona']) && $opts['icona']) {
            $html .= '<h1 class="sign-image-wrapper">
                ' . htmlspecialchars($opts['icona']) . '
            </h1>';
        }
        else {
            $html .= '
                <img
                    src="' . htmlspecialchars($imagePath) . '"
                    alt="' . htmlspecialchars($name) . '"
                    class="sign-real-image"
                    loading="lazy"
                >
            ';
        }

        // Label testo
        if ($label) {
            $html .= '
                <span class="label">'
                    . htmlspecialchars($name) .
                '</span>
            ';
        }

        $html .= '</div>';

        return $html;
    }
}

if (!function_exists('hl_icon')) {

    /**
     * Mini libreria SVG icone
     */

    function hl_icon(string $name, int $size = 20, string $extra = ''): string {

        $stroke = '
            stroke="currentColor"
            stroke-width="2"
            stroke-linecap="round"
            stroke-linejoin="round"
            fill="none"
        ';

        $paths = [

            'book' =>
                '<path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
                 <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>',

            'gamepad' =>
                '<line x1="6" y1="11" x2="10" y2="11"/>
                 <line x1="8" y1="9" x2="8" y2="13"/>
                 <line x1="15" y1="12" x2="15.01" y2="12"/>
                 <line x1="18" y1="10" x2="18.01" y2="10"/>
                 <rect x="2" y="6" width="20" height="12" rx="2"/>',

            'target' =>
                '<circle cx="12" cy="12" r="10"/>
                 <circle cx="12" cy="12" r="6"/>
                 <circle cx="12" cy="12" r="2"/>',

            'home' =>
                '<path d="M3 12l9-9 9 9"/>
                 <path d="M5 10v10a1 1 0 0 0 1 1h3v-6h6v6h3a1 1 0 0 0 1-1V10"/>',

            'user' =>
                '<path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                 <circle cx="12" cy="7" r="4"/>',

            'check' =>
                '<polyline points="20 6 9 17 4 12"/>',

            'x' =>
                '<line x1="18" y1="6" x2="6" y2="18"/>
                 <line x1="6" y1="6" x2="18" y2="18"/>',

            'search' =>
                '<circle cx="11" cy="11" r="8"/>
                 <line x1="21" y1="21" x2="16.65" y2="16.65"/>',

            'heart' =>
                '<path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"
                 fill="currentColor"
                 stroke="none"/>',

            'star' =>
                '<polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"
                 fill="currentColor"
                 stroke="none"/>',

            'menu' =>
                '<line x1="3" y1="12" x2="21" y2="12"/>
                 <line x1="3" y1="6" x2="21" y2="6"/>
                 <line x1="3" y1="18" x2="21" y2="18"/>',

            'camera' =>
                '<path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/>
                 <circle cx="12" cy="13" r="4"/>',

            'mic' =>
                '<path d="M12 1a3 3 0 0 0-3 3v8a3 3 0 0 0 6 0V4a3 3 0 0 0-3-3z"/>
                 <path d="M19 10v2a7 7 0 0 1-14 0v-2"/>
                 <line x1="12" y1="19" x2="12" y2="23"/>',
        ];

        $svg = $paths[$name] ?? $paths['star'];

        $cls = $extra
            ? ' class="' . htmlspecialchars($extra) . '"'
            : '';

        return '
            <svg
                width="' . $size . '"
                height="' . $size . '"
                viewBox="0 0 24 24"
                ' . $stroke . '
                ' . $cls . '
            >
                ' . $svg . '
            </svg>
        ';
    }
}