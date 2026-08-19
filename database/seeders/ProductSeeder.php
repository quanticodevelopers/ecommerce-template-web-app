<?php

namespace Database\Seeders;

use App\Enums\ProductFlag;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Carbon\CarbonImmutable;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = $this->products();
        $brandIds = Brand::query()->pluck('id', 'slug');
        $categoryIds = Category::query()->pluck('id', 'slug');
        $publishedFrom = CarbonImmutable::create(2026, 1, 5, 9);

        foreach ($products as $index => $productData) {
            [$sku, $name, $brandSlug, $categorySlug, $basePrice, $salePrice, $shortDescription] = $productData;

            $product = Product::query()->updateOrCreate(
                ['sku' => $sku],
                [
                    'brand_id' => $brandIds[$brandSlug],
                    'category_id' => $categoryIds[$categorySlug],
                    'barcode' => $this->barcode($index + 1),
                    'name' => $name,
                    'slug' => Str::slug("{$name} {$sku}"),
                    'short_description' => $shortDescription,
                    'description' => "<p>{$shortDescription}</p><p>Producto original con garantía y respaldo de la marca. Ideal para comprar en Perú con entrega a nivel nacional.</p>",
                    'base_price' => $basePrice,
                    'sale_price' => $salePrice,
                    'flag' => match (true) {
                        ($index + 1) % 5 === 0 => ProductFlag::NEW,
                        ($index + 1) % 3 === 0 => ProductFlag::FEATURED,
                        default => null,
                    },
                    'published_at' => $publishedFrom->addDays($index),
                ],
            );

            $imageBasePath = "images/products/{$product->getKey()}";

            $product->images()->updateOrCreate(
                ['position' => 0],
                [
                    'path' => "{$imageBasePath}/xl/frontal.avif",
                    'variants' => [
                        'md' => "{$imageBasePath}/md/frontal.avif",
                        'sm' => "{$imageBasePath}/sm/frontal.avif",
                    ],
                    'alt' => "{$name} - vista principal",
                ],
            );
        }
    }

    /**
     * @return array<int, array{string, string, string, string, string, string, string}>
     */
    private function products(): array
    {
        return [
            ['SAM-A55-256', 'Samsung Galaxy A55 5G 256GB', 'samsung', 'celulares-smartphones', '1699.00', '1399.00', 'Smartphone 5G con pantalla Super AMOLED, cámara de 50 MP y 256 GB.'],
            ['SAM-S24FE-256', 'Samsung Galaxy S24 FE 256GB', 'samsung', 'celulares-smartphones', '2999.00', '2499.00', 'Smartphone Galaxy AI con pantalla Dynamic AMOLED 2X y cámara triple.'],
            ['APL-IP15-128', 'Apple iPhone 15 128GB', 'apple', 'celulares-smartphones', '3699.00', '3299.00', 'iPhone con Dynamic Island, cámara principal de 48 MP y conector USB-C.'],
            ['APL-IP16-128', 'Apple iPhone 16 128GB', 'apple', 'celulares-smartphones', '4299.00', '3899.00', 'iPhone con chip A18, control de cámara y pantalla Super Retina XDR.'],
            ['XIA-RN14P-256', 'Xiaomi Redmi Note 14 Pro 5G 256GB', 'xiaomi', 'celulares-smartphones', '1599.00', '1299.00', 'Smartphone 5G con cámara de alta resolución y carga rápida.'],
            ['XIA-14T-512', 'Xiaomi 14T 5G 512GB', 'xiaomi', 'celulares-smartphones', '2499.00', '2099.00', 'Smartphone de alto rendimiento con óptica Leica y almacenamiento de 512 GB.'],
            ['SAM-A16-128', 'Samsung Galaxy A16 128GB', 'samsung', 'celulares-smartphones', '799.00', '649.00', 'Smartphone con pantalla Super AMOLED, batería de larga duración y 128 GB.'],

            ['LEN-IDSLIM3-I5', 'Lenovo IdeaPad Slim 3 15.6 pulgadas Core i5', 'lenovo', 'laptops-computadoras', '2499.00', '2199.00', 'Laptop con procesador Intel Core i5, 16 GB de RAM y SSD de 512 GB.'],
            ['LEN-LOQ-R5', 'Lenovo LOQ 15.6 pulgadas Ryzen 5 RTX 3050', 'lenovo', 'laptops-computadoras', '3699.00', '3299.00', 'Laptop gaming con Ryzen 5, gráficos RTX 3050 y pantalla de alta frecuencia.'],
            ['HP-15-FC-R5', 'HP 15.6 pulgadas Ryzen 5 16GB 512GB SSD', 'hp', 'laptops-computadoras', '2399.00', '2099.00', 'Laptop ligera con Ryzen 5, memoria de 16 GB y almacenamiento SSD.'],
            ['HP-VICTUS-I5', 'HP Victus 15.6 pulgadas Core i5 RTX 4050', 'hp', 'laptops-computadoras', '4299.00', '3899.00', 'Laptop gaming con gráficos RTX 4050 y sistema de refrigeración optimizado.'],
            ['APL-MBA-M2-256', 'Apple MacBook Air 13 pulgadas M2 256GB', 'apple', 'laptops-computadoras', '4299.00', '3899.00', 'Notebook ultradelgada con chip M2, pantalla Liquid Retina y batería para todo el día.'],
            ['APL-MBA-M3-512', 'Apple MacBook Air 15 pulgadas M3 512GB', 'apple', 'laptops-computadoras', '6499.00', '5999.00', 'Notebook de 15 pulgadas con chip M3, 16 GB de memoria y SSD de 512 GB.'],

            ['SAM-TV55DU8000', 'Samsung Smart TV Crystal UHD 55 pulgadas DU8000', 'samsung', 'televisores', '2199.00', '1799.00', 'Televisor 4K con procesador Crystal, diseño delgado y plataforma Smart TV.'],
            ['SAM-TV65Q60D', 'Samsung QLED 4K Smart TV 65 pulgadas Q60D', 'samsung', 'televisores', '3599.00', '2999.00', 'Televisor QLED 4K con volumen de color y tecnología Quantum Dot.'],
            ['LG-TV55UT8050', 'LG UHD Smart TV 55 pulgadas UT8050', 'lg', 'televisores', '2099.00', '1699.00', 'Televisor UHD 4K con webOS, procesador inteligente y control por voz.'],
            ['LG-TV65QNED80', 'LG QNED Smart TV 65 pulgadas QNED80', 'lg', 'televisores', '3999.00', '3499.00', 'Televisor QNED 4K con color enriquecido, HDR y webOS.'],
            ['SON-TV55X80L', 'Sony Bravia 4K Google TV 55 pulgadas X80L', 'sony', 'televisores', '3299.00', '2799.00', 'Google TV 4K con procesador X1, Dolby Vision y compatibilidad con PlayStation.'],

            ['JBL-FLIP6-BLK', 'Parlante portátil JBL Flip 6', 'jbl', 'audio', '549.00', '449.00', 'Parlante Bluetooth resistente al agua con sonido potente y hasta 12 horas de batería.'],
            ['JBL-CHARGE5', 'Parlante portátil JBL Charge 5', 'jbl', 'audio', '749.00', '629.00', 'Parlante Bluetooth IP67 con banco de energía integrado y sonido JBL Pro.'],
            ['JBL-TUNE770NC', 'Audífonos inalámbricos JBL Tune 770NC', 'jbl', 'audio', '399.00', '329.00', 'Audífonos over-ear con cancelación adaptativa de ruido y gran autonomía.'],
            ['SON-WH1000XM5', 'Audífonos Sony WH-1000XM5', 'sony', 'audio', '1699.00', '1399.00', 'Audífonos inalámbricos premium con cancelación de ruido y audio de alta resolución.'],
            ['SAM-HWQ600C', 'Barra de sonido Samsung HW-Q600C 3.1.2 canales', 'samsung', 'audio', '1699.00', '1299.00', 'Barra de sonido con Dolby Atmos, subwoofer inalámbrico y Q-Symphony.'],

            ['APL-WATCH-S10', 'Apple Watch Series 10 GPS 46 mm', 'apple', 'wearables', '2099.00', '1849.00', 'Reloj inteligente con pantalla amplia, métricas de salud y resistencia al agua.'],
            ['SAM-WATCH7-44', 'Samsung Galaxy Watch7 44 mm', 'samsung', 'wearables', '1299.00', '999.00', 'Smartwatch con seguimiento de salud, GPS y análisis avanzado del sueño.'],
            ['XIA-WATCH2', 'Xiaomi Watch 2', 'xiaomi', 'wearables', '799.00', '649.00', 'Reloj inteligente con Wear OS, GPS de doble banda y monitoreo deportivo.'],
            ['XIA-BAND9', 'Xiaomi Smart Band 9', 'xiaomi', 'wearables', '229.00', '179.00', 'Pulsera inteligente con pantalla AMOLED y seguimiento de actividad y sueño.'],

            ['LG-REF-GT32WPP', 'Refrigeradora LG Top Freezer 312 L GT32WPP', 'lg', 'refrigeracion', '2399.00', '1999.00', 'Refrigeradora con tecnología DoorCooling+, compresor Smart Inverter y 312 litros.'],
            ['SAM-REF-RT38', 'Refrigeradora Samsung No Frost 384 L RT38', 'samsung', 'refrigeracion', '2899.00', '2399.00', 'Refrigeradora No Frost con tecnología Digital Inverter y amplio almacenamiento.'],
            ['MAB-REF-RMA300', 'Refrigeradora Mabe No Frost 300 L RMA300', 'mabe', 'refrigeracion', '2199.00', '1799.00', 'Refrigeradora de 300 litros con sistema No Frost y dispensador de agua.'],
            ['WHI-REF-WRE57', 'Refrigeradora Whirlpool No Frost 443 L WRE57', 'whirlpool', 'refrigeracion', '3499.00', '2999.00', 'Refrigeradora de gran capacidad con panel electrónico y enfriamiento uniforme.'],

            ['LG-LAV-WT19', 'Lavadora LG carga superior 19 kg Smart Inverter', 'lg', 'lavado-secado', '2299.00', '1899.00', 'Lavadora de 19 kg con motor Smart Inverter y movimiento TurboDrum.'],
            ['SAM-LAV-WA19', 'Lavadora Samsung carga superior 19 kg Ecobubble', 'samsung', 'lavado-secado', '2399.00', '1999.00', 'Lavadora con Ecobubble, filtro de pelusas y tecnología Digital Inverter.'],
            ['WHI-LAV-18', 'Lavadora Whirlpool carga superior 18 kg Xpert System', 'whirlpool', 'lavado-secado', '2099.00', '1749.00', 'Lavadora de 18 kg con ciclos especializados y sistema de lavado Xpert.'],
            ['MAB-LAV-LMA', 'Lavadora Mabe automática 16 kg', 'mabe', 'lavado-secado', '1799.00', '1499.00', 'Lavadora automática con canasta de gran capacidad y programas de lavado.'],

            ['MAB-COC-EM7620', 'Cocina a gas Mabe 6 hornillas EM7620', 'mabe', 'cocina', '1899.00', '1599.00', 'Cocina de seis quemadores con horno amplio, grill y encendido eléctrico.'],
            ['WHI-COC-WFR7300', 'Cocina Whirlpool 5 hornillas WFR7300', 'whirlpool', 'cocina', '2399.00', '1999.00', 'Cocina con cinco quemadores, horno con grill y parrillas de hierro fundido.'],
            ['LG-MWO-MH6535', 'Horno microondas LG 25 L MH6535GIS', 'lg', 'cocina', '599.00', '499.00', 'Microondas con tecnología Smart Inverter, grill y capacidad de 25 litros.'],
            ['SAM-MWO-MS23', 'Horno microondas Samsung 23 L MS23K3513', 'samsung', 'cocina', '479.00', '399.00', 'Microondas con interior de cerámica, modo Eco y descongelado rápido.'],

            ['OST-BLSTKAG', 'Licuadora Oster clásica 3 velocidades', 'oster', 'electrodomesticos-pequenos', '349.00', '289.00', 'Licuadora con vaso de vidrio refractario, motor potente y acople metálico.'],
            ['OST-FRY-4L', 'Freidora de aire Oster 4 litros', 'oster', 'electrodomesticos-pequenos', '449.00', '349.00', 'Freidora de aire con control de temperatura y canasta antiadherente de 4 litros.'],
            ['PHI-AIRFRY-XL', 'Freidora de aire Philips Essential XL 6.2 L', 'philips', 'electrodomesticos-pequenos', '799.00', '649.00', 'Freidora con tecnología Rapid Air, pantalla digital y capacidad familiar.'],
            ['OST-BVSTDC', 'Cafetera programable Oster 12 tazas', 'oster', 'electrodomesticos-pequenos', '299.00', '239.00', 'Cafetera programable con filtro permanente y función de pausa para servir.'],
            ['PHI-IRON-3000', 'Plancha a vapor Philips Serie 3000', 'philips', 'electrodomesticos-pequenos', '199.00', '159.00', 'Plancha con vapor continuo, suela cerámica y sistema antigoteo.'],

            ['LEV-501-ORIGINAL', 'Jean Levi’s 501 Original para hombre', 'levis', 'ropa-hombre', '349.00', '279.00', 'Jean de corte recto clásico con bragueta de botones y denim resistente.'],
            ['TH-M-CUSTOM-POLO', 'Polo Tommy Hilfiger Custom Fit para hombre', 'tommy-hilfiger', 'ropa-hombre', '299.00', '239.00', 'Polo de algodón con cuello camisero y logo bordado.'],
            ['ADI-M-ESS-TEE', 'Polo Adidas Essentials para hombre', 'adidas', 'ropa-hombre', '129.00', '99.00', 'Polo deportivo de algodón con corte regular y logo Adidas.'],
            ['PUM-M-ESS-HOOD', 'Polera Puma Essentials para hombre', 'puma', 'ropa-hombre', '249.00', '199.00', 'Polera con capucha, bolsillo canguro y felpa suave.'],

            ['LEV-W-721-SKINNY', 'Jean Levi’s 721 High Rise Skinny para mujer', 'levis', 'ropa-mujer', '379.00', '299.00', 'Jean de tiro alto y corte skinny confeccionado en denim elástico.'],
            ['TH-W-SLIM-POLO', 'Polo Tommy Hilfiger Slim Fit para mujer', 'tommy-hilfiger', 'ropa-mujer', '289.00', '229.00', 'Polo entallado de algodón elástico con detalles clásicos de la marca.'],
            ['ADI-W-3S-LEGG', 'Leggings Adidas 3 Tiras para mujer', 'adidas', 'ropa-mujer', '179.00', '139.00', 'Leggings de cintura media con diseño clásico de tres tiras.'],
            ['PUM-W-EVOSTRIPE', 'Polera Puma Evostripe para mujer', 'puma', 'ropa-mujer', '279.00', '219.00', 'Polera deportiva de corte ergonómico con tecnología para controlar la humedad.'],

            ['NIK-AIRMAX-SC', 'Zapatillas Nike Air Max SC', 'nike', 'calzado', '399.00', '329.00', 'Zapatillas urbanas con amortiguación Max Air y combinación de materiales duraderos.'],
            ['NIK-REVOLUTION7', 'Zapatillas Nike Revolution 7', 'nike', 'calzado', '299.00', '239.00', 'Zapatillas de running con mediasuela de espuma y malla transpirable.'],
            ['ADI-RUNFALCON5', 'Zapatillas Adidas Runfalcon 5', 'adidas', 'calzado', '279.00', '219.00', 'Zapatillas para correr con amortiguación Cloudfoam y suela de caucho.'],
            ['ADI-GRANDCOURT2', 'Zapatillas Adidas Grand Court 2.0', 'adidas', 'calzado', '299.00', '239.00', 'Zapatillas de estilo tenis con exterior sintético y diseño clásico.'],
            ['PUM-CAVEN20', 'Zapatillas Puma Caven 2.0', 'puma', 'calzado', '269.00', '209.00', 'Zapatillas urbanas inspiradas en el básquet con plantilla acolchada SoftFoam+.'],

            ['SAM-GUARDIT3', 'Mochila Samsonite Guardit 3.0 para laptop 15.6', 'samsonite', 'mochilas-maletas', '349.00', '289.00', 'Mochila para laptop con compartimentos organizadores y diseño profesional.'],
            ['SAM-ECODIVER', 'Mochila Samsonite Ecodiver mediana', 'samsonite', 'mochilas-maletas', '599.00', '479.00', 'Mochila resistente al agua elaborada con materiales reciclados.'],
            ['SAM-SCURE-69', 'Maleta Samsonite S’Cure Spinner 69 cm', 'samsonite', 'mochilas-maletas', '1199.00', '949.00', 'Maleta rígida mediana con cuatro ruedas, cierre de tres puntos y candado TSA.'],

            ['CAS-GA2100-1A', 'Reloj Casio G-Shock GA-2100-1A', 'casio', 'relojes', '699.00', '579.00', 'Reloj analógico digital resistente a impactos y al agua hasta 200 metros.'],
            ['CAS-AE1200WH', 'Reloj Casio World Time AE-1200WH', 'casio', 'relojes', '249.00', '199.00', 'Reloj digital con hora mundial, cronómetro y batería de larga duración.'],
            ['CAS-MTPVD01', 'Reloj Casio analógico MTP-VD01', 'casio', 'relojes', '299.00', '239.00', 'Reloj analógico de acero inoxidable con indicador de fecha y estilo clásico.'],

            ['RB-AVIATOR-3025', 'Lentes Ray-Ban Aviator Classic RB3025', 'ray-ban', 'lentes-sol', '699.00', '579.00', 'Lentes de sol tipo aviador con montura metálica y protección UV.'],
            ['RB-WAYFARER-2140', 'Lentes Ray-Ban Original Wayfarer RB2140', 'ray-ban', 'lentes-sol', '749.00', '619.00', 'Lentes Wayfarer de acetato con diseño icónico y cristales protectores.'],
            ['RB-ERIKA-4171', 'Lentes Ray-Ban Erika RB4171', 'ray-ban', 'lentes-sol', '649.00', '529.00', 'Lentes de sol redondos con montura ligera y lentes degradados.'],

            ['PHI-DRY-BHD350', 'Secadora de cabello Philips BHD350', 'philips', 'belleza', '229.00', '179.00', 'Secadora con accesorio ThermoProtect, ionizador y seis ajustes de velocidad y temperatura.'],
            ['PHI-STYLE-BHS530', 'Alisadora Philips Serie 5000 BHS530', 'philips', 'belleza', '349.00', '279.00', 'Alisadora con placas cerámicas, control de temperatura y tecnología ThermoShield.'],

            ['PHI-SHAVER-S5000', 'Afeitadora Philips Series 5000', 'philips', 'cuidado-personal', '499.00', '399.00', 'Afeitadora recargable en seco y húmedo con cabezales flexibles.'],
            ['XIA-TOOTHBRUSH-T700', 'Cepillo dental eléctrico Xiaomi T700', 'xiaomi', 'cuidado-personal', '299.00', '239.00', 'Cepillo dental inteligente con pantalla LED, modos personalizables y carga inalámbrica.'],
        ];
    }

    private function barcode(int $position): string
    {
        $base = sprintf('77520000%04d', $position);
        $sum = 0;

        foreach (str_split($base) as $index => $digit) {
            $sum += (int) $digit * ($index % 2 === 0 ? 1 : 3);
        }

        return $base.((10 - ($sum % 10)) % 10);
    }
}
