/* HandLearn — Memory LIS */
(function () {
    const words = (window.MEMORY_WORDS || []).slice(0, 8);

    const introCard = document.getElementById('introCard');
    const gameCard  = document.getElementById('gameCard');
    const endCard   = document.getElementById('endCard');
    const board     = document.getElementById('memoryBoard');
    const startBtn  = document.getElementById('memStartBtn');
    const restartBtn= document.getElementById('memRestart');
    const movesVal  = document.getElementById('movesVal');
    const pairsVal  = document.getElementById('pairsVal');
    const endMoves  = document.getElementById('endMoves');
    const endScore  = document.getElementById('endScore');

    let moves = 0, matched = 0, dirty = false;
    let flipped = [];
    let lock = false;

    function shuffle(a) { return a.map(v => [Math.random(), v]).sort((x,y)=>x[0]-y[0]).map(p => p[1]); }

    function handSvg() {
        return `<svg class="sign-svg" viewBox="0 0 96 96" aria-hidden="true">
          <g fill="currentColor" stroke="rgba(0,0,0,.08)" stroke-width="1">
            <rect x="22" y="20" width="10" height="46" rx="5"/>
            <rect x="36" y="14" width="10" height="52" rx="5"/>
            <rect x="50" y="10" width="10" height="56" rx="5"/>
            <rect x="64" y="18" width="10" height="48" rx="5"/>
            <rect x="6"  y="32" width="10" height="34" rx="5" transform="rotate(-30 11 49)"/>
            <path d="M8 60 Q8 88 44 88 H 70 Q 88 88 88 68 V 50 Q 88 42 76 42 V 60 Z"/>
          </g>
        </svg>`;
    }

    function buildBoard() {
        board.innerHTML = '';
        moves = 0; matched = 0; flipped = []; lock = false;
        movesVal.textContent = '0';
        pairsVal.textContent = `0/${words.length}`;

        const cards = shuffle([
            ...words.map(w => ({ key: w, type: 'sign' })),
            ...words.map(w => ({ key: w, type: 'word' })),
        ]);

        cards.forEach(c => {
            const el = document.createElement('div');
            el.className = 'mem-card';
            el.dataset.key = c.key;
            el.dataset.type = c.type;
            el.innerHTML = `
                <span class="face-back">?</span>
                ${c.type === 'sign' ? handSvg() : `<span style="display:none; font-weight:700;">${c.key}</span>`}
            `;
            el.addEventListener('click', () => onCardClick(el, c));
            board.appendChild(el);
        });
    }

    function onCardClick(el, c) {
        if (lock) return;
        if (el.classList.contains('flipped') || el.classList.contains('matched')) return;

        el.classList.add('flipped');
        // mostra contenuto
        const back = el.querySelector('.face-back'); if (back) back.style.display = 'none';
        if (c.type === 'sign') {
            const svg = el.querySelector('svg'); if (svg) svg.style.display = '';
        } else {
            const sp = el.querySelector('span:not(.face-back)'); if (sp) sp.style.display = '';
        }
        flipped.push({ el, c });

        if (flipped.length === 2) {
            moves++; movesVal.textContent = moves;
            const [a, b] = flipped;
            if (a.c.key === b.c.key && a.c.type !== b.c.type) {
                a.el.classList.add('matched');
                b.el.classList.add('matched');
                matched++;
                pairsVal.textContent = `${matched}/${words.length}`;
                flipped = [];
                if (matched === words.length) setTimeout(endGame, 500);
            } else {
                lock = true;
                setTimeout(() => {
                    [a, b].forEach(x => {
                        x.el.classList.remove('flipped');
                        const back = x.el.querySelector('.face-back'); if (back) back.style.display = '';
                        const svg = x.el.querySelector('svg');         if (svg)  svg.style.display = 'none';
                        const sp  = x.el.querySelector('span:not(.face-back)'); if (sp) sp.style.display = 'none';
                    });
                    flipped = []; lock = false;
                }, 850);
            }
        }
    }

    function startGame() {
        introCard.style.display = 'none';
        endCard.style.display   = 'none';
        gameCard.style.display  = '';
        dirty = true;
        buildBoard();
    }

    function endGame() {
        dirty = false;
        gameCard.style.display = 'none';
        endCard.style.display  = '';
        endMoves.textContent   = moves;
        // punteggio: 100 base - penalità per mosse extra
        const minMoves = words.length;
        const score = Math.max(0, 100 - (moves - minMoves) * 5);
        endScore.textContent = score;

        try {
            fetch('../api/save_progress.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ type: 'score', gioco: 'memory', punteggio: score })
            }).catch(()=>{});
        } catch(e){}
    }

    startBtn.addEventListener('click', startGame);
    restartBtn.addEventListener('click', startGame);

    window.addEventListener('beforeunload', e => {
        if (dirty) { e.preventDefault(); e.returnValue = ''; }
    });
})();
