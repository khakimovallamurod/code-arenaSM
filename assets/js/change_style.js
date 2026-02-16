// (function() {
//     const savedTheme = localStorage.getItem('theme') || 'light';
    
//     // Apply saved theme on page load
//     document.addEventListener('DOMContentLoaded', function() {
//         applyTheme(savedTheme);
//         createThemeSwitcher();
//     });

//     // Function to apply theme
//     function applyTheme(theme) {
//         const styleLink = document.getElementById('theme-stylesheet') || createStyleLink();
        
//         if (theme === 'dark') {
//             styleLink.href = 'assets/css/styles-dark.css';
//         } else {
//             styleLink.href = 'assets/css/styles-light.css';
//         }
        
//         // Save to localStorage
//         localStorage.setItem('theme', theme);
        
//         // Update button icon if it exists
//         updateButtonIcon(theme);
//     }

//     // Create stylesheet link if it doesn't exist
//     function createStyleLink() {
//         const link = document.createElement('link');
//         link.id = 'theme-stylesheet';
//         link.rel = 'stylesheet';
//         link.href = 'assets/css/styles-light.css';
//         document.head.appendChild(link);
//         return link;
//     }

//     function createThemeSwitcher() {
//         // Create button
//         const button = document.createElement('button');
//         button.id = 'theme-toggle';
//         button.setAttribute('aria-label', 'Toggle theme');
//         button.style.cssText = `
//             position: fixed;
//             bottom: 2rem;
//             right: 2rem;
//             width: 60px;
//             height: 60px;
//             border-radius: 50%;
//             border: none;
//             background: #1e3a8a;
//             color: white;
//             font-size: 1.5rem;
//             cursor: pointer;
//             box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
//             transition: all 0.3s ease;
//             z-index: 9999;
//             display: flex;
//             align-items: center;
//             justify-content: center;
//         `;
        
//         // Set initial icon
//         updateButtonIcon(savedTheme, button);
        
//         // Add hover effect
//         button.addEventListener('mouseenter', function() {
//             this.style.transform = 'scale(1.1)';
//             this.style.boxShadow = '0 6px 20px rgba(0, 0, 0, 0.25)';
//         });
        
//         button.addEventListener('mouseleave', function() {
//             this.style.transform = 'scale(1)';
//             this.style.boxShadow = '0 4px 12px rgba(0, 0, 0, 0.15)';
//         });
        
//         // Add click event
//         button.addEventListener('click', function() {
//             const currentTheme = localStorage.getItem('theme') || 'light';
//             const newTheme = currentTheme === 'light' ? 'dark' : 'light';
            
//             // Add animation
//             this.style.transform = 'rotate(360deg) scale(1.1)';
//             setTimeout(() => {
//                 this.style.transform = 'scale(1)';
//             }, 300);
            
//             applyTheme(newTheme);
//         });
        
//         // Append to body
//         document.body.appendChild(button);
//     }

//     // Update button icon based on current theme
//     function updateButtonIcon(theme, button) {
//         const btn = button || document.getElementById('theme-toggle');
//         if (btn) {
//             if (theme === 'dark') {
//                 btn.innerHTML = '☀️'; // Sun for dark mode (click to go light)
//                 btn.title = 'Switch to Light Mode';
//             } else {
//                 btn.innerHTML = '🌙'; // Moon for light mode (click to go dark)
//                 btn.title = 'Switch to Dark Mode';
//             }
//         }
//     }
// })();
