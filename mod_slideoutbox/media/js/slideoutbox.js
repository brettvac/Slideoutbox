/**
 * Slide Out Box Module
 * @version 1.7
 * @license GPL-2.0
 */
jQuery(function($) {

    // Read the module options provided by Joomla.
    const options = Joomla.getOptions('mod_slideoutbox.vars');

    const moduleId = options.moduleId;
    const scrollDepth = Number(options.scrollDepth);
    const initialDelayEnabled = Number(options.initialDelayEnabled) === 1;
    const initialDelay = Number(options.initialDelay) || 0;
    const cookieExpire = options.cookieExpire;

    // Detect whether the incoming URL contains an anchor.
    const hasFragment = window.location.hash.length > 1;

    // Build the name of this module's close cookie.
    const cookieName = "mod_slideoutbox_closed_" + moduleId;

    // Track whether the slideout has been activated.
    let hasAppeared = false;

    // Track whether the initial delay is pending.
    let initialDelayPending = false;

    // Track whether initial browser positioning is complete.
    let navigationSettled = !hasFragment || !initialDelayEnabled;

    // Store the initial delay timer.
    let initialDelayTimer = null;

    // Store the anchor-position settling timer.
    let settlingTimer = null;

    // Set a cookie for the specified number of days.
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

    // Return the value of the named cookie.
    function getCookie(cname) {
        const name = cname + "=";
        const ca = document.cookie.split(';');

        for (let i = 0; i < ca.length; i++) {
            let c = ca[i];

            while (c.charAt(0) === ' ') {
                c = c.substring(1);
            }

            if (c.indexOf(name) === 0) {
                return c.substring(name.length);
            }
        }

        return "";
    }

    // Calculate the current scroll percentage.
    function getScrollPercent() {
        const scrollTop = $(window).scrollTop();
        const docHeight = $(document).height() - $(window).height();

        if (docHeight > 0) {
            return (scrollTop / docHeight) * 100;
        }

        return 100;
    }

    // Display the slideout.
    function activateSlideout() {

        if (hasAppeared) {
            return;
        }

        $('#sbox-' + moduleId)
            .addClass('active')
            .parent('.sbox')
            .addClass('active');

        hasAppeared = true;

        // Remove the scroll listener after activation.
        $(window).off('scroll', handleScroll);
    }

    // Remove the slideout if a close cookie exists.
    function checkCookie() {
        const closedCookie = getCookie(cookieName);

        if (closedCookie !== "") {
            $('#sbox-' + moduleId).parent('.sbox').remove();
            return false;
        }

        return true;
    }

    // Remove the slideout and set its close cookie.
    function closeSlideout() {
        $('#sbox-' + moduleId).parent('.sbox').remove();

        setCookie(cookieName, "closed", cookieExpire);
    }

    // Start the initial delay.
    function startInitialDelay() {

        if (initialDelayPending || hasAppeared) {
            return;
        }

        initialDelayPending = true;

        initialDelayTimer = setTimeout(function() {

            initialDelayPending = false;
            initialDelayTimer = null;

            // Activate only if the visitor is still beyond the threshold.
            if (getScrollPercent() >= scrollDepth) {
                activateSlideout();
            }

        }, initialDelay);
    }

    // Check the scroll position and activate the slideout.
    function handleScroll() {

        if (hasAppeared || !navigationSettled) {
            return;
        }

        const scrollPercent = getScrollPercent();

        // If the initial delay is pending, do not activate immediately.
        if (initialDelayPending) {

            // Cancel the delay if the visitor scrolls back above the threshold.
            if (scrollPercent < scrollDepth) {

                clearTimeout(initialDelayTimer);

                initialDelayTimer = null;
                initialDelayPending = false;
            }

            return;
        }

        // Activate normally when the visitor reaches the threshold.
        if (scrollPercent >= scrollDepth) {
            activateSlideout();
        }
    }

    // Evaluate the initial scroll position after browser navigation settles.
    function evaluateInitialPosition() {

        navigationSettled = true;

        const initialScrollPercent = getScrollPercent();

        if (initialScrollPercent >= scrollDepth && initialDelayEnabled) {

            startInitialDelay();

        } else {

            handleScroll();
        }
    }

    // Wait for the browser's initial anchor positioning to settle.
    function waitForAnchorPosition() {

        const maxWait = 3000;
        const settleDelay = 300;
        const minimumWait = 500;
        const checkInterval = 50;

        const startTime = Date.now();

        let previousScrollTop = window.scrollY;
        let lastMovementTime = startTime;

        function checkPosition() {

            const currentScrollTop = window.scrollY;
            const currentTime = Date.now();

            // Detect changes in the browser's scroll position.
            if (currentScrollTop !== previousScrollTop) {

                previousScrollTop = currentScrollTop;
                lastMovementTime = currentTime;
            }

            const elapsedTime = currentTime - startTime;
            const stableTime = currentTime - lastMovementTime;

            // Allow a minimum settling period after page load.
            // Then wait until the scroll position has remained stable.
            if (
                elapsedTime >= minimumWait &&
                stableTime >= settleDelay
            ) {

                clearTimeout(settlingTimer);
                evaluateInitialPosition();

                return;
            }

            // Safety timeout prevents indefinite waiting.
            if (elapsedTime >= maxWait) {

                clearTimeout(settlingTimer);
                evaluateInitialPosition();

                return;
            }

            settlingTimer = setTimeout(checkPosition, checkInterval);
        }

        // Begin checking after the browser has completed page loading.
        if (document.readyState === 'complete') {

            checkPosition();

        } else {

            $(window).one('load', checkPosition);
        }
    }

    // If cookie expiration is -1, delete any existing close cookie.
    if (Number(cookieExpire) === -1 && getCookie(cookieName) !== "") {
        setCookie(cookieName, "", -1);
    }

    // Proceed only if no close cookie remains.
    if (checkCookie()) {

        // Register the scroll handler.
        $(window).on('scroll', handleScroll);

        // Handle clicks on the close button.
        $('#sbox-' + moduleId + ' .close').on('click', function(event) {
            event.preventDefault();
            closeSlideout();
        });

        // Handle links that close the slideout before navigating.
        $('#sbox-' + moduleId).on('click', '.sbox-close', function(event) {
            event.preventDefault();

            closeSlideout();

            const href = $(this).attr('href');

            if (href && href !== '#') {
                window.location.href = href;
            }
        });

        if (hasFragment && initialDelayEnabled) {

            // Explicitly handle direct anchor navigation.
            waitForAnchorPosition();

        } else {

            // Normal page visit: preserve immediate scroll activation.
            navigationSettled = true;
            handleScroll();
        }
    }
});
