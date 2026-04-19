<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
  <title>Aolt | 25 Hero Headers For Websites</title>
  <!-- Google Fonts for modern typography -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,600;14..32,700;14..32,800&display=swap" rel="stylesheet">
  <!-- Font Awesome 6 (free icons) -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      background: #faf9fc;
      font-family: 'Inter', sans-serif;
      color: #1a1a2e;
      line-height: 1.4;
      scroll-behavior: smooth;
    }

    /* custom scrollbar */
    ::-webkit-scrollbar {
      width: 8px;
    }
    ::-webkit-scrollbar-track {
      background: #e9e8f0;
    }
    ::-webkit-scrollbar-thumb {
      background: #9d8dff;
      border-radius: 20px;
    }

    /* main container */
    .container {
      max-width: 1440px;
      margin: 0 auto;
      padding: 2rem 2rem 5rem;
    }

    /* header / branding section – inspired by "Aolt" aesthetic */
    .brand-header {
      display: flex;
      justify-content: space-between;
      align-items: baseline;
      flex-wrap: wrap;
      gap: 1rem;
      margin-bottom: 3rem;
      border-bottom: 2px solid rgba(157, 141, 255, 0.25);
      padding-bottom: 1.5rem;
    }
    .logo-area {
      display: flex;
      align-items: baseline;
      gap: 0.5rem;
      flex-wrap: wrap;
    }
    .logo {
      font-size: 2.2rem;
      font-weight: 800;
      letter-spacing: -0.02em;
      background: linear-gradient(135deg, #2b2d42, #6c63ff);
      background-clip: text;
      -webkit-background-clip: text;
      color: transparent;
    }
    .badge {
      background: #eceaff;
      padding: 0.2rem 0.8rem;
      border-radius: 40px;
      font-size: 0.85rem;
      font-weight: 600;
      color: #4a3f9e;
    }
    .hero-quote {
      font-size: 0.95rem;
      color: #5b5b7a;
      background: #ffffffcc;
      backdrop-filter: blur(2px);
    }
    .available-tag {
      background: #1e1e2f;
      color: #f3f0ff;
      padding: 0.5rem 1.2rem;
      border-radius: 60px;
      font-weight: 500;
      font-size: 0.9rem;
      display: inline-flex;
      align-items: center;
      gap: 0.6rem;
      letter-spacing: -0.2px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.02);
    }
    .available-tag i {
      font-size: 1rem;
      color: #b9aeff;
    }

    /* main title section – matching the "25 Hero Headers For Websites" statement */
    .hero-title-section {
      text-align: center;
      margin: 2rem 0 3rem;
    }
    .hero-title-section h1 {
      font-size: 3.8rem;
      font-weight: 800;
      letter-spacing: -0.02em;
      background: linear-gradient(125deg, #1f1c3b, #3a2e8f, #7b6eff);
      background-clip: text;
      -webkit-background-clip: text;
      color: transparent;
      margin-bottom: 0.75rem;
      line-height: 1.2;
    }
    .hero-title-section p {
      font-size: 1.2rem;
      color: #4a4a6a;
      max-width: 620px;
      margin: 0 auto;
    }

    /* filter / category bar (for interactive CSS demo) */
    .filter-bar {
      display: flex;
      flex-wrap: wrap;
      justify-content: center;
      gap: 0.6rem;
      margin-bottom: 3rem;
    }
    .filter-btn {
      background: transparent;
      border: 1px solid #ddd9f0;
      padding: 0.5rem 1.2rem;
      border-radius: 60px;
      font-family: 'Inter', sans-serif;
      font-weight: 500;
      font-size: 0.85rem;
      color: #2d2a4a;
      cursor: pointer;
      transition: all 0.25s ease;
    }
    .filter-btn i {
      margin-right: 6px;
      font-size: 0.8rem;
    }
    .filter-btn:hover {
      background: #f0edff;
      border-color: #9d8dff;
      transform: translateY(-2px);
    }
    .filter-btn.active {
      background: #2b2a4c;
      border-color: #2b2a4c;
      color: white;
      box-shadow: 0 6px 12px rgba(46, 41, 92, 0.15);
    }

    /* card grid layout — 25 hero header preview cards */
    .headers-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(330px, 1fr));
      gap: 2rem;
      margin-top: 1rem;
    }

    /* each card represents a unique hero header style */
    .hero-card {
      background: #ffffff;
      border-radius: 28px;
      box-shadow: 0 15px 30px -12px rgba(0, 0, 0, 0.08), 0 2px 5px rgba(0,0,0,0.02);
      overflow: hidden;
      transition: all 0.3s cubic-bezier(0.2, 0, 0, 1);
      cursor: pointer;
      border: 1px solid #f0eefc;
    }
    .hero-card:hover {
      transform: translateY(-8px);
      box-shadow: 0 28px 38px -16px rgba(68, 51, 122, 0.2);
      border-color: #cbc3ff;
    }

    /* preview area — this is where the actual "hero header" mini demo lives */
    .card-preview {
      background: #ffffff;
      padding: 1.5rem 1.5rem 1.2rem;
      border-bottom: 1px solid #f0edff;
      transition: background 0.2s;
    }

    /* each hero style uses custom classes – we'll apply inline or specific CSS via unique classes */
    /* base reset for preview inner elements */
    .hero-mini {
      width: 100%;
      border-radius: 20px;
      transition: all 0.2s;
    }

    /* style variations for different hero headers (25 unique flavors) */
    /* we'll differentiate by background, layout, typography — all using CSS */
    /* to keep CSS organized but dynamic, we use attribute or nth-child styling + specific classes */
    
    /* but here: we assign individual hero-style classes for diversity */
    .hero-style-1 { background: linear-gradient(115deg, #f9f3ff, #e9e2ff); padding: 1.4rem; border-radius: 20px; }
    .hero-style-2 { background: #0c0a1f; padding: 1.4rem; border-radius: 20px; color: white; }
    .hero-style-3 { background: #fff8e7; border-left: 5px solid #ffb347; padding: 1.2rem; }
    .hero-style-4 { background: #eef2ff; display: flex; align-items: center; gap: 1rem; flex-wrap: wrap; justify-content: space-between; }
    .hero-style-5 { background: radial-gradient(circle at top left, #ffe6f0, #ffd9e8); text-align: center; }
    .hero-style-6 { background: #1e2a3e; color: #f0f3fa; display: flex; gap: 0.8rem; align-items: center; }
    .hero-style-7 { background: #d9e3f0; backdrop-filter: blur(2px); font-family: monospace; font-weight: bold; }
    .hero-style-8 { background: #ffffff; box-shadow: 0 6px 14px rgba(0,0,0,0.03); border: 1px solid #e9e3ff; border-radius: 28px; }
    .hero-style-9 { background: #fffaec; border-bottom: 3px solid #a594fd; }
    .hero-style-10 { background: #2c2c3a; color: #f2eefc; border-radius: 32px; text-align: center; }
    .hero-style-11 { background: linear-gradient(145deg, #fff2d7, #ffe1b9); display: flex; flex-direction: column; gap: 0.3rem; }
    .hero-style-12 { background: #c7e9fb; font-weight: 500; border-radius: 24px; padding: 1rem; text-shadow: 0 1px 0 white; }
    .hero-style-13 { background: #f1eaff; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.5rem; }
    .hero-style-14 { background: #e3daff; box-shadow: inset 0 -2px 0 #aa99ff; text-align: right; }
    .hero-style-15 { background: #1a1a2e; color: #e9e2ff; border-radius: 30px; display: flex; gap: 1rem; align-items: center; }
    .hero-style-16 { background: #d6e5fa; font-size: 0.9rem; letter-spacing: -0.2px; border-radius: 16px; text-align: center; }
    .hero-style-17 { background: #fef7e0; border: 2px dashed #bcaaff; }
    .hero-style-18 { background: #eef0fa; background-image: radial-gradient(#cbbff0 0.8px, transparent 0.8px); background-size: 16px 16px; }
    .hero-style-19 { background: #2c3e50; color: #ecf0f1; display: flex; flex-direction: column; gap: 8px; border-radius: 24px; }
    .hero-style-20 { background: #fff3db; font-family: 'Inter', sans-serif; font-weight: 600; border-left: 8px solid #a27eff; }
    .hero-style-21 { background: #faf0fc; display: flex; gap: 12px; align-items: baseline; flex-wrap: wrap; justify-content: center; }
    .hero-style-22 { background: #e6e9ff; border-radius: 40px; padding: 0.8rem 1.2rem; text-align: center; }
    .hero-style-23 { background: #1f1b38; color: #dad2ff; border-radius: 24px; display: flex; align-items: center; justify-content: space-between; }
    .hero-style-24 { background: #d5d0f0; background: linear-gradient(135deg, #ffffff, #ede7ff); }
    .hero-style-25 { background: #13122c; color: #e2dcff; border-radius: 18px; text-align: center; box-shadow: 0 8px 0 #5b4bc4; }

    /* shared content style inside previews */
    .hero-mini h3 {
      font-size: 1.1rem;
      font-weight: 700;
      margin-bottom: 6px;
    }
    .hero-mini p {
      font-size: 0.7rem;
      opacity: 0.8;
      margin-bottom: 10px;
    }
    .mini-btn {
      display: inline-block;
      background: rgba(98, 78, 204, 0.12);
      padding: 0.2rem 0.9rem;
      border-radius: 40px;
      font-size: 0.65rem;
      font-weight: 600;
      color: #433e8b;
      text-decoration: none;
    }
    .dark-mini-btn {
      background: rgba(255,255,240,0.15);
      color: #f0eaff;
    }
    /* card footer info */
    .card-info {
      padding: 1rem 1.5rem 1.2rem;
      background: #ffffff;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    .card-info span {
      font-size: 0.75rem;
      font-weight: 600;
      background: #f3f1fe;
      padding: 0.2rem 0.7rem;
      border-radius: 50px;
      color: #3d3492;
    }
    .card-info i {
      color: #9a8dff;
      font-size: 0.9rem;
      transition: 0.2s;
    }
    .hero-card:hover .card-info i {
      transform: translateX(4px);
    }

    /* "Available for" section matching original prompt */
    .available-section {
      margin-top: 5rem;
      text-align: center;
      background: #f1effa;
      border-radius: 72px;
      padding: 2.5rem 2rem;
      backdrop-filter: blur(2px);
      box-shadow: 0 5px 20px rgba(0,0,0,0.02);
    }
    .available-section h3 {
      font-size: 1.8rem;
      font-weight: 700;
      letter-spacing: -0.3px;
      background: linear-gradient(145deg, #25233c, #6b5fd2);
      background-clip: text;
      -webkit-background-clip: text;
      color: transparent;
      margin-bottom: 1rem;
    }
    .available-badges {
      display: flex;
      justify-content: center;
      gap: 1.8rem;
      flex-wrap: wrap;
      margin-top: 1rem;
    }
    .avail-item {
      background: white;
      padding: 0.7rem 1.5rem;
      border-radius: 60px;
      display: inline-flex;
      align-items: center;
      gap: 10px;
      font-weight: 600;
      box-shadow: 0 4px 8px rgba(0,0,0,0.02);
      border: 1px solid #e2daff;
    }
    .avail-item i {
      font-size: 1.2rem;
      color: #5f51d9;
    }

    footer {
      margin-top: 4rem;
      text-align: center;
      font-size: 0.8rem;
      color: #706c96;
      border-top: 1px solid #e5e0fa;
      padding-top: 2rem;
    }

    @media (max-width: 750px) {
      .container { padding: 1rem 1rem 3rem; }
      .hero-title-section h1 { font-size: 2.5rem; }
      .brand-header { flex-direction: column; align-items: flex-start; }
    }
  </style>
</head>
<body>
<div class="container">
  <!-- header inspired by "Aolt" branding -->
  <div class="brand-header">
    <div class="logo-area">
      <div class="logo">Aolt</div>
      <div class="badge">design system</div>
      <div class="hero-quote"><i class="fas fa-bolt" style="font-size: 0.8rem;"></i> modern hero blocks</div>
    </div>
    <div class="available-tag">
      <i class="fas fa-check-circle"></i> Available for <strong>Figma, React, HTML/CSS</strong>
    </div>
  </div>

  <!-- main title matching prompt: "25 Hero Headers For Websites" -->
  <div class="hero-title-section">
    <h1>25 Hero Headers <br>For Websites</h1>
    <p>Unique, ready‑to‑use hero sections — each crafted with pure CSS. Hover to explore.</p>
  </div>

  <!-- interactive filter to show "all" or specific categories (just for extra CSS interactivity) -->
  <div class="filter-bar">
    <button class="filter-btn active" data-filter="all"><i class="fas fa-layer-group"></i> All 25</button>
    <button class="filter-btn" data-filter="dark"><i class="fas fa-moon"></i> Dark hero</button>
    <button class="filter-btn" data-filter="light"><i class="fas fa-sun"></i> Light & bright</button>
    <button class="filter-btn" data-filter="gradient"><i class="fas fa-chart-line"></i> Gradient style</button>
  </div>

  <!-- grid container for 25 hero header previews -->
  <div class="headers-grid" id="headersGrid"></div>

  <!-- "Available for" section matches the exact prompt context -->
  <div class="available-section">
    <h3><i class="fas fa-gem"></i> Available for</h3>
    <div class="available-badges">
      <div class="avail-item"><i class="fab fa-figma"></i> Figma</div>
      <div class="avail-item"><i class="fab fa-react"></i> React</div>
      <div class="avail-item"><i class="fab fa-css3-alt"></i> CSS Modules</div>
      <div class="avail-item"><i class="fas fa-file-code"></i> HTML / Tailwind</div>
    </div>
    <p style="margin-top: 1.2rem; font-size: 0.85rem;">✨ 25 responsive hero headers, copy & paste ready. Each component uses modern CSS techniques.</p>
  </div>
  <footer>
    ⚡ Aolt — 25 Hero Headers · Interactive CSS gallery · Hover effects & style variations
  </footer>
</div>

<script>
  // generate 25 distinct hero header designs using pure CSS classes and content variations
  // each card represents a different hero header layout with unique copy, style, and category flags.
  const heroesData = [
    { name: "Stellar Glow", desc: "Bold gradient headline + CTA", styleClass: "hero-style-1", category: "gradient", previewHTML: `<h3>✨ Stellar Launch</h3><p>Next-gen platform for creators</p><a href="#" class="mini-btn">Get early access →</a>` },
    { name: "Midnight Pro", desc: "Dark minimal hero", styleClass: "hero-style-2", category: "dark", previewHTML: `<h3>Midnight Pro</h3><p>Dark mode ready · high contrast</p><a href="#" class="mini-btn dark-mini-btn">Explore</a>` },
    { name: "Sunbeam", desc: "Warm & inviting", styleClass: "hero-style-3", category: "light", previewHTML: `<h3>☀️ Sunbeam Studio</h3><p>Creative digital agency</p><a href="#" class="mini-btn">View work</a>` },
    { name: "FlexEdge", desc: "Split layout", styleClass: "hero-style-4", category: "light", previewHTML: `<div><h3>FlexEdge</h3><p>Build faster</p></div><a href="#" class="mini-btn">Start →</a>` },
    { name: "Radial Bloom", desc: "Soft radial gradient", styleClass: "hero-style-5", category: "gradient", previewHTML: `<h3>Radial Bloom</h3><p>Design that inspires</p><a href="#" class="mini-btn">Discover</a>` },
    { name: "Slate Core", desc: "Tech dark hero", styleClass: "hero-style-6", category: "dark", previewHTML: `<i class="fas fa-cube"></i><div><h3>Slate Core</h3><p>DevOps redefined</p><a href="#" class="mini-btn dark-mini-btn">Read docs</a></div>` },
    { name: "Monospace Vibe", desc: "Retro terminal", styleClass: "hero-style-7", category: "light", previewHTML: `<h3>>_ monospace.hero</h3><p>classic dev aesthetic</p><a href="#" class="mini-btn">$ get started</a>` },
    { name: "Glassmorph", desc: "Frosted panel", styleClass: "hero-style-8", category: "light", previewHTML: `<h3>Glassmorph UI</h3><p>Modern translucent</p><a href="#" class="mini-btn">Preview</a>` },
    { name: "Underline Pop", desc: "Accent border", styleClass: "hero-style-9", category: "gradient", previewHTML: `<h3>Underline Studios</h3><p>Bold brand identity</p><a href="#" class="mini-btn">Portfolio →</a>` },
    { name: "Dark Elegance", desc: "Sophisticated dark", styleClass: "hero-style-10", category: "dark", previewHTML: `<h3>Dark Elegance</h3><p>Luxury minimalism</p><a href="#" class="mini-btn dark-mini-btn">Shop now</a>` },
    { name: "Butter & Honey", desc: "Warm minimal", styleClass: "hero-style-11", category: "light", previewHTML: `<h3>Butter & Honey</h3><p>Artisan bakery</p><a href="#" class="mini-btn">Order online</a>` },
    { name: "Breeze", desc: "Soft blue airy", styleClass: "hero-style-12", category: "light", previewHTML: `<h3>Breeze ⛅</h3><p>Fresh start today</p><a href="#" class="mini-btn">Learn more</a>` },
    { name: "Lavender Split", desc: "Two-column hero", styleClass: "hero-style-13", category: "gradient", previewHTML: `<span><h3>Lavender</h3><p>calm & creative</p></span><a href="#" class="mini-btn">Join →</a>` },
    { name: "RTL Flow", desc: "Right-aligned edge", styleClass: "hero-style-14", category: "light", previewHTML: `<h3>RTL Flow</h3><p>Global design system</p><a href="#" class="mini-btn">Explore</a>` },
    { name: "Neon Noir", desc: "Purple dark vibes", styleClass: "hero-style-15", category: "dark", previewHTML: `<i class="fas fa-bolt" style="font-size: 1.2rem;"></i><div><h3>Neon Noir</h3><p>Cyberpunk aesthetics</p><a href="#" class="mini-btn dark-mini-btn">Launch</a></div>` },
    { name: "Cloud Nine", desc: "Soft & centered", styleClass: "hero-style-16", category: "light", previewHTML: `<h3>Cloud Nine ☁️</h3><p>simplicity meets speed</p><a href="#" class="mini-btn">Get started</a>` },
    { name: "Sketch Dash", desc: "Dashed creative", styleClass: "hero-style-17", category: "light", previewHTML: `<h3>SketchDash</h3><p>Design prototype fast</p><a href="#" class="mini-btn">Try demo</a>` },
    { name: "DotGrid", desc: "Subtle pattern", styleClass: "hero-style-18", category: "gradient", previewHTML: `<h3>DotGrid</h3><p>Playful background</p><a href="#" class="mini-btn">Learn more</a>` },
    { name: "Midnight Navy", desc: "Deep navy", styleClass: "hero-style-19", category: "dark", previewHTML: `<h3>Midnight Navy</h3><p>Confidence in code</p><a href="#" class="mini-btn dark-mini-btn">View docs</a>` },
    { name: "Golden Quill", desc: "Left accent", styleClass: "hero-style-20", category: "light", previewHTML: `<h3>Golden Quill</h3><p>Editorial elegance</p><a href="#" class="mini-btn">Read story</a>` },
    { name: "Centered Charm", desc: "Centered everything", styleClass: "hero-style-21", category: "light", previewHTML: `<h3>Centered Charm</h3><p>Balanced & clean</p><a href="#" class="mini-btn">Discover</a><i class="fas fa-arrow-right"></i>` },
    { name: "Pill Talk", desc: "Fully rounded", styleClass: "hero-style-22", category: "gradient", previewHTML: `<h3>Pill Talk UI</h3><p>Soft & friendly</p><a href="#" class="mini-btn">Get in touch</a>` },
    { name: "Space Between", desc: "Space layout", styleClass: "hero-style-23", category: "dark", previewHTML: `<span><h3>SpaceBetween</h3><p>Flexible hero</p></span><a href="#" class="mini-btn dark-mini-btn">Try</a>` },
    { name: "Frost Purple", desc: "Subtle gradient", styleClass: "hero-style-24", category: "gradient", previewHTML: `<h3>Frost Purple</h3><p>Dreamy & futuristic</p><a href="#" class="mini-btn">Explore</a>` },
    { name: "Drop Shadow King", desc: "Bold shadow", styleClass: "hero-style-25", category: "dark", previewHTML: `<h3>Shadow King</h3><p>Strong presence</p><a href="#" class="mini-btn dark-mini-btn">Join waitlist</a>` }
  ];

  const gridContainer = document.getElementById('headersGrid');
  
  function renderCards(filterType = 'all') {
    if (!gridContainer) return;
    gridContainer.innerHTML = '';
    heroesData.forEach((hero, idx) => {
      let categoryMatch = true;
      if (filterType === 'dark') categoryMatch = hero.category === 'dark';
      else if (filterType === 'light') categoryMatch = hero.category === 'light';
      else if (filterType === 'gradient') categoryMatch = hero.category === 'gradient';
      else categoryMatch = true;
      if (!categoryMatch) return;
      
      const card = document.createElement('div');
      card.className = 'hero-card';
      card.setAttribute('data-category', hero.category);
      // inner structure: preview + info
      card.innerHTML = `
        <div class="card-preview">
          <div class="hero-mini ${hero.styleClass}">
            ${hero.previewHTML}
          </div>
        </div>
        <div class="card-info">
          <span>${hero.name}</span>
          <i class="fas fa-arrow-right"></i>
        </div>
      `;
      gridContainer.appendChild(card);
    });
    // if no cards after filter? but we have all categories non-empty
    if (gridContainer.children.length === 0) {
      const msg = document.createElement('div');
      msg.style.textAlign = 'center';
      msg.style.width = '100%';
      msg.style.padding = '3rem';
      msg.innerText = '✨ No headers match this filter, try another ✨';
      gridContainer.appendChild(msg);
    }
  }

  // initial render all 25
  renderCards('all');
  
  // interactive filter buttons
  const filterBtns = document.querySelectorAll('.filter-btn');
  filterBtns.forEach(btn => {
    btn.addEventListener('click', function(e) {
      filterBtns.forEach(b => b.classList.remove('active'));
      this.classList.add('active');
      const filterVal = this.getAttribute('data-filter');
      renderCards(filterVal);
    });
  });
  
  // small interaction for demo: prevent default on any mini button clicks (just for demo)
  document.addEventListener('click', (e) => {
    if (e.target.matches('.mini-btn, .mini-btn.dark-mini-btn')) {
      e.preventDefault();
      // subtle feedback for demo
      const originalText = e.target.innerText;
      e.target.innerText = '✨ demo! ✨';
      setTimeout(() => { if(e.target) e.target.innerText = originalText; }, 800);
    }
  });
</script>
</body>
</html>