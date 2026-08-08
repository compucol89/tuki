<?php

namespace App\Services;

use App\Models\Organizer;

/**
 * Fuente de verdad de la completitud del perfil de un organizador.
 *
 * Define los pasos obligatorios para que un organizador sea público
 * (aparezca en /organizadores y se indexe) y los expone para la UI
 * (checklist del builder de perfil) y para las consultas del directorio.
 */
class OrganizerProfileChecklistService
{
    /**
     * Pasos del perfil. Orden = orden de armado sugerido.
     *
     * @return array<int, array{key: string, label: string, complete: bool, hint: string}>
     */
    public function steps(Organizer $organizer): array
    {
        $info = $organizer->organizer_info;

        $profileName = trim((string) ($info->name ?? ''));
        $profileBio = trim((string) ($info->details ?? ''));
        $profileLocation = trim(implode(' ', array_filter([
            $info->city ?? '',
            $info->state ?? '',
            $info->country ?? '',
            $info->address ?? '',
        ])));

        $socialFields = [
            $organizer->website,
            $organizer->instagram,
            $organizer->tiktok,
            $organizer->facebook,
            $organizer->twitter,
            $organizer->linkedin,
        ];
        $socialCount = collect($socialFields)
            ->filter(fn ($value) => trim((string) $value) !== '')
            ->count();

        $hasPublishedPastEvent = $this->hasPublishedPastEvent($organizer);

        return [
            [
                'key' => 'photo',
                'label' => __('Foto de perfil'),
                'complete' => !empty($organizer->photo),
                'hint' => __('Usá una imagen cuadrada y reconocible.'),
            ],
            [
                'key' => 'cover',
                'label' => __('Portada'),
                'complete' => !empty($organizer->cover_photo),
                'hint' => __('Mostrá ambiente, público o escenario.'),
            ],
            [
                'key' => 'name',
                'label' => __('Nombre público'),
                'complete' => $profileName !== '' && $profileName !== $organizer->username,
                'hint' => __('Que coincida con tu marca en redes.'),
            ],
            [
                'key' => 'description',
                'label' => __('Descripción clara'),
                'complete' => mb_strlen($profileBio) >= 80,
                'hint' => __('Mínimo 80 caracteres con qué hacés y dónde.'),
            ],
            [
                'key' => 'location',
                'label' => __('Ubicación'),
                'complete' => $profileLocation !== '',
                'hint' => __('Ayuda a búsquedas por ciudad o país.'),
            ],
            [
                'key' => 'social',
                'label' => __('Redes o sitio web'),
                'complete' => $socialCount > 0,
                'hint' => __('Refuerzan identidad para Google e IA.'),
            ],
            [
                'key' => 'email_verified',
                'label' => __('Email verificado'),
                'complete' => !is_null($organizer->email_verified_at),
                'hint' => __('Confirmá tu email para validar el contacto.'),
            ],
            [
                'key' => 'event',
                'label' => __('Experiencia real'),
                'complete' => $hasPublishedPastEvent,
                'hint' => __('Mínimo un evento publicado y ya realizado.'),
            ],
        ];
    }

    /**
     * ¿El perfil cumple TODOS los pasos obligatorios?
     */
    public function isComplete(Organizer $organizer): bool
    {
        return collect($this->steps($organizer))->every(fn ($step) => $step['complete']);
    }

    /**
     * Paso clave: mínimo un evento publicado (status 1) y con fecha de fin pasada.
     */
    public function hasPublishedPastEvent(Organizer $organizer): bool
    {
        return $organizer->events()
            ->where('status', 1)
            ->where('end_date', '<', now())
            ->exists();
    }

    /**
     * Cantidad de pasos completados.
     */
    public function completedCount(Organizer $organizer): int
    {
        return collect($this->steps($organizer))->where('complete', true)->count();
    }
}
