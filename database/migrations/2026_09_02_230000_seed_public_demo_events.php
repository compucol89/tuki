<?php

use App\Support\EventRefundPolicy;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();
        $languageId = (int) (DB::table('languages')->where('is_default', 1)->value('id')
            ?: DB::table('languages')->where('code', 'es')->value('id')
            ?: 8);

        $categoryIds = $this->ensureCategories($languageId, $now);
        $organizerIds = $this->ensureOrganizers($languageId, $now);
        $refundPolicy = EventRefundPolicy::canonicalPlainText();

        foreach ($this->events() as $event) {
            $organizerId = $organizerIds[$event['organizer']];
            $categoryId = $categoryIds[$event['category']];
            $eventId = $this->upsertEvent($event, $organizerId, $now);

            DB::table('event_contents')->updateOrInsert(
                ['slug' => $event['slug'], 'language_id' => $languageId],
                [
                    'event_id' => $eventId,
                    'event_category_id' => $categoryId,
                    'title' => $event['title'],
                    'description' => $event['description'],
                    'meta_keywords' => $event['meta_keywords'],
                    'meta_description' => $event['meta_description'],
                    'address' => $event['address'],
                    'country' => $event['country'],
                    'state' => $event['state'],
                    'city' => $event['city'],
                    'zip_code' => null,
                    'google_calendar_id' => null,
                    'refund_policy' => $refundPolicy,
                    'updated_at' => $now,
                    'created_at' => $now,
                ]
            );

            DB::table('tickets')->where('event_id', $eventId)->delete();

            foreach ($event['tickets'] as $ticket) {
                DB::table('tickets')->insert([
                    'event_id' => $eventId,
                    'event_type' => 'venue',
                    'title' => $ticket['title'],
                    'ticket_available_type' => 'limited',
                    'ticket_available' => $ticket['available'],
                    'max_ticket_buy_type' => 'limited',
                    'max_buy_ticket' => $ticket['max_buy'],
                    'description' => $ticket['description'],
                    'pricing_type' => $ticket['pricing_type'],
                    'price' => (string) $ticket['price'],
                    'f_price' => (float) $ticket['price'],
                    'early_bird_discount' => 'disable',
                    'early_bird_discount_amount' => null,
                    'early_bird_discount_type' => null,
                    'early_bird_discount_date' => null,
                    'early_bird_discount_time' => null,
                    'variations' => null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }

            DB::table('event_images')->where('event_id', $eventId)->delete();
            DB::table('event_images')->insert([
                'event_id' => $eventId,
                'image' => $event['gallery_image'],
                'format' => 'gallery',
                'generation_method' => $event['generation_method'],
                'source_image_hash' => null,
                'validation_ssim_score' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        $slugs = array_column($this->events(), 'slug');
        $eventIds = DB::table('event_contents')->whereIn('slug', $slugs)->pluck('event_id')->all();

        if (!empty($eventIds)) {
            DB::table('tickets')->whereIn('event_id', $eventIds)->delete();
            DB::table('event_images')->whereIn('event_id', $eventIds)->delete();
            DB::table('event_contents')->whereIn('event_id', $eventIds)->delete();
            DB::table('events')->whereIn('id', $eventIds)->delete();
        }

        DB::table('organizer_infos')
            ->whereIn('organizer_id', DB::table('organizers')->whereIn('email', array_keys($this->organizers()))->pluck('id'))
            ->delete();
        DB::table('organizers')->whereIn('email', array_keys($this->organizers()))->delete();

        DB::table('event_categories')
            ->whereIn('slug', ['arte-y-diseno', 'bienestar'])
            ->whereIn('image', ['demo-arte-diseno-2026.jpg', 'demo-bienestar-2026.jpg'])
            ->delete();
    }

    private function ensureCategories(int $languageId, mixed $now): array
    {
        $categories = [
            'fiestas' => ['name' => 'Fiestas', 'image' => '64562d5ae960a.png', 'serial_number' => 5],
            'negocios-y-networking' => ['name' => 'Negocios y Networking', 'image' => '64562d5ae960a.png', 'serial_number' => 6],
            'gastronomia' => ['name' => 'Gastronomía', 'image' => '64562d5ae960a.png', 'serial_number' => 7],
            'arte-y-diseno' => ['name' => 'Arte y Diseño', 'image' => 'demo-arte-diseno-2026.jpg', 'serial_number' => 8],
            'bienestar' => ['name' => 'Bienestar', 'image' => 'demo-bienestar-2026.jpg', 'serial_number' => 9],
        ];

        $ids = [];

        foreach ($categories as $slug => $category) {
            $existing = DB::table('event_categories')
                ->where('language_id', $languageId)
                ->where('slug', $slug)
                ->first();

            if ($existing) {
                $ids[$slug] = (int) $existing->id;
                continue;
            }

            $ids[$slug] = (int) DB::table('event_categories')->insertGetId([
                'name' => $category['name'],
                'language_id' => $languageId,
                'image' => $category['image'],
                'slug' => $slug,
                'status' => 1,
                'serial_number' => $category['serial_number'],
                'is_featured' => 'yes',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        return $ids;
    }

    private function ensureOrganizers(int $languageId, mixed $now): array
    {
        $ids = [];

        foreach ($this->organizers() as $email => $organizer) {
            $existing = DB::table('organizers')->where('email', $email)->first();
            $payload = [
                'photo' => null,
                'cover_photo' => null,
                'phone' => $organizer['phone'],
                'username' => $organizer['username'],
                'status' => '1',
                'amount' => 0,
                'email_verified_at' => $now,
                'email_verification_token' => null,
                'email_verification_sent_at' => null,
                'facebook' => null,
                'twitter' => null,
                'linkedin' => null,
                'website' => $organizer['website'],
                'instagram' => $organizer['instagram'],
                'tiktok' => null,
                'meta_pixel_id' => null,
                'theme_version' => 'light',
                'updated_at' => $now,
            ];

            if ($existing) {
                DB::table('organizers')->where('id', $existing->id)->update($payload);
                $ids[$organizer['key']] = (int) $existing->id;
            } else {
                $ids[$organizer['key']] = (int) DB::table('organizers')->insertGetId($payload + [
                    'email' => $email,
                    'password' => Hash::make(Str::random(40)),
                    'created_at' => $now,
                ]);
            }

            DB::table('organizer_infos')->updateOrInsert(
                ['organizer_id' => $ids[$organizer['key']], 'language_id' => $languageId],
                [
                    'name' => $organizer['name'],
                    'country' => $organizer['country'],
                    'city' => $organizer['city'],
                    'state' => $organizer['state'],
                    'zip_code' => null,
                    'address' => $organizer['address'],
                    'details' => $organizer['details'],
                    'designation' => $organizer['designation'],
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
        }

        return $ids;
    }

    private function upsertEvent(array $event, int $organizerId, mixed $now): int
    {
        $eventContent = DB::table('event_contents')->where('slug', $event['slug'])->first();
        $eventId = $eventContent && DB::table('events')->where('id', $eventContent->event_id)->exists()
            ? (int) $eventContent->event_id
            : null;

        $payload = [
            'organizer_id' => $organizerId,
            'thumbnail' => $event['thumbnail'],
            'og_image' => null,
            'ai_metadata' => json_encode(['source' => 'tukipass-public-demo-events'], JSON_UNESCAPED_UNICODE),
            'status' => '1',
            'manual_badge' => 'Demo',
            'date_type' => 'single',
            'countdown_status' => 1,
            'start_date' => $event['start_date'],
            'start_time' => $event['start_time'],
            'duration' => $event['duration'],
            'end_date' => $event['end_date'],
            'end_time' => $event['end_time'],
            'end_date_time' => $event['end_date_time'],
            'event_type' => 'venue',
            'event_addons_enabled' => 0,
            'limit_free_tickets_per_person' => 0,
            'free_tickets_per_person_limit' => 2,
            'is_featured' => 'yes',
            'latitude' => $event['latitude'],
            'longitude' => $event['longitude'],
            'instructions' => $event['instructions'],
            'meeting_url' => null,
            'ticket_logo' => null,
            'meta_pixel_id' => null,
            'google_analytics_id' => null,
            'tiktok_pixel_id' => null,
            'spotify_url' => null,
            'youtube_url' => null,
            'ticket_image' => null,
            'updated_at' => $now,
        ];

        if ($eventId !== null) {
            DB::table('events')->where('id', $eventId)->update($payload);
            return $eventId;
        }

        return (int) DB::table('events')->insertGetId($payload + [
            'views_count' => 0,
            'views_last_24h' => 0,
            'views_last_reset' => null,
            'created_at' => $now,
        ]);
    }

    private function organizers(): array
    {
        return [
            'pawsos.eventos@tukipass.com' => [
                'key' => 'pawsos',
                'username' => 'PawSos Eventos',
                'name' => 'PawSos Eventos',
                'phone' => '1128573839',
                'website' => 'https://infoboliches.com.ar/tour-de-bares-y-boliches/',
                'instagram' => 'https://www.instagram.com/tour_de_bares_palermo',
                'designation' => 'Organización y producción de eventos',
                'country' => 'Argentina',
                'state' => 'Buenos Aires',
                'city' => 'Ciudad Autónoma de Buenos Aires',
                'address' => 'Nicaragua 4346, Palermo, Ciudad Autónoma de Buenos Aires',
                'details' => 'PawSos Eventos organiza experiencias nocturnas, recorridos por bares, celebraciones grupales y salidas especiales en Buenos Aires.',
            ],
            'altura.palermo@tukipass.com' => [
                'key' => 'altura',
                'username' => 'Altura Palermo',
                'name' => 'Altura Palermo',
                'phone' => '11 6000 2401',
                'website' => 'https://tukipass.com',
                'instagram' => 'https://www.instagram.com/tukipass',
                'designation' => 'Experiencias corporativas y encuentros sociales',
                'country' => 'Argentina',
                'state' => 'Buenos Aires',
                'city' => 'Ciudad Autónoma de Buenos Aires',
                'address' => 'Humboldt 1890, Palermo',
                'details' => 'Productora demo especializada en encuentros de networking, after office y experiencias de marca en terrazas urbanas.',
            ],
            'distrito.creativo@tukipass.com' => [
                'key' => 'distrito',
                'username' => 'Distrito Creativo BA',
                'name' => 'Distrito Creativo BA',
                'phone' => '11 6000 2402',
                'website' => 'https://tukipass.com',
                'instagram' => 'https://www.instagram.com/tukipass',
                'designation' => 'Curaduría, diseño y experiencias culturales',
                'country' => 'Argentina',
                'state' => 'Buenos Aires',
                'city' => 'Ciudad Autónoma de Buenos Aires',
                'address' => 'Av. Caseros 1750, Distrito Creativo',
                'details' => 'Equipo demo dedicado a muestras, charlas y recorridos curatoriales para comunidades creativas.',
            ],
            'sabores.que.unen@tukipass.com' => [
                'key' => 'sabores',
                'username' => 'Sabores que Unen',
                'name' => 'Sabores que Unen',
                'phone' => '11 6000 2403',
                'website' => 'https://tukipass.com',
                'instagram' => 'https://www.instagram.com/tukipass',
                'designation' => 'Ferias gastronómicas y experiencias al aire libre',
                'country' => 'Argentina',
                'state' => 'Buenos Aires',
                'city' => 'Ciudad Autónoma de Buenos Aires',
                'address' => 'Nicaragua 4500, Palermo',
                'details' => 'Productora demo de encuentros gastronómicos con música, cocineros invitados y propuestas para compartir.',
            ],
            'natura.experiencias@tukipass.com' => [
                'key' => 'natura',
                'username' => 'Natura Experiencias',
                'name' => 'Natura Experiencias',
                'phone' => '11 6000 2404',
                'website' => 'https://tukipass.com',
                'instagram' => 'https://www.instagram.com/tukipass',
                'designation' => 'Retiros, bienestar y comunidad',
                'country' => 'Uruguay',
                'state' => 'Maldonado',
                'city' => 'Punta del Este',
                'address' => 'Natura Ecolodge, Punta del Este',
                'details' => 'Equipo demo enfocado en retiros de yoga, meditación, alimentación consciente y actividades de reconexión.',
            ],
            'club.demo@tukipass.com' => [
                'key' => 'club',
                'username' => 'Club Demo Producciones',
                'name' => 'Club Demo Producciones',
                'phone' => '11 1234 5678',
                'website' => 'https://tukipass.com',
                'instagram' => 'https://www.instagram.com/tukipass',
                'designation' => 'Fiestas, música urbana y nightlife',
                'country' => 'Argentina',
                'state' => 'Buenos Aires',
                'city' => 'Ciudad Autónoma de Buenos Aires',
                'address' => 'Av. Corrientes 5500, Villa Crespo',
                'details' => 'Productora demo de fiestas nocturnas con DJs invitados, barras y experiencias VIP.',
            ],
        ];
    }

    private function events(): array
    {
        $demoInstructions = 'Evento demo: podés simular tu selección online, pero no se generan reservas, entradas ni pagos reales.';

        return [
            [
                'organizer' => 'pawsos',
                'category' => 'fiestas',
                'title' => 'Tour de bares y boliches en Palermo - Recorrido viernes',
                'slug' => 'tour-de-bares-y-boliches-en-palermo-recorrido-viernes-demo',
                'thumbnail' => 'demo-palermo-bar-tour-2026.jpg',
                'gallery_image' => 'demo-palermo-bar-tour-2026.jpg',
                'generation_method' => 'user-provided-demo',
                'start_date' => '2026-09-04',
                'start_time' => '21:00',
                'duration' => '7h',
                'end_date' => '2026-09-05',
                'end_time' => '04:00',
                'end_date_time' => '2026-09-05 04:00:00',
                'latitude' => '-34.5909',
                'longitude' => '-58.4251',
                'address' => 'Nicaragua 4346, Palermo, Ciudad Autónoma de Buenos Aires',
                'country' => 'Argentina',
                'state' => 'Buenos Aires',
                'city' => 'Ciudad Autónoma de Buenos Aires',
                'description' => $this->barTourDescription('Recorrido del viernes 4 de septiembre. La experiencia empieza a las 21:00 y se realiza en la zona de Palermo, con punto de encuentro en Nicaragua 4346.'),
                'meta_keywords' => 'tour de bares Palermo, recorrido viernes, bares en Palermo, boliches Palermo, salida nocturna Buenos Aires, PawSos Eventos',
                'meta_description' => 'Recorrido demo por bares y boliches en Palermo: tres bares, pizza libre, shot de bienvenida, promos y cierre en boliche.',
                'instructions' => $demoInstructions,
                'tickets' => [
                    ['title' => 'Recorrido viernes 4 de septiembre', 'price' => 25000, 'pricing_type' => 'normal', 'available' => 80, 'max_buy' => 6, 'description' => 'Incluye recorrido por tres bares y un boliche, pizza libre, bebidas de regalo, shot de bienvenida, descuentos, promociones y beneficios del circuito.'],
                ],
            ],
            [
                'organizer' => 'pawsos',
                'category' => 'fiestas',
                'title' => 'Tour de bares y boliches en Palermo - Recorrido sábado',
                'slug' => 'tour-de-bares-y-boliches-en-palermo-recorrido-sabado-demo',
                'thumbnail' => 'demo-palermo-bar-tour-2026.jpg',
                'gallery_image' => 'demo-palermo-bar-tour-2026.jpg',
                'generation_method' => 'user-provided-demo',
                'start_date' => '2026-09-05',
                'start_time' => '21:00',
                'duration' => '7h',
                'end_date' => '2026-09-06',
                'end_time' => '04:00',
                'end_date_time' => '2026-09-06 04:00:00',
                'latitude' => '-34.5909',
                'longitude' => '-58.4251',
                'address' => 'Nicaragua 4346, Palermo, Ciudad Autónoma de Buenos Aires',
                'country' => 'Argentina',
                'state' => 'Buenos Aires',
                'city' => 'Ciudad Autónoma de Buenos Aires',
                'description' => $this->barTourDescription('Recorrido del sábado 5 de septiembre. La experiencia empieza a las 21:00 y se realiza en la zona de Palermo, con punto de encuentro en Nicaragua 4346.'),
                'meta_keywords' => 'tour de bares Palermo, recorrido sábado, bares en Palermo, boliches Palermo, salida nocturna Buenos Aires, PawSos Eventos',
                'meta_description' => 'Recorrido demo del sábado por bares y boliches en Palermo con pizza libre, shot de bienvenida, promos y cierre en boliche.',
                'instructions' => $demoInstructions,
                'tickets' => [
                    ['title' => 'Recorrido sábado 5 de septiembre', 'price' => 25000, 'pricing_type' => 'normal', 'available' => 90, 'max_buy' => 6, 'description' => 'Incluye recorrido por tres bares y un boliche, pizza libre, bebidas de regalo, shot de bienvenida, descuentos, promociones y beneficios del circuito.'],
                ],
            ],
            [
                'organizer' => 'pawsos',
                'category' => 'fiestas',
                'title' => 'Tour de bares en Palermo - Recorrido sin cargo',
                'slug' => 'tour-de-bares-en-palermo-recorrido-sin-cargo-demo',
                'thumbnail' => 'demo-palermo-bar-tour-2026.jpg',
                'gallery_image' => 'demo-palermo-bar-tour-2026.jpg',
                'generation_method' => 'user-provided-demo',
                'start_date' => '2026-09-02',
                'start_time' => '20:55',
                'duration' => '5h 35m',
                'end_date' => '2026-09-03',
                'end_time' => '02:30',
                'end_date_time' => '2026-09-03 02:30:00',
                'latitude' => '-34.5909',
                'longitude' => '-58.4251',
                'address' => 'Nicaragua 4346, Palermo, Ciudad Autónoma de Buenos Aires',
                'country' => 'Argentina',
                'state' => 'Buenos Aires',
                'city' => 'Ciudad Autónoma de Buenos Aires',
                'description' => $this->barTourDescription('Recorrido sin cargo sujeto a fechas disponibles. La experiencia empieza a las 21:00 y se realiza en la zona de Palermo, con punto de encuentro en Nicaragua 4346.', 'La reserva sin cargo no incluye todas las promociones. Completá tus datos y el equipo organizador te contacta para confirmar fechas, cupos y beneficios disponibles.'),
                'meta_keywords' => 'tour de bares gratis Palermo, recorrido sin cargo, bares en Palermo, salida nocturna Buenos Aires, PawSos Eventos',
                'meta_description' => 'Recorrido demo sin cargo por bares en Palermo, sujeto a disponibilidad de fechas y beneficios vigentes.',
                'instructions' => $demoInstructions,
                'tickets' => [
                    ['title' => 'Recorrido sin cargo', 'price' => 0, 'pricing_type' => 'free', 'available' => 50, 'max_buy' => 1, 'description' => 'No incluye todas las promociones. El recorrido sin cargo está sujeto a disponibilidad de fechas; completá tus datos y el equipo te contacta a la brevedad.'],
                ],
            ],
            [
                'organizer' => 'altura',
                'category' => 'negocios-y-networking',
                'title' => 'Rooftop Networking Night: conexiones, música y coctelería en Palermo',
                'slug' => 'rooftop-networking-night-conexiones-musica-y-cocteleria-en-palermo-demo',
                'thumbnail' => 'demo-rooftop-networking-2026.jpg',
                'gallery_image' => 'demo-rooftop-networking-2026.jpg',
                'generation_method' => 'user-provided-demo-asset',
                'start_date' => '2026-10-24',
                'start_time' => '19:30',
                'duration' => null,
                'end_date' => '2026-10-25',
                'end_time' => '01:00',
                'end_date_time' => '2026-10-25 01:00:00',
                'latitude' => '-34.5845',
                'longitude' => '-58.4333',
                'address' => 'Humboldt 1890, Palermo, Ciudad Autónoma de Buenos Aires',
                'country' => 'Argentina',
                'state' => 'Buenos Aires',
                'city' => 'Ciudad Autónoma de Buenos Aires',
                'description' => '<p><strong>Evento demo.</strong> Una noche de networking en altura para conversar, compartir proyectos y abrir nuevas oportunidades en un ambiente cuidado.</p><p>La propuesta combina coctelería, música suave y dinámicas simples para que la conexión fluya sin formalidades innecesarias.</p><ul><li>Recepción en rooftop desde las 19:30 hs.</li><li>Música ambiente, barra y espacios de conversación.</li><li>Dinámicas de presentación para profesionales, emprendedores y equipos.</li><li>Vista urbana, cupos limitados y acceso digital con reserva online.</li></ul>',
                'meta_keywords' => 'evento demo, TukiPass, rooftop networking night: conexiones, música y coctelería en palermo',
                'meta_description' => 'Networking en rooftop de Palermo con música, coctelería y espacios para conectar con profesionales, marcas y proyectos.',
                'instructions' => $demoInstructions,
                'tickets' => [
                    ['title' => 'Entrada general', 'price' => 18000, 'pricing_type' => 'normal', 'available' => 90, 'max_buy' => 4, 'description' => 'Acceso al rooftop, acreditación digital y participación en las dinámicas de networking.'],
                    ['title' => 'After office plus', 'price' => 28000, 'pricing_type' => 'normal', 'available' => 50, 'max_buy' => 4, 'description' => 'Incluye acceso general, consumición de bienvenida y mesa compartida prioritaria.'],
                    ['title' => 'VIP rooftop', 'price' => 45000, 'pricing_type' => 'normal', 'available' => 24, 'max_buy' => 2, 'description' => 'Incluye ingreso prioritario, sector reservado y selección de coctelería de la casa.'],
                ],
            ],
            [
                'organizer' => 'distrito',
                'category' => 'arte-y-diseno',
                'title' => 'Art & Design Weekend: exhibiciones, charlas y experiencias creativas',
                'slug' => 'art-design-weekend-exhibiciones-charlas-y-experiencias-creativas-demo',
                'thumbnail' => 'demo-art-design-weekend-2026.jpg',
                'gallery_image' => 'demo-art-design-weekend-2026.jpg',
                'generation_method' => 'user-provided-demo-asset',
                'start_date' => '2026-11-09',
                'start_time' => '16:00',
                'duration' => null,
                'end_date' => '2026-11-09',
                'end_time' => '21:00',
                'end_date_time' => '2026-11-09 21:00:00',
                'latitude' => '-34.6376',
                'longitude' => '-58.3644',
                'address' => 'Av. Caseros 1750, Distrito Creativo, Ciudad Autónoma de Buenos Aires',
                'country' => 'Argentina',
                'state' => 'Buenos Aires',
                'city' => 'Ciudad Autónoma de Buenos Aires',
                'description' => '<p><strong>Evento demo.</strong> Un encuentro para descubrir obra contemporánea, diseño argentino y conversaciones con referentes del circuito creativo.</p><p>Ideal para quienes quieren recorrer muestras, escuchar charlas y cerrar la tarde con una instancia de networking cultural.</p><ul><li>Exhibiciones de arte, diseño de objetos e instalaciones.</li><li>Charlas breves con artistas, curadores y estudios invitados.</li><li>Recorridos guiados por salas seleccionadas.</li><li>Espacio de café y encuentro para conectar con la comunidad creativa.</li></ul>',
                'meta_keywords' => 'evento demo, TukiPass, art & design weekend: exhibiciones, charlas y experiencias creativas',
                'meta_description' => 'Fin de semana de arte y diseño con exhibiciones, charlas, recorridos curatoriales y experiencias creativas en Buenos Aires.',
                'instructions' => $demoInstructions,
                'tickets' => [
                    ['title' => 'Pase general', 'price' => 12000, 'pricing_type' => 'normal', 'available' => 120, 'max_buy' => 6, 'description' => 'Acceso a exhibiciones, agenda abierta y recorrido libre por las salas.'],
                    ['title' => 'Charlas + networking', 'price' => 20000, 'pricing_type' => 'normal', 'available' => 70, 'max_buy' => 4, 'description' => 'Incluye pase general, acceso a charlas destacadas y encuentro de cierre.'],
                    ['title' => 'VIP preview', 'price' => 35000, 'pricing_type' => 'normal', 'available' => 28, 'max_buy' => 2, 'description' => 'Incluye ingreso prioritario, recorrido curatorial y asiento reservado en charlas principales.'],
                ],
            ],
            [
                'organizer' => 'sabores',
                'category' => 'gastronomia',
                'title' => 'Gourmet Street Food Fest: sabores, música y aire libre en Palermo',
                'slug' => 'gourmet-street-food-fest-sabores-musica-y-aire-libre-en-palermo-demo',
                'thumbnail' => 'demo-gourmet-street-food-2026.jpg',
                'gallery_image' => 'demo-gourmet-street-food-2026.jpg',
                'generation_method' => 'user-provided-demo-asset',
                'start_date' => '2026-11-17',
                'start_time' => '13:00',
                'duration' => null,
                'end_date' => '2026-11-17',
                'end_time' => '20:00',
                'end_date_time' => '2026-11-17 20:00:00',
                'latitude' => '-34.5879',
                'longitude' => '-58.4255',
                'address' => 'Nicaragua 4500, Palermo, Ciudad Autónoma de Buenos Aires',
                'country' => 'Argentina',
                'state' => 'Buenos Aires',
                'city' => 'Ciudad Autónoma de Buenos Aires',
                'description' => '<p><strong>Evento demo.</strong> Una feria gastronómica para comer rico, descubrir nuevos sabores y pasar la tarde al aire libre.</p><p>La experiencia reúne puestos seleccionados, música en vivo y opciones de degustación para recorrer sin apuro.</p><ul><li>Puestos gourmet, cocina urbana y propuestas de autor.</li><li>Música durante la tarde y zonas para sentarse.</li><li>Opciones dulces, saladas y bebidas de estación.</li><li>Entradas con beneficios para quienes reservan online.</li></ul>',
                'meta_keywords' => 'evento demo, TukiPass, gourmet street food fest: sabores, música y aire libre en palermo',
                'meta_description' => 'Festival gastronómico al aire libre en Palermo con puestos gourmet, música y experiencias de degustación.',
                'instructions' => $demoInstructions,
                'tickets' => [
                    ['title' => 'Entrada general', 'price' => 9000, 'pricing_type' => 'normal', 'available' => 180, 'max_buy' => 8, 'description' => 'Acceso a la feria, música y recorrido por todos los puestos gastronómicos.'],
                    ['title' => 'Pase degustación', 'price' => 22000, 'pricing_type' => 'normal', 'available' => 100, 'max_buy' => 6, 'description' => 'Incluye entrada general, cinco degustaciones seleccionadas y una bebida sin alcohol.'],
                    ['title' => 'Experiencia premium', 'price' => 38000, 'pricing_type' => 'normal', 'available' => 36, 'max_buy' => 4, 'description' => 'Incluye pase degustación, mesa prioritaria y selección especial de cocina invitada.'],
                ],
            ],
            [
                'organizer' => 'natura',
                'category' => 'bienestar',
                'title' => 'Retiro de bienestar: yoga, meditación y desconexión frente al agua',
                'slug' => 'retiro-de-bienestar-yoga-meditacion-y-desconexion-frente-al-agua-demo',
                'thumbnail' => 'demo-retiro-bienestar-2026.jpg',
                'gallery_image' => 'demo-retiro-bienestar-2026.jpg',
                'generation_method' => 'user-provided-demo-asset',
                'start_date' => '2026-10-24',
                'start_time' => '10:00',
                'duration' => null,
                'end_date' => '2026-10-26',
                'end_time' => '18:00',
                'end_date_time' => '2026-10-26 18:00:00',
                'latitude' => '-34.9369',
                'longitude' => '-54.9325',
                'address' => 'Natura Ecolodge, Punta del Este, Maldonado, Uruguay',
                'country' => 'Uruguay',
                'state' => 'Maldonado',
                'city' => 'Punta del Este',
                'description' => '<p><strong>Evento demo.</strong> Un retiro para bajar el ritmo, respirar mejor y volver a lo esencial con una agenda cuidada de bienestar.</p><p>Durante el encuentro hay prácticas guiadas, pausas de descanso, alimentación consciente y actividades grupales en un entorno natural.</p><ul><li>Clases de yoga y meditación para distintos niveles.</li><li>Charlas sobre hábitos, descanso y alimentación consciente.</li><li>Momentos de silencio, caminatas suaves y conexión con la naturaleza.</li><li>Cupos limitados para sostener una experiencia tranquila y personalizada.</li></ul>',
                'meta_keywords' => 'evento demo, TukiPass, retiro de bienestar: yoga, meditación y desconexión frente al agua',
                'meta_description' => 'Retiro de bienestar con yoga, meditación, alimentación consciente y actividades de conexión en entorno natural.',
                'instructions' => $demoInstructions,
                'tickets' => [
                    ['title' => 'Pase día', 'price' => 32000, 'pricing_type' => 'normal', 'available' => 60, 'max_buy' => 3, 'description' => 'Incluye actividades del día, clase de yoga, meditación guiada y brunch saludable.'],
                    ['title' => 'Fin de semana completo', 'price' => 86000, 'pricing_type' => 'normal', 'available' => 36, 'max_buy' => 2, 'description' => 'Incluye acceso a la agenda completa del retiro, talleres y comidas principales.'],
                    ['title' => 'Experiencia premium', 'price' => 140000, 'pricing_type' => 'normal', 'available' => 12, 'max_buy' => 1, 'description' => 'Incluye agenda completa, kit de bienvenida, grupo reducido y sesión de bienestar personalizada.'],
                ],
            ],
            [
                'organizer' => 'club',
                'category' => 'fiestas',
                'title' => 'Noche de Cachengue: perreo, beats en vivo y fiesta hasta tarde',
                'slug' => 'noche-de-cachengue-perreo-beats-en-vivo-y-fiesta-hasta-tarde-demo',
                'thumbnail' => 'demo-noche-cachengue-2026.jpg',
                'gallery_image' => 'demo-noche-cachengue-2026.jpg',
                'generation_method' => 'user-provided-demo-asset',
                'start_date' => '2026-10-18',
                'start_time' => '23:59',
                'duration' => null,
                'end_date' => '2026-10-19',
                'end_time' => '05:30',
                'end_date_time' => '2026-10-19 05:30:00',
                'latitude' => '-34.5994',
                'longitude' => '-58.4398',
                'address' => 'Av. Corrientes 5500, Villa Crespo, Ciudad Autónoma de Buenos Aires',
                'country' => 'Argentina',
                'state' => 'Buenos Aires',
                'city' => 'Ciudad Autónoma de Buenos Aires',
                'description' => '<p><strong>Evento demo.</strong> Una noche de cachengue para bailar sin pausa con DJs invitados, visuales y una pista pensada para arrancar tarde y terminar arriba.</p><p>La propuesta combina hits urbanos, perreo, barra premium y opciones VIP para grupos que quieren una experiencia más cómoda.</p><ul><li>Line up demo con DJs invitados durante toda la noche.</li><li>Beats en vivo, visuales y pista principal.</li><li>Barra premium y área VIP con cupos limitados.</li><li>Ingreso solo para mayores de 18 años con DNI físico.</li></ul>',
                'meta_keywords' => 'evento demo, TukiPass, noche de cachengue: perreo, beats en vivo y fiesta hasta tarde',
                'meta_description' => 'Fiesta de cachengue en Buenos Aires con DJs, barra premium, área VIP e ingreso para mayores de 18 años.',
                'instructions' => $demoInstructions,
                'tickets' => [
                    ['title' => 'Entrada general anticipada', 'price' => 15000, 'pricing_type' => 'normal', 'available' => 220, 'max_buy' => 6, 'description' => 'Acceso general a la fiesta con reserva online y entrada digital.'],
                    ['title' => 'General + consumición', 'price' => 22000, 'pricing_type' => 'normal', 'available' => 140, 'max_buy' => 6, 'description' => 'Incluye entrada general y una consumición seleccionada en barra.'],
                    ['title' => 'VIP barra premium', 'price' => 38000, 'pricing_type' => 'normal', 'available' => 60, 'max_buy' => 4, 'description' => 'Incluye ingreso prioritario, acceso al área VIP y beneficio en barra premium.'],
                    ['title' => 'Mesa grupo 4', 'price' => 120000, 'pricing_type' => 'normal', 'available' => 10, 'max_buy' => 1, 'description' => 'Incluye acceso para cuatro personas, mesa reservada y atención preferencial durante la noche.'],
                ],
            ],
        ];
    }

    private function barTourDescription(string $dateText, ?string $priceText = null): string
    {
        $priceText ??= 'Reservando online por Tukipass, el valor demo es de $25.000 por persona. Valor de referencia en puerta: $30.000 por persona, solo con reserva previa y sujeto a disponibilidad.';

        return '<p><strong>Evento demo.</strong> Publicación ficticia creada para probar la experiencia de Tukipass con información realista de un tour nocturno en Palermo.</p>'
            . '<p>El recorrido propone visitar tres bares y cerrar la noche en un boliche, con acompañamiento durante todo el circuito y beneficios pensados para grupos, cumpleaños y salidas entre amigos.</p>'
            . '<h3>Qué incluye</h3>'
            . '<ul><li>Recorrido por tres bares y un boliche en Palermo.</li><li>Pizza libre durante la experiencia.</li><li>Shot de bienvenida y bebidas de regalo según disponibilidad del recorrido.</li><li>Karaoke, juegos de mesa o juegos de alcohol en algunos bares.</li><li>Promociones, descuentos y opciones dos por uno en barra.</li><li>Ingreso al boliche sin hacer fila, sujeto a capacidad y condiciones del lugar.</li></ul>'
            . '<h3>Información del recorrido</h3>'
            . '<p>' . $dateText . '</p>'
            . '<p>' . $priceText . '</p>'
            . '<h3>Consultas</h3>'
            . '<p>WhatsApp: 11 2857 3839. Instagram: @tour_de_bares_palermo. Más información: infoboliches.com.ar/tour-de-bares-y-boliches/.</p>';
    }
};
