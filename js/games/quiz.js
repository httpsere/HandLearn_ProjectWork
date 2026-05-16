/* HandLearn — Quiz dei segni */
(function () {
    const pool = (window.QUIZ_POOL || []).slice();
    const ROUNDS = Math.min(10, Math.max(4, pool.length));

    const introCard = document.getElementById('introCard');
    const gameCard  = document.getElementById('gameCard');
    const endCard   = document.getElementById('endCard');
    const startBtn  = document.getElementById('quizStartBtn');
    const restartBtn= document.getElementById('restartBtn');
    const signTarget= document.getElementById('signTarget');
    const choicesEl = document.getElementById('choices');
    const scoreVal  = document.getElementById('scoreVal');
    const roundInfo = document.getElementById('roundInfo');
    const hint      = document.getElementById('hint');
    const endScore  = document.getElementById('endScore');
    const endCorrect= document.getElementById('endCorrect');
    const endTitle  = document.getElementById('endTitle');
    const endText   = document.getElementById('endText');

    let round = 0, score = 0, correct = 0, currentSet = [];
    let dirty = false; // serve per beforeunload

    function shuffle(a) { return a.map(v => [Math.random(), v]).sort((x,y)=>x[0]-y[0]).map(p => p[1]); }

    function makeSign(parola, color) {
        const colors = ['primary','amber','emerald','sky','violet','pink','rose'];
        const c = color || colors[Math.abs([...parola].reduce((a,c)=>a+c.charCodeAt(0),0)) % colors.length];
        return `
        <div class="sign sign-${c} sign-large" style="aspect-ratio:1; border-radius: 16px;">
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
          <span class="label">${parola}</span>
        </div>`;
    }

    function nextRound() {
        if (round >= ROUNDS) return endGame();

        const correctWord = currentSet[round];
        const distractors = shuffle(pool.filter(w => w !== correctWord)).slice(0, 3);
        const options = shuffle([correctWord, ...distractors]);

        roundInfo.textContent = `Round ${round + 1} / ${ROUNDS}`;
        scoreVal.textContent  = score;
        signTarget.innerHTML  = makeSign(correctWord);
        hint.textContent      = 'Quale parola corrisponde al segno mostrato?';

        choicesEl.innerHTML = '';
        options.forEach(opt => {
            const btn = document.createElement('button');
            btn.className = 'choice-btn';
            btn.textContent = opt;
            btn.addEventListener('click', () => onAnswer(btn, opt, correctWord));
            choicesEl.appendChild(btn);
        });
    }

    function onAnswer(btn, opt, correctWord) {
        Array.from(choicesEl.querySelectorAll('button')).forEach(b => b.disabled = true);
        if (opt === correctWord) {
            btn.classList.add('correct');
            score += 10;
            correct++;
            hint.textContent = '✓ Corretto!';
        } else {
            btn.classList.add('wrong');
            // evidenzia anche quella giusta
            Array.from(choicesEl.querySelectorAll('button')).forEach(b => {
                if (b.textContent === correctWord) b.classList.add('correct');
            });
            hint.textContent = `✗ Era "${correctWord}"`;
        }
        scoreVal.textContent = score;
        round++;
        setTimeout(nextRound, 950);
    }

    function startGame() {
        round = 0; score = 0; correct = 0; dirty = true;
        currentSet = shuffle(pool).slice(0, ROUNDS);
        introCard.style.display = 'none';
        endCard.style.display   = 'none';
        gameCard.style.display  = '';
        nextRound();
    }

    function endGame() {
        dirty = false;
        gameCard.style.display = 'none';
        endCard.style.display  = '';
        endScore.textContent   = score;
        endCorrect.textContent = correct + ' / ' + ROUNDS;

        if (correct === ROUNDS) {
            endTitle.textContent = 'Perfetto! Tutte corrette';
            endText.textContent  = 'Hai dominato il quiz. Prova un livello più difficile.';
        } else if (correct >= ROUNDS * 0.7) {
            endTitle.textContent = 'Ottimo lavoro!';
            endText.textContent  = 'Punteggio molto buono. Riprova per fare anche meglio.';
        } else if (correct >= ROUNDS / 2) {
            endTitle.textContent = 'Quasi ci sei';
            endText.textContent  = 'Ripassa un po\' nelle lezioni e riprova.';
        } else {
            endTitle.textContent = 'Continua ad allenarti';
            endText.textContent  = 'Vai alle lezioni e poi torna a sfidare il quiz.';
        }

        // (Opzionale) salva punteggio se loggato
        try {
            fetch('../api/save_progress.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ type: 'score', gioco: 'quiz', punteggio: score })
            }).catch(()=>{});
        } catch(e){}
    }

    startBtn.addEventListener('click', startGame);
    restartBtn.addEventListener('click', startGame);

    window.addEventListener('beforeunload', e => {
        if (dirty) { e.preventDefault(); e.returnValue = ''; }
    });
})();
