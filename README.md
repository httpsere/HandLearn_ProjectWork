# HandLearn — v2

Web app didattica per imparare la **Lingua dei Segni Italiana (LIS)** con
contenuti, giochi, dizionario e **riconoscimento gesti AI** in tempo reale via
webcam.

> **v2 — design system professionale, dashboard educativa, esercitazioni
> guidate, hub giochi unificato, schede segno e schede lezione.**

---

## Stack

| Strato      | Tecnologia                                          |
| ----------- | --------------------------------------------------- |
| Frontend    | HTML, CSS (design system v2), JavaScript vanilla    |
| Backend web | PHP 8 (sessioni, prepared statements)               |
| Database    | MySQL / MariaDB                                     |
| Backend AI  | Node.js + Express + TensorFlow.js + MediaPipe Hands |

---

## Struttura del progetto

```
HandLearn-main/
├── config/db.php                      conn MySQL + URL backend Node
├── includes/
│   ├── auth.php                       sessioni, login/logout helpers
│   ├── header.php  navbar.php  footer.php
│   └── components/sign_visual.php     SVG dei segni + libreria icone
├── css/style.css                      design system v2 (light)
├── js/
│   ├── main.js                        UI globali (mobile menu, search, filtri)
│   ├── practice.js                    pagina Esercitati (TF.js + MediaPipe)
│   └── games/{quiz,memory,spelling,sfida}.js
├── assets/{logo.svg, logo.png}
├── sql/
│   ├── handlearn.sql                  schema base + dati di esempio
│   └── handlearn_v2.sql               migrazione v2 (lezioni-segni N-N)
├── api/save_progress.php              salvataggio progressi/punteggi
├── index.php                          Home (dashboard introduttiva)
├── impara.php                         lista lezioni filtrabili
├── lezione.php                        scheda singola lezione + CTA esercitati
├── esercitati.php                     tutor AI con feedback strutturato
├── dizionario.php                     ricerca + filtri categoria/lettera
├── segno.php                          scheda dettaglio singolo segno
├── about.php                          chi siamo, mission, tecnologia
├── profilo.php                        progressi, XP, punteggi
├── login.php  register.php  logout.php  recupera.php  reset_password.php
├── gioca.php                          hub giochi
├── gioca/{quiz,memory,spelling,sfida}.php
└── server/                            backend Node.js AI
    ├── server.js  package.json
    ├── train.js  mean.js              riallenamento modello (CSV → TF)
    ├── public/{recognize,tutor,collect,giocoPunti}.html   UI di test (admin)
    ├── model/                         modello pre-allenato (A-Z)
    └── gestures.csv  scores.json
```

---

## Versione 2 aggiornata

1. **Design system v2** — palette indaco/ambra/smeraldo, tipografia
   Plus Jakarta Sans + Inter, ombre soft, micro-animazioni, focus accessibili,
   fully responsive.
2. **Niente più emoji come contenuto didattico**: ogni segno è rappresentato
   da uno **SVG coerente** generato dal componente `sign_visual.php`.
   Le icone UI sono Lucide-style inline.
3. **Home come vera dashboard**: CTA "Esercitati" prominente, quick access
   alle 4 sezioni, mission del progetto.
4. **Pagina dettaglio lezione** (`lezione.php`) con segni della lezione e
   CTA "Esercitati con questo segno" che apre il tutor con quel target.
5. **Pagina dettaglio segno** (`segno.php`) con descrizione, lezioni
   correlate e segni della stessa categoria.
6. **Esercitati** (`esercitati.php`) ridisegnata: target visuale, feedback
   testuale ("Bene!"/"Quasi!"/"Riprova"), barra confidenza animata,
   pillole di progresso, supporto a `?segno=X` e `?lezione=N`.
7. **Hub giochi** unificato: quiz, memory, spelling e **sfida AI** integrata
   nello stesso layout (niente più redirect a pagine Node esterne).
   Tutti i giochi hanno: schermata pre-gioco, gioco, fine partita con punteggio,
   pulsanti "rigioca" / "torna alla lista", conferma `beforeunload` per
   evitare uscite accidentali.
8. **Auto-login dopo registrazione**: l'utente non deve più rifare il login,
   viene portato in home con un toast di benvenuto.
9. **Funzione "Raccogli campioni" nascosta** dal flusso utente normale
   (resta accessibile via URL diretto a
   `http://localhost:3000/collect.html` per admin/sviluppatori).
10. **DB v2**: tabella `lezioni_segni` (relazione N-N), popolamento ricco
    di alfabeto A-Z, numeri, saluti, e collegamento ai segni AI già allenati.

---

## Requisiti

- **PHP** ≥ 8.0 con estensioni `mysqli` e `session`
- **MySQL** ≥ 5.7 / **MariaDB** ≥ 10.3
- **Node.js** ≥ 18 (solo per il backend AI)
- Un server web — XAMPP, Laragon, MAMP o `php -S` integrato

## Installazione

### 1. Posiziona il progetto
Copia in `htdocs/HandLearn-main/` (XAMPP) o equivalente.

### 2. Crea il database
```bash
# da phpMyAdmin: importa entrambi i file SQL nell'ordine
mysql -u root -p < sql/handlearn.sql
mysql -u root -p < sql/handlearn_v2.sql
```

### 3. Verifica `config/db.php`
Default per XAMPP:
```php
DB_HOST = 'localhost';
DB_USER = 'root';
DB_PASS = '';
DB_NAME = 'handlearn';
NODE_SERVER_URL = 'http://localhost:3000';
```

### 4. Avvia il backend Node.js
```bash
cd server
npm install
npm start    # → http://localhost:3000
```

### 5. Apri il sito
- **XAMPP / Apache**: http://localhost/HandLearn-main/
- **PHP integrato**:
  ```bash
  cd HandLearn-main
  php -S localhost:8080
  # http://localhost:8080
  ```

---

## Mappa delle pagine

| URL                       | Descrizione                                         |
| ------------------------- | --------------------------------------------------- |
| `/` o `index.php`         | Home dashboard con CTA Esercitati                   |
| `/impara.php`             | Lista lezioni filtrabili (sidebar categorie/livello)|
| `/lezione.php?id=X`       | Dettaglio lezione con segni + "Esercitati"          |
| `/dizionario.php`         | Ricerca + filtri categoria + alfabeto               |
| `/segno.php?id=X`         | Dettaglio segno + CTA esercitati                    |
| `/esercitati.php`         | Tutor AI (default: alfabeto)                        |
| `/esercitati.php?segno=X` | Tutor focalizzato su un singolo segno               |
| `/esercitati.php?lezione=N`| Tutor focalizzato sui segni di una lezione          |
| `/gioca.php`              | Hub giochi                                          |
| `/gioca/quiz.php`         | Quiz a scelta multipla                              |
| `/gioca/memory.php`       | Memory segno↔parola                                 |
| `/gioca/spelling.php`     | Spelling lettera-per-lettera                        |
| `/gioca/sfida.php`        | Sfida AI a tempo (60s)                              |
| `/about.php`              | Chi siamo, mission, tecnologia, contesto            |
| `/profilo.php`            | Profilo, progressi, punteggi (richiede login)       |
| `/login.php`              | Login (username o email)                            |
| `/register.php`           | Registrazione + auto-login                          |
| `/recupera.php`           | Recupero password                                   |

### Solo per admin / dev (non linkati nella UI)
| URL                                     | Descrizione                          |
| --------------------------------------- | ------------------------------------ |
| `http://localhost:3000/collect.html`    | Raccolta campioni di gesto (training)|
| `http://localhost:3000/recognize.html`  | UI nativa di riconoscimento          |
| `http://localhost:3000/tutor.html`      | UI nativa modalità tutor             |
| `http://localhost:3000/giocoPunti.html` | Gioco AI versione standalone Node    |

---


## Database

| Tabella         | Scopo                                            |
| --------------- | ------------------------------------------------ |
| `utenti`        | Account (password con `password_hash`)           |
| `categorie`     | Alfabeto, Numeri, Saluti, Famiglia, ecc.         |
| `lezioni`       | Lezioni con titolo, livello, categoria           |
| `segni`         | Voci del dizionario                              |
| `lezioni_segni` | (v2) Relazione N-N: quali segni per quale lezione|
| `progressi`     | Stato di avanzamento per lezione/utente          |
| `punteggi`      | Punteggi giochi per utente                       |

---

## Riallenare il modello AI (opzionale)

```bash
cd server
node train.js   # legge gestures.csv, salva model/model.json + weights.bin + labels.json
```

Per raccogliere nuovi campioni: http://localhost:3000/collect.html
(non linkato nella UI utente; uso interno).

---

## Sicurezza

- Password con `password_hash()` (bcrypt).
- Tutte le query usano **prepared statements**.
- `session_regenerate_id` al login.
- CORS configurato lato Node per limitare l'origine.

> Per produzione: HTTPS + `session.cookie_secure=1`, sender SMTP per il
> reset password (oggi mostra il link in pagina a scopo dimostrativo),
> rate limiting sui form auth.

---

## Note di accessibilità

- Focus visibile su tutti gli elementi interattivi (`outline: 3px solid`).
- Contrasti WCAG AA su testo principale.
- Etichette `aria-label` sulla navbar, sui pulsanti icona, sull'avatar.
- Feedback visivo + testuale (mai solo colori) sui giochi e sul tutor.
- Riconoscimento webcam sempre privato: il video non lascia il dispositivo.

---

## Licenza

Progetto didattico — uso libero per scopi educativi.
