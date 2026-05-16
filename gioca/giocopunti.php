```php
<?php
require_once '../includes/auth.php';
include '../includes/header.php';
?>

<link rel="stylesheet" href="../css/style.css">

<style>

.game-wrapper{
    max-width:1200px;
    margin:0 auto;
    padding:40px 20px;
}

.game-header{
    text-align:center;
    margin-bottom:30px;
}

.game-header h1{
    font-size:2.5rem;
    margin-bottom:10px;
}

.game-header p{
    color:var(--text-muted);
    max-width:700px;
    margin:0 auto;
}

.ai-game-layout{
    display:grid;
    grid-template-columns:1fr 340px;
    gap:24px;
    align-items:start;
}

.camera-card,
.sidebar-card{
    background:var(--surface);
    border:1px solid var(--border);
    border-radius:24px;
    box-shadow:var(--shadow-md);
    overflow:hidden;
}

.camera-area{
    position:relative;
    width:100%;
    aspect-ratio:4/3;
    background:#000;
}

#video,
#canvas{
    position:absolute;
    inset:0;
    width:100%;
    height:100%;
    object-fit:cover;
}

.game-controls{
    padding:20px;
}

#gestureBox{
    background:linear-gradient(135deg,#4f46e5,#7c3aed);
    color:#fff;
    padding:18px;
    border-radius:16px;
    font-size:1.4rem;
    font-weight:700;
    text-align:center;
    margin-bottom:18px;
}

#timerBarContainer{
    width:100%;
    height:14px;
    background:#e5e7eb;
    border-radius:999px;
    overflow:hidden;
    margin-bottom:20px;
}

#timerBar{
    height:100%;
    width:100%;
    background:linear-gradient(90deg,#10b981,#84cc16);
    transition:width .15s linear;
}

.stats-grid{
    display:grid;
    grid-template-columns:repeat(3,1fr);
    gap:12px;
}

.stat-box{
    background:var(--surface-soft);
    border-radius:16px;
    padding:16px;
    text-align:center;
}

.stat-label{
    font-size:.85rem;
    color:var(--text-muted);
    margin-bottom:6px;
}

.stat-value{
    font-size:1.3rem;
    font-weight:700;
}

.sidebar-card{
    padding:24px;
}

.sidebar-card h2{
    margin-bottom:18px;
}

#leaderboardList{
    list-style:none;
    padding:0;
    margin:0;
}

#leaderboardList li{
    padding:12px;
    border-bottom:1px solid var(--border);
    display:flex;
    justify-content:space-between;
}

.game-buttons{
    display:flex;
    gap:12px;
    margin-top:24px;
}

.game-btn{
    flex:1;
    border:none;
    padding:14px;
    border-radius:14px;
    font-weight:700;
    cursor:pointer;
    transition:.2s;
}

.primary-btn{
    background:var(--primary);
    color:#fff;
}

.primary-btn:hover{
    transform:translateY(-2px);
}

.secondary-btn{
    background:#e5e7eb;
}

@media(max-width:960px){

    .ai-game-layout{
        grid-template-columns:1fr;
    }

}

</style>

<main class="game-wrapper">

    <div class="game-header">
        <h1>Gioco Punti AI</h1>
        <p>
            Esegui il segno LIS mostrato il più velocemente possibile.
            Guadagna punti, crea combo e scala la classifica.
        </p>
    </div>

    <div class="ai-game-layout">

        <!-- AREA GIOCO -->
        <section class="camera-card">

            <div class="camera-area">
                <video id="video" autoplay playsinline></video>
                <canvas id="canvas"></canvas>
            </div>

            <div class="game-controls">

                <div id="gestureBox">
                    Premi "Inizia Partita"
                </div>

                <div id="timerBarContainer">
                    <div id="timerBar"></div>
                </div>

                <div class="stats-grid">

                    <div class="stat-box">
                        <div class="stat-label">Punti</div>
                        <div class="stat-value" id="score">0</div>
                    </div>

                    <div class="stat-box">
                        <div class="stat-label">Combo</div>
                        <div class="stat-value" id="multiplier">x1</div>
                    </div>

                    <div class="stat-box">
                        <div class="stat-label">Vite</div>
                        <div class="stat-value" id="lives">3</div>
                    </div>

                </div>

                <div class="game-buttons">
                    <button id="startButton" class="game-btn primary-btn">
                        Inizia Partita
                    </button>

                    <button id="restartButton" class="game-btn secondary-btn">
                        Rigioca
                    </button>
                </div>

            </div>

        </section>

        <!-- CLASSIFICA -->
        <aside class="sidebar-card">

            <h2>Classifica Top 10</h2>

            <ol id="leaderboardList"></ol>

        </aside>

    </div>

</main>

<!-- TensorFlow -->
<script src="https://cdn.jsdelivr.net/npm/@tensorflow/tfjs"></script>
<script src="https://cdn.jsdelivr.net/npm/@tensorflow/tfjs-backend-webgl"></script>

<!-- MediaPipe -->
<script src="https://cdn.jsdelivr.net/npm/@mediapipe/camera_utils/camera_utils.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@mediapipe/control_utils/control_utils.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@mediapipe/drawing_utils/drawing_utils.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@mediapipe/hands/hands.js"></script>

<script>

const NODE_SERVER = "http://localhost:3000";

const video = document.getElementById("video");
const canvas = document.getElementById("canvas");
const ctx = canvas.getContext("2d");

const startButton = document.getElementById("startButton");
const restartButton = document.getElementById("restartButton");

const gestureBox = document.getElementById("gestureBox");
const timerBar = document.getElementById("timerBar");

const scoreEl = document.getElementById("score");
const multiplierEl = document.getElementById("multiplier");
const livesEl = document.getElementById("lives");

const leaderboardList = document.getElementById("leaderboardList");

const HAND_CONNECTIONS = window.HAND_CONNECTIONS;

let model, reverseLabelMap;
let gameInterval, hands, camera;

let score=0;
let lives=3;
let streak=0;
let multiplier=1;

let currentGesture="";
let lastGesture="";
let timeLeft=8;
let lastSuccessTime=0;

async function initGame(){

    if(!model){

        model = await tf.loadLayersModel(
            NODE_SERVER + "/model/model.json"
        );

        const labels = await fetch(
            NODE_SERVER + "/model/labels.json"
        ).then(r=>r.json());

        reverseLabelMap = {};

        labels.forEach((l,i)=>{
            reverseLabelMap[i]=l;
        });
    }
    score=0;
    lives=3;
    streak=0;
    multiplier=1;

    updateUI();
    setupHands();
    pickRandomGesture();
    startGameLoop();
}

function setupHands(){

    hands = new Hands({
        locateFile:file =>
            `https://cdn.jsdelivr.net/npm/@mediapipe/hands/${file}`
    });

    hands.setOptions({
        maxNumHands:1,
        minDetectionConfidence:0.7,
        minTrackingConfidence:0.7
    });

    hands.onResults(onHandsResults);

    camera = new Camera(video,{
        onFrame: async ()=>{
            await hands.send({image:video});
        },
        width:640,
        height:480
    });

    camera.start();
}

function onHandsResults(results){

    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;

    ctx.clearRect(0,0,canvas.width,canvas.height);

    ctx.drawImage(
        results.image,
        0,
        0,
        canvas.width,
        canvas.height
    );

    if(!results.multiHandLandmarks?.length) return;

    const lm = results.multiHandLandmarks[0];

    const base = lm[0];

    const scale = Math.hypot(
        lm[9].x-base.x,
        lm[9].y-base.y,
        lm[9].z-base.z
    );

    const features = lm.flatMap(p=>[
        (p.x-base.x)/scale,
        (p.y-base.y)/scale,
        (p.z-base.z)/scale
    ]);

    tf.tidy(()=>{

        const input = tf.tensor2d([features]);

        const prediction = model.predict(input);

        const scores = Array.from(prediction.dataSync());

        const indexed = scores.map((score,i)=>({
            score,
            index:i
        }));

        indexed.sort((a,b)=>b.score-a.score);

        const best = indexed[0];

        const predictedLabel =
            reverseLabelMap[best.index];

        const confidence = best.score;

        let color = "#ef4444";

        if(
            predictedLabel===currentGesture &&
            confidence>0.85
        ){

            const now = Date.now();

            if(now-lastSuccessTime>1000){

                handleSuccess();

                pickRandomGesture();

                lastSuccessTime = now;
            }

            color="#10b981";
        }

        drawConnectors(
            ctx,
            lm,
            HAND_CONNECTIONS,
            {color,lineWidth:3}
        );

        drawLandmarks(ctx,lm,{
            color,
            radius:4
        });

    });

}

function startGameLoop(){

    clearInterval(gameInterval);

    gameInterval = setInterval(()=>{

        timeLeft -= 0.1;

        if(timeLeft<=0){

            handleFailure();

            pickRandomGesture();
        }

        timerBar.style.width =
            (timeLeft/8*100)+"%";

    },100);
}

function pickRandomGesture(){

    const labels = Object.values(reverseLabelMap);

    do{
        currentGesture =
            labels[Math.floor(Math.random()*labels.length)];
    }
    while(currentGesture===lastGesture);

    lastGesture = currentGesture;

    gestureBox.textContent =
        "Fai il gesto: " + currentGesture;

    timeLeft = 8;
}

function handleSuccess(){

    streak++;

    multiplier =
        streak>=10 ? 10 :
        streak>=3 ? 3 : 1;

    score += 10*multiplier;

    updateUI();
}

function handleFailure(){

    lives--;

    streak=0;
    multiplier=1;

    score -= 5;

    updateUI();

    if(lives<=0){
        endGame();
    }
}

function updateUI(){

    scoreEl.textContent = score;
    multiplierEl.textContent = "x"+multiplier;
    livesEl.textContent = lives;
}

async function endGame(){

    clearInterval(gameInterval);

    gestureBox.textContent =
        "Partita terminata";

    const name =
        prompt("Inserisci il tuo nome","Player");

    try{

        const res = await fetch(
            NODE_SERVER + "/saveScore",
            {
                method:"POST",
                headers:{
                    "Content-Type":"application/json"
                },
                body:JSON.stringify({
                    name,
                    score
                })
            }
        );

        const scores = await res.json();

        updateLeaderboard(scores);

    }catch(err){

        console.error(err);
    }
}

function updateLeaderboard(scores){

    leaderboardList.innerHTML="";

    scores.forEach(s=>{

        const li = document.createElement("li");

        li.innerHTML = `
            <span>${s.name}</span>
            <strong>${s.score}</strong>
        `;

        leaderboardList.appendChild(li);
    });
}

startButton.addEventListener("click", initGame);
restartButton.addEventListener("click", initGame);

</script>

<?php include '../includes/footer.php'; ?>
```
