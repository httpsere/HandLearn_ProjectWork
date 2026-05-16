/* HandLearn — Spelling */
(function () {
    const words = (window.SPELLING_WORDS || []).slice();
    const TOT = words.length;

    const introCard = document.getElementById('introCard');
    const gameCard  = document.getElementById('gameCard');
    const endCard   = document.getElementById('endCard');
    const startBtn  = document.getElementById('spStart');
    const restart   = document.getElementById('spRestart');
    const lettersEl = document.getElementById('spLetters');
    const input     = document.getElementById('spInput');
    const checkBtn  = document.getElementById('spCheck');
    const hint      = document.getElementById('spHint');
    const scoreEl   = document.getElementById('spScore');
    const roundEl   = document.getElementById('spRound');
    const endScore  = document.getElementById('endScore');
    const endRight  = document.getElementById('endRight');

    let round = 0, score = 0, right = 0, dirty = false;

    function letterCard(L) {
        const colors = ['violet','amber','emerald','sky','rose','pink'];
        const c = colors[L.charCodeAt(0) % colors.length];
        return `<div class="sign sign-${c}" style="width:84px; height:84px; border-radius:14px;">
          <svg viewBox="0 0 96 96" aria-hidden="true">
            <g fill="white" stroke="rgba(0,0,0,.08)" stroke-width="1">
              <rect x="22" y="20" width="10" height="46" rx="5"/>
              <rect x="36" y="14" width="10" height="52" rx="5"/>
              <rect x="50" y="10" width="10" height="56" rx="5"/>
              <rect x="64" y="18" width="10" height="48" rx="5"/>
              <rect x="6"  y="32" width="10" height="34" rx="5" transform="rotate(-30 11 49)"/>
              <path d="M8 60 Q8 88 44 88 H 70 Q 88 88 88 68 V 50 Q 88 42 76 42 V 60 Z"/>
            </g>
          </svg>
          <span class="label">${L}</span>
        </div>`;
    }

    function renderRound() {
        if (round >= TOT) return endGame();
        const w = words[round];
        roundEl.textContent = `Parola ${round + 1} / ${TOT}`;
        scoreEl.textContent = score;
        lettersEl.innerHTML = w.split('').map(letterCard).join('');
        input.value = '';
        hint.textContent = '';
        input.focus();
    }

    function check() {
        const w = words[round];
        const a = (input.value || '').trim().toUpperCase();
        if (!a) return;
        if (a === w) {
            score += 15; right++;
            hint.innerHTML = '<span style="color:#047857">✓ Corretto!</span>';
        } else {
            hint.innerHTML = `<span style="color:#991b1b">✗ Era "${w}"</span>`;
        }
        scoreEl.textContent = score;
        round++;
        setTimeout(renderRound, 800);
    }

    function startGame() {
        round = 0; score = 0; right = 0; dirty = true;
        introCard.style.display = 'none';
        endCard.style.display   = 'none';
        gameCard.style.display  = '';
        renderRound();
    }
    function endGame() {
        dirty = false;
        gameCard.style.display = 'none';
        endCard.style.display  = '';
        endScore.textContent   = score;
        endRight.textContent   = right;

        try {
            fetch('../api/save_progress.php', {
                method:'POST', headers:{'Content-Type':'application/json'},
                body: JSON.stringify({ type:'score', gioco:'spelling', punteggio: score })
            }).catch(()=>{});
        } catch(e){}
    }

    checkBtn.addEventListener('click', check);
    input.addEventListener('keydown', e => { if (e.key === 'Enter') check(); });
    startBtn.addEventListener('click', startGame);
    restart.addEventListener('click', startGame);

    window.addEventListener('beforeunload', e => {
        if (dirty) { e.preventDefault(); e.returnValue = ''; }
    });
})();
