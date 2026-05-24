/**
 * Slide Out Box Module
 *
 * @version 1.2
 * @license GPL-2.0
 */
jQuery(function($) {
    // Get slidebox options passed by the template file
    const options = Joomla.getOptions('mod_slideoutbox');
    
    const moduleId = options.moduleId;
    const scrollDepth = options.scrollDepth;
    const cookieExpire = options.cookieExpire;

    // Flag to track if slideout has appeared
    let hasAppeared = false;

    // Sets a cookie with name, value, and expiration days (0 = immediate expiry)
    function setCookie(cname, cvalue, exdays) {
        const d = new Date();
        if (exdays === 0) {
            d.setTime(d.getTime() - 1); // Expire immediately
        } else {
            d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
        }
        const expires = "expires=" + d.toUTCString();
        document.cookie = cname + "=" + cvalue + "; " + expires + "; path=/";
    }
    
    // Retrieves the value of a cookie by name
    function getCookie(cname) {
        const name = cname + "=";
        const ca = document.cookie.split(';');
        for (let i = 0; i < ca.length; i++) {
            let c = ca[i];
            // Remove leading whitespace from the cookie string
            while (c.charAt(0) == ' ')
              c = c.substring(1);

            // Return the cookie value if the current cookie matches the requested name
            if (c.indexOf(name) == 0) 
              return c.substring(name.length);
        }
        return ""; // Empty string if cookie not found
    }

    // Check if close cookie exists
    function checkCookie() {
        const closedCookie = getCookie("mod_slideoutbox_closed_" + moduleId);
        if (closedCookie != "") {
            $('#sbox-' + moduleId).parent('.sbox').remove(); // Remove the Slideoutbox DOM element
            return false;  
        }
        return true; // Allow the slideout to proceed
    }

    function handleScroll() {
        if (hasAppeared) {
            return;
        }

        // Get current scroll position in pixels
        const scrollTop = $(window).scrollTop();

        // Calculate total scrollable height of the document
        const docHeight = $(document).height() - $(window).height();

        let scrollPercent = 0; 

        // If docHeight is 0 or less, there is no scrollbar.
        // This means 100% of the content is already visible to the user.
        if (docHeight > 0) {
            scrollPercent = (scrollTop / docHeight) * 100; 
        } else {
            scrollPercent = 100; 
        }

        if (scrollPercent >= scrollDepth) {
            $('#sbox-' + moduleId).addClass('active');

            hasAppeared = true;

            // Remove scroll listener after activation
            $(window).off('scroll', handleScroll);
        }
    }

    // Only proceed if no close cookie is set
    if (checkCookie()) {
        $(window).on('scroll', handleScroll);

        // Bind the listener AND instantly fire it once to check the page height
        $(window).on('scroll', handleScroll).trigger('scroll');

        // Set the close cookie if the user clicks the close button
        $('#sbox-' + moduleId + ' .close').on('click', function() {
            $('#sbox-' + moduleId).parent('.sbox').remove();

            setCookie(
                "mod_slideoutbox_closed_" + moduleId,
                "closed",
                cookieExpire
            );
        });
    }
});