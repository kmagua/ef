<?php
$this->title = 'Equalization Fund Portal';
$web = Yii::getAlias('@web');
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= $this->title ?></title>

<!-- Tailwind CSS -->
<script src="https://cdn.tailwindcss.com"></script>

<!-- Google Fonts -->
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
  body { font-family: 'Poppins', sans-serif; }

  /* Animation */
  .fade-up { animation: fadeUp 1s ease-in-out; }
  @keyframes fadeUp { from {opacity:0; transform: translateY(30px);} to {opacity:1; transform: translateY(0);} }

  /* Glow effect */
  .glow { box-shadow: 0 0 15px rgba(34,197,94,0.4); }
</style>
</head>
<body class="bg-gray-50">

<!-- Hero Section -->
<section class="relative h-[40vh] flex items-center justify-center text-center bg-cover bg-center" 
         style="background-image:url('<?= $web ?>/igfr_front/img/kenya-pattern.png');">
  <div class="absolute inset-0 bg-gradient-to-r from-green-800/90 to-green-600/90"></div>
  <div class="relative z-10 max-w-2xl mx-auto px-6 text-white">
    <h1 class="text-2xl md:text-4xl font-bold fade-up drop-shadow-md">Equalization Fund Portal</h1>
    <p class="mt-2 text-sm md:text-lg opacity-90 fade-up">Promoting equity in Kenya’s marginalized regions</p>
    <a href="#about" 
       class="mt-4 inline-block bg-white text-green-600 px-5 py-2 rounded-full font-semibold shadow-lg 
              hover:bg-green-100 hover:scale-105 transition transform">
       Explore More
    </a>
  </div>
</section>



<!-- About Section -->
<section id="about" class="container mx-auto px-6 py-10">
  <div class="grid md:grid-cols-2 gap-10 items-center">
    <div class="bg-white rounded-2xl shadow-xl p-6 md:p-10 glow hover:scale-105 transition">
      <h2 class="text-3xl font-bold text-green-800 mb-4">About the Fund</h2>
      <p class="text-gray-700 leading-relaxed">
        Established under <strong>Article 204(1)</strong> of the Constitution of Kenya, the Equalization Fund promotes fairness by redistributing national resources. 
        It is guided by the <strong>PFM (Equalization Fund) Regulations, 2021</strong>, ensuring effective development in marginalized areas.
      </p>
    </div>
    <div>
      <img src="<?= $web ?>/igfr_front/img/eq.png" alt="Equalization Fund" class="rounded-xl shadow-2xl w-full object-contain hover:scale-105 transition duration-500">
    </div>
  </div>
</section>
<!-- Policies Section -->
<section id="focus-areas" class="bg-gray-80 py-5">
  <div class="container mx-auto px-6 text-center mb-12">
    <h2 class="text-3xl font-bold text-green-800">Marginalization Policies</h2>
    <p class="text-gray-600 mt-3">Key policies guiding allocations to marginalized areas</p>
  </div>
  <div class="container mx-auto grid md:grid-cols-4 gap-8 px-6">
    <?php
      $cards = [
        ["fa-book", "Legal Framework", "Article 216(4) requires CRA to identify marginalized areas guiding Equalization Fund allocations."],
        ["fa-lightbulb", "First Policy", "Approved in 2014, identifying 14 counties via reports and the County Development Index."],
        ["fa-chart-line", "Second Policy", "Focuses on deprivation index (water, education, sanitation, electricity) covering 1,424 areas in 34 counties."],
        ["fa-balance-scale", "Implementation", "PFM Regulations 2021 guide funds as conditional grants under the Division of Revenue Act."]
      ];
      foreach ($cards as $c): ?>
      <div class="bg-white p-8 rounded-2xl shadow-lg hover:scale-105 transition glow">
        <div class="mx-auto bg-green-100 text-green-800 w-16 h-16 flex items-center justify-center rounded-full shadow mb-5">
          <i class="fas <?= $c[0] ?> text-2xl"></i>
        </div>
        <h5 class="text-xl font-semibold text-green-800"><?= $c[1] ?></h5>
        <p class="mt-3 text-gray-600 text-sm"><?= $c[2] ?></p>
      </div>
    <?php endforeach; ?>
  </div>
</section>

<!-- Impact Numbers -->
<section class="bg-gradient-to-r from-green-700 to-green-900 text-white py-20">
  <div class="container mx-auto text-center">
    <h2 class="text-3xl font-bold mb-12">Impact in Numbers</h2>
    <div class="grid md:grid-cols-3 gap-10">
      <div class="p-8 bg-green-800 rounded-2xl shadow-lg hover:scale-105 transition glow">
        <h3 class="text-6xl font-bold counter" data-count="150">0</h3>
        <p class="mt-3 text-lg">Projects Funded</p>
      </div>
      <div class="p-8 bg-green-800 rounded-2xl shadow-lg hover:scale-105 transition glow">
        <h3 class="text-6xl font-bold counter" data-count="350">0</h3>
        <p class="mt-3 text-lg">Communities Reached</p>
      </div>
      <div class="p-8 bg-green-800 rounded-2xl shadow-lg hover:scale-105 transition glow">
        <h3 class="text-6xl font-bold counter" data-count="1200">0</h3>
        <p class="mt-3 text-lg">Beneficiaries</p>
      </div>
    </div>
  </div>
</section>

<!-- Footer -->
<footer class="bg-green-950 text-gray-200 py-10 mt-10">
  <div class="container mx-auto px-6 text-center">
    <p class="text-lg font-semibold">&copy; <?= date('Y') ?> Equalization Fund Kenya</p>
    <p class="text-sm opacity-75 mt-1">Empowering Communities • Driving Equity • Building the Future</p>
    <div class="flex justify-center gap-6 mt-4">
      <a href="#about" class="hover:text-white">About</a>
      <a href="#focus-areas" class="hover:text-white">Policies</a>
      <a href="#" class="hover:text-white">Contact</a>
    </div>
  </div>
</footer>

<!-- Counter Script -->
<script>
document.addEventListener("DOMContentLoaded", () => {
  const counters = document.querySelectorAll(".counter");
  const speed = 200;
  const animate = (counter) => {
    const target = +counter.getAttribute("data-count");
    const count = +counter.innerText;
    const increment = target / speed;
    if (count < target) {
      counter.innerText = Math.ceil(count + increment);
      setTimeout(() => animate(counter), 20);
    } else counter.innerText = target;
  };
  counters.forEach(counter => animate(counter));
});
</script>

</body>
</html>
