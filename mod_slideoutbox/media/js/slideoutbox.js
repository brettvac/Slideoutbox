/**
 * Slide Out Box Module
 * @version 1.6
 * @license GPL-2.0
 */
jQuery(function($) {
    // Read the module options provided by the Joomla template.
    const options = Joomla.getOptions('mod_slideoutbox.vars');

    const moduleId = options.moduleId;
    const scrollDepth = options.scrollDepth;
    const cookieExpire = options.cookieExpire;

    // Build the name of this module's close cookie.
    const cookieName = "mod_slideoutbox_closed_" + moduleId;

    // Track whether the slideout has been activated during this page view.
    let hasAppeared = false;

    // Set a cookie for the specified number of days. A duration of 0 or -1 expires the cookie immediately.
    function setCookie(cname, cvalue, exdays) {
        const d = new Date();

        if (Number(exdays) <= 0) {
            d.setTime(d.getTime() - 1000);
        } else {
            d.setTime(d.getTime() + (Number(exdays) * 24 * 60 * 60 * 1000));
        }

        const expires = "expires=" + d.toUTCString();

        document.cookie = cname + "=" + cvalue + "; " + expires + "; path=/";
    }

    // Return the value of the named cookie, or an empty string if it is absent.
    function getCookie(cname) {
        const name = cname + "=";
        const ca = document.cookie.split(';');

        for (let i = 0; i < ca.length; i++) {
            let c = ca[i];

            // Remove leading whitespace from the cookie string.
            while (c.charAt(0) === ' ') {
                c = c.substring(1);
            }

            // Return the value if the current cookie matches the requested name.
            if (c.indexOf(name) === 0) {
                return c.substring(name.length);
            }
        }

        return "";
    }

    // Remove the slideout if a close cookie exists. Otherwise, allow the slideout to display.
    function checkCookie() {
        const closedCookie = getCookie(cookieName);

        if (closedCookie !== "") {
            $('#sbox-' + moduleId).parent('.sbox').remove();
            return false;
        }

        return true;
    }

    // Remove the slideout and set or expire its close cookie according to the configured duration.
    function closeSlideout() {
        $('#sbox-' + moduleId).parent('.sbox').remove();

        setCookie(cookieName, "closed", cookieExpire);
    }

    // Check the scroll position and activate the slideout when the configured threshold is reached.
    function handleScroll() {
        if (hasAppeared) {
            return;
        }

        // Get the current scroll position in pixels.
        const scrollTop = $(window).scrollTop();

        // Calculate the total scrollable height of the document.
        const docHeight = $(document).height() - $(window).height();

        let scrollPercent = 0;

        // If there is no scrollable content, treat the page as 100% scrolled.
        if (docHeight > 0) {
            scrollPercent = (scrollTop / docHeight) * 100;
        } else {
            scrollPercent = 100;
        }

        if (scrollPercent >= scrollDepth) {
            // Activate the inner box animation and reveal the outer container.
            $('#sbox-' + moduleId)
                .addClass('active')
                .parent('.sbox')
                .addClass('active');

            hasAppeared = true;

            // Remove the scroll listener after activation.
            $(window).off('scroll', handleScroll);
        }
    }

    // If the configured duration is -1, delete any existing close cookie during initialization so the slideout can appear again.
    if (Number(cookieExpire) === -1 && getCookie(cookieName) !== "") {
        setCookie(cookieName, "", -1);
    }

    // Proceed only if no close cookie remains after initialization.
    if (checkCookie()) {

        // Register the scroll handler and immediately check the current position.
        $(window).on('scroll', handleScroll).trigger('scroll');

        // Handle clicks on the close button.
        $('#sbox-' + moduleId + ' .close').on('click', function(event) {
            event.preventDefault();
            closeSlideout();
        });

        // Handle clicks on links that close the slideout before navigating.
        $('#sbox-' + moduleId).on('click', '.sbox-close', function(event) {
            event.preventDefault();

            closeSlideout();

            // Navigate to the link destination after closing the slideout.
            const href = $(this).attr('href');

            if (href && href !== '#') {
                window.location.href = href;
            }
        });
    }
});