/**
 * Influencers Marketing Module - Dark Mode
 */

(function() {
    'use strict';

    const THEME_KEY = 'im_theme_preference';
    const THEME_DARK = 'dark';
    const THEME_LIGHT = 'light';

    // Initialize on document ready
    $(document).ready(function() {
        initDarkMode();
    });

    function initDarkMode() {
        // Load saved theme preference
        const savedTheme = localStorage.getItem(THEME_KEY);
        if (savedTheme) {
            applyTheme(savedTheme);
        } else {
            // Check system preference
            if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
                applyTheme(THEME_DARK);
            }
        }

        // Create toggle button
        createToggleButton();

        // Listen for system theme changes
        if (window.matchMedia) {
            window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', function(e) {
                if (!localStorage.getItem(THEME_KEY)) {
                    applyTheme(e.matches ? THEME_DARK : THEME_LIGHT);
                }
            });
        }
    }

    function createToggleButton() {
        const currentTheme = document.documentElement.getAttribute('data-theme') || THEME_LIGHT;
        const icon = currentTheme === THEME_DARK ? '☀️' : '🌙';

        const button = $(`
            <button class="im-dark-mode-toggle" id="im-theme-toggle" title="Changer de thème">
                <span class="im-theme-icon">${icon}</span>
            </button>
        `);

        $('body').append(button);

        button.on('click', function() {
            toggleTheme();
        });
    }

    function toggleTheme() {
        const currentTheme = document.documentElement.getAttribute('data-theme') || THEME_LIGHT;
        const newTheme = currentTheme === THEME_DARK ? THEME_LIGHT : THEME_DARK;

        applyTheme(newTheme);
        localStorage.setItem(THEME_KEY, newTheme);
    }

    function applyTheme(theme) {
        // Add transition class
        document.body.classList.add('im-theme-transition');

        // Apply theme
        document.documentElement.setAttribute('data-theme', theme);

        // Update toggle button icon
        const icon = theme === THEME_DARK ? '☀️' : '🌙';
        $('.im-theme-icon').html(icon);

        // Update toggle button title
        const title = theme === THEME_DARK ? 'Passer en mode clair' : 'Passer en mode sombre';
        $('#im-theme-toggle').attr('title', title);

        // Remove transition class after animation
        setTimeout(function() {
            document.body.classList.remove('im-theme-transition');
        }, 300);

        // Trigger custom event
        $(document).trigger('im:themeChanged', [theme]);
    }

    function getCurrentTheme() {
        return document.documentElement.getAttribute('data-theme') || THEME_LIGHT;
    }

    // Export functions
    window.IMDarkMode = {
        toggle: toggleTheme,
        set: applyTheme,
        get: getCurrentTheme
    };

    // Listen for theme changes to update charts and other components
    $(document).on('im:themeChanged', function(e, theme) {
        // Update Chart.js default colors if charts exist
        if (typeof Chart !== 'undefined') {
            updateChartTheme(theme);
        }
    });

    function updateChartTheme(theme) {
        if (theme === THEME_DARK) {
            Chart.defaults.color = '#e0e0e0';
            Chart.defaults.borderColor = '#3d3d3d';
        } else {
            Chart.defaults.color = '#666';
            Chart.defaults.borderColor = '#e0e0e0';
        }

        // Redraw all charts
        Chart.instances.forEach(function(chart) {
            chart.update();
        });
    }

})();
