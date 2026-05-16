/* HandLearn — Memory LIS */
(function () {

    const words = (window.MEMORY_WORDS || []).slice(0, 8);

    const introCard  = document.getElementById('introCard');
    const gameCard   = document.getElementById('gameCard');
    const endCard    = document.getElementById('endCard');
    const board      = document.getElementById('memoryBoard');

    const startBtn   = document.getElementById('memStartBtn');
    const restartBtn = document.getElementById('memRestart');

    const movesVal   = document.getElementById('movesVal');
    const pairsVal   = document.getElementById('pairsVal');

    const endMoves   = document.getElementById('endMoves');
    const endScore   = document.getElementById('endScore');

    let moves = 0;
    let matched = 0;

    let flipped = [];
    let lock = false;

    /*
    |--------------------------------------------------------------------------
    | Shuffle
    |--------------------------------------------------------------------------
    */

    function shuffle(arr) {

        return arr
            .map(v => [Math.random(), v])
            .sort((a, b) => a[0] - b[0])
            .map(v => v[1]);
    }

    /*
    |--------------------------------------------------------------------------
    | PATH IMMAGINI
    |--------------------------------------------------------------------------
    | Le immagini DEVONO chiamarsi:
    |
    | a.png
    | b.png
    | c.png
    |
    */

    function getImagePath(letter) {

        return `/HandLearn-main/assets/segni_immagini/${letter.toLowerCase()}.png`;
    }

    /*
    |--------------------------------------------------------------------------
    | Build Board
    |--------------------------------------------------------------------------
    */

    function buildBoard() {

        board.innerHTML = '';

        moves = 0;
        matched = 0;
        flipped = [];
        lock = false;

        movesVal.textContent = '0';
        pairsVal.textContent = `0/${words.length}`;

        const cards = shuffle([
            ...words.map(w => ({ key: w, type: 'sign' })),
            ...words.map(w => ({ key: w, type: 'word' }))
        ]);

        cards.forEach(card => {

            const el = document.createElement('div');

            el.className = 'mem-card';

            const imagePath = getImagePath(card.key);

            el.innerHTML = `

                <div class="face-back">?</div>

                ${
                    card.type === 'sign'

                    ? `

                        <img
                            class="sign-img"
                            src="${imagePath}"
                            alt="${card.key}"
                        >

                    `

                    : `

                        <div class="word-label">
                            ${card.key}
                        </div>

                    `
                }
            `;

            el.addEventListener('click', () => {
                onCardClick(el, card);
            });

            board.appendChild(el);
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Click carta
    |--------------------------------------------------------------------------
    */

    function onCardClick(el, card) {

        if (lock) return;

        if (
            el.classList.contains('flipped') ||
            el.classList.contains('matched')
        ) {
            return;
        }

        el.classList.add('flipped');

        const back = el.querySelector('.face-back');

        if (back) {
            back.style.display = 'none';
        }

        const img = el.querySelector('.sign-img');
        if (img) {
            img.style.display = 'block';
        }

        const label = el.querySelector('.word-label');
        if (label) {
            label.style.display = 'flex';
        }

        flipped.push({ el, card });

        /*
        |--------------------------------------------------------------------------
        | 2 carte
        |--------------------------------------------------------------------------
        */

        if (flipped.length === 2) {

            moves++;
            movesVal.textContent = moves;

            const [a, b] = flipped;

            const isMatch =
                a.card.key === b.card.key &&
                a.card.type !== b.card.type;

            if (isMatch) {

                a.el.classList.add('matched');
                b.el.classList.add('matched');

                matched++;

                pairsVal.textContent = `${matched}/${words.length}`;

                flipped = [];

                if (matched === words.length) {
                    setTimeout(endGame, 500);
                }

            } else {

                lock = true;

                setTimeout(() => {

                    [a, b].forEach(item => {

                        item.el.classList.remove('flipped');

                        const back = item.el.querySelector('.face-back');

                        if (back) {
                            back.style.display = 'flex';
                        }

                        const img = item.el.querySelector('.sign-img');

                        if (img) {
                            img.style.display = 'none';
                        }

                        const label = item.el.querySelector('.word-label');

                        if (label) {
                            label.style.display = 'none';
                        }

                    });

                    flipped = [];
                    lock = false;

                }, 800);
            }
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Start
    |--------------------------------------------------------------------------
    */

    function startGame() {

        introCard.style.display = 'none';
        endCard.style.display = 'none';
        gameCard.style.display = '';

        buildBoard();
    }

    /*
    |--------------------------------------------------------------------------
    | End
    |--------------------------------------------------------------------------
    */

    function endGame() {

        gameCard.style.display = 'none';
        endCard.style.display = '';

        endMoves.textContent = moves;

        const minMoves = words.length;

        const score = Math.max(
            0,
            100 - ((moves - minMoves) * 5)
        );

        endScore.textContent = score;
    }

    startBtn.addEventListener('click', startGame);
    restartBtn.addEventListener('click', startGame);

})();