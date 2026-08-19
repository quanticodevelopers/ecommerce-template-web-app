<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Seeder;

class BrandSeeder extends Seeder
{
    public function run(): void
    {
        $brands = [
            ['Samsung', 'samsung', 'BR0001', 'Tecnología y electrodomésticos para una vida conectada.'],
            ['Apple', 'apple', 'BR0002', 'Dispositivos personales, computadoras y accesorios premium.'],
            ['Xiaomi', 'xiaomi', 'BR0003', 'Tecnología inteligente con una propuesta accesible.'],
            ['Lenovo', 'lenovo', 'BR0004', 'Computadoras confiables para trabajo, estudio y gaming.'],
            ['HP', 'hp', 'BR0005', 'Equipos de cómputo y soluciones para productividad.'],
            ['LG', 'lg', 'BR0006', 'Electrónica y electrodomésticos para el hogar.'],
            ['Sony', 'sony', 'BR0007', 'Entretenimiento, imagen y sonido de alto rendimiento.'],
            ['JBL', 'jbl', 'BR0008', 'Audio portátil y personal con sonido potente.'],
            ['Whirlpool', 'whirlpool', 'BR0009', 'Electrodomésticos duraderos para cocina y lavandería.'],
            ['Mabe', 'mabe', 'BR0010', 'Línea blanca práctica para hogares latinoamericanos.'],
            ['Oster', 'oster', 'BR0011', 'Electrodomésticos para cocinar y compartir en familia.'],
            ['Philips', 'philips', 'BR0012', 'Tecnología para el hogar, belleza y cuidado personal.'],
            ['Adidas', 'adidas', 'BR0013', 'Ropa y calzado deportivo de uso urbano.'],
            ['Nike', 'nike', 'BR0014', 'Calzado y prendas inspiradas en el deporte.'],
            ['Puma', 'puma', 'BR0015', 'Moda deportiva con diseño contemporáneo.'],
            ['Levi’s', 'levis', 'BR0016', 'Denim y prendas casuales de estilo clásico.'],
            ['Tommy Hilfiger', 'tommy-hilfiger', 'BR0017', 'Moda casual americana de estilo clásico.'],
            ['Casio', 'casio', 'BR0018', 'Relojes resistentes, precisos y funcionales.'],
            ['Ray-Ban', 'ray-ban', 'BR0019', 'Lentes de sol icónicos con protección UV.'],
            ['Samsonite', 'samsonite', 'BR0020', 'Mochilas y equipaje pensados para viajar mejor.'],
        ];

        foreach ($brands as [$name, $slug, $code, $description]) {
            Brand::query()->updateOrCreate(
                ['slug' => $slug],
                [
                    'name' => $name,
                    'code' => $code,
                    'short_description' => $description,
                    'logo_path' => $slug === 'tommy-hilfiger'
                        ? 'images/brands/brand-tommy-hilfiger-brtqp5.webp'
                        : null,
                    'is_active' => true,
                ],
            );
        }
    }
}
