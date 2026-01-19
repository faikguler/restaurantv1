jQuery(document).ready(function($) {
    
    // Header scroll effect
    $(window).on('scroll', function() {
        const header = $('#header');
        if ($(window).scrollTop() > 50) {
            header.addClass('scrolled');
        } else {
            header.removeClass('scrolled');
        }
    });
    
    // Mobile Menu Toggle
    $('#mobileMenuBtn').on('click', function() {
        const nav = $('#mainNav');
        nav.toggleClass('active');
        
        if (nav.hasClass('active')) {
            $(this).html('<i class="fas fa-times"></i>');
        } else {
            $(this).html('<i class="fas fa-bars"></i>');
        }
    });
    
    // Close mobile menu when clicking on a link
    $('#mainNav a').on('click', function() {
        $('#mainNav').removeClass('active');
        $('#mobileMenuBtn').html('<i class="fas fa-bars"></i>');
    });
    
    // Menu Tab Switching
    $('.tab-btn').on('click', function() {
        // Remove active class from all buttons and categories
        $('.tab-btn').removeClass('active');
        $('.menu-category').removeClass('active');
        
        // Add active class to clicked button
        $(this).addClass('active');
        
        // Show corresponding category
        const categoryId = $(this).data('category');
        $('#' + categoryId).addClass('active');
        
        // Scroll to top of menu section only in single page mode
        if (!$('body').hasClass('multi-page-mode')) {
            $('html, body').animate({
                scrollTop: $('#menu').offset().top - 100
            }, 500);
        }
    });
    
    // Reservation Form Submission
    $('#reservationForm').on('submit', function(e) {
        e.preventDefault();
        
        // Show loading state
        const submitBtn = $(this).find('.form-submit-btn');
        const originalText = submitBtn.html();
        submitBtn.html('<i class="fas fa-spinner fa-spin"></i> Processing...').prop('disabled', true);
        
        const formData = {
            action: 'restaurant_reservation',
            nonce: restaurantTheme.nonce,
            name: $('#name').val().trim(),
            email: $('#email').val().trim(),
            phone: $('#phone').val().trim(),
            date: $('#date').val(),
            time: $('#time').val(),
            guests: $('#guests').val(),
            message: $('#message').val().trim()
        };
        
        // Basic validation
        if (!formData.name || !formData.email || !formData.phone || !formData.date || !formData.time || !formData.guests) {
            alert('Please fill all required fields.');
            submitBtn.html(originalText).prop('disabled', false);
            return;
        }
        
        // Email validation
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(formData.email)) {
            alert('Please enter a valid email address.');
            submitBtn.html(originalText).prop('disabled', false);
            return;
        }
        
        $.ajax({
            url: restaurantTheme.ajaxurl,
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert(response.data.message || 'Thank you! Your reservation has been submitted. We will contact you shortly to confirm your booking.');
                    
                    // Reset form
                    $('#reservationForm')[0].reset();
                    
                    // Set default date to tomorrow
                    const tomorrow = new Date();
                    tomorrow.setDate(tomorrow.getDate() + 1);
                    $('#date').val(tomorrow.toISOString().split('T')[0]);
                    
                    // Show confirmation message on page
                    $('#reservationForm').before(
                        '<div class="reservation-success" style="background: #d4edda; color: #155724; padding: 15px; border-radius: 8px; margin-bottom: 20px; text-align: center;">' +
                        '<i class="fas fa-check-circle"></i> ' + 
                        (response.data.message || 'Your reservation has been submitted successfully!') +
                        '</div>'
                    );
                    
                    // Remove success message after 10 seconds
                    setTimeout(function() {
                        $('.reservation-success').fadeOut(500, function() {
                            $(this).remove();
                        });
                    }, 10000);
                } else {
                    alert(response.data.message || 'Sorry, there was an error submitting your reservation. Please try again or call us directly.');
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', status, error);
                alert('Sorry, there was an error submitting your reservation. Please try again or call us directly.');
            },
            complete: function() {
                submitBtn.html(originalText).prop('disabled', false);
            }
        });
    });
    
    // Contact Form Submission
    $('#contactForm').on('submit', function(e) {
        e.preventDefault();
        
        const formData = {
            action: 'restaurant_contact',
            nonce: restaurantTheme.nonce,
            name: $('#contact-name').val(),
            email: $('#contact-email').val(),
            phone: $('#contact-phone').val(),
            method: $('#contact-method').val(),
            message: $('#contact-message').val()
        };
        
        $.ajax({
            url: restaurantTheme.ajaxurl,
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    alert(response.data.message || 'Thank you for your message! We will get back to you as soon as possible.');
                    $('#contactForm')[0].reset();
                } else {
                    alert('Sorry, there was an error sending your message. Please try again or call us directly.');
                }
            },
            error: function() {
                alert('Sorry, there was an error sending your message. Please try again or call us directly.');
            }
        });
    });
    
    // Set minimum date for reservation to today
    const dateInput = $('#date');
    if (dateInput.length) {
        const today = new Date().toISOString().split('T')[0];
        dateInput.attr('min', today);
        
        // Set default date to tomorrow
        const tomorrow = new Date();
        tomorrow.setDate(tomorrow.getDate() + 1);
        dateInput.val(tomorrow.toISOString().split('T')[0]);
    }
    
    // Smooth scrolling for anchor links (only in single page mode)
    if (!$('body').hasClass('multi-page-mode')) {
        $('a[href^="#"]').on('click', function(e) {
            const targetId = $(this).attr('href');
            if (targetId === '#') return;
            
            const targetElement = $(targetId);
            if (targetElement.length) {
                e.preventDefault();
                $('html, body').animate({
                    scrollTop: targetElement.offset().top - 100
                }, 800);
            }
        });
    }
    
    // Multi-page mode: Active menu item highlighting
    if ($('body').hasClass('multi-page-mode')) {
        const currentUrl = window.location.pathname;
        const homeUrl = restaurantTheme.homeUrl.replace(window.location.origin, '');
        
        $('#mainNav a').each(function() {
            const linkUrl = $(this).attr('href');
            
            if (currentUrl === linkUrl || 
                (currentUrl === '/' && linkUrl === homeUrl) ||
                (linkUrl !== homeUrl && linkUrl !== '/' && currentUrl.includes(linkUrl))) {
                $(this).parent().addClass('current-menu-item');
            }
        });
    }
});