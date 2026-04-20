<?php
declare(strict_types=1);

define('ZYCUS_GTM_ID', 'GTM-KG8889HK');
define('ZYCUS_GA4_ID', 'G-1MG1YKNRDF');
define('ZYCUS_VER',    '1.0.0');

/* ----------------------------------------------------------
   Theme setup
---------------------------------------------------------- */
add_action('after_setup_theme', function (): void {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', ['search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'script', 'style']);
    add_theme_support('elementor');
    add_theme_support('editor-styles');

    register_nav_menus([
        'primary' => __('Primary Navigation', 'zycus-elementor'),
    ]);
});

/* ----------------------------------------------------------
   Enqueue styles and scripts
---------------------------------------------------------- */
add_action('wp_enqueue_scripts', function (): void {
    $uri = get_template_directory_uri();

    wp_enqueue_style('google-inter',
        'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap',
        [], null
    );

    wp_enqueue_style('zycus-tokens',      $uri . '/assets/css/tokens.css',             ['google-inter'], ZYCUS_VER);
    wp_enqueue_style('zycus-global',      $uri . '/assets/css/global.css',             ['zycus-tokens'], ZYCUS_VER);
    wp_enqueue_style('zycus-components',  $uri . '/assets/css/components.css',         ['zycus-global'], ZYCUS_VER);
    wp_enqueue_style('zycus-animations',  $uri . '/assets/css/animations.css',         ['zycus-global'], ZYCUS_VER);
    wp_enqueue_style('zycus-elementor',   $uri . '/assets/css/elementor-overrides.css',['zycus-global'], ZYCUS_VER);
    wp_enqueue_style('zycus-main',        get_stylesheet_uri(),                         ['zycus-components', 'zycus-animations', 'zycus-elementor'], ZYCUS_VER);

    wp_enqueue_script('zycus-motion',     $uri . '/assets/js/motion.js',    [], ZYCUS_VER, ['strategy' => 'defer']);
    wp_enqueue_script('zycus-marquee',    $uri . '/assets/js/marquee.js',   ['zycus-motion'], ZYCUS_VER, ['strategy' => 'defer']);
    wp_enqueue_script('zycus-sticky-cta', $uri . '/assets/js/sticky-cta.js',['zycus-motion'], ZYCUS_VER, ['strategy' => 'defer']);
    wp_enqueue_script('zycus-tracking',   $uri . '/assets/js/tracking.js',  [], ZYCUS_VER, ['strategy' => 'defer']);
});

/* ----------------------------------------------------------
   GTM — inject in <head> at priority 1 (before wp_head content)
---------------------------------------------------------- */
add_action('wp_head', function (): void {
    $id = ZYCUS_GTM_ID;
    if (!$id) return;
    echo "<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);})(window,document,'script','dataLayer','" . esc_js($id) . "');</script>\n";
}, 1);

/* ----------------------------------------------------------
   GTM noscript — inject immediately after <body> opens
---------------------------------------------------------- */
add_action('wp_body_open', function (): void {
    $id = ZYCUS_GTM_ID;
    if (!$id) return;
    echo '<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=' . esc_attr($id) . '" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>' . "\n";
}, 1);

/* ----------------------------------------------------------
   JSON-LD Organization schema
---------------------------------------------------------- */
add_action('wp_head', function (): void {
    $schema = [
        '@context' => 'https://schema.org',
        '@type'    => 'Organization',
        'name'     => 'Zycus',
        'url'      => 'https://zycus.com',
        'logo'     => get_template_directory_uri() . '/assets/img/zycus-logo.webp',
        'sameAs'   => ['https://www.linkedin.com/company/zycus'],
    ];
    echo '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_SLASHES) . "</script>\n";
}, 5);

/* ----------------------------------------------------------
   Remove default WordPress styles/scripts we don't need
---------------------------------------------------------- */
add_action('wp_enqueue_scripts', function (): void {
    wp_dequeue_style('wp-block-library');
    wp_dequeue_style('wp-block-library-theme');
    wp_dequeue_style('classic-theme-styles');
    wp_dequeue_style('global-styles');
}, 100);

/* ----------------------------------------------------------
   Shortcode: [zycus_demo_form] — fallback if Elementor Forms
   is not available. Renders the full single-page form with
   built-in JS step logic and WP nonce protection.
---------------------------------------------------------- */
add_shortcode('zycus_demo_form', function (): string {
    ob_start();
    $nonce = wp_create_nonce('zycus_demo_form');
    $action = esc_url(admin_url('admin-post.php'));
    ?>
    <div class="demo-card">
      <div class="section__head">
        <h2 class="h2">See Merlin AI Live on Your Own Data</h2>
        <p class="section__sub">Book a 30-minute working session with a Zycus solution architect.</p>
      </div>
      <form id="demo-form-el" class="demo-form" method="POST" action="<?php echo $action; ?>" novalidate>
        <input type="hidden" name="action" value="zycus_submit_demo">
        <input type="hidden" name="_wpnonce" value="<?php echo esc_attr($nonce); ?>">
        <input type="text" name="website_url" tabindex="-1" autocomplete="off"
               style="position:absolute;left:-9999px" aria-hidden="true">

        <fieldset class="form-step">
          <legend class="form-step__legend">
            <span class="form-step__label">Step 1 of 3</span>
            <span class="form-step__title">About You</span>
          </legend>
          <div class="form-grid">
            <div class="form-field form-field--full">
              <label for="ze_email">Work Email <span class="req" aria-hidden="true">*</span></label>
              <input type="email" id="ze_email" name="email" required placeholder="you@company.com">
            </div>
            <div class="form-field">
              <label for="ze_first">First Name <span class="req" aria-hidden="true">*</span></label>
              <input type="text" id="ze_first" name="first_name" required>
            </div>
            <div class="form-field">
              <label for="ze_last">Last Name <span class="req" aria-hidden="true">*</span></label>
              <input type="text" id="ze_last" name="last_name" required>
            </div>
          </div>
        </fieldset>

        <fieldset class="form-step">
          <legend class="form-step__legend">
            <span class="form-step__label">Step 2 of 3</span>
            <span class="form-step__title">About Your Company</span>
          </legend>
          <div class="form-grid">
            <div class="form-field form-field--full">
              <label for="ze_company">Company Name <span class="req" aria-hidden="true">*</span></label>
              <input type="text" id="ze_company" name="company_name" required>
            </div>
            <div class="form-field">
              <label for="ze_size">Company Size <span class="req" aria-hidden="true">*</span></label>
              <select id="ze_size" name="company_size" required>
                <option value="" disabled selected>Select size&hellip;</option>
                <optgroup label="SMB">
                  <option value="small">1&ndash;49 &mdash; Startup / SMB</option>
                  <option value="mid">50&ndash;499 &mdash; Mid-Market</option>
                </optgroup>
                <optgroup label="Enterprise">
                  <option value="enterprise">500&ndash;4,999 &mdash; Enterprise</option>
                  <option value="large_enterprise">5,000+ &mdash; Large Enterprise</option>
                </optgroup>
              </select>
            </div>
            <div class="form-field">
              <label for="ze_role">Your Role <span class="req" aria-hidden="true">*</span></label>
              <select id="ze_role" name="role" required>
                <option value="" disabled selected>Select role&hellip;</option>
                <optgroup label="Decision Makers">
                  <option value="procurement_leader">Procurement Leader (CPO / VP / Director)</option>
                  <option value="finance_leader">Finance Leader (CFO / VP Finance)</option>
                </optgroup>
                <optgroup label="Practitioners">
                  <option value="procurement_team">Procurement Team Member</option>
                  <option value="it">IT / Systems Owner</option>
                </optgroup>
                <option value="other">Other</option>
              </select>
            </div>
          </div>
        </fieldset>

        <fieldset class="form-step">
          <legend class="form-step__legend">
            <span class="form-step__label">Step 3 of 3</span>
            <span class="form-step__title">Your Priority</span>
          </legend>
          <div class="form-grid">
            <div class="form-field form-field--full">
              <label for="ze_usecase">Primary Use Case <span class="req" aria-hidden="true">*</span></label>
              <select id="ze_usecase" name="use_case" required>
                <option value="" disabled selected>What should Merlin AI solve first?</option>
                <optgroup label="Upstream">
                  <option value="s2c">Sourcing &amp; Contract Management</option>
                  <option value="supplier_mgmt">Supplier Management &amp; Risk</option>
                </optgroup>
                <optgroup label="Downstream">
                  <option value="ap">Invoice Automation &amp; AP</option>
                </optgroup>
                <optgroup label="Full Platform">
                  <option value="s2p">End-to-End Source-to-Pay</option>
                </optgroup>
              </select>
            </div>
            <div class="form-field form-field--full">
              <label for="ze_notes">Anything else we should know?</label>
              <textarea id="ze_notes" name="notes" rows="4"
                        placeholder="Optional — current systems, timelines, constraints…"></textarea>
            </div>
          </div>
        </fieldset>

        <button type="submit" class="btn btn--primary btn--lg btn--block">Book My Demo</button>
        <p class="demo-form__microcopy">
          <span aria-hidden="true">&#128274;</span>
          Secure &amp; confidential. No spam, no credit card required.
        </p>
      </form>
    </div>
    <?php
    return (string) ob_get_clean();
});

/* ----------------------------------------------------------
   Handle fallback form submission (admin-post.php route)
---------------------------------------------------------- */
add_action('admin_post_nopriv_zycus_submit_demo', 'zycus_handle_demo_submission');
add_action('admin_post_zycus_submit_demo',        'zycus_handle_demo_submission');

function zycus_handle_demo_submission(): void {
    if (!isset($_POST['_wpnonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['_wpnonce'])), 'zycus_demo_form')) {
        wp_die('Invalid request.', 'Error', ['response' => 403]);
    }

    if (!empty($_POST['website_url'])) {
        wp_safe_redirect(home_url('/'));
        exit;
    }

    $first  = sanitize_text_field(wp_unslash($_POST['first_name'] ?? ''));
    $last   = sanitize_text_field(wp_unslash($_POST['last_name']  ?? ''));
    $email  = sanitize_email(wp_unslash($_POST['email']           ?? ''));
    $company = sanitize_text_field(wp_unslash($_POST['company_name'] ?? ''));

    if (!$first || !$last || !is_email($email) || !$company) {
        wp_safe_redirect(add_query_arg('error', '1', wp_get_referer() ?: home_url('/')));
        exit;
    }

    $to      = get_option('admin_email');
    $subject = 'New Zycus Demo Request — ' . $first . ' ' . $last;
    $body    = sprintf(
        "Name: %s %s\nEmail: %s\nCompany: %s\nSize: %s\nRole: %s\nUse Case: %s\nNotes: %s",
        $first, $last, $email, $company,
        sanitize_text_field(wp_unslash($_POST['company_size'] ?? '')),
        sanitize_text_field(wp_unslash($_POST['role']         ?? '')),
        sanitize_text_field(wp_unslash($_POST['use_case']     ?? '')),
        sanitize_textarea_field(wp_unslash($_POST['notes']    ?? ''))
    );
    wp_mail($to, $subject, $body);

    $redirect = add_query_arg([
        'fname' => rawurlencode($first),
        'email' => rawurlencode($email),
    ], home_url('/thank-you/'));

    wp_safe_redirect($redirect);
    exit;
}
