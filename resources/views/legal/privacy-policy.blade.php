@extends('layouts.app', [
    'title' => 'Privacy Policy | CS2 PickLab',
])

@section('content')
<section class="legal-page">
    <div class="legal-card">
        <p class="legal-kicker">
            Legal
        </p>

        <h1 class="legal-title">
            Privacy Policy
        </h1>

        <p class="legal-updated">
            Last updated: {{ now()->format('F j, Y') }}
        </p>

        <div class="legal-content">
            <p>
                CS2 PickLab (“CS2 PickLab,” “we,” “our,” or “us”) respects your privacy. This Privacy Policy explains how we collect, use, share, and protect information when you use cs2picklabs.com, dev.cs2picklabs.com, and related services.
            </p>

            <h2>Information We Collect</h2>

            <p>
                We may collect information you provide directly, including your name, email address, display name, profile details, gaming platform names, account credentials, order information, and communications you send to us.
            </p>

            <p>
                If you sign in using Google or another third-party provider, we may receive information such as your provider account ID, name, email address, and profile image, depending on the provider and your account permissions.
            </p>

            <p>
                We may also collect technical information automatically, including IP address, browser type, device information, pages viewed, referring URLs, session data, cookies, and similar usage data.
            </p>

            <h2>How We Use Information</h2>

            <p>We use information to:</p>

            <ul>
                <li>Provide and maintain the website;</li>
                <li>Create and manage user accounts;</li>
                <li>Process purchases, subscriptions, and account activity;</li>
                <li>Display Pick’em recommendations, match content, shop content, and user profile features;</li>
                <li>Send password reset links, service messages, and account notices;</li>
                <li>Improve site performance, security, and user experience;</li>
                <li>Prevent fraud, abuse, unauthorized access, and misuse;</li>
                <li>Comply with legal obligations.</li>
            </ul>

            <h2>Cookies, Analytics, and Advertising</h2>

            <p>
                We may use cookies and similar technologies to remember user sessions, measure traffic, improve the website, and support advertising or affiliate features.
            </p>

            <p>
                If we use Google Analytics, Google may collect and process information about how users interact with the site. Google requires sites using Google Analytics to disclose the use of Analytics and how data is collected and processed.
            </p>

            <p>
                If we use Google AdSense or other advertising partners, third-party vendors, including Google, may use cookies to serve ads based on your prior visits to this site or other websites. Google’s advertising cookies allow Google and its partners to serve ads based on visits to this site and/or other sites.
            </p>

            <p>
                Users may be able to manage personalized advertising preferences through Google’s ad settings or through browser/device privacy controls.
            </p>

            <h2>Affiliate Links</h2>

            <p>
                Some pages may include affiliate links. If you click an affiliate link and make a purchase, we may receive a commission at no additional cost to you. Affiliate relationships do not guarantee endorsement of a product, service, or merchant.
            </p>

            <h2>How We Share Information</h2>

            <p>
                We do not sell personal information. We may share information with service providers that help us operate the website, process payments, send email, provide analytics, secure the site, or deliver advertising and affiliate functionality.
            </p>

            <p>
                We may also disclose information if required by law, legal process, security needs, fraud prevention, or to protect the rights, safety, and property of CS2 PickLab, users, or others.
            </p>

            <h2>Account Security</h2>

            <p>
                You are responsible for maintaining the confidentiality of your login credentials and for activity that occurs under your account. Notify us if you believe your account has been compromised.
            </p>

            <h2>Data Retention</h2>

            <p>
                We retain information as long as reasonably necessary to provide services, maintain records, resolve disputes, enforce agreements, comply with legal obligations, and support security or fraud-prevention needs.
            </p>

            <h2>Your Choices</h2>

            <p>
                You may update profile information through your account settings. You may also request account assistance, correction, or deletion by contacting us.
            </p>

            <p>
                Browser settings may allow you to block or delete cookies. Some features may not work properly if cookies are disabled.
            </p>

            <h2>Children’s Privacy</h2>

            <p>
                CS2 PickLab is not intended for children under 13. We do not knowingly collect personal information from children under 13.
            </p>

            <h2>International Users</h2>

            <p>
                If you access the site from outside the United States, you understand that your information may be processed in the United States or other locations where our service providers operate.
            </p>

            <h2>Changes to This Policy</h2>

            <p>
                We may update this Privacy Policy from time to time. The updated version will be posted on this page with a revised “Last updated” date.
            </p>

            <h2>Contact</h2>

            <p>
                Questions about this Privacy Policy may be sent to:
                <strong>support@cs2picklabs.com</strong>
            </p>
        </div>
    </div>
</section>
@endsection