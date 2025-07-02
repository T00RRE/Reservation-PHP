<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Restaurant;
use App\Models\Table;
use App\Models\User;
use App\Models\Menu;
use App\Models\Category;
use App\Models\Dish;
use App\Models\WorkingHour;
use Illuminate\Support\Facades\Hash;
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Utwórz użytkowników
        $admin = User::create([
            'name' => 'Administrator',
            'email' => 'admin@restaurantbook.pl',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'role' => 'admin'
        ]);

        $customer1 = User::create([
            'name' => 'Jan Kowalski',
            'email' => 'jan@example.pl',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'role' => 'customer'
        ]);

        $customer2 = User::create([
            'name' => 'Anna Nowak',
            'email' => 'anna@example.pl',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'role' => 'customer'
        ]);

        // 2. Utwórz restauracje
        $restaurants = [
            [
                'name' => 'Restauracja Italiana',
                'description' => 'Autentyczna kuchnia włoska w samym sercu miasta. Świeże składniki, tradycyjne przepisy i przytulna atmosfera.',
                'address' => 'ul. Floriańska 12, 31-021 Kraków',
                'phone' => '+48 12 345 67 89',
                'email' => 'kontakt@italiana.pl',
                'rating' => 4.5,
                'is_active' => true
            ],
            [
                'name' => 'Sushi Master',
                'description' => 'Najlepsze sushi w mieście. Świeże ryby dostarczone codziennie, doświadczeni sushi masterzy.',
                'address' => 'ul. Nowy Świat 15, 00-029 Warszawa',
                'phone' => '+48 22 123 45 67',
                'email' => 'zamowienia@sushimaster.pl',
                'rating' => 4.8,
                'is_active' => true
            ],
            [
                'name' => 'Karczma Polska',
                'description' => 'Tradycyjna polska kuchnia jak u babci. Domowe pierogi, kotlet schabowy i żurek na żytnim kwasie.',
                'address' => 'Rynek Główny 1, 31-042 Kraków',
                'phone' => '+48 12 987 65 43',
                'email' => 'rezerwacje@karczmapolska.pl',
                'rating' => 4.2,
                'is_active' => true
            ]
        ];

        foreach ($restaurants as $index => $restaurantData) {
            $restaurant = Restaurant::create($restaurantData);
            
            // Utwórz stoliki dla każdej restauracji
            $tableSizes = [2, 2, 4, 4, 4, 6, 6, 8];
            foreach ($tableSizes as $tableIndex => $capacity) {
                Table::create([
                    'restaurant_id' => $restaurant->id,
                    'table_number' => 'S' . ($tableIndex + 1),
                    'capacity' => $capacity,
                    'status' => 'available',
                    'description' => "Stolik dla {$capacity} osób"
                ]);
            }

            // Dodaj godziny pracy (pon-ndz)
            for ($day = 1; $day <= 7; $day++) {
                WorkingHour::create([
                    'restaurant_id' => $restaurant->id,
                    'day_of_week' => $day % 7, // 0=niedziela, 1=poniedziałek...
                    'open_time' => '12:00',
                    'close_time' => '22:00',
                    'is_closed' => false
                ]);
            }

            // Utwórz menu dla każdej restauracji
            $this->createMenuForRestaurant($restaurant, $index);
        }

        // Utwórz managera dla pierwszej restauracji
        User::create([
            'name' => 'Maria Włoska',
            'email' => 'manager@italiana.pl',
            'password' => bcrypt('password'),
            'role' => 'manager',
            'restaurant_id' => 1
        ]);

        // Utwórz personel dla drugiej restauracji  
        User::create([
            'name' => 'Takeshi Yamamoto',
            'email' => 'staff@sushimaster.pl',
            'password' => bcrypt('password'),
            'role' => 'staff',
            'restaurant_id' => 2
        ]);
    }

    private function createMenuForRestaurant($restaurant, $index)
    {
        $menus = [
            // Restauracja Italiana
            [
                'name' => 'Menu Główne',
                'categories' => [
                    'Przystawki' => [
                        ['name' => 'Bruschetta', 'price' => 18, 'description' => 'Chrupiące pieczywo z pomidorami i bazylią'],
                        ['name' => 'Carpaccio', 'price' => 32, 'description' => 'Cienkie plastry wołowiny z rukolą i parmezanem'],
                        ['name' => 'Antipasti', 'price' => 28, 'description' => 'Mix włoskich przystawek']
                    ],
                    'Dania główne' => [
                        ['name' => 'Spaghetti Carbonara', 'price' => 38, 'description' => 'Klasyczne spaghetti z boczkiem i sosem jajecznym'],
                        ['name' => 'Pizza Margherita', 'price' => 32, 'description' => 'Pizza z pomidorami, mozzarellą i bazylią'],
                        ['name' => 'Osso Buco', 'price' => 65, 'description' => 'Duszona golonka cielęca w białym winie']
                    ],
                    'Desery' => [
                        ['name' => 'Tiramisu', 'price' => 22, 'description' => 'Klasyczny włoski deser z mascarpone'],
                        ['name' => 'Panna Cotta', 'price' => 18, 'description' => 'Delikatny deser z owocami leśnymi']
                    ]
                ]
            ],
            // Sushi Master  
            [
                'name' => 'Menu Sushi',
                'categories' => [
                    'Nigiri' => [
                        ['name' => 'Sake Nigiri', 'price' => 12, 'description' => 'Łosoś na ryżu sushi'],
                        ['name' => 'Maguro Nigiri', 'price' => 15, 'description' => 'Tuńczyk na ryżu sushi'],
                        ['name' => 'Ebi Nigiri', 'price' => 10, 'description' => 'Krewetka na ryżu sushi']
                    ],
                    'Maki' => [
                        ['name' => 'California Roll', 'price' => 28, 'description' => 'Krab, awokado, ogórek w sezamie'],
                        ['name' => 'Philadelphia Roll', 'price' => 32, 'description' => 'Łosoś, ser philadelphia, ogórek'],
                        ['name' => 'Spicy Tuna Roll', 'price' => 35, 'description' => 'Tuńczyk w ostrym sosie, awokado']
                    ],
                    'Sashimi' => [
                        ['name' => 'Sake Sashimi', 'price' => 25, 'description' => '6 kawałków świeżego łososia'],
                        ['name' => 'Maguro Sashimi', 'price' => 30, 'description' => '6 kawałków tuńczyka bluefin']
                    ]
                ]
            ],
            // Karczma Polska
            [
                'name' => 'Menu Tradycyjne',
                'categories' => [
                    'Zupy' => [
                        ['name' => 'Żurek staropolski', 'price' => 16, 'description' => 'Z kiełbasą i jajkiem w chlebku'],
                        ['name' => 'Rosół z makaronem', 'price' => 12, 'description' => 'Tradycyjny rosół z kury'],
                        ['name' => 'Krupnik', 'price' => 14, 'description' => 'Zupa z kaszą perłową i warzywami']
                    ],
                    'Dania główne' => [
                        ['name' => 'Schabowy z kapustą', 'price' => 35, 'description' => 'Kotlet schabowy z kapustą zasmażaną i ziemniakami'],
                        ['name' => 'Pierogi ruskie', 'price' => 28, 'description' => '8 sztuk z serem i ziemniakami, skwarki'],
                        ['name' => 'Gołąbki w sosie', 'price' => 32, 'description' => 'Tradycyjne gołąbki z mięsem i ryżem'],
                        ['name' => 'Bigos staropolski', 'price' => 26, 'description' => 'Z różnymi rodzajami mięsa i kiełbasy']
                    ],
                    'Desery' => [
                        ['name' => 'Sernik na zimno', 'price' => 18, 'description' => 'Z polewą truskawkową'],
                        ['name' => 'Makowiec', 'price' => 15, 'description' => 'Domowy makowiec z lukrem']
                    ]
                ]
            ]
        ];

        $menuData = $menus[$index];
        $menu = Menu::create([
            'restaurant_id' => $restaurant->id,
            'name' => $menuData['name'],
            'description' => 'Nasze specjalności',
            'is_active' => true,
            'sort_order' => 1
        ]);

        $categoryOrder = 1;
        foreach ($menuData['categories'] as $categoryName => $dishes) {
            $category = Category::create([
                'menu_id' => $menu->id,
                'name' => $categoryName,
                'description' => "Kategoria: {$categoryName}",
                'is_active' => true,
                'sort_order' => $categoryOrder++
            ]);

            $dishOrder = 1;
            foreach ($dishes as $dishData) {
                Dish::create([
                    'category_id' => $category->id,
                    'name' => $dishData['name'],
                    'description' => $dishData['description'],
                    'price' => $dishData['price'],
                    'is_vegetarian' => str_contains($dishData['name'], 'Margherita') || str_contains($dishData['name'], 'Pierogi'),
                    'is_vegan' => false,
                    'is_available' => true,
                    'rating' => rand(40, 50) / 10, // 4.0-5.0
                    'sort_order' => $dishOrder++
                ]);
            }
        }
    }
}