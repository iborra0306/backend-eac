<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CriteriosEvaluacionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ruta
        $path = database_path('seeders/csv/criterios_evaluacion.csv');

        // Control de errores
        if (!file_exists($path)) {
            $this->command->error("CSV no encontrado: $path");
            return;
        }

        // Leer todas las líneas y parsear con str_getcsv
        $rows = array_map('str_getcsv', file($path));

        // El primer registro es la cabecera
        $header = array_map('trim', array_shift($rows));

        $data = [];
        foreach ($rows as $row) {
            if (count($row) < count($header)) continue;
            $rec = array_combine($header, $row);

            $idModulo = trim($rec['cod_modulo']);
            $idRa = trim($rec['id_ra']);

            // Buscamos el ID del RA que insertamos en el seeder anterior
            // Filtramos por el código del RA y por el módulo al que pertenece
            $raId = DB::table('resultados_aprendizaje')
                ->where('codigo', $idRa)
                ->whereIn('modulo_id', function($query) use ($idModulo) {
                    $query->select('id')->from('modulos')->where('codigo', $idModulo);
                })
                ->value('id');

            if ($raId === null) {
                // Si no lo encuentra, nos saltamos esta fila en lugar de intentar meter un null
                continue;
            }

            $data[] = [
                'resultado_aprendizaje_id' => $raId,
                'codigo' => trim($rec['id_criterio']),
                'descripcion' => trim($rec['definicion'] ?? ''),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // Insertar/actualizar usando upsert para evitar duplicados por 'codigo'
        DB::transaction(function () use ($data) {
            foreach (array_chunk($data, 200) as $chunk) {
                DB::table('criterios_evaluacion')->upsert(
                    $chunk,
                    ['codigo'], // llave única para evitar duplicados
                    ['descripcion', 'updated_at']
                );
            }
        });

    }
}
