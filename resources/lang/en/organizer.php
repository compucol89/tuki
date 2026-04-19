<?php

return [
    'login' => [
        'page_heading' => 'Organizer sign in',

        'seo' => [
            'meta_description_default' => 'Sign in to your Tukipass organizer dashboard to manage events, ticket sales, and attendees in one place.',
            'meta_keywords_default' => 'Tukipass organizer, event dashboard, sell tickets, login',
            'robots' => 'noindex, follow',
            'og_image_alt' => ':site logo',
        ],

        'visual_title_line1' => 'Sell more tickets',
        'visual_title_line2' => 'with less friction.',
        'visual_subtitle' => 'Publish your event, get paid online, and run sales and attendees from one dashboard. Built for producers, venues, and agencies.',

        'stats' => [
            ['num' => 'Online sales', 'label' => 'Tickets & bookings'],
            ['num' => 'Get paid', 'label' => 'Integrated payments'],
            ['num' => 'Your ops', 'label' => 'Reports & control'],
        ],

        'logo_alt' => ':site logo',

        'form_eyebrow' => 'Organizer dashboard',
        'form_title' => 'Welcome back',
        'form_subtitle' => 'Sign in to manage events, sales, and attendees. New here? Create a free account and start when you are ready.',

        'username_label' => 'Username',
        'username_placeholder' => 'Your organizer username',
        'password_label' => 'Password',
        'password_placeholder' => 'Your password',
        'forgot_password' => 'Forgot your password?',

        'submit' => 'Sign in to dashboard',
        'loading' => 'Please wait…',

        'footer_no_account' => 'Don’t have an organizer account yet?',
        'footer_signup' => 'Create one free and start selling',
    ],

    'signup' => [
        'page_heading' => 'Organizer sign up',

        'seo' => [
            'meta_description_default' => 'Create your Tukipass organizer account: publish events, sell tickets online, and run sales and attendees from one dashboard.',
            'meta_keywords_default' => 'Tukipass organizer signup, sell tickets, event dashboard',
            'robots' => 'noindex, follow',
            'og_image_alt' => ':site logo',
        ],

        'visual_title_line1' => 'Sell with clarity',
        'visual_title_line2' => 'and stay in control.',
        'visual_subtitle' => 'Publish your event, get paid online, and run sales and attendees in one place—less back-and-forth, more focus on the show.',

        'stats' => [
            ['num' => 'Free to join', 'label' => 'No signup fee'],
            ['num' => 'Get paid online', 'label' => 'Integrated payments'],
            ['num' => 'One dashboard', 'label' => 'Sales & reports'],
        ],

        'stats_aria_label' => 'Why create an organizer account',

        'aria_toggle_password' => 'Show or hide password',

        'logo_alt' => ':site logo',

        'form_title' => 'Create your organizer account',
        'form_subtitle' => 'Enter your details and continue to the dashboard to set up your event. No card required to sign up—just what you need to operate.',

        'field_name_label' => 'Full name',
        'field_name_placeholder' => 'As on your ID',

        'field_username_label' => 'Username',
        'field_username_placeholder' => 'Choose a unique username',

        'field_email_label' => 'Email',
        'field_email_placeholder' => 'For notifications and billing',

        'field_password_label' => 'Password',
        'field_password_placeholder' => 'At least 6 characters',

        'field_password_confirm_label' => 'Confirm password',
        'field_password_confirm_placeholder' => 'Re-enter your password',

        'password_mismatch' => 'Passwords do not match.',

        'password_strength' => [
            'very_weak' => 'Very weak',
            'weak' => 'Weak',
            'good' => 'Good',
            'strong' => 'Strong',
        ],

        'submit' => 'Create account and continue',
        'loading' => 'Please wait…',

        'footer_has_account' => 'Already have an organizer account?',
        'footer_login' => 'Sign in',
    ],
];
