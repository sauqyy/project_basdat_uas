// SCHEDIFY Landing Page JavaScript

// Function to generate random star positions
function multipleBoxShadow(n) {
    let value = '';
    for (let i = 0; i < n; i++) {
        const x = Math.floor(Math.random() * 2000);
        const y = Math.floor(Math.random() * 2000);
        value += `${x}px ${y}px #FFF`;
        if (i < n - 1) {
            value += ', ';
        }
    }
    return value;
}

// Initialize stars when page loads
function initializeStars() {
    // Generate stars for each layer
    const shadowsSmall = multipleBoxShadow(700);
    const shadowsMedium = multipleBoxShadow(200);
    const shadowsBig = multipleBoxShadow(100);

    // Apply shadows to star elements
    const stars = document.getElementById('stars');
    const stars2 = document.getElementById('stars2');
    const stars3 = document.getElementById('stars3');

    if (stars) {
        stars.style.boxShadow = shadowsSmall;
    }
    if (stars2) {
        stars2.style.boxShadow = shadowsMedium;
    }
    if (stars3) {
        stars3.style.boxShadow = shadowsBig;
    }
}

// Add interactivity to login button
function initializeLoginButton() {
    const loginBtn = document.querySelector('.login-btn');
    if (loginBtn) {
        loginBtn.addEventListener('click', function() {
            // You can replace this with actual login functionality
            alert('Login functionality would be implemented here');
        });
    }
}

// Add smooth scrolling for navigation links
function initializeNavigation() {
    const navLinks = document.querySelectorAll('.nav-menu a');
    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const targetId = this.getAttribute('href');
            
            // Add smooth scroll functionality here
            if (targetId.startsWith('#')) {
                const targetElement = document.querySelector(targetId);
                if (targetElement) {
                    targetElement.scrollIntoView({
                        behavior: 'smooth'
                    });
                }
            }
        });
    });
}

// Regenerate stars periodically for variety
function startStarRegeneration() {
    setInterval(() => {
        const newShadowsSmall = multipleBoxShadow(700);
        const newShadowsMedium = multipleBoxShadow(200);
        const newShadowsBig = multipleBoxShadow(100);

        const stars = document.getElementById('stars');
        const stars2 = document.getElementById('stars2');
        const stars3 = document.getElementById('stars3');

        if (stars) {
            stars.style.boxShadow = newShadowsSmall;
        }
        if (stars2) {
            stars2.style.boxShadow = newShadowsMedium;
        }
        if (stars3) {
            stars3.style.boxShadow = newShadowsBig;
        }
    }, 30000); // Regenerate every 30 seconds
}

// Add parallax effect to stars
function addParallaxEffect() {
    window.addEventListener('scroll', function() {
        const scrolled = window.pageYOffset;
        const stars = document.getElementById('stars');
        const stars2 = document.getElementById('stars2');
        const stars3 = document.getElementById('stars3');

        if (stars) {
            stars.style.transform = `translateY(${scrolled * 0.5}px)`;
        }
        if (stars2) {
            stars2.style.transform = `translateY(${scrolled * 0.3}px)`;
        }
        if (stars3) {
            stars3.style.transform = `translateY(${scrolled * 0.1}px)`;
        }
    });
}

// Add typing effect to title
function addTypingEffect() {
    const titleElement = document.querySelector('#title span:first-child');
    if (titleElement) {
        const text = titleElement.textContent;
        titleElement.textContent = '';
        
        let i = 0;
        const typeWriter = () => {
            if (i < text.length) {
                titleElement.textContent += text.charAt(i);
                i++;
                setTimeout(typeWriter, 100);
            }
        };
        
        // Start typing effect after a short delay
        setTimeout(typeWriter, 1000);
    }
}

// Initialize all functionality when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    initializeStars();
    initializeLoginButton();
    initializeNavigation();
    startStarRegeneration();
    addParallaxEffect();
    addTypingEffect();
});

// Add some additional interactive effects
document.addEventListener('mousemove', function(e) {
    const stars = document.getElementById('stars');
    const stars2 = document.getElementById('stars2');
    const stars3 = document.getElementById('stars3');
    
    const x = e.clientX / window.innerWidth;
    const y = e.clientY / window.innerHeight;
    
    if (stars) {
        stars.style.transform = `translate(${x * 10}px, ${y * 10}px)`;
    }
    if (stars2) {
        stars2.style.transform = `translate(${x * 20}px, ${y * 20}px)`;
    }
    if (stars3) {
        stars3.style.transform = `translate(${x * 30}px, ${y * 30}px)`;
    }
});
