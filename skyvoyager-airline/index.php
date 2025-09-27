<?php require_once 'includes/header.php'; ?>

<div class="hero-section" style="background: linear-gradient(rgba(106, 13, 173, 0.8), rgba(106, 13, 173, 0.8)), url('assets/images/airplane.jpg') no-repeat center center; background-size: cover; color: white; padding: 5rem 0; text-align: center; margin-bottom: 3rem;">
    <h1 style="font-size: 3rem; margin-bottom: 1rem;">Fly With <span style="color: var(--accent-color);">SkyWings</span></h1>
    <p style="font-size: 1.2rem; max-width: 800px; margin: 0 auto 2rem;">Experience the joy of flying with our premium services and competitive prices</p>
    <a href="flights/search.php" class="btn btn-accent" style="font-size: 1.1rem; padding: 0.8rem 2rem;">Book Your Flight Now</a>
</div>

<div class="features-container" style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 2rem; margin-bottom: 3rem;">
    <div class="feature-card" style="background: white; padding: 2rem; border-radius: 8px; text-align: center; box-shadow: var(--shadow); border-top: 4px solid var(--secondary-purple);">
        <i class="fas fa-globe-americas" style="font-size: 2.5rem; color: var(--primary-purple); margin-bottom: 1rem;"></i>
        <h3 style="color: var(--dark-purple);">100+ Destinations</h3>
        <p>Fly to destinations across all continents with our extensive network</p>
    </div>
    <div class="feature-card" style="background: white; padding: 2rem; border-radius: 8px; text-align: center; box-shadow: var(--shadow); border-top: 4px solid var(--secondary-purple);">
        <i class="fas fa-medal" style="font-size: 2.5rem; color: var(--primary-purple); margin-bottom: 1rem;"></i>
        <h3 style="color: var(--dark-purple);">Award Winning</h3>
        <p>Recognized as the best airline for customer service 3 years running</p>
    </div>
    <div class="feature-card" style="background: white; padding: 2rem; border-radius: 8px; text-align: center; box-shadow: var(--shadow); border-top: 4px solid var(--secondary-purple);">
        <i class="fas fa-smile" style="font-size: 2.5rem; color: var(--primary-purple); margin-bottom: 1rem;"></i>
        <h3 style="color: var(--dark-purple);">Happy Customers</h3>
        <p>Over 5 million satisfied passengers fly with us each year</p>
    </div>
</div>

<div class="special-offers" style="background: var(--light-purple); padding: 2rem; border-radius: 8px; margin-bottom: 3rem;">
    <h2 style="color: var(--dark-purple); text-align: center; margin-bottom: 1.5rem;">Special Offers</h2>
    <div class="offers-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 1.5rem;">
        <div class="offer-card" style="background: white; padding: 1.5rem; border-radius: 8px; box-shadow: var(--shadow);">
            <h3 style="color: var(--primary-purple); margin-top: 0;">Summer Getaway</h3>
            <p style="color: var(--dark-purple); font-weight: bold;">Up to 30% off</p>
            <p>Book your summer vacation now and save big on select destinations!</p>
            <a href="flights/search.php" class="btn" style="display: block; text-align: center; margin-top: 1rem;">View Deals</a>
        </div>
        <div class="offer-card" style="background: white; padding: 1.5rem; border-radius: 8px; box-shadow: var(--shadow);">
            <h3 style="color: var(--primary-purple); margin-top: 0;">Business Class</h3>
            <p style="color: var(--dark-purple); font-weight: bold;">15% Discount</p>
            <p>Upgrade your experience with our premium business class seats.</p>
            <a href="flights/search.php" class="btn" style="display: block; text-align: center; margin-top: 1rem;">Learn More</a>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>