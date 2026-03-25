<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ModulosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
{
    $pathModulos = database_path('seeders/csv/modulos.csv');
    $pathRelaciones = database_path('seeders/csv/ciclo_modulo_relaciones.csv'); // El segundo CSV

    if (!file_exists($pathModulos) || !file_exists($pathRelaciones)) {
        $this->command->error("Faltan archivos CSV.");
        return;
    }

    // 1. CARGAMOS LAS RELACIONES EN UN ARRAY PARA CONSULTAR RÁPIDO
    // Queremos algo como: ['483' => '12242002', '484' => '12242002', ...]
    $relacionesRows = array_map('str_getcsv', file($pathRelaciones));
    $relHeader = array_map('trim', array_shift($relacionesRows));
    $mapaCiclos = [];
    foreach ($relacionesRows as $row) {
        $rel = array_combine($relHeader, $row);
        // Guardamos: el código del módulo es la llave, el código del ciclo el valor
        $mapaCiclos[trim($rel['cod_modulo'])] = trim($rel['cod_ciclo']);
    }

    // 2. PROCESAMOS LOS MÓDULOS
    $rows = array_map('str_getcsv', file($pathModulos));
    $header = array_map('trim', array_shift($rows));

    $data = [];
    foreach ($rows as $row) {
        if (count($row) < count($header)) continue;
        $rec = array_combine($header, $row);

        $codModulo = trim($rec['cod_modulo']);

        // Buscamos el código del ciclo que corresponde a este módulo en nuestro mapa
        $codCiclo = $mapaCiclos[$codModulo] ?? null;

        // Ahora buscamos el ID real (el número 1, 2, 3...) de ese ciclo en la DB
        $cicloId = null;
        if ($codCiclo) {
            $cicloId = DB::table('ciclos_formativos')
                ->where('codigo', $codCiclo)
                ->value('id');
        }

        $data[] = [
            'nombre' => trim($rec['nombre_modulo'] ?? ''),
            'codigo' => $codModulo,
            'ciclo_formativo_id' => $cicloId, // ¡Ya tenemos el ID!
            'descripcion' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    // 3. INSERTAR
    DB::transaction(function () use ($data) {
        foreach (array_chunk($data, 200) as $chunk) {
            DB::table('modulos')->upsert(
                $chunk,
                ['codigo'],
                ['nombre', 'ciclo_formativo_id', 'updated_at']
            );
        }
    });
}
}
