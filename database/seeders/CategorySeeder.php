<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['slug' => 'tecnologia', 'name' => 'Tecnología', 'code' => 'CA0001', 'description' => 'Equipos tecnológicos para trabajo, estudio y entretenimiento.'],
            ['slug' => 'hogar', 'name' => 'Hogar', 'code' => 'CA0002', 'description' => 'Soluciones para equipar y disfrutar cada ambiente del hogar.'],
            ['slug' => 'moda', 'name' => 'Moda', 'code' => 'CA0003', 'description' => 'Prendas y calzado para un estilo versátil y actual.'],
            ['slug' => 'accesorios', 'name' => 'Accesorios', 'code' => 'CA0004', 'description' => 'Complementos funcionales para el día a día.'],
            ['slug' => 'belleza-cuidado-personal', 'name' => 'Belleza y cuidado personal', 'code' => 'CA0005', 'description' => 'Productos para bienestar, arreglo y cuidado personal.'],
            ['slug' => 'celulares-smartphones', 'name' => 'Celulares y smartphones', 'code' => 'CA0006', 'parent' => 'tecnologia', 'description' => 'Smartphones para comunicación, fotografía y productividad.'],
            ['slug' => 'laptops-computadoras', 'name' => 'Laptops y computadoras', 'code' => 'CA0007', 'parent' => 'tecnologia', 'description' => 'Computadoras portátiles para estudio, oficina y gaming.'],
            ['slug' => 'televisores', 'name' => 'Televisores', 'code' => 'CA0008', 'parent' => 'tecnologia', 'description' => 'Televisores inteligentes de alta definición.'],
            ['slug' => 'audio', 'name' => 'Audio', 'code' => 'CA0009', 'parent' => 'tecnologia', 'description' => 'Audífonos, parlantes y barras de sonido.'],
            ['slug' => 'wearables', 'name' => 'Wearables', 'code' => 'CA0010', 'parent' => 'tecnologia', 'description' => 'Relojes y pulseras inteligentes para una vida conectada.'],
            ['slug' => 'linea-blanca', 'name' => 'Línea blanca', 'code' => 'CA0011', 'parent' => 'hogar', 'description' => 'Electrodomésticos principales para cocina y lavandería.'],
            ['slug' => 'electrodomesticos-pequenos', 'name' => 'Electrodomésticos pequeños', 'code' => 'CA0012', 'parent' => 'hogar', 'description' => 'Equipos compactos para preparar alimentos y facilitar tareas.'],
            ['slug' => 'muebles-decoracion', 'name' => 'Muebles y decoración', 'code' => 'CA0013', 'parent' => 'hogar', 'description' => 'Muebles y detalles para organizar y renovar los ambientes.'],
            ['slug' => 'refrigeracion', 'name' => 'Refrigeración', 'code' => 'CA0014', 'parent' => 'linea-blanca', 'description' => 'Refrigeradoras y congeladoras para conservar alimentos.'],
            ['slug' => 'lavado-secado', 'name' => 'Lavado y secado', 'code' => 'CA0015', 'parent' => 'linea-blanca', 'description' => 'Lavadoras y secadoras para el cuidado de la ropa.'],
            ['slug' => 'cocina', 'name' => 'Cocina', 'code' => 'CA0016', 'parent' => 'linea-blanca', 'description' => 'Cocinas, hornos y campanas para equipar tu cocina.'],
            ['slug' => 'ropa-hombre', 'name' => 'Ropa para hombre', 'code' => 'CA0017', 'parent' => 'moda', 'description' => 'Prendas casuales y urbanas para hombre.'],
            ['slug' => 'ropa-mujer', 'name' => 'Ropa para mujer', 'code' => 'CA0018', 'parent' => 'moda', 'description' => 'Prendas cómodas y versátiles para mujer.'],
            ['slug' => 'calzado', 'name' => 'Calzado', 'code' => 'CA0019', 'parent' => 'moda', 'description' => 'Zapatillas y calzado urbano para diferentes ocasiones.'],
            ['slug' => 'mochilas-maletas', 'name' => 'Mochilas y maletas', 'code' => 'CA0020', 'parent' => 'accesorios', 'description' => 'Equipaje para oficina, estudio y viajes.'],
            ['slug' => 'relojes', 'name' => 'Relojes', 'code' => 'CA0021', 'parent' => 'accesorios', 'description' => 'Relojes clásicos y deportivos para uso diario.'],
            ['slug' => 'lentes-sol', 'name' => 'Lentes de sol', 'code' => 'CA0022', 'parent' => 'accesorios', 'description' => 'Lentes con protección UV y diseños atemporales.'],
            ['slug' => 'belleza', 'name' => 'Belleza', 'code' => 'CA0023', 'parent' => 'belleza-cuidado-personal', 'description' => 'Equipos y accesorios para peinado y belleza.'],
            ['slug' => 'cuidado-personal', 'name' => 'Cuidado personal', 'code' => 'CA0024', 'parent' => 'belleza-cuidado-personal', 'description' => 'Equipos para higiene y cuidado personal cotidiano.'],
        ];

        $categoryIds = [];

        foreach ($categories as $categoryData) {
            $category = Category::query()->updateOrCreate(
                ['slug' => $categoryData['slug']],
                [
                    'name' => $categoryData['name'],
                    'code' => $categoryData['code'],
                    'parent_id' => isset($categoryData['parent']) ? $categoryIds[$categoryData['parent']] : null,
                    'short_description' => $categoryData['description'],
                    'is_active' => true,
                ],
            );

            $categoryIds[$categoryData['slug']] = $category->getKey();
        }
    }
}
