const fs = require('fs');
const path = require('path');

const root = path.resolve(__dirname, '..');

function read(relativePath) {
  return fs.readFileSync(path.join(root, relativePath), 'utf8');
}

function assertContains(file, pattern, label) {
  const source = read(file);
  const matched = pattern instanceof RegExp ? pattern.test(source) : source.includes(pattern);
  if (!matched) {
    throw new Error(`${file} is missing ${label}`);
  }
}

function assertNotContains(file, pattern, label) {
  const source = read(file);
  const matched = pattern instanceof RegExp ? pattern.test(source) : source.includes(pattern);
  if (matched) {
    throw new Error(`${file} should not contain ${label}`);
  }
}

const organizerMigration = fs.readdirSync(path.join(root, 'database/migrations'))
  .find((file) => file.endsWith('_add_profile_fields_to_organizers_table.php'));

if (!organizerMigration) {
  throw new Error('Missing organizer profile fields migration');
}

assertContains('app/Models/Organizer.php', "'cover_photo'", 'cover_photo fillable');
assertContains('app/Models/Organizer.php', "'website'", 'website fillable');
assertContains('app/Models/Organizer.php', "'instagram'", 'instagram fillable');
assertContains('app/Models/Organizer.php', "'tiktok'", 'tiktok fillable');
assertContains('app/Models/Organizer.php', "'meta_pixel_id'", 'meta_pixel_id fillable');

assertContains('app/Http/Controllers/FrontEnd/OrganizerController.php', '$upcomingEvents', 'upcoming events query');
assertContains('app/Http/Controllers/FrontEnd/OrganizerController.php', '$pastEvents', 'past events query');
assertContains('app/Http/Controllers/FrontEnd/OrganizerController.php', "where('events.status', 1)", 'published event filter');
assertContains('app/Http/Controllers/FrontEnd/OrganizerController.php', "where('events.end_date_time', '>=',", 'upcoming end date filter');
assertContains('app/Http/Controllers/FrontEnd/OrganizerController.php', "where('events.end_date_time', '<',", 'past end date filter');

assertContains('resources/views/frontend/organizer/details.blade.php', 'ProfilePage', 'ProfilePage JSON-LD');
assertContains('resources/views/frontend/organizer/details.blade.php', 'ItemList', 'event ItemList JSON-LD');
assertContains('resources/views/frontend/organizer/details.blade.php', 'org-profile-pixel-noscript', 'body noscript Meta Pixel fallback');
assertContains('resources/views/frontend/organizer/details.blade.php', "'Contact'", 'Meta Pixel Contact event');
assertContains('resources/views/frontend/organizer/details.blade.php', 'org-timeline--upcoming', 'upcoming events section');
assertContains('resources/views/frontend/organizer/details.blade.php', 'org-timeline--past', 'past events section');
assertContains('resources/views/frontend/organizer/details.blade.php', 'org-timeline--single-upcoming', 'single upcoming layout flag');
assertContains('resources/views/frontend/organizer/details.blade.php', 'org-active-agenda', 'single active agenda layout');
assertContains('resources/views/frontend/organizer/details.blade.php', 'frontend.partials.event-card', 'home event card partial');
assertContains('resources/views/frontend/organizer/details.blade.php', 'org-archive-card', 'past event archive card');
assertContains('resources/views/frontend/organizer/details.blade.php', 'data-org-share-profile', 'profile share button');
assertContains('resources/views/frontend/organizer/details.blade.php', 'data-org-copy-profile', 'profile copy link button');
assertContains('resources/views/frontend/organizer/details.blade.php', "querySelectorAll('[data-org-share-profile]')", 'multiple share buttons handler');
assertContains('resources/views/frontend/organizer/details.blade.php', "querySelectorAll('[data-org-contact-pixel]')", 'multiple contact pixel handler');
assertContains('resources/views/frontend/organizer/details.blade.php', '$eventPlaceholderUrl', 'event image fallback');
assertContains('resources/views/frontend/organizer/details.blade.php', "'eventImageFallbackUrl' => $coverUrl", 'organizer event card image fallback');
assertContains('resources/views/frontend/organizer/details.blade.php', '$eventsCssPath', 'events CSS loading');
assertContains('resources/views/frontend/organizer/details.blade.php', '$homeCssPath', 'home CSS loading');
assertContains('resources/views/frontend/organizer/details.blade.php', '@foreach($upcomingEvents as $event)', 'all upcoming events home-card loop');
assertNotContains('resources/views/frontend/organizer/details.blade.php', 'org-profile-about', 'separate organizer about section');
assertNotContains('resources/views/frontend/organizer/details.blade.php', 'org-featured-event', 'custom featured upcoming event');
assertNotContains('resources/views/frontend/organizer/details.blade.php', 'upcomingListEvents', 'split upcoming event list');

assertContains('resources/views/frontend/partials/event-card.blade.php', '$eventImageFallbackUrl', 'optional event card fallback image');

assertContains('resources/views/backend/end-user/organizer/edit.blade.php', 'meta_pixel_id', 'admin Meta Pixel input');
assertContains('resources/views/organizer/edit-profile.blade.php', 'meta_pixel_id', 'organizer Meta Pixel input');
assertContains('resources/views/organizer/edit-profile.blade.php', 'opb-next-actions', 'organizer profile next actions');
assertContains('resources/views/organizer/edit-profile.blade.php', 'opb-readiness-list', 'organizer profile SEO and Meta readiness list');
assertContains('resources/views/organizer/edit-profile.blade.php', 'data-profile-bio-count', 'organizer profile bio character counter');
assertContains('app/Http/Controllers/BackEnd/Organizer/OrganizerController.php', 'profileDashboard', 'organizer dashboard profile readiness data');
assertContains('app/Http/Controllers/BackEnd/Organizer/OrganizerController.php', 'Subí portada', 'direct cover upload dashboard action');
assertContains('app/Http/Controllers/BackEnd/Organizer/OrganizerController.php', 'Agregá Instagram', 'direct Instagram dashboard action');
assertContains('app/Http/Controllers/BackEnd/Organizer/OrganizerController.php', 'Publicá tu primer evento', 'direct first event dashboard action');
assertContains('app/Http/Controllers/BackEnd/Organizer/OrganizerController.php', "route('choose-event-type')", 'first event action points to event type chooser');
assertContains('resources/views/organizer/index.blade.php', 'od-profile-score', 'organizer dashboard profile score');
assertContains('resources/views/organizer/index.blade.php', 'od-profile-score__actions', 'organizer dashboard profile action links');
assertContains('resources/views/organizer/index.blade.php', 'od-profile-score__action-hint', 'organizer dashboard action hint copy');
assertContains('resources/views/organizer/index.blade.php', 'route(\'organizer.edit.profile\')', 'organizer dashboard edit profile link');
assertContains('app/Http/Controllers/BackEnd/Organizer/OrganizerManagementController.php', 'profileQualityByOrganizer', 'admin organizer profile quality data');
assertContains('app/Http/Controllers/BackEnd/Organizer/OrganizerManagementController.php', 'profileOpportunityFilters', 'admin organizer profile opportunity filters');
assertContains('app/Http/Controllers/BackEnd/Organizer/OrganizerManagementController.php', 'applyOrganizerOpportunityFilter', 'admin organizer profile filter query');
assertContains('resources/views/backend/end-user/organizer/index.blade.php', 'admin-profile-quality', 'admin organizer profile quality column');
assertContains('resources/views/backend/end-user/organizer/index.blade.php', 'admin-profile-quality__missing', 'admin organizer missing profile signals');
assertContains('resources/views/backend/end-user/organizer/index.blade.php', 'admin-profile-filters', 'admin organizer profile filter controls');
assertContains('resources/views/backend/end-user/organizer/index.blade.php', 'name="profile_filter"', 'admin organizer profile filter select');
assertContains('resources/views/backend/end-user/organizer/index.blade.php', 'Perfil público', 'admin organizer public profile label');
assertContains('app/Http/Controllers/BackEnd/Organizer/OrganizerManagementController.php', 'profileQualityDetail', 'admin organizer detail profile quality data');
assertContains('resources/views/backend/end-user/organizer/details.blade.php', 'admin-profile-diagnostic', 'admin organizer detail quality diagnostic');
assertContains('resources/views/backend/end-user/organizer/details.blade.php', 'admin-profile-diagnostic__signals', 'admin organizer detail quality signals');
assertContains('resources/views/backend/end-user/organizer/details.blade.php', 'Impacto', 'admin organizer detail impact copy');

console.log('Organizer profile static verification passed');
