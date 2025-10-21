<?php
require_once 'config.php';

try {
    $data = [];
    
    // Home content
    $stmt = $pdo->prepare("SELECT * FROM home_content ORDER BY id LIMIT 1");
    $stmt->execute();
    $data['home'] = $stmt->fetch() ?: null;
    
    // About content
    $stmt = $pdo->prepare("SELECT * FROM about_content ORDER BY id LIMIT 1");
    $stmt->execute();
    $data['about'] = $stmt->fetch() ?: null;
    
    // Contact content
    $stmt = $pdo->prepare("SELECT * FROM contact_content ORDER BY id LIMIT 1");
    $stmt->execute();
    $data['contact'] = $stmt->fetch() ?: null;
    
    // Projects
    $stmt = $pdo->prepare("SELECT * FROM projects ORDER BY sort_order ASC, id DESC LIMIT 100");
    $stmt->execute();
    $data['projects'] = $stmt->fetchAll();
    
    // Services
    $stmt = $pdo->prepare("SELECT * FROM services ORDER BY id LIMIT 1");
    $stmt->execute();
    $data['services'] = $stmt->fetch() ?: null;
    
    // Vision content
    $stmt = $pdo->prepare("SELECT * FROM vision_content ORDER BY id LIMIT 1");
    $stmt->execute();
    $data['vision'] = $stmt->fetch() ?: null;
    
    // Mission content
    $stmt = $pdo->prepare("SELECT * FROM mission_content ORDER BY id LIMIT 1");
    $stmt->execute();
    $data['mission'] = $stmt->fetch() ?: null;
    
    $current_year = date('Y');
    
} catch (PDOException $e) {
    die('Database error: ' . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>PT. Superior Teknik Indonesia - Engineering Excellence</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <link href="static/css/style.css" rel="stylesheet">
  <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
  <style>
    /* Blok seleksi teks dan copy */
body {
  -webkit-user-select: none;
  -moz-user-select: none;
  -ms-user-select: none;
  user-select: none;
}

/* Blok drag gambar juga */
img {
  -webkit-user-drag: none;
  user-drag: none;
}

    /* Hero Section - Modern Glassmorphism Design */
    #hero {
      position: relative;
      height: 100vh;
      overflow: hidden;
      display: flex;
      align-items: center;
      justify-content: center;
      color: #fff;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    #hero .parallax-layer {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-position: center;
      background-repeat: no-repeat;
      background-size: cover;
      will-change: transform;
    }

    #hero .layer-bg {
      background-image: url('pictures/hero-background.jpg');
      background-attachment: fixed;
      filter: brightness(0.4) blur(1px);
      z-index: 1;
      transform: scale(1.05);
      transition: transform 0.5s ease;
    }

    #hero .layer-overlay {
      background: linear-gradient(135deg, rgba(102, 126, 234, 0.8) 0%, rgba(118, 75, 162, 0.9) 100%);
      z-index: 2;
    }

    #hero .content {
      position: relative;
      z-index: 3;
      max-width: 1200px;
      width: 100%;
      padding: 0 2rem;
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 4rem;
      align-items: center;
      animation: fadeDown 1.5s ease;
    }

    /* Left Content Block */
    .hero-left {
      text-align: left;
    }

    .hero-logo-card {
      background: rgba(255, 255, 255, 0.1);
      backdrop-filter: blur(20px);
      border: 1px solid rgba(255, 255, 255, 0.2);
      border-radius: 20px;
      padding: 2rem;
      margin-bottom: 2rem;
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
      transition: all 0.3s ease;
    }

    .hero-logo-card:hover {
      transform: translateY(-3px);
      box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
      background: rgba(255, 255, 255, 0.15);
    }

    .hero-logo-initials {
      display: flex;
      gap: 1rem;
      margin-bottom: 1.5rem;
    }

    .logo-square {
      width: 60px;
      height: 60px;
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 2rem;
      font-weight: bold;
      color: white;
    }

    .logo-square.s {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    .logo-square.t {
      background: linear-gradient(135deg, #20c997 0%, #17a2b8 100%);
    }

    .hero-company-name {
      font-size: 3rem;
      font-weight: 800;
      line-height: 1.1;
      margin-bottom: 0.5rem;
    }

    .hero-company-name .superior {
      background: linear-gradient(135deg, #20c997 0%, #17a2b8 100%);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
    }

    .hero-company-name .indonesia {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
    }

    .hero-tagline {
      font-size: 1.2rem;
      color: rgba(255, 255, 255, 0.9);
      margin-bottom: 2rem;
      font-weight: 300;
    }

    .hero-buttons {
      display: flex;
      gap: 1rem;
      flex-wrap: wrap;
    }

    .btn-hero-primary {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      border: none;
      color: white;
      padding: 12px 30px;
      border-radius: 50px;
      font-weight: 600;
      text-decoration: none;
      transition: all 0.3s ease;
      box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
    }

    .btn-hero-primary:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(102, 126, 234, 0.6);
      color: white;
    }

    .btn-hero-secondary {
      background: transparent;
      border: 2px solid rgba(255, 255, 255, 0.8);
      color: white;
      padding: 10px 30px;
      border-radius: 50px;
      font-weight: 600;
      text-decoration: none;
      transition: all 0.3s ease;
    }

    .btn-hero-secondary:hover {
      background: rgba(255, 255, 255, 0.1);
      border-color: white;
      color: white;
      transform: translateY(-2px);
    }

    /* Right Content Block */
    .hero-right {
      display: flex;
      justify-content: center;
      align-items: center;
    }

    .hero-large-card {
      background: rgba(255, 255, 255, 0.1);
      backdrop-filter: blur(20px);
      border: 1px solid rgba(255, 255, 255, 0.2);
      border-radius: 30px;
      padding: 4rem 3rem;
      text-align: center;
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
      position: relative;
      overflow: hidden;
      transition: all 0.3s ease;
    }

    .hero-large-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 12px 40px rgba(0, 0, 0, 0.2);
    }

    .hero-large-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: linear-gradient(45deg, transparent, rgba(255, 255, 255, 0.1), transparent);
      transform: translateX(-100%);
      transition: transform 0.6s ease;
    }

    .hero-large-card:hover::before {
      transform: translateX(100%);
    }

    .hero-large-s {
      font-size: 8rem;
      font-weight: 900;
      color: rgba(255, 255, 255, 0.3);
      line-height: 1;
      margin-bottom: 1rem;
      text-shadow: 0 0 30px rgba(255, 255, 255, 0.2);
    }

    .hero-large-text {
      font-size: 1.5rem;
      font-weight: 600;
      color: white;
      letter-spacing: 2px;
      text-transform: uppercase;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
      #hero .content {
        grid-template-columns: 1fr;
        gap: 2rem;
        text-align: center;
        padding: 0 1rem;
      }
      
      .hero-left {
        text-align: center;
      }
      
      .hero-company-name {
        font-size: 2.5rem;
      }
      
      .hero-large-s {
        font-size: 6rem;
      }
      
      .hero-buttons {
        justify-content: center;
      }
      
      .hero-large-card {
        padding: 3rem 2rem;
      }
      
      .hero-logo-card {
        padding: 1.5rem;
      }
    }

    @media (max-width: 480px) {
      .hero-company-name {
        font-size: 2rem;
      }
      
      .hero-large-s {
        font-size: 4rem;
      }
      
      .hero-large-text {
        font-size: 1.2rem;
      }
      
      .hero-buttons {
        flex-direction: column;
        align-items: center;
      }
      
      .btn-hero-primary,
      .btn-hero-secondary {
        width: 100%;
        max-width: 250px;
        text-align: center;
      }
    }

    @keyframes fadeDown {
      from { opacity: 0; transform: translateY(-20px); }
      to { opacity: 1; transform: translateY(0); }
    }

    /* Sections */
    section {
      padding: 80px 0;
      min-height: 60vh;
      position: relative;
      overflow: hidden;
    }

    section h2 {
      font-weight: 700;
      text-transform: uppercase;
      margin-bottom: 30px;
      position: relative;
      z-index: 1;
      letter-spacing: 1px;
    }

    /* Project Section Styles */
    .project-card {
      border: none;
      border-radius: 15px;
      box-shadow: 0 5px 15px rgba(0,0,0,0.1);
      transition: transform 0.3s ease, box-shadow 0.3s ease;
      overflow: hidden;
    }
    
    .project-card:hover {
      transform: translateY(-8px);
      box-shadow: 0 15px 30px rgba(0,0,0,0.2);
    }
    
    .project-card .card-img-top {
      height: 200px;
      object-fit: cover;
      transition: transform 0.3s ease;
    }
    
    .project-card:hover .card-img-top {
      transform: scale(1.05);
    }
    
    .project-card .card-body {
      padding: 1.5rem;
    }
    
    .project-card .card-title {
      font-size: 1.1rem;
      font-weight: 600;
      color: #002d72;
      margin-bottom: 0.5rem;
    }
    
    .project-card .card-text {
      font-size: 0.9rem;
      color: #666;
      line-height: 1.5;
    }

    /* Buttons */
    #scrollTop {
      position: fixed;
      bottom: 30px;
      right: 30px;
      background: #003399;
      color: #fff;
      border: none;
      border-radius: 50%;
      width: 50px;
      height: 50px;
      display: none;
      align-items: center;
      justify-content: center;
      box-shadow: 0 4px 10px rgba(0,0,0,0.3);
      cursor: pointer;
      z-index: 999;
      transition: background 0.3s ease;
    }

    #scrollTop:hover {
      background: #002266;
    }

    #scrollTop.show {
      display: flex;
    }

    #wa-btn {
      position: fixed;
      bottom: 30px;
      left: 30px;
      background: #25D366;
      color: white;
      border: none;
      border-radius: 50%;
      width: 60px;
      height: 60px;
      display: flex;
      align-items: center;
      justify-content: center;
      box-shadow: 0 4px 15px rgba(37, 211, 102, 0.4);
      cursor: pointer;
      z-index: 999;
      transition: all 0.3s ease;
      text-decoration: none;
      font-size: 24px;
    }

    #wa-btn:hover {
      background: #128C7E;
      transform: scale(1.1);
      color: white;
    }

    /* Services Section Styles */
    .service-title {
      text-align: center;
      margin-bottom: 3rem;
    }
    
    .title-prefix {
      display: block;
      font-size: 1rem;
      color: #666;
      margin-bottom: 0.5rem;
    }
    
    .title-main {
      display: block;
      font-size: 2.5rem;
      font-weight: 700;
      color: #002d72;
      text-transform: uppercase;
    }
    
    .service-image {
      margin-bottom: 1rem;
      position: relative;
      z-index: 10;
      overflow: visible;
    }
    
    .service-image img {
      border-radius: 10px;
      box-shadow: 0 5px 15px rgba(0,0,0,0.1);
      transition: transform 0.3s ease;
      display: block !important;
      width: 100% !important;
      height: auto !important;
      position: relative;
      z-index: 15;
      opacity: 1 !important;
      visibility: visible !important;
    }
    
    .service-image img:hover {
      transform: scale(1.05);
    }
    
    .service-categories {
      margin-top: 2rem;
    }
    
    .service-category-item {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      padding: 2rem 1.5rem;
      border-radius: 15px;
      text-align: center;
      height: 100%;
      transition: transform 0.3s ease;
    }
    
    .service-category-item:hover {
      transform: translateY(-5px);
    }
    
    .service-category-item h4 {
      font-size: 1.5rem;
      font-weight: 700;
      margin-bottom: 1rem;
      color: white;
    }
    
    .service-category-item ul {
      list-style: none;
      padding: 0;
      margin: 0;
    }
    
    .service-category-item li {
      padding: 0.3rem 0;
      font-size: 0.9rem;
      opacity: 0.9;
    }

    /* Vision & Mission Section Styles */
    .vision-section {
      background: #f8f9fa;
    }
    
    .vision-title {
      font-size: 1.8rem;
      font-weight: 600;
      color: #002d72;
      line-height: 1.6;
      margin: 0;
    }
    
    .vision-image img {
      border-radius: 15px;
      box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    }

    .mission-section {
      background: white;
    }
    
    .mission-flow {
      display: flex;
      justify-content: center;
      flex-wrap: wrap;
      gap: 1rem;
    }
    
    .mission-item {
      flex: 1;
      min-width: 200px;
      max-width: 250px;
    }
    
    .mission-box {
      background: white;
      border-radius: 15px;
      padding: 2rem 1.5rem;
      text-align: center;
      box-shadow: 0 5px 15px rgba(0,0,0,0.1);
      transition: transform 0.3s ease, box-shadow 0.3s ease;
      border-top: 4px solid;
      height: 100%;
      display: flex;
      flex-direction: column;
      justify-content: center;
    }
    
    .mission-box:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 25px rgba(0,0,0,0.15);
    }
    
    .mission-box.quality {
      border-top-color: #007bff;
    }
    
    .mission-box.competitiveness {
      border-top-color: #6f42c1;
    }
    
    .mission-box.concreteness {
      border-top-color: #6f42c1;
    }
    
    .mission-box.punctuality {
      border-top-color: #6f42c1;
    }
    
    .mission-box.satisfaction {
      border-top-color: #6c757d;
    }
    
    .mission-box h4 {
      font-size: 1.3rem;
      font-weight: 700;
      color: #002d72;
      margin-bottom: 0.5rem;
    }
    
    .mission-box p {
      font-size: 0.9rem;
      color: #666;
      margin: 0;
      line-height: 1.4;
    }
    
    @media (max-width: 768px) {
      .mission-flow {
        flex-direction: column;
        align-items: center;
      }
      
      .mission-item {
        max-width: 100%;
        width: 100%;
      }
    }

    /* Projects Section Styles */
    .projects-container {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
      gap: 2rem;
      max-width: 1200px;
      margin: 0 auto;
    }
    
    .project-card {
      background: white;
      border-radius: 15px;
      box-shadow: 0 5px 15px rgba(0,0,0,0.1);
      transition: transform 0.3s ease, box-shadow 0.3s ease;
      overflow: hidden;
      display: flex;
      flex-direction: column;
    }
    
    .project-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 25px rgba(0,0,0,0.15);
    }
    
    .project-image {
      width: 100%;
      height: 200px;
      object-fit: cover;
      border-radius: 15px 15px 0 0;
    }
    
    .project-content {
      padding: 1.5rem;
      flex: 1;
      display: flex;
      flex-direction: column;
    }
    
    .project-title {
      font-size: 1.1rem;
      font-weight: 600;
      color: #002d72;
      margin-bottom: 0.5rem;
      line-height: 1.3;
    }
    
    .project-client {
      font-size: 0.9rem;
      color: #666;
      margin-bottom: 0.5rem;
      display: flex;
      align-items: center;
    }
    
    .project-description {
      font-size: 0.9rem;
      color: #666;
      line-height: 1.4;
      margin: 0;
      flex: 1;
    }
    
    @media (max-width: 768px) {
      .projects-container {
        grid-template-columns: 1fr;
        gap: 1.5rem;
      }
    }

    /* Contact Section Styles */
    .contact-card {
      background: white;
      border-radius: 15px;
      padding: 2rem 1.5rem;
      text-align: center;
      box-shadow: 0 5px 15px rgba(0,0,0,0.1);
      transition: transform 0.3s ease, box-shadow 0.3s ease;
      height: 100%;
      border: none;
    }
    
    .contact-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 25px rgba(0,0,0,0.15);
    }
    
    .contact-icon {
      width: 60px;
      height: 60px;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 1rem;
      color: white;
      font-size: 1.5rem;
    }
    
    .contact-name {
      font-size: 1.2rem;
      font-weight: 600;
      color: #002d72;
      margin-bottom: 0.5rem;
    }
    
    .contact-role {
      color: #666;
      font-size: 0.9rem;
      margin-bottom: 1rem;
    }
    
    .contact-link {
      display: inline-block;
      padding: 0.5rem 1rem;
      border-radius: 25px;
      text-decoration: none;
      font-weight: 500;
      transition: all 0.3s ease;
    }
    
    .whatsapp-link {
      background: #25D366;
      color: white;
    }
    
    .whatsapp-link:hover {
      background: #128C7E;
      color: white;
      transform: scale(1.05);
    }
    
    .email-link {
      background: #007bff;
      color: white;
    }
    
    .email-link:hover {
      background: #0056b3;
      color: white;
      transform: scale(1.05);
    }
  </style>
</head>
<body>
  <!-- Navigation -->
  <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
    <div class="container">
      <a class="navbar-brand" href="#home">
        <img src="pictures/logo.png" alt="PT Superior Teknik Indotama" height="50">
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item">
            <a class="nav-link" href="#hero">HOME</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#about">ABOUT</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#services">SERVICES</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#projects">PROJECTS</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#vision">VISION</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#contact">CONTACT</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="login.php">
              <i class="fas fa-lock me-1"></i>ADMIN
            </a>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <!-- Hero Section -->
  <section id="hero">
    <div class="parallax-layer layer-bg"></div>
    <div class="parallax-layer layer-overlay"></div>
    <div class="content" data-aos="fade-up" data-aos-duration="1000">
      <!-- Left Content Block -->
      <div class="hero-left">
        <!--<div class="hero-logo-card">
          <div class="hero-logo-initials">
            <div class="logo-square s">S</div>
            <div class="logo-square t">T</div>
          </div>
        </div>-->  
          <div class="hero-company-name">
            <span class="superior"><?php echo htmlspecialchars($data['home']['hero_title2'] ?? 'Engineering Excellence Since 2022'); ?></span><br><br>
            <span class="indonesia"> </span>
          </div>
          <div class="hero-tagline">
            <?php echo htmlspecialchars($data['home']['hero_subtitle'] ?? 'Engineering Excellence Since 2022'); ?>
          </div>
        
        <div class="hero-buttons">
          <?php if ($data['home']['hero_button_text']): ?>
          <a href="<?php echo htmlspecialchars($data['home']['hero_button_link'] ?? '#contact'); ?>" class="btn-hero-primary">
            <?php echo htmlspecialchars($data['home']['hero_button_text']); ?>
          </a>
          <?php else: ?>
          <a href="#about" class="btn-hero-primary">Learn More</a>
          <?php endif; ?>
          <a href="#contact" class="btn-hero-secondary">Contact Us</a>
        </div>
      </div>
      
      <!-- Right Content Block -->
      <div class="hero-right">
        <div class="hero-large-card">
          <div class="hero-large-s"><img src="pictures/logo.png" alt="PT Superior Teknik Indonesia" height="150"></div>
            <div class="hero-company-name">  
              <div class="hero-large-text">  
                <span class="superior">
                  <?php echo htmlspecialchars($data['home']['hero_title1'] ?? 'PT Superior Teknik Indotama'); ?>
                </span>
              </div>
            </div>
        </div>
      </div>
    </div>
  </section>

  <!-- About Section -->
  <section id="about" class="company-profile-section py-5 bg-white" data-aos="fade-up">
    <div class="container">
      <div class="row align-items-center">
        <div class="col-lg-6 mb-4 mb-lg-0" data-aos="fade-right">
          <div class="profile-image position-relative">
            <?php if ($data['about'] && $data['about']['images']): ?>
            <img 
              src="pictures/<?php echo htmlspecialchars($data['about']['images']); ?>"
              alt="Company Profile"
              class="img-fluid rounded shadow"
              loading="lazy"
              data-aos="fade-up" data-aos-delay="100">
            <?php else: ?>
            <img 
              src="pictures/company-profile.png"
              alt="Company Profile"
              class="img-fluid rounded shadow"
              loading="lazy"
              data-aos="fade-up" data-aos-delay="100">
            <?php endif; ?>
          </div>
        </div>
        <div class="col-lg-6" data-aos="fade-left">
          <div class="profile-content">
            <h2 class="section-title mb-3" data-aos="fade-up"><?php echo htmlspecialchars($data['about']['title'] ?? 'Tentang Kami'); ?></h2>
            <p class="profile-description mb-4" data-aos="fade-up" data-aos-delay="100">
              <?php echo nl2br(htmlspecialchars($data['about']['description'] ?? 'Deskripsi tentang perusahaan akan ditampilkan di sini.')); ?>
            </p>
            <div class="d-flex gap-4 flex-wrap">
              <div class="text-center" data-aos="fade-up" data-aos-delay="200">
                <h4 class="fw-bold text-primary mb-0">50+</h4>
                <small>Projects Completed</small>
              </div>
              <div class="text-center" data-aos="fade-up" data-aos-delay="300">
                <h4 class="fw-bold text-primary mb-0">3+</h4>
                <small>Years Experience</small>
              </div>
              <div class="text-center" data-aos="fade-up" data-aos-delay="400">
                <h4 class="fw-bold text-primary mb-0">100%</h4>
                <small>Client Satisfaction</small>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Services Section -->
  <section id="services" class="bg-light" data-aos="fade-up">
    <div class="container">
      <!-- Mechanical System -->
      <div class="service-category mb-5">
        <div class="row">
          <div class="col-12" data-aos="fade-up">
            <h2 class="service-title">
              <span class="title-prefix">WHAT WE DO :</span>
              <span class="title-main">MECHANICAL SYSTEM</span>
            </h2>
          </div>
        </div>
        <div class="row g-4 mb-4">
          <div class="col-lg-4" data-aos="fade-up" data-aos-delay="100">
            <div class="service-image">
              <img src="<?php echo htmlspecialchars($data['services']['mechanical_image_url'] ?? 'pictures/mechanical-1.png'); ?>" 
                   alt="HVAC Systems - PT. Superior Teknik Indonesia" 
                   class="img-fluid rounded" 
                   loading="lazy"
                   width="400"
                   height="300">
            </div>
          </div>
          <div class="col-lg-4" data-aos="fade-up" data-aos-delay="200">
            <div class="service-image">
              <img src="<?php echo htmlspecialchars($data['services']['mechanical_image2'] ?? 'pictures/mechanical-2.png'); ?>" 
                   alt="Fire Fighting Systems - PT. Superior Teknik Indonesia" 
                   class="img-fluid rounded" 
                   loading="lazy"
                   width="400"
                   height="300">
            </div>
          </div>
          <div class="col-lg-4" data-aos="fade-up" data-aos-delay="300">
            <div class="service-image">
              <img src="<?php echo htmlspecialchars($data['services']['mechanical_image3'] ?? 'pictures/mechanical-3.png'); ?>" 
                   alt="Utility & Process Systems - PT. Superior Teknik Indonesia" 
                   class="img-fluid rounded" 
                   loading="lazy"
                   width="400"
                   height="300">
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-12">
            <div class="service-categories">
              <div class="row g-3">
                <div class="col-lg-4" data-aos="fade-up" data-aos-delay="400">
                  <div class="service-category-item hvac">
                    <h4>HVAC</h4>
                    <ul>
                      <li>VRV System</li>
                      <li>Chilled Water System</li>
                      <li>Ductworks</li>
                      <li>etc.</li>
                    </ul>
                  </div>
                </div>
                <div class="col-lg-4" data-aos="fade-up" data-aos-delay="500">
                  <div class="service-category-item fire-fighting">
                    <h4>Fire Fighting</h4>
                    <ul>
                      <li>Sprinklers</li>
                      <li>Hydrants</li>
                      <li>Fire Suppression</li>
                      <li>etc.</li>
                    </ul>
                  </div>
                </div>
                <div class="col-lg-4" data-aos="fade-up" data-aos-delay="600">
                  <div class="service-category-item utility">
                    <h4>Utility & Process</h4>
                    <ul>
                      <li>Gas piping</li>
                      <li>Compressed Air</li>
                      <li>Chiller, Boiler</li>
                      <li>etc.</li>
                    </ul>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Electrical System -->
      <div class="service-category">
        <div class="row">
          <div class="col-12" data-aos="fade-up">
            <h2 class="service-title">
              <span class="title-prefix">WHAT WE DO :</span>
              <span class="title-main">ELECTRICAL SYSTEM</span>
            </h2>
          </div>
        </div>
        <div class="row g-4 mb-4">
          <div class="col-lg-4" data-aos="fade-up" data-aos-delay="100">
            <div class="service-image">
              <img src="<?php echo htmlspecialchars($data['services']['electrical_image_url'] ?? 'pictures/electrical-1.png'); ?>" 
                   alt="Power Distribution - PT. Superior Teknik Indonesia" 
                   class="img-fluid rounded" 
                   loading="lazy"
                   width="400"
                   height="300">
            </div>
          </div>
          <div class="col-lg-4" data-aos="fade-up" data-aos-delay="200">
            <div class="service-image">
              <img src="<?php echo htmlspecialchars($data['services']['electrical_image2'] ?? 'pictures/electrical-2.png'); ?>" 
                   alt="Low Current Systems - PT. Superior Teknik Indonesia" 
                   class="img-fluid rounded" 
                   loading="lazy"
                   width="400"
                   height="300">
            </div>
          </div>
          <div class="col-lg-4" data-aos="fade-up" data-aos-delay="300">
            <div class="service-image">
              <img src="<?php echo htmlspecialchars($data['services']['electrical_image3'] ?? 'pictures/electrical-3.png'); ?>" 
                   alt="General Electrical - PT. Superior Teknik Indonesia" 
                   class="img-fluid rounded" 
                   loading="lazy"
                   width="400"
                   height="300">
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-12">
            <div class="service-categories">
              <div class="row g-3">
                <div class="col-lg-4" data-aos="fade-up" data-aos-delay="400">
                  <div class="service-category-item power">
                    <h4>Power Distribution</h4>
                    <ul>
                      <li>MV/LV panels</li>
                      <li>Cabling</li>
                    </ul>
                  </div>
                </div>
                <div class="col-lg-4" data-aos="fade-up" data-aos-delay="500">
                  <div class="service-category-item low-current">
                    <h4>Low Current</h4>
                    <ul>
                      <li>BMS</li>
                      <li>Fire Alarms</li>
                    </ul>
                  </div>
                </div>
                <div class="col-lg-4" data-aos="fade-up" data-aos-delay="600">
                  <div class="service-category-item general">
                    <h4>General Electrical</h4>
                    <ul>
                      <li>Lighting</li>
                      <li>Wiring</li>
                      <li>Electrical Instruments</li>
                    </ul>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Projects Section -->
  <section id="projects" class="py-5 mb-5" data-aos="fade-up" style="margin-bottom: 4rem !important;">
    <div class="container">
      <h2 class="text-center mb-5" data-aos="fade-up">COMPLETED AND ONGOING PROJECTS</h2>
      <?php if ($data['projects']): ?>
      <?php 
      $active_projects = array_filter($data['projects'], function($project) {
        return $project['status'] == 1;
      });
      $projects_array = array_values($active_projects);
      $total_projects = count($projects_array);
      ?>
      <div class="projects-container">
        <?php foreach ($projects_array as $index => $project): ?>
        <div class="project-card" data-aos="fade-up" data-aos-delay="<?php echo ($index + 1) * 100; ?>">
          <?php if ($project['image_url']): ?>
          <img src="<?php echo htmlspecialchars($project['image_url']); ?>" 
               alt="<?php echo htmlspecialchars($project['title']); ?>" 
               class="project-image" 
               loading="lazy">
          <?php else: ?>
          <img src="pictures/placeholder.png" 
               alt="<?php echo htmlspecialchars($project['title']); ?>" 
               class="project-image" 
               loading="lazy">
          <?php endif; ?>
          <div class="project-content">
            <h5 class="project-title"><?php echo htmlspecialchars($project['title']); ?></h5>
            <?php if ($project['client']): ?>
            <p class="project-client">
              <i class="fas fa-building me-1"></i><?php echo htmlspecialchars($project['client']); ?>
            </p>
            <?php endif; ?>
            <p class="project-description"><?php echo htmlspecialchars($project['description']); ?></p>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>
    </div>
  </section>

  <!-- Vision Section -->
  <section id="vision" class="py-5 bg-light">
    <div class="container">
      <!-- Vision Content -->
      <div class="row align-items-center mb-5">
        <div class="col-lg-6" data-aos="fade-right">
          <h2 class="mb-4">THE VISION</h2>
          <p class="lead"><?php echo htmlspecialchars($data['vision']['description'] ?? 'To be the most trusted and preferred partner for engineering and construction solutions in Indonesia.'); ?></p>
        </div>
        <div class="col-lg-6" data-aos="fade-left">
          <?php if ($data['vision'] && $data['vision']['image_url']): ?>
          <img src="<?php echo htmlspecialchars($data['vision']['image_url']); ?>" 
               alt="Vision" class="img-fluid rounded shadow" loading="lazy">
          <?php else: ?>
          <img src="pictures/vision-placeholder.png" 
               alt="Vision" class="img-fluid rounded shadow" loading="lazy">
          <?php endif; ?>
        </div>
      </div>

      <!-- Mission Content -->
      <div class="row">
        <div class="col-12">
          <h2 class="text-center mb-5">THE MISSION</h2>
          <div class="mission-flow">
            <div class="mission-item" data-aos="fade-up" data-aos-delay="100">
              <div class="mission-box quality">
                <h4>Quality</h4>
                <p><?php echo htmlspecialchars($data['mission']['quality'] ?? 'Deliver high value'); ?></p>
              </div>
            </div>
            <div class="mission-item" data-aos="fade-up" data-aos-delay="200">
              <div class="mission-box competitiveness">
                <h4>Competitiveness</h4>
                <p><?php echo htmlspecialchars($data['mission']['competitiveness'] ?? 'Best market pricing'); ?></p>
              </div>
            </div>
            <div class="mission-item" data-aos="fade-up" data-aos-delay="300">
              <div class="mission-box concreteness">
                <h4>Concreteness</h4>
                <p><?php echo htmlspecialchars($data['mission']['concreteness'] ?? 'Sustainable growth'); ?></p>
              </div>
            </div>
            <div class="mission-item" data-aos="fade-up" data-aos-delay="400">
              <div class="mission-box punctuality">
                <h4>Punctuality</h4>
                <p><?php echo htmlspecialchars($data['mission']['punctuality'] ?? 'On-time delivery'); ?></p>
              </div>
            </div>
            <div class="mission-item" data-aos="fade-up" data-aos-delay="500">
              <div class="mission-box satisfaction">
                <h4>Satisfaction</h4>
                <p><?php echo htmlspecialchars($data['mission']['satisfaction'] ?? 'Client-focused outcomes'); ?></p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Contact Section -->
  <section id="contact" class="py-5 bg-light">
    <div class="container">
      <?php if ($data['contact']): ?>
      <h2 class="text-center mb-5"><?php echo htmlspecialchars($data['contact']['title'] ?? 'Hubungi Kami'); ?></h2>
      <div class="row justify-content-center">
        <!-- Contact Person 1 -->
        <?php if ($data['contact']['person1_name'] && $data['contact']['person1_status'] == 1): ?>
        <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="100">
          <div class="contact-card">
            <div class="contact-icon">
              <i class="fas fa-user"></i>
            </div>
            <h5 class="contact-name"><?php echo htmlspecialchars($data['contact']['person1_name']); ?></h5>
            <p class="contact-role">Contact Person</p>
            <a href="https://wa.me/<?php echo str_replace(['+', '-', ' '], '', $data['contact']['person1_phone']); ?>" 
               class="contact-link whatsapp-link" 
               target="_blank" 
               rel="noopener noreferrer">
              <i class="fab fa-whatsapp"></i> <?php echo htmlspecialchars($data['contact']['person1_phone']); ?>
            </a>
          </div>
        </div>
        <?php endif; ?>
        
        <!-- Contact Person 2 -->
        <?php if ($data['contact']['person2_name'] && $data['contact']['person2_status'] == 1): ?>
        <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="200">
          <div class="contact-card">
            <div class="contact-icon">
              <i class="fas fa-user"></i>
            </div>
            <h5 class="contact-name"><?php echo htmlspecialchars($data['contact']['person2_name']); ?></h5>
            <p class="contact-role">Contact Person</p>
            <a href="https://wa.me/<?php echo str_replace(['+', '-', ' '], '', $data['contact']['person2_phone']); ?>" 
               class="contact-link whatsapp-link" 
               target="_blank" 
               rel="noopener noreferrer">
              <i class="fab fa-whatsapp"></i> <?php echo htmlspecialchars($data['contact']['person2_phone']); ?>
            </a>
          </div>
        </div>
        <?php endif; ?>
        
        <!-- Email -->
        <?php if ($data['contact']['email']): ?>
        <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="300">
          <div class="contact-card">
            <div class="contact-icon">
              <i class="fas fa-envelope"></i>
            </div>
            <h5 class="contact-name">Email</h5>
            <p class="contact-role">General Information</p>
            <a href="mailto:<?php echo htmlspecialchars($data['contact']['email']); ?>?subject=Informasi Layanan PT Superior Teknik Indonesia" 
               class="contact-link email-link" 
               target="_blank" 
               rel="noopener noreferrer">
              <i class="fas fa-envelope"></i> <?php echo htmlspecialchars($data['contact']['email']); ?>
            </a>
          </div>
        </div>
        <?php endif; ?>
      </div>
      <?php endif; ?>
    </div>
  </section>

  <!-- Footer -->
  <footer class="bg-dark text-light py-4">
    <div class="container">
      <div class="row">
        <div class="col-md-6">
          <p>&copy; <?php echo $current_year; ?> PT. Superior Teknik Indonesia. All rights reserved.</p>
        </div>
        <div class="col-md-6 text-end">
          <p>Engineering Excellence</p>
        </div>
      </div>
    </div>
  </footer>

  <!-- WhatsApp Button -->
  <?php if ($data['contact'] && $data['contact']['person1_phone']): ?>
  <a href="https://wa.me/<?php echo str_replace(['+', '-', ' '], '', $data['contact']['person1_phone']); ?>?text=<?php echo urlencode($data['contact']['text_wa'] ?? 'Halo PT Superior Teknik Indonesia'); ?>" 
     id="wa-btn" target="_blank">
    <i class="fab fa-whatsapp"></i>
  </a>
  <?php endif; ?>

  <!-- Scroll Top Button -->
  <button id="scrollTop"><i class="fas fa-arrow-up"></i></button>

  <!-- Scripts -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
  <script src="https://unpkg.com/scrollreveal"></script>
  <script>
    AOS.init();

    // Navbar scroll effect
    const navbar = document.querySelector('.navbar');
    window.addEventListener('scroll', () => {
      navbar.classList.toggle('scrolled', window.scrollY > 50);
    });

    // Scroll Top
    const scrollTopBtn = document.getElementById('scrollTop');
    window.addEventListener('scroll', () => {
      scrollTopBtn.classList.toggle('show', window.scrollY > 300);
    });
    scrollTopBtn.addEventListener('click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));

    // Scroll Reveal
    ScrollReveal().reveal('section', {
      distance: '40px',
      duration: 1000,
      easing: 'ease-out',
      origin: 'bottom',
      interval: 200,
      opacity: 0,
      scale: 0.98
    });

    // Active link on scroll
    const sections = document.querySelectorAll('section[id]');
    const navLinks = document.querySelectorAll('.navbar-nav .nav-link');
    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        const id = entry.target.getAttribute('id');
        const link = document.querySelector(`.navbar-nav .nav-link[href="#${id}"]`);
        if (entry.isIntersecting) {
          navLinks.forEach(l => l.classList.remove('active'));
          if (link) link.classList.add('active');
        }
      });
    }, { rootMargin: "-30% 0px -70% 0px" });
    sections.forEach(section => observer.observe(section));

    // Lazyload effect
    document.addEventListener("DOMContentLoaded", () => {
      const lazyImages = document.querySelectorAll('img[loading="lazy"]');
      lazyImages.forEach(img => {
        img.addEventListener('load', () => img.classList.add('lazyloaded'));
      });
    });
  </script>
<!-- Anti Copy & Inspect Protection -->
<script>
  // Nonaktifkan klik kanan
  document.addEventListener('contextmenu', function(e) {
    e.preventDefault();
  });

  // Nonaktifkan shortcut keyboard untuk inspect dan view source
  document.onkeydown = function(e) {
    if (e.keyCode == 123) { // F12
      return false;
    }
    if (e.ctrlKey && e.shiftKey && 
        (e.keyCode == 'I'.charCodeAt(0) || 
         e.keyCode == 'J'.charCodeAt(0) || 
         e.keyCode == 'C'.charCodeAt(0))) {
      return false;
    }
    if (e.ctrlKey && (e.keyCode == 'U'.charCodeAt(0))) {
      return false;
    }
  };

  // Deteksi developer tools terbuka
  (function() {
    const devtools = /./;
    devtools.toString = function() { this.opened = true; };
    setInterval(function() {
      const before = new Date();
      console.profile();
      console.profileEnd();
      if (before - new Date() > 100 || devtools.opened) {
        document.body.innerHTML = "<h1 style='text-align:center;margin-top:20%;color:red'>❌ Developer Tools tidak diizinkan ❌</h1>";
      }
    }, 1000);
  })();
</script>

</body>
</html>
