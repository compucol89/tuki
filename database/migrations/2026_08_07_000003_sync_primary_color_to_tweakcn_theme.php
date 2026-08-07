<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Alinea el primary_color de la base de datos con el primary canónico
     * del theme TweakCN (#e05d38) para eliminar el doble primary (#F97316).
     * La personalización admin sigue activa: alimenta la cadena canónica.
     */
    public function up(): void
    {
        // El campo almacena el hex sin '#', tal como lo envía el jscolor del admin
        DB::table('basic_settings')
            ->where('uniqid', 12345)
            ->update(['primary_color' => 'e05d38']);
    }

    public function down(): void
    {
        DB::table('basic_settings')
            ->where('uniqid', 12345)
            ->update(['primary_color' => 'F97316']);
    }
};
