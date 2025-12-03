<?php
$this->title = 'FiscalBridge Information System';
$web = Yii::getAlias('@web');

use yii\helpers\Html;
use yii\helpers\Url;
?>

<section class="hero">
    <canvas id="particles"></canvas>
    <div class="hero-overlay"></div>
    <div class="container text-center">
        <h1 id="hero-text" class="hero-title"></h1>
        <p class="fade-in">Promoting equitable resource allocation, financial transparency, and sustainable economic development.</p>
        <a href="<?= Url::to(['/backend/default/login']) ?>" class="btn-get-started">Get Started</a>
    </div>
</section>

<style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap');

:root {
    --primary-teal: #008a8a;
    --goldenrod: #daa520;
    --dark-teal: #0e4f4f;
    --light-gold: #f5c85c;
    --charcoal-black: #1b1f22;
    --white: #ffffff;
}

body {
    font-family: 'Poppins', sans-serif;
    background-color: var(--charcoal-black);
    color: var(--white);
    margin: 0;
    padding: 0;
    overflow-x: hidden;
}

/* ==== HERO ==== */
.hero {
    position: relative;
    background: linear-gradient(135deg, var(--primary-teal), var(--dark-teal));
    text-align: center;
    padding: 40px 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-direction: column;
    overflow: hidden;
    min-height: 30vh;
    max-height: 300px;
}
.hero-overlay {
    position: absolute;
    top: 0; left: 0;
    width: 100%; height: 100%;
    background: rgba(0, 0, 0, 0.5);
    z-index: 1;
}
.hero .container {
    position: relative;
    z-index: 2;
    max-width: 800px;
}
.hero-title {
    font-weight: 700;
    font-size: 1.6rem;
    display: inline-block;
    background: linear-gradient(to right, var(--goldenrod), var(--light-gold));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    transition: opacity 0.3s ease-in-out;
}
.fade-in {
    font-size: 0.9rem;
    font-weight: 500;
    opacity: 0;
    animation: fadeInUp 1.5s ease-in-out forwards;
}
.btn-get-started {
    background: var(--goldenrod);
    color: var(--charcoal-black);
    padding: 10px 28px;
    font-weight: 600;
    border-radius: 50px;
    font-size: 0.95rem;
    transition: all 0.3s ease-in-out;
    display: inline-block;
    margin-top: 15px;
    box-shadow: 0px 0px 8px var(--light-gold);
    text-decoration: none;
}
.btn-get-started:hover {
    background: var(--primary-teal);
    color: var(--white);
    transform: scale(1.08);
    box-shadow: 0px 0px 12px var(--goldenrod);
}
@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}
@media (max-width: 768px) {
    .hero-title { font-size: 1.3rem; }
    .fade-in { font-size: 0.8rem; }
}
#particles {
    position: absolute;
    top: 0; left: 0;
    width: 100%; height: 100%;
    z-index: 0;
}

/* ==== FEATURE SECTION ==== */
.feature-section {
    background: linear-gradient(60deg, #fcf3e4, #008A8A);
    padding: 40px 30px;
    border-radius: 40px; /* curved smooth edges */
    margin: 20px auto;
    position: relative;
    overflow: hidden;
    width: 95%; /* gives margin space on sides */
}
.feature-section::before,
.feature-section::after {
    content: "";
    position: absolute;
    width: 220px;
    height: 220px;
    border-radius: 50%;
    z-index: 0;
}
.feature-section::before {
    top: -90px; right: -90px;
    background: radial-gradient(circle, rgba(255,255,255,0.4), transparent);
}
.feature-section::after {
    bottom: -90px; left: -90px;
    background: radial-gradient(circle, rgba(255,255,255,0.25), transparent);
}

/* Full-width container inside */
.feature-section .feature-container {
    width: 100%;
    max-width: 100%;
    margin: 0 auto;
}

/* Grid for auto-adjust */
.feature-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
    gap: 1.5rem;
}

/* ==== CARDS ==== */
.feature-card {
    border-radius: 14px;
    padding: 16px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.15);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    background: rgba(255, 255, 255, 0.05);
}
.feature-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 6px 16px rgba(0,0,0,0.25);
}
.glass-card {
    background: rgba(255, 255, 255, 0.25) !important;
    backdrop-filter: blur(12px);
    border: 1px solid rgba(255, 255, 255, 0.35);
}
.feature-icon {
    width: 42px;
    height: 42px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.1rem;
    margin: 0 auto 8px;
    transition: all 0.3s ease;
}
.feature-card:hover .feature-icon { transform: scale(1.1); }
.feature-card h5 {
    font-size: 0.9rem;
    margin-bottom: 6px;
}
.feature-card p {
    font-size: 0.8rem;
    margin-bottom: 8px;
}
.feature-list {
    list-style: none;
    padding: 0;
    margin: 6px 0;
}
.feature-list li {
    font-size: 0.8rem;
    margin-bottom: 4px;
    display: flex;
    align-items: center;
}
.feature-list li i {
    margin-right: 6px;
    font-size: 0.85rem;
}
.card-body .btn {
    font-size: 0.75rem;
    padding: 5px 12px;
    border-radius: 16px;
}

/* ==== RIBBON ==== */
.ribbon {
    position: absolute;
    top: -18px;
    left: 50%;
    transform: translateX(-50%);
    background: linear-gradient(135deg, #2E8B57, #8B4513);
    padding: 8px 28px;
    border-radius: 40px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.25);
    z-index: 10;
    font-weight: 600;
    color: #fff;
    text-shadow: 1px 1px 3px rgba(0,0,0,0.35);
    font-size: 1rem;
}
</style>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const heroText = document.getElementById("hero-text");
    const textArray = [
        "Strengthening Fiscal Governance",
        "Ensuring Equitable Resource Allocation",
        "Enhancing Financial Transparency & Efficiency"
    ];
    let index = 0;
    function typeEffect() {
        heroText.style.opacity = "0";
        setTimeout(() => {
            heroText.innerHTML = textArray[index];
            heroText.style.opacity = "1";
            index = (index + 1) % textArray.length;
        }, 400);
    }
    setInterval(typeEffect, 3000);
    typeEffect();
});

// Particle Background
const canvas = document.getElementById("particles");
const ctx = canvas.getContext("2d");
canvas.width = window.innerWidth;
canvas.height = window.innerHeight;

let particles = [];
class Particle {
    constructor(x, y, radius, color, velocity) {
        this.x = x; this.y = y;
        this.radius = radius;
        this.color = color;
        this.velocity = velocity;
    }
    draw() {
        ctx.beginPath();
        ctx.arc(this.x, this.y, this.radius, 0, Math.PI * 2, false);
        ctx.fillStyle = this.color;
        ctx.fill();
    }
    update() {
        this.x += this.velocity.x;
        this.y += this.velocity.y;
        if (this.x - this.radius < 0 || this.x + this.radius > canvas.width) this.velocity.x *= -1;
        if (this.y - this.radius < 0 || this.y + this.radius > canvas.height) this.velocity.y *= -1;
        this.draw();
    }
}
function initParticles() {
    particles = [];
    for (let i = 0; i < 25; i++) {
        const radius = Math.random() * 2 + 1;
        const x = Math.random() * canvas.width;
        const y = Math.random() * canvas.height;
        const color = "rgba(255,255,255,0.7)";
        const velocity = { x: (Math.random() - 0.5), y: (Math.random() - 0.5) };
        particles.push(new Particle(x, y, radius, color, velocity));
    }
}
function animateParticles() {
    requestAnimationFrame(animateParticles);
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    particles.forEach(p => p.update());
}
initParticles();
animateParticles();
window.addEventListener("resize", () => {
    canvas.width = window.innerWidth;
    canvas.height = window.innerHeight;
    initParticles();
});
</script>

<!-- ==== FEATURES SECTION ==== -->
<div class="container-fluid position-relative my-5 p-0">
    <div class="ribbon text-center">System Features</div>

    <section id="features" class="feature-section">
      <div class="feature-container">
        <div class="feature-grid mt-4">
          <!-- Card 1 -->
          <div class="card feature-card border-0 text-white" style="background: linear-gradient(135deg, #2E8B57, #3cb371);">
            <div class="card-body text-center">
              <div class="feature-icon mb-2" style="background: #ffffff; color: #2E8B57;"><i class="fa-solid fa-building-columns"></i></div>
              <h5 class="card-title">Intergovernmental Fiscal Relations</h5>
              <p class="card-text">Manage fiscal policies with strengthened collaboration among government entities.</p>
              <ul class="feature-list text-start">
                <li><i class="fa-solid fa-circle-check"></i> Policy frameworks development</li>
                <li><i class="fa-solid fa-circle-check"></i> Efficient budget allocation</li>
                <li><i class="fa-solid fa-circle-check"></i> Expenditure monitoring</li>
              </ul>
            <!-- ?= \yii\helpers\Html::a('IGFR Portal', ['site/igfrd'], ['class' => 'btn btn-light mt-2']) ? -->
            <a href="<?= Url::to(['site/igfrd']) ?>" class="btn btn-light mt-2">IGFR Portal</a>


</a>

            </div>
          </div>

          <!-- Card 2 -->
          <div class="card feature-card border-0 text-dark glass-card">
            <div class="card-body text-center">
              <div class="feature-icon mb-2" style="background: #2E8B57; color: #fff;"><i class="fa-solid fa-hand-holding-dollar"></i></div>
              <h5 class="card-title">Equalization Fund</h5>
              <p class="card-text">Ensure fair resource distribution to empower marginalized regions.</p>
              <ul class="feature-list text-start">
                <li><i class="fa-solid fa-circle-check"></i> Funding transparency & accountability</li>
                <li><i class="fa-solid fa-circle-check"></i> Targeted economic growth</li>
                <li><i class="fa-solid fa-circle-check"></i> Regional equity & social impact</li>
              </ul>
     <!--< ?= \yii\helpers\Html::a('Equalization Fund', ['site/equalization'], ['class' => 'btn btn-success mt-2']) ?> -->
     <a href="<?= Url::to(['site/equalization']) ?>" class="btn btn-success mt-2">Equaization Fund</a>

            </div>
          </div>

          <!-- Card 3 -->
          <div class="card feature-card border-0 text-white" style="background: linear-gradient(135deg, #8B4513, #a0522d);">
            <div class="card-body text-center">
              <div class="feature-icon mb-2" style="background: #ffffff; color: #8B4513;"><i class="fa-solid fa-rocket"></i></div>
              <h5 class="card-title">Innovative & Secure Solutions</h5>
              <p class="card-text">Leverage technology, analytics, and collaboration for sustainable progress.</p>
              <ul class="feature-list text-start">
                <li><i class="fa-solid fa-circle-check"></i> Modern design & usability</li>
                <li><i class="fa-solid fa-circle-check"></i> Real-time data analytics</li>
                <li><i class="fa-solid fa-circle-check"></i> Collaborative tools</li>
                <li><i class="fa-solid fa-circle-check"></i> Security & compliance</li>
              </ul>
              <a href="#" class="btn btn-warning mt-1">Learn More</a>
            </div>
          </div>
        </div>
      </div>
    </section>
</div>
