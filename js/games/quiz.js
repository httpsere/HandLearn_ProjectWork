/* HandLearn — Quiz dei segni */
(function () {

    const pool = (window.QUIZ_POOL || []).slice();

    const ROUNDS = Math.min(10, Math.max(4, pool.length));

    const introCard  = document.getElementById('introCard');
    const gameCard   = document.getElementById('gameCard');
    const endCard    = document.getElementById('endCard');

    const startBtn   = document.getElementById('quizStartBtn');
    const restartBtn = document.getElementById('restartBtn');

    const signTarget = document.getElementById('signTarget');
    const choicesEl  = document.getElementById('choices');

    const scoreVal   = document.getElementById('scoreVal');
    const roundInfo  = document.getElementById('roundInfo');

    const hint       = document.getElementById('hint');

    const endScore   = document.getElementById('endScore');
    const endCorrect = document.getElementById('endCorrect');
    const endTitle   = document.getElementById('endTitle');
    const endText    = document.getElementById('endText');

    let round = 0;
    let score = 0;
    let correct = 0;
    let currentSet = [];

    let dirty = false;

    function shuffle(a) {
        return a
            .map(v => [Math.random(), v])
            .sort((x,y)=>x[0]-y[0])
            .map(p => p[1]);
    }

    /*
    |--------------------------------------------------------------------------
    | IMMAGINE SEGNO
    |--------------------------------------------------------------------------
    */
    function makeSignImage(parola) {

        const imgName = parola
            .toLowerCase()
            .replace(/\s+/g, '_');

        return `
            <div class="quiz-sign-image">
                <img
                    src="../assets/segni_immagini/${imgName}.png"
                    alt="Segno"
                    style="
                        width:100%;
                        max-width:260px;
                        border-radius:18px;
                        object-fit:contain;
                        background:white;
                        padding:10px;
                    "
                    onerror="this.src='../assets/default.svg'"
                >
            </div>
        `;
    }

    function nextRound() {

        if (round >= ROUNDS) {
            return endGame();
        }

        const correctWord = currentSet[round];

        const distractors = shuffle(
            pool.filter(w => w !== correctWord)
        ).slice(0, 3);

        const options = shuffle([
            correctWord,
            ...distractors
        ]);

        roundInfo.textContent =
            `Round ${round + 1} / ${ROUNDS}`;

        scoreVal.textContent = score;

        /*
        |--------------------------------------------------------------------------
        | MOSTRA SOLO IMMAGINE
        |--------------------------------------------------------------------------
        */
        signTarget.innerHTML = makeSignImage(correctWord);

        hint.textContent =
            'seleziona la risposta corretta';

        choicesEl.innerHTML = '';

        options.forEach(opt => {

            const btn = document.createElement('button');

            btn.className = 'choice-btn';

            btn.textContent = opt;

            btn.addEventListener('click', () => {
                onAnswer(btn, opt, correctWord);
            });

            choicesEl.appendChild(btn);
        });
    }

    function onAnswer(btn, opt, correctWord) {

        Array.from(
            choicesEl.querySelectorAll('button')
        ).forEach(b => b.disabled = true);

        if (opt === correctWord) {

            btn.classList.add('correct');

            score += 10;

            correct++;

            hint.textContent = '✓ Esatto!';

        } else {

            btn.classList.add('wrong');

            Array.from(
                choicesEl.querySelectorAll('button')
            ).forEach(b => {

                if (b.textContent === correctWord) {
                    b.classList.add('correct');
                }

            });

            hint.textContent =
                `✗ Risposta sbagliata`;

        }

        scoreVal.textContent = score;

        round++;

        setTimeout(nextRound, 1000);
    }

    function startGame() {

        round = 0;
        score = 0;
        correct = 0;

        dirty = true;

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

            endTitle.textContent = 'Perfetto!';
            endText.textContent  =
                'Hai completato il quiz senza errori.';

        } else if (correct >= ROUNDS * 0.7) {

            endTitle.textContent = 'Ottimo lavoro!';
            endText.textContent  =
                'Hai ottenuto un ottimo punteggio.';

        } else {

            endTitle.textContent =
                'Continua ad allenarti';

            endText.textContent =
                'Riprova il quiz per migliorare.';
        }

        try {

            fetch('../api/save_progress.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    type: 'score',
                    gioco: 'quiz',
                    punteggio: score
                })
            }).catch(()=>{});

        } catch(e){}
    }

    startBtn.addEventListener('click', startGame);

    restartBtn.addEventListener('click', startGame);

    window.addEventListener('beforeunload', e => {

        if (dirty) {
            e.preventDefault();
            e.returnValue = '';
        }

    });

})();