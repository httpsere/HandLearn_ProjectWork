/* =============================================================
   HandLearn - main.js
   Interazioni globali del front-end (no dipendenze esterne)
============================================================= */

document.addEventListener('DOMContentLoaded', () => {
    initMobileMenu();
    initDictionarySearch();
    initAlphabetFilter();
    initCategoryFilter();
});

/* ---- Mobile menu toggle ---- */
function initMobileMenu() {
    const btn = document.getElementById('mobileMenuBtn');
    if (!btn) return;
    const links = document.querySelector('.nav-links');
    const auth  = document.querySelector('.nav-auth');
    btn.addEventListener('click', () => {
        links?.classList.toggle('is-open');
        auth?.classList.toggle('is-open');
    });
}

/* ---- Live search nel dizionario ---- */
function initDictionarySearch() {
    const input = document.querySelector('[data-dictionary-search]');
    if (!input) return;
    const cards = document.querySelectorAll('[data-word]');
    input.addEventListener('input', e => {
        const q = e.target.value.trim().toLowerCase();
        cards.forEach(c => {
            const word = (c.dataset.word || '').toLowerCase();
            c.style.display = word.includes(q) ? '' : 'none';
        });
    });
}

/* ---- Filtro alfabetico ---- */
function initAlphabetFilter() {
    const buttons = document.querySelectorAll('[data-letter]');
    if (!buttons.length) return;
    const cards = document.querySelectorAll('[data-word]');
    buttons.forEach(btn => {
        btn.addEventListener('click', e => {
            e.preventDefault();
            buttons.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            const letter = btn.dataset.letter;
            cards.forEach(c => {
                if (letter === 'all') { c.style.display = ''; return; }
                const w = (c.dataset.word || '').toLowerCase();
                c.style.display = w.startsWith(letter.toLowerCase()) ? '' : 'none';
            });
        });
    });
}

/* ---- Filtro categorie (impara) ---- */
function initCategoryFilter() {
    const buttons = document.querySelectorAll('[data-category]');
    if (!buttons.length) return;
    const cards = document.querySelectorAll('[data-lesson-category]');
    buttons.forEach(btn => {
        btn.addEventListener('click', e => {
            e.preventDefault();
            buttons.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            const cat = btn.dataset.category;
            cards.forEach(c => {
                c.style.display = (cat === 'all' || c.dataset.lessonCategory === cat) ? '' : 'none';
            });
        });
    });
}
