import http from 'k6/http';
import { check, sleep } from 'k6';

// --------------------------------------------------------------------------
// CONFIGURATION
// --------------------------------------------------------------------------
// CONFIGURATION
// --------------------------------------------------------------------------
export const options = {
    insecureSkipTLSVerify: true, // Ignore SSL certificate errors (common in local dev)
    // Traffic/Load simulation stages
    stages: [
        { duration: '10s', target: 50 }, // Ramp up to 50 users over 10 seconds
        { duration: '30s', target: 50 }, // Sustain 50 users for 30 seconds
        { duration: '10s', target: 0 },  // Ramp down to 0 users over 10 seconds
    ],
    // Success thresholds
    thresholds: {
        http_req_duration: ['p(95)<2000'], // 95% of requests must complete below 2s
        http_req_failed: ['rate<0.01'],    // Failure rate should be less than 1%
    },
};

// CONSTANTS
const BASE_URL = 'https://elicrochet-ecommerce.test';
const USER_PASSWORD = 'password';
const PRODUCT_SLUG = 'unicornio-magico';

export default function () {
    // ----------------------------------------------------------------------
    // STEP 1: PREPARE USER (Unique per VU to avoid session locking)
    // ----------------------------------------------------------------------
    // __VU starts at 1. We have users user1@example.com ... user55@example.com
    const email = `user${__VU}@example.com`;

    // ----------------------------------------------------------------------
    // STEP 2: LOGIN
    // ----------------------------------------------------------------------

    // Visit Login Page to get CSRF Token
    let res = http.get(`${BASE_URL}/login`);

    const loginPageSuccess = check(res, {
        'Login page loaded': (r) => r.status === 200,
    });

    if (!loginPageSuccess) {
        console.error(`Login page failed loading for ${email}. Status: ${res.status}`);
        return;
    }

    // Extract CSRF token from hidden input
    // Flexible regex for single or double quotes
    const csrfMatch = res.body.match(/name=["']_token["']\s+value=["']([^"']+)["']/);
    const csrfToken = csrfMatch ? csrfMatch[1] : null;

    if (!csrfToken) {
        console.error(`Failed to find CSRF token for ${email}.`);
        // Uncomment to debug body content
        // console.log(res.body); 
        return;
    }

    // Submit Login credentials
    res = http.post(`${BASE_URL}/login`, {
        _token: csrfToken,
        email: email,
        password: USER_PASSWORD,
    });

    const loginSuccess = check(res, {
        'Login successful': (r) => r.status === 200 || r.status === 302,
    });

    if (!loginSuccess) {
        console.error(`Login failed for ${email}. Status: ${res.status}`);
        return;
    }

    // ----------------------------------------------------------------------
    // STEP 1.5: VISIT PRODUCT PAGE (To get fresh CSRF token after login rotation)
    // ----------------------------------------------------------------------
    // Laravel regenerates the session/token on login. We need the NEW token.

    res = http.get(`${BASE_URL}/producto/${PRODUCT_SLUG}`);

    check(res, {
        'Product page loaded': (r) => r.status === 200,
    });

    // Extract NEW CSRF token from product page
    const csrfMatch2 = res.body.match(/name=["']_token["']\s+value=["']([^"']+)["']/);
    const newToken = csrfMatch2 ? csrfMatch2[1] : null;

    if (!newToken) {
        console.error(`Failed to find FRESH CSRF token on product page for ${email}`);
        return;
    }

    // ----------------------------------------------------------------------
    // STEP 2: ADD PRODUCT TO CART
    // ----------------------------------------------------------------------
    // We add an item so the checkout page is not empty (and doesn't redirect)

    // NOTE: CartController uses lockForUpdate(), so high concurrency on the SAME product
    // might cause lock wait timeouts or slow responses.
    res = http.post(`${BASE_URL}/cart/add/${PRODUCT_SLUG}`, {
        _token: newToken,
        quantity: 1
    });

    const addToCartSuccess = check(res, {
        'Added to cart': (r) => r.status === 200 || r.status === 302,
    });

    if (!addToCartSuccess) {
        console.error(`Add to cart failed for ${email}. Status: ${res.status} Body: ${res.body.substring(0, 100)}...`);
        return;
    }

    // ----------------------------------------------------------------------
    // STEP 3: VISIT CHECKOUT
    // ----------------------------------------------------------------------

    res = http.get(`${BASE_URL}/checkout`);

    check(res, {
        'Checkout loaded OK': (r) => r.status === 200,
        // Verify key content exists to ensure we didn't get a 200 error page
        'Checkout content verify': (r) => r.body.includes('Resumen') || r.body.includes('Checkout'),
    });

    // Short pause between iterations to simulate real user think time
    sleep(1);
}
