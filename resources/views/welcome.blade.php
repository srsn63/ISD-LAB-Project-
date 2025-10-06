<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Lalon Airport - Your Gateway to the World</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    body {margin:0;font-family:Arial, sans-serif;background:#f4f4f4;color:#333;}
    header {position:fixed;top:0;left:0;width:100%;z-index:1000;transition:0.3s;padding:15px 0;}
    .navbar {display:flex;justify-content:space-between;align-items:center;width:90%;margin:auto;}
    .logo {font-size:24px;font-weight:bold;color:white;}
    .nav-links {display:flex;gap:20px;list-style:none;}
    .nav-links li a {color:white;text-decoration:none;font-weight:bold;}
    .menu-toggle {display:none;flex-direction:column;cursor:pointer;}
    .menu-toggle span {width:25px;height:3px;background:white;margin:3px 0;}
    header.scrolled {background:rgba(0,0,0,0.8);}
    .hero {height:100vh;background:linear-gradient(to right,#00416A,#E4E5E6);display:flex;flex-direction:column;align-items:center;justify-content:center;text-align:center;color:white;position:relative;}
    .hero h1 {font-size:48px;margin:0;}
    .hero p {font-size:20px;margin:15px 0;}
    .search-box {margin-top:20px;}
    .search-box input {padding:10px;border:none;border-radius:5px;width:250px;}
    .search-box button {padding:10px 20px;border:none;border-radius:5px;background:#00416A;color:white;cursor:pointer;}
    .clouds {position:absolute;top:0;left:0;width:100%;height:100%;overflow:hidden;z-index:0;}
    .clouds img {position:absolute;width:200px;opacity:0.6;animation:moveclouds 60s linear infinite;}
    @keyframes moveclouds {0%{left:-200px;}100%{left:100%;}}
    section {padding:80px 20px;text-align:center;}
    .section-title {font-size:32px;margin-bottom:20px;}
    .quick-actions {display:flex;flex-wrap:wrap;justify-content:center;gap:20px;}
    .action-card {background:white;padding:20px;border-radius:10px;box-shadow:0 5px 15px rgba(0,0,0,0.1);width:220px;}
    .action-card i {font-size:40px;margin-bottom:10px;color:#00416A;}
    .stats {display:flex;flex-wrap:wrap;justify-content:center;gap:30px;margin-top:40px;}
    .stat-card {background:#00416A;color:white;padding:20px;border-radius:10px;width:200px;}
    .features {display:grid;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));gap:20px;margin-top:40px;}
    .feature-item {background:white;padding:20px;border-radius:10px;box-shadow:0 5px 15px rgba(0,0,0,0.1);}
    .newsletter input {padding:10px;width:250px;border:none;border-radius:5px;}
    .newsletter button {padding:10px 20px;border:none;border-radius:5px;background:#00416A;color:white;margin-left:10px;cursor:pointer;}
    footer {background:#333;color:white;padding:40px 20px;text-align:center;}
    footer .footer-sections {display:flex;flex-wrap:wrap;justify-content:center;gap:40px;}
    footer .footer-section {max-width:200px;}
    .scroll-top {position:fixed;bottom:20px;right:20px;background:#00416A;color:white;border:none;padding:10px 15px;border-radius:50%;cursor:pointer;display:none;}
    .scroll-top.active {display:block;}
    @media(max-width:768px){
      .nav-links{display:none;flex-direction:column;position:absolute;top:60px;right:20px;background:#00416A;padding:20px;border-radius:10px;}
      .nav-links.active{display:flex;}
      .menu-toggle{display:flex;}
    }
  </style>
</head>
<body>
  <header id="navbar">
    <div class="navbar">
      <div class="logo">Lalon Airport</div>
      <ul class="nav-links" id="navLinks">
        <li><a href="#flights">Flights</a></li>
        <li><a href="#booking">Booking</a></li>
        <li><a href="#status">Flight Status</a></li>
        <li><a href="#contact">Contact</a></li>
      </ul>
      <div class="menu-toggle" id="menuToggle">
        <span></span><span></span><span></span>
      </div>
    </div>
  </header>

  <section class="hero">
    <div class="clouds">
      <img src="https://i.ibb.co/8cP7v7T/cloud.png" style="top:50px;animation-duration:80s;">
      <img src="https://i.ibb.co/8cP7v7T/cloud.png" style="top:150px;animation-duration:120s;">
    </div>
    <h1>Welcome to Lalon Airport</h1>
    <p>Your Gateway to the World</p>
    <div class="search-box">
      <input type="text" placeholder="Search flights...">
      <button>Search</button>
    </div>
  </section>

  <section id="flights">
    <h2 class="section-title">Quick Actions</h2>
    <div class="quick-actions">
      <div class="action-card"><i class="fa-solid fa-plane"></i><h3>Book a Flight</h3><p>Find and book your next journey.</p></div>
      <div class="action-card"><i class="fa-solid fa-suitcase-rolling"></i><h3>Manage Booking</h3><p>Modify or cancel your reservations.</p></div>
      <div class="action-card"><i class="fa-solid fa-clock"></i><h3>Flight Status</h3><p>Check real-time flight information.</p></div>
      <div class="action-card"><i class="fa-solid fa-map"></i><h3>Airport Map</h3><p>Navigate through terminals easily.</p></div>
    </div>
  </section>

  <section id="booking">
    <h2 class="section-title">Our Stats</h2>
    <div class="stats">
      <div class="stat-card"><h2>2M+</h2><p>Passengers Served</p></div>
      <div class="stat-card"><h2>120+</h2><p>Destinations</p></div>
      <div class="stat-card"><h2>50+</h2><p>Airlines</p></div>
      <div class="stat-card"><h2>24/7</h2><p>Support</p></div>
    </div>
  </section>

  <section id="status">
    <h2 class="section-title">Why Choose Us?</h2>
    <div class="features">
      <div class="feature-item"><i class="fa-solid fa-shield-alt"></i><h3>Safe & Secure</h3><p>We ensure your safety at every step.</p></div>
      <div class="feature-item"><i class="fa-solid fa-wifi"></i><h3>Free Wi-Fi</h3><p>Stay connected throughout the airport.</p></div>
      <div class="feature-item"><i class="fa-solid fa-utensils"></i><h3>Food & Beverages</h3><p>Enjoy world-class dining options.</p></div>
      <div class="feature-item"><i class="fa-solid fa-car"></i><h3>Parking Facilities</h3><p>Secure and spacious parking available.</p></div>
    </div>
  </section>

  <section class="newsletter" id="contact">
    <h2 class="section-title">Stay Updated</h2>
    <p>Subscribe to our newsletter for the latest updates.</p>
    <input type="email" placeholder="Enter your email">
    <button>Subscribe</button>
  </section>

  <footer>
    <div class="footer-sections">
      <div class="footer-section"><h3>About Us</h3><p>Lalon Airport connects you to the world with excellence and care.</p></div>
      <div class="footer-section"><h3>Quick Links</h3><p><a href="#flights" style="color:white;">Flights</a><br><a href="#booking" style="color:white;">Booking</a></p></div>
      <div class="footer-section"><h3>Contact</h3><p>Email: support@lalonairport.com<br>Phone: +123 456 7890</p></div>
    </div>
    <p>&copy; 2025 Lalon Airport. All rights reserved.</p>
  </footer>

  <button class="scroll-top"><i class="fa-solid fa-arrow-up"></i></button>

  <script>
    const menuToggle=document.getElementById("menuToggle");
    const navLinks=document.getElementById("navLinks");
    const navbar=document.getElementById("navbar");
    const scrollTop=document.querySelector(".scroll-top");
    menuToggle.addEventListener("click",()=>{navLinks.classList.toggle("active");});
    window.addEventListener("scroll",()=>{navbar.classList.toggle("scrolled",window.scrollY>50);scrollTop.classList.toggle("active",window.scrollY>300);});
    scrollTop.addEventListener("click",()=>{window.scrollTo({top:0,behavior:"smooth"});});
  </script>
</body>
</html>
