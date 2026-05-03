<?php

namespace App\Providers;

use App\Models\BasicSettings\PageHeading;
use App\Models\BasicSettings\SEO;
use App\Models\BasicSettings\SocialMedia;
use App\Models\ContactPage;
use App\Models\HomePage\Section;
use App\Models\Journal\Blog;
use App\Models\Language;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
  /**
   * Register any application services.
   *
   * @return void
   */
  public function register()
  {
    //
  }

  /**
   * Bootstrap any application services.
   *
   * @return void
   */
  public function boot()
  {
    if (app()->environment('production')) {
      URL::forceScheme('https');
    }

    if (!app()->runningInConsole()) {
      # code...
      Paginator::useBootstrap();

      $data = DB::table('basic_settings')->select('favicon', 'website_title', 'logo', 'timezone', 'preloader', 'event_guest_checkout_status', 'primary_color')->first();

      if ($data === null) {
        $data = (object) [
          'favicon' => '',
          'website_title' => config('app.name', 'Tukipass'),
          'logo' => '',
          'timezone' => config('app.timezone', 'UTC'),
          'preloader' => '',
          'event_guest_checkout_status' => 0,
          'primary_color' => '#F97316',
        ];
      } elseif (empty($data->timezone)) {
        $data->timezone = config('app.timezone', 'UTC');
      } else {
        try {
          new \DateTimeZone((string) $data->timezone);
        } catch (\Exception $e) {
          $data->timezone = config('app.timezone', 'UTC');
        }
      }

      // send this information to only back-end view files
      View::composer('backend.*', function ($view) {
        if (Auth::guard('admin')->check() == true) {
          $authAdmin = Auth::guard('admin')->user();
          $role = null;

          if (!is_null($authAdmin->role_id)) {
            $role = $authAdmin->role()->first();
          }
        }

        $language = Language::where('is_default', 1)->first();

        $websiteSettings = DB::table('basic_settings')->select('admin_theme_version', 'base_currency_symbol_position', 'base_currency_symbol', 'base_currency_text')->first();

        $footerText = $language->footerContent()->first();

        if (Auth::guard('admin')->check() == true) {
          $view->with('roleInfo', $role);
        }

        $view->with('defaultLang', $language);
        $view->with('settings', $websiteSettings);
        $view->with('footerTextInfo', $footerText);
      });

      // send this information to only back-end view files
      View::composer('organizer.*', function ($view) {


        $language = Language::where('is_default', 1)->first();

        //$websiteSettings = DB::table('basic_settings')->select('admin_theme_version')->first();
        $websiteSettings = DB::table('basic_settings')->select('admin_theme_version', 'base_currency_symbol', 'base_currency_symbol_position', 'base_currency_text', 'base_currency_text_position', 'base_currency_rate', 'organizer_email_verification')->first();

        $footerText = $language->footerContent()->first();


        $view->with('defaultLang', $language);
        $view->with('settings', $websiteSettings);
        $view->with('footerTextInfo', $footerText);
      });


      // send this information to only front-end view files
      View::composer('frontend.*', function ($view) {
        $cacheTTL = now()->addHours(6);

        $cachedBasicData = Cache::remember('frontend_basic_settings', $cacheTTL, function () {
          return DB::table('basic_settings')->select('theme_version', 'footer_logo', 'primary_color', 'breadcrumb_overlay_color', 'breadcrumb_overlay_opacity', 'breadcrumb', 'email_address', 'contact_number', 'address', 'latitude', 'longitude', 'base_currency_symbol', 'base_currency_symbol_position', 'base_currency_text', 'base_currency_text_position', 'base_currency_rate', 'is_shop_rating', 'facebook_login_status', 'google_login_status', 'google_recaptcha_status')->first();
        });

        if ($cachedBasicData === null) {
          $cachedBasicData = (object) [
            'theme_version' => 3,
            'footer_logo' => '',
            'primary_color' => '#F97316',
            'breadcrumb_overlay_color' => '#000000',
            'breadcrumb_overlay_opacity' => '0.5',
            'breadcrumb' => '',
            'email_address' => '',
            'contact_number' => '',
            'address' => '',
            'latitude' => '',
            'longitude' => '',
            'base_currency_symbol' => '$',
            'base_currency_symbol_position' => 'left',
            'base_currency_text' => 'ARS',
            'base_currency_text_position' => 'left',
            'base_currency_rate' => 1,
            'is_shop_rating' => 0,
            'facebook_login_status' => 0,
            'google_login_status' => 0,
            'google_recaptcha_status' => 0,
          ];
        }

        $cachedAllLanguages = Cache::remember('frontend_all_languages', $cacheTTL, function () {
          return Language::all();
        });

        $cachedLanguageEs = Cache::remember('frontend_language_es', $cacheTTL, function () {
          return Language::where('code', 'es')->first();
        });

        $language = $cachedLanguageEs;
        if (!$language) {
          $locale = null;
          if (Session::has('lang')) {
            $locale = Session::get('lang');
          }
          if (empty($locale)) {
            $language = Language::where('is_default', 1)->first();
          } else {
            $language = Language::where('code', $locale)->first();
            if (empty($language)) {
              $language = Language::where('is_default', 1)->first();
            }
          }
        }

        $cachedSocialMedias = Cache::remember('frontend_social_medias', $cacheTTL, function () {
          return SocialMedia::orderBy('serial_number', 'asc')->get();
        });

        $cachedSeo = Cache::remember('frontend_seo_' . $language->id, $cacheTTL, function () use ($language) {
          return SEO::where('language_id', $language->id)->first();
        });

        $cachedPageHeading = Cache::remember('frontend_page_heading_' . $language->id, $cacheTTL, function () use ($language) {
          return PageHeading::where('language_id', $language->id)->first();
        });

        $cachedMenuBuilder = Cache::remember('frontend_menu_builder_' . $language->id, $cacheTTL, function () use ($language) {
          return $language->menuInfo()->first();
        });

        if (is_null($cachedMenuBuilder)) {
          $menus = json_encode([]);
        } else {
          $menus = $cachedMenuBuilder->menus;
        }

        $cachedAnnouncementPopup = Cache::remember('frontend_announcement_popup_' . $language->id, $cacheTTL, function () use ($language) {
          return $language->announcementPopup()->where('status', 1)->orderBy('serial_number', 'asc')->get();
        });

        $cachedCookieAlert = Cache::remember('frontend_cookie_alert_' . $language->id, $cacheTTL, function () use ($language) {
          return $language->cookieAlertInfo()->first();
        });

        $cachedSectionStatus = Cache::remember('frontend_section_status', $cacheTTL, function () {
          return Section::query()->pluck('footer_section_status')->first();
        });

        if ($cachedSectionStatus == 1) {
          $cachedFooterContent = Cache::remember('frontend_footer_content_' . $language->id, $cacheTTL, function () use ($language) {
            return $language->footerContent()->first();
          });

          $cachedQuickLinks = Cache::remember('frontend_quick_links_' . $language->id, $cacheTTL, function () use ($language) {
            return $language->footerQuickLink()->orderBy('serial_number', 'asc')->get();
          });

          if ($cachedBasicData->theme_version != 3) {
            $blogs = Blog::join('blog_informations', 'blogs.id', '=', 'blog_informations.blog_id')
              ->where('blog_informations.language_id', '=', $language->id)
              ->select('blogs.image', 'blogs.created_at', 'blog_informations.title', 'blog_informations.slug')
              ->orderByDesc('blogs.created_at')
              ->limit(3)
              ->get();
          }

          if ($cachedBasicData->theme_version == 2) {
            $newsletterTitle = $language->newsletterSec()->pluck('title')->first();
          }
        }

        $bex = ContactPage::where('language_id', $language->id)->first();

        $view->with('basicInfo', $cachedBasicData);
        $view->with('seo', $cachedSeo);
        $view->with('bex', $bex);
        $view->with('allLanguageInfos', $cachedAllLanguages);
        $view->with('currentLanguageInfo', $language);
        $view->with('socialMediaInfos', $cachedSocialMedias);
        $view->with('menuInfos', $menus);
        $view->with('popupInfos', $cachedAnnouncementPopup);
        $view->with('cookieAlertInfo', $cachedCookieAlert);
        $view->with('footerSecStatus', $cachedSectionStatus);
        $view->with('pageHeading', $cachedPageHeading);

        if ($cachedSectionStatus == 1) {
          $view->with('footerInfo', $cachedFooterContent);
          $view->with('quickLinkInfos', $cachedQuickLinks);

          if ($cachedBasicData->theme_version != 3) {
            $view->with('latestBlogInfos', $blogs);
          }

          if ($cachedBasicData->theme_version == 2) {
            $view->with('newsletterTitle', $newsletterTitle);
          }
        }
      });


      // send this information to both front-end & back-end view files
      View::share(['websiteInfo' => $data]);
    }
  }
}
