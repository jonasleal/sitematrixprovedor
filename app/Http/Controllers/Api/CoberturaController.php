<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PoligonoCobertura;

class CoberturaController extends Controller
{
    public function check(Request $request)
    {
        $lat = $request->lat;
        $lng = $request->lng;

        // Se não mandou coordenada, negamos por segurança
        if (!$lat || !$lng) {
            return response()->json(['tem_cobertura' => false]);
        }

        // Pega todos os seus desenhos ativos do banco
        $poligonos = PoligonoCobertura::where('ativo', true)->get();

        foreach ($poligonos as $poligono) {
            // Usa matemática geométrica para ver se o ponto do cliente está dentro do desenho
            if ($this->pontoDentroDoPoligono($lat, $lng, $poligono->coordenadas)) {
                return response()->json([
                    'tem_cobertura' => true, 
                    'area_nome' => $poligono->nome 
                ]);
            }
        }

        return response()->json(['tem_cobertura' => false]);
    }

    // Algoritmo Ray Casting (Lógica Geométrica)
    private function pontoDentroDoPoligono($latitude, $longitude, $polygon)
    {
        $vertices = count($polygon);
        $intersections = 0;

        for ($i = 0, $j = $vertices - 1; $i < $vertices; $j = $i++) {
            // Suporta formatos diferentes de salvamento do Leaflet
            $lat_i = $polygon[$i]['lat'] ?? $polygon[$i][0];
            $lng_i = $polygon[$i]['lng'] ?? $polygon[$i][1];
            $lat_j = $polygon[$j]['lat'] ?? $polygon[$j][0];
            $lng_j = $polygon[$j]['lng'] ?? $polygon[$j][1];

            if ((($lng_i > $longitude) != ($lng_j > $longitude)) &&
                ($latitude < ($lat_j - $lat_i) * ($longitude - $lng_i) / ($lng_j - $lng_i) + $lat_i)) {
                $intersections++;
            }
        }

        // Se o número de interseções for ímpar, o ponto está DENTRO do polígono!
        return ($intersections % 2) != 0;
    }
}