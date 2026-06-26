<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use App\Models\Brand;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Coupon;
use App\Models\Review;
use App\Models\Banner;
use App\Models\Blog;
use App\Models\Setting;
use App\Models\Affiliate;
use App\Models\LoyaltyPoint;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // ==========================================
        // 1. ROLES AND PERMISSIONS
        // ==========================================
        $roles = ['Super Admin', 'Admin', 'Staff', 'Customer', 'Affiliate'];
        foreach ($roles as $roleName) {
            Role::findOrCreate($roleName);
        }

        // ==========================================
        // 2. SEED USERS & ROLES
        // ==========================================
        // Admin
        $adminUser = User::create([
            'name' => 'Uden Admin',
            'email' => 'admin@udenshop.com',
            'password' => Hash::make('password'),
            'phone' => '081234567890',
            'membership_level' => 'Platinum',
            'points_balance' => 5000,
        ]);
        $adminUser->assignRole('Super Admin');

        // Customer
        $customerUser = User::create([
            'name' => 'Budi Santoso',
            'email' => 'customer@udenshop.com',
            'password' => Hash::make('password'),
            'phone' => '082345678901',
            'membership_level' => 'Silver',
            'points_balance' => 120,
        ]);
        $customerUser->assignRole('Customer');

        // Affiliate User
        $affiliateUser = User::create([
            'name' => 'Ahmad Affiliate',
            'email' => 'affiliate@udenshop.com',
            'password' => Hash::make('password'),
            'phone' => '083456789012',
            'membership_level' => 'Gold',
            'points_balance' => 850,
            'referral_code' => 'UDEN123',
        ]);
        $affiliateUser->assignRole('Affiliate');

        // Create Affiliate record
        Affiliate::create([
            'user_id' => $affiliateUser->id,
            'referral_code' => 'UDEN123',
            'total_earnings' => 250000.00,
            'balance' => 150000.00,
        ]);

        // ==========================================
        // 3. SETTINGS
        // ==========================================
        $settings = [
            'site_name' => 'UdenShop',
            'site_tagline' => 'Belanja Mudah, Cepat, dan Terpercaya',
            'site_logo' => 'https://images.unsplash.com/photo-1472851294608-062f824d296e?w=100',
            'site_favicon' => 'https://images.unsplash.com/photo-1472851294608-062f824d296e?w=16',
            'contact_email' => 'support@udenshop.com',
            'contact_phone' => '+6281234567890',
            'contact_whatsapp' => '6281234567890',
            'contact_address' => 'Jl. E-Commerce No. 8, Jakarta, Indonesia',
            'api_midtrans_client_key' => 'SB-Mid-client-MockKey123',
            'api_midtrans_server_key' => 'SB-Mid-server-MockKey456',
            'api_rajaongkir_key' => 'MockRajaOngkirKey789',
            'seo_title' => 'UdenShop - Belanja Mudah, Cepat, dan Terpercaya',
            'seo_description' => 'UdenShop adalah toko online terlengkap yang menyediakan gadget, fashion, otomotif, makanan, dan lainnya dengan harga terbaik.',
            'seo_keywords' => 'udenshop, e-commerce indonesia, toko online murah, belanja gadget, fashion lokal',
        ];

        foreach ($settings as $key => $value) {
            Setting::create(['key' => $key, 'value' => $value]);
        }

        // ==========================================
        // 4. BRANDS
        // ==========================================
        $brandsData = [
            ['name' => 'Apple', 'is_popular' => true],
            ['name' => 'Samsung', 'is_popular' => true],
            ['name' => 'ASUS', 'is_popular' => true],
            ['name' => 'Xiaomi', 'is_popular' => true],
            ['name' => 'Zara', 'is_popular' => true],
            ['name' => 'Toyota', 'is_popular' => false],
            ['name' => 'Honda', 'is_popular' => true],
            ['name' => 'IKEA', 'is_popular' => true],
            ['name' => 'Nestle', 'is_popular' => false],
            ['name' => 'PMA Office', 'is_popular' => false],
            ['name' => 'Nike', 'is_popular' => true],
            ['name' => 'Adidas', 'is_popular' => true],
            ['name' => 'Uniqlo', 'is_popular' => true],
        ];

        $brands = [];
        foreach ($brandsData as $b) {
            $brands[$b['name']] = Brand::create([
                'name' => $b['name'],
                'slug' => Str::slug($b['name']),
                'logo' => null,
                'is_popular' => $b['is_popular'],
            ]);
        }

        // ==========================================
        // 5. CATEGORIES & SUB-CATEGORIES
        // ==========================================
        $categoriesData = [
            'Elektronik' => ['TV', 'AC', 'Speaker'],
            'Fashion' => ['Pria', 'Wanita', 'Anak-anak', 'Celana', 'Sepatu', 'Topi', 'Jaket'],
            'Kesehatan' => ['Vitamin', 'Masker'],
            'Otomotif' => ['Helm', 'Oli', 'Aksesoris Mobil'],
            'Rumah Tangga' => ['Kursi', 'Meja', 'Lampu'],
            'Komputer' => ['Laptop', 'Keyboard', 'Mouse'],
            'Gadget' => ['Smartphone', 'Tablet', 'Smartwatch'],
            'Makanan' => ['Keripik', 'Mi Instan'],
            'Minuman' => ['Kopi', 'Teh', 'Soda'],
            'Peralatan Kantor' => ['Kertas', 'Pena', 'Binder'],
        ];

        $cats = [];
        $subCats = [];
        foreach ($categoriesData as $catName => $subs) {
            $cat = Category::create([
                'name' => $catName,
                'slug' => Str::slug($catName),
                'description' => 'Semua produk kategori ' . $catName,
                'icon' => match($catName) {
                    'Elektronik' => 'tv',
                    'Fashion' => 'shirt',
                    'Kesehatan' => 'heart-pulse',
                    'Otomotif' => 'car',
                    'Rumah Tangga' => 'home',
                    'Komputer' => 'laptop',
                    'Gadget' => 'smartphone',
                    'Makanan' => 'utensils',
                    'Minuman' => 'glass-water',
                    'Peralatan Kantor' => 'briefcase',
                    default => 'tag',
                }
            ]);
            $cats[$catName] = $cat;

            foreach ($subs as $subName) {
                $sub = SubCategory::create([
                    'category_id' => $cat->id,
                    'name' => $subName,
                    'slug' => Str::slug($subName),
                ]);
                $subCats[$catName . '_' . $subName] = $sub;
            }
        }

        // ==========================================
        // 6. PRODUCTS (PHYSICAL, DIGITAL, VARIANT, BUNDLE, AFFILIATE)
        // ==========================================
        // A. Physical Product (Smartphone)
        $iphone = Product::create([
            'category_id' => $cats['Gadget']->id,
            'sub_category_id' => $subCats['Gadget_Smartphone']->id,
            'brand_id' => $brands['Apple']->id,
            'name' => 'iPhone 15 Pro Max',
            'slug' => 'iphone-15-pro-max',
            'SKU' => 'IPH15PM-256',
            'barcode' => '190198456789',
            'price' => 22999000.00,
            'sale_price' => 21499000.00,
            'stock' => 15,
            'weight' => 221.0,
            'dimensions' => '15.9 x 7.67 x 0.82 cm',
            'description' => 'iPhone 15 Pro Max memiliki desain titanium yang kuat dan ringan dengan tepi berkontur baru. Kamera Utama 48 MP yang disempurnakan dan chip A17 Pro terdepan di industri.',
            'specifications' => [
                'Layar' => 'Super Retina XDR OLED 6.7 inci',
                'Chipset' => 'Apple A17 Pro (3nm)',
                'RAM/Storage' => '8GB / 256GB',
                'Kamera Utama' => '48MP + 12MP + 12MP',
                'Baterai' => '4441 mAh',
            ],
            'thumbnail' => 'https://images.unsplash.com/photo-1695048133142-1a20484d2569?w=400',
            'type' => 'physical',
            'is_active' => true,
        ]);

        // Product Images
        ProductImage::create([
            'product_id' => $iphone->id,
            'image_path' => 'https://images.unsplash.com/photo-1695048133142-1a20484d2569?w=600',
            'sort_order' => 1
        ]);
        ProductImage::create([
            'product_id' => $iphone->id,
            'image_path' => 'https://images.unsplash.com/photo-1510557880182-3d4d3cba35a5?w=600',
            'sort_order' => 2
        ]);

        // B. Variant Product (Mechanical Keyboard)
        $keyboard = Product::create([
            'category_id' => $cats['Komputer']->id,
            'sub_category_id' => $subCats['Komputer_Keyboard']->id,
            'brand_id' => $brands['ASUS']->id,
            'name' => 'ASUS ROG Azoth Keyboard',
            'slug' => 'asus-rog-azoth-keyboard',
            'SKU' => 'ROG-AZOTH-MECH',
            'price' => 3899000.00,
            'sale_price' => null,
            'stock' => 10,
            'weight' => 1180.0,
            'dimensions' => '32.6 x 13.6 x 4.0 cm',
            'description' => 'Keyboard mekanis gaming ROG Azoth dengan form factor 75%, peredam silikon, switch ROG NX yang dapat ditukar (hot-swappable), serta layar OLED mini.',
            'specifications' => [
                'Tipe Switch' => 'ROG NX Red / Blue (Hot-Swappable)',
                'Konektivitas' => 'Bluetooth 5.1 / 2.4GHz Wireless / Wired USB',
                'Layar' => 'OLED 2 inci',
                'Layout' => '75% form factor',
            ],
            'thumbnail' => 'https://images.unsplash.com/photo-1587829741301-dc798b83add3?w=400',
            'type' => 'variant',
            'is_active' => true,
        ]);

        // C. Digital Product (Programming Book E-Book)
        $ebook = Product::create([
            'category_id' => $cats['Peralatan Kantor']->id,
            'sub_category_id' => $subCats['Peralatan Kantor_Kertas']->id,
            'brand_id' => $brands['PMA Office']->id,
            'name' => 'E-Book Laravel 12 Best Practices',
            'slug' => 'e-book-laravel-12-best-practices',
            'SKU' => 'EBK-LAR12-BP',
            'price' => 149000.00,
            'sale_price' => 99000.00,
            'stock' => 9999, // digital has unlimited stock
            'weight' => 0.0,
            'description' => 'Buku panduan digital lengkap untuk menguasai arsitektur Laravel 12 dari tingkat pemula hingga mahir dengan Service Layer, Repository Pattern, dan API Sanctum.',
            'specifications' => [
                'Format' => 'PDF, EPUB',
                'Jumlah Halaman' => '320 Halaman',
                'Bahasa' => 'Indonesia',
                'Edisi' => 'Maret 2026',
            ],
            'thumbnail' => 'https://images.unsplash.com/photo-1544716278-ca5e3f4abd8c?w=400',
            'type' => 'digital',
            'is_active' => true,
        ]);

        // D. Affiliate Product (Web Hosting)
        $hosting = Product::create([
            'category_id' => $cats['Elektronik']->id,
            'sub_category_id' => $subCats['Elektronik_TV']->id,
            'brand_id' => $brands['PMA Office']->id,
            'name' => 'Niagahoster Cloud Hosting Premium',
            'slug' => 'niagahoster-cloud-hosting-premium',
            'SKU' => 'AFF-NHOSTER',
            'price' => 1250000.00,
            'sale_price' => 590000.00,
            'stock' => 999,
            'weight' => 0.0,
            'description' => 'Layanan cloud hosting berkinerja tinggi dari Niagahoster dengan domain gratis, SSL tak terbatas, dan dukungan teknis 24/7.',
            'thumbnail' => 'https://images.unsplash.com/photo-1600132806370-bf17e65e942f?w=400',
            'type' => 'affiliate',
            'affiliate_url' => 'https://www.niagahoster.co.id/ref/mock-uden-affiliate',
            'is_active' => true,
        ]);

        // E. Bundle Product (ASUS ROG Azoth + Mouse Bundle)
        $bundle = Product::create([
            'category_id' => $cats['Komputer']->id,
            'sub_category_id' => $subCats['Komputer_Keyboard']->id,
            'brand_id' => $brands['ASUS']->id,
            'name' => 'ROG Ultimate Gaming Bundle',
            'slug' => 'rog-ultimate-gaming-bundle',
            'SKU' => 'ROG-BUNDLE-AZ-HARP',
            'price' => 5500000.00,
            'sale_price' => 4999000.00,
            'stock' => 5,
            'weight' => 2000.0,
            'description' => 'Paket hemat gaming periferal premium yang terdiri dari Keyboard Mekanis ASUS ROG Azoth dan Mouse ROG Harpe Ace Aim Lab Edition.',
            'thumbnail' => 'https://images.unsplash.com/photo-1618384887929-16ec33fab9ef?w=400',
            'type' => 'bundle',
            'is_active' => true,
        ]);

        // Seed some additional products for filter & shop pagination tests
        Product::create([
            'category_id' => $cats['Fashion']->id,
            'sub_category_id' => $subCats['Fashion_Pria']->id,
            'brand_id' => $brands['Zara']->id,
            'name' => 'Zara Casual Slimfit Shirt',
            'slug' => 'zara-casual-slimfit-shirt',
            'SKU' => 'ZR-SHIRT-M',
            'price' => 499000.00,
            'stock' => 50,
            'weight' => 300.0,
            'description' => 'Kemeja pria kasual Zara dengan potongan slimfit dan bahan katun berkualitas tinggi.',
            'thumbnail' => 'https://images.unsplash.com/photo-1596755094514-f87e34085b2c?w=400',
            'type' => 'physical',
            'is_active' => true,
        ]);

        Product::create([
            'category_id' => $cats['Otomotif']->id,
            'sub_category_id' => $subCats['Otomotif_Helm']->id,
            'brand_id' => $brands['Honda']->id,
            'name' => 'Honda Classic Retro Helmet',
            'slug' => 'honda-classic-retro-helmet',
            'SKU' => 'HND-HELMET-RET',
            'price' => 350000.00,
            'sale_price' => 299000.00,
            'stock' => 30,
            'weight' => 1400.0,
            'description' => 'Helm retro klasik berstandar SNI persembahan Honda Genuine Accessories.',
            'thumbnail' => 'https://images.unsplash.com/photo-1568772585407-9361f9bf3a87?w=400',
            'type' => 'physical',
            'is_active' => true,
        ]);

        // Kesehatan Product
        Product::create([
            'category_id' => $cats['Kesehatan']->id,
            'sub_category_id' => $subCats['Kesehatan_Vitamin']->id,
            'brand_id' => $brands['Nestle']->id,
            'name' => 'Blackmores Multivitamin Active',
            'slug' => 'blackmores-multivitamin-active',
            'SKU' => 'BM-MULTI-ACTIVE',
            'price' => 180000.00,
            'sale_price' => 150000.00,
            'stock' => 100,
            'weight' => 200.0,
            'description' => 'Blackmores Multivitamin Active merupakan suplemen kesehatan dengan kandungan vitamin, mineral, dan herbal membantu menjaga stamina tubuh.',
            'thumbnail' => 'https://images.unsplash.com/photo-1584308666744-24d5c474f2ae?w=400',
            'type' => 'physical',
            'is_active' => true,
        ]);

        // Rumah Tangga Product
        Product::create([
            'category_id' => $cats['Rumah Tangga']->id,
            'sub_category_id' => $subCats['Rumah Tangga_Kursi']->id,
            'brand_id' => $brands['IKEA']->id,
            'name' => 'IKEA Markus Ergonomic Chair',
            'slug' => 'ikea-markus-ergonomic-chair',
            'SKU' => 'IK-MARKUS-CHAIR',
            'price' => 2499000.00,
            'sale_price' => 2299000.00,
            'stock' => 15,
            'weight' => 15000.0,
            'description' => 'Kursi kantor ergonomis IKEA Markus dengan sandaran jaring yang memberikan sirkulasi udara baik dan dukungan punggung maksimal.',
            'thumbnail' => 'https://images.unsplash.com/photo-1592078615290-033ee584e267?w=400',
            'type' => 'physical',
            'is_active' => true,
        ]);

        // Makanan Product
        Product::create([
            'category_id' => $cats['Makanan']->id,
            'sub_category_id' => $subCats['Makanan_Keripik']->id,
            'brand_id' => $brands['Nestle']->id,
            'name' => 'Keripik Singkong Pedas Gila',
            'slug' => 'keripik-singkong-pedas-gila',
            'SKU' => 'MK-KRIPIK-SING',
            'price' => 18000.00,
            'stock' => 150,
            'weight' => 150.0,
            'description' => 'Keripik singkong renyah dengan bumbu cabai rawit asli yang super pedas dan gurih menggugah selera.',
            'thumbnail' => 'https://images.unsplash.com/photo-1566478989037-eec170784d20?w=400',
            'type' => 'physical',
            'is_active' => true,
        ]);

        // Minuman Product
        Product::create([
            'category_id' => $cats['Minuman']->id,
            'sub_category_id' => $subCats['Minuman_Kopi']->id,
            'brand_id' => $brands['Nestle']->id,
            'name' => 'Arabica Gayo Coffee Beans 250g',
            'slug' => 'arabica-gayo-coffee-beans-250g',
            'SKU' => 'MN-KOPI-GAYO',
            'price' => 75000.00,
            'sale_price' => 69000.00,
            'stock' => 60,
            'weight' => 250.0,
            'description' => 'Biji kopi Arabika Gayo premium pilihan dengan aroma khas tanah Gayo Aceh, rasa seimbang, dan tingkat keasaman medium.',
            'thumbnail' => 'https://images.unsplash.com/photo-1559056199-641a0ac8b55e?w=400',
            'type' => 'physical',
            'is_active' => true,
        ]);

        // Peralatan Kantor Product
        Product::create([
            'category_id' => $cats['Peralatan Kantor']->id,
            'sub_category_id' => $subCats['Peralatan Kantor_Pena']->id,
            'brand_id' => $brands['PMA Office']->id,
            'name' => 'Premium Gel Pen Box of 12',
            'slug' => 'premium-gel-pen-box-of-12',
            'SKU' => 'PK-PEN-BOX',
            'price' => 45000.00,
            'stock' => 200,
            'weight' => 150.0,
            'description' => 'Satu kotak pena gel premium Joyko isi 12 pcs dengan tinta hitam pekat, ujung mata pena 0.5mm yang lancar untuk menulis cepat.',
            'thumbnail' => 'https://images.unsplash.com/photo-1583485088034-697b5bc54ccd?w=400',
            'type' => 'physical',
            'is_active' => true,
        ]);

        // Elektronik Speaker Product
        Product::create([
            'category_id' => $cats['Elektronik']->id,
            'sub_category_id' => $subCats['Elektronik_Speaker']->id,
            'brand_id' => $brands['Samsung']->id,
            'name' => 'Samsung Galaxy Buds 2 Pro',
            'slug' => 'samsung-galaxy-buds-2-pro',
            'SKU' => 'SS-BUDS2-PRO',
            'price' => 2799000.00,
            'sale_price' => 2499000.00,
            'stock' => 25,
            'weight' => 100.0,
            'description' => 'Samsung Galaxy Buds 2 Pro menghadirkan audio 24-bit Hi-Fi berkualitas tinggi dengan Intelligent Active Noise Cancelling (ANC).',
            'thumbnail' => 'https://images.unsplash.com/photo-1590658268037-6bf12165a8df?w=400',
            'type' => 'physical',
            'is_active' => true,
        ]);

        // Xiaomi Product (Gadget -> Smartphone)
        Product::create([
            'category_id' => $cats['Gadget']->id,
            'sub_category_id' => $subCats['Gadget_Smartphone']->id,
            'brand_id' => $brands['Xiaomi']->id,
            'name' => 'Xiaomi Redmi Note 13 Pro',
            'slug' => 'xiaomi-redmi-note-13-pro',
            'SKU' => 'XM-RDNOTE-13P',
            'price' => 3799000.00,
            'sale_price' => 3599000.00,
            'stock' => 30,
            'weight' => 187.0,
            'description' => 'Smartphone mid-range andalan dari Xiaomi dengan kamera utama 200 MP, layar AMOLED 120Hz, dan pengisian daya turbo 67W.',
            'thumbnail' => 'https://images.unsplash.com/photo-1598327105666-5b89351aff97?w=400',
            'type' => 'physical',
            'is_active' => true,
        ]);

        // Toyota Product (Otomotif -> Aksesoris Mobil)
        Product::create([
            'category_id' => $cats['Otomotif']->id,
            'sub_category_id' => $subCats['Otomotif_Aksesoris Mobil']->id,
            'brand_id' => $brands['Toyota']->id,
            'name' => 'Toyota Premium Car Air Purifier',
            'slug' => 'toyota-premium-car-air-purifier',
            'SKU' => 'TY-AIR-PURIFIER',
            'price' => 450000.00,
            'stock' => 15,
            'weight' => 500.0,
            'description' => 'Air Purifier mobil original Toyota untuk menyaring udara kotor dan menghasilkan ion negatif agar kabin mobil tetap segar dan bersih.',
            'thumbnail' => 'https://images.unsplash.com/photo-1511919884226-fd3cad34687c?w=400',
            'type' => 'physical',
            'is_active' => true,
        ]);

        // Fashion Celana Product
        Product::create([
            'category_id' => $cats['Fashion']->id,
            'sub_category_id' => $subCats['Fashion_Celana']->id,
            'brand_id' => $brands['Zara']->id,
            'name' => 'Zara Slimfit Chino Pants',
            'slug' => 'zara-slimfit-chino-pants',
            'SKU' => 'ZR-CHINO-PANTS',
            'price' => 599000.00,
            'stock' => 40,
            'weight' => 400.0,
            'description' => 'Celana Chino slimfit pria dari bahan katun stretch yang sangat nyaman digunakan untuk acara kasual maupun semi-formal.',
            'thumbnail' => 'https://images.unsplash.com/photo-1624378439575-d8705ad7ae80?w=400',
            'type' => 'physical',
            'is_active' => true,
        ]);

        // Fashion Sepatu Product
        Product::create([
            'category_id' => $cats['Fashion']->id,
            'sub_category_id' => $subCats['Fashion_Sepatu']->id,
            'brand_id' => $brands['Zara']->id,
            'name' => 'Zara Leather Sneakers White',
            'slug' => 'zara-leather-sneakers-white',
            'SKU' => 'ZR-SNEAK-WHITE',
            'price' => 899000.00,
            'stock' => 20,
            'weight' => 900.0,
            'description' => 'Sepatu sneakers kulit putih minimalis dengan desain elegan, sol empuk, dan sangat serbaguna untuk berbagai outfit.',
            'thumbnail' => 'https://images.unsplash.com/photo-1549298916-b41d501d3772?w=400',
            'type' => 'physical',
            'is_active' => true,
        ]);

        // Fashion Topi Product
        Product::create([
            'category_id' => $cats['Fashion']->id,
            'sub_category_id' => $subCats['Fashion_Topi']->id,
            'brand_id' => $brands['Zara']->id,
            'name' => 'Zara Classic Wool Fedora',
            'slug' => 'zara-classic-wool-fedora',
            'SKU' => 'ZR-FEDORA-WOOL',
            'price' => 399000.00,
            'stock' => 15,
            'weight' => 150.0,
            'description' => 'Topi fedora klasik dari bahan wool berkualitas tinggi untuk melengkapi gaya fashion elegan Anda.',
            'thumbnail' => 'https://images.unsplash.com/photo-1533055640609-24b498dfd74c?w=400',
            'type' => 'physical',
            'is_active' => true,
        ]);

        // Fashion Jaket Product
        Product::create([
            'category_id' => $cats['Fashion']->id,
            'sub_category_id' => $subCats['Fashion_Jaket']->id,
            'brand_id' => $brands['Zara']->id,
            'name' => 'Zara Vintage Denim Jacket',
            'slug' => 'zara-vintage-denim-jacket',
            'SKU' => 'ZR-DENIM-JKT',
            'price' => 799000.00,
            'stock' => 25,
            'weight' => 800.0,
            'description' => 'Jaket denim gaya vintage dengan detail washing washed-out klasik yang memberikan tampilan retro modern yang trendi.',
            'thumbnail' => 'https://images.unsplash.com/photo-1576995853123-5a10305d93c0?w=400',
            'type' => 'physical',
            'is_active' => true,
        ]);

        // --- NEW FASHION PRODUCTS ---

        // Uniqlo Jogger Pants (Celana)
        Product::create([
            'category_id' => $cats['Fashion']->id,
            'sub_category_id' => $subCats['Fashion_Celana']->id,
            'brand_id' => $brands['Uniqlo']->id,
            'name' => 'Uniqlo Cargo Jogger Pants',
            'slug' => 'uniqlo-cargo-jogger-pants',
            'SKU' => 'UQ-CARGO-JGR',
            'price' => 499000.00,
            'stock' => 45,
            'weight' => 450.0,
            'description' => 'Celana jogger kargo pria dari Uniqlo dengan bahan katun twill stretch premium, pinggang elastis, dan kantong fungsional.',
            'thumbnail' => 'https://images.unsplash.com/photo-1517423568366-8b83523034fd?w=400',
            'type' => 'physical',
            'is_active' => true,
        ]);

        // Adidas Slim Trackpants (Celana)
        Product::create([
            'category_id' => $cats['Fashion']->id,
            'sub_category_id' => $subCats['Fashion_Celana']->id,
            'brand_id' => $brands['Adidas']->id,
            'name' => 'Adidas Slim Fit Trackpants',
            'slug' => 'adidas-slim-fit-trackpants',
            'SKU' => 'AD-SLIM-TRK',
            'price' => 799000.00,
            'stock' => 30,
            'weight' => 380.0,
            'description' => 'Celana training olahraga Adidas bermotif ikonik 3-stripes dengan potongan slim fit dan bahan daur ulang Primegreen.',
            'thumbnail' => 'https://images.unsplash.com/photo-1552664730-d307ca884978?w=400',
            'type' => 'physical',
            'is_active' => true,
        ]);

        // Adidas Ultraboost 1.0 (Sepatu)
        Product::create([
            'category_id' => $cats['Fashion']->id,
            'sub_category_id' => $subCats['Fashion_Sepatu']->id,
            'brand_id' => $brands['Adidas']->id,
            'name' => 'Adidas Ultraboost 1.0 Sneakers',
            'slug' => 'adidas-ultraboost-1-0-sneakers',
            'SKU' => 'AD-UB1-BLK',
            'price' => 2999000.00,
            'stock' => 15,
            'weight' => 850.0,
            'description' => 'Sepatu lari ikonik Adidas Ultraboost 1.0 dengan bantalan Boost legendaris dan rajutan adidas PRIMEKNIT yang pas di kaki.',
            'thumbnail' => 'https://images.unsplash.com/photo-1608231387042-66d1773070a5?w=400',
            'type' => 'physical',
            'is_active' => true,
        ]);

        // Nike Air Force 1 '07 (Sepatu)
        Product::create([
            'category_id' => $cats['Fashion']->id,
            'sub_category_id' => $subCats['Fashion_Sepatu']->id,
            'brand_id' => $brands['Nike']->id,
            'name' => "Nike Air Force 1 '07",
            'slug' => 'nike-air-force-1-07',
            'SKU' => 'NK-AF1-07-WHT',
            'price' => 1729000.00,
            'stock' => 22,
            'weight' => 950.0,
            'description' => "Sepatu sneakers Nike Air Force 1 '07 legendaris dengan warna putih klasik, bahan kulit premium, dan unit Nike Air untuk kenyamanan maksimal.",
            'thumbnail' => 'https://images.unsplash.com/photo-1600185365483-26d7a4cc7519?w=400',
            'type' => 'physical',
            'is_active' => true,
        ]);

        // Adidas Trefoil Classic Cap (Topi)
        Product::create([
            'category_id' => $cats['Fashion']->id,
            'sub_category_id' => $subCats['Fashion_Topi']->id,
            'brand_id' => $brands['Adidas']->id,
            'name' => 'Adidas Trefoil Classic Cap',
            'slug' => 'adidas-trefoil-classic-cap',
            'SKU' => 'AD-TRF-CAP',
            'price' => 299000.00,
            'stock' => 50,
            'weight' => 120.0,
            'description' => 'Topi baseball klasik Adidas dengan logo Trefoil yang dibordir di bagian depan, bahan katun twill nyaman, dan strap gesper belakang.',
            'thumbnail' => 'https://images.unsplash.com/photo-1588850561407-ed78c282e89b?w=400',
            'type' => 'physical',
            'is_active' => true,
        ]);

        // Nike Metal Swoosh H86 Cap (Topi)
        Product::create([
            'category_id' => $cats['Fashion']->id,
            'sub_category_id' => $subCats['Fashion_Topi']->id,
            'brand_id' => $brands['Nike']->id,
            'name' => 'Nike Metal Swoosh H86 Cap',
            'slug' => 'nike-metal-swoosh-h86-cap',
            'SKU' => 'NK-SWSH-H86',
            'price' => 329000.00,
            'stock' => 40,
            'weight' => 110.0,
            'description' => 'Topi sporty Nike Sportswear Heritage86 dengan desain klasik 6-panel, berbahan campuran polyester katun, dan logo Swoosh logam.',
            'thumbnail' => 'https://images.unsplash.com/photo-1521369909029-2afed882baee?w=400',
            'type' => 'physical',
            'is_active' => true,
        ]);

        // Nike Tech Fleece Full-Zip Hoodie (Jaket)
        Product::create([
            'category_id' => $cats['Fashion']->id,
            'sub_category_id' => $subCats['Fashion_Jaket']->id,
            'brand_id' => $brands['Nike']->id,
            'name' => 'Nike Tech Fleece Full-Zip Hoodie',
            'slug' => 'nike-tech-fleece-full-zip-hoodie',
            'SKU' => 'NK-TF-ZIP-GRY',
            'price' => 1599000.00,
            'stock' => 18,
            'weight' => 700.0,
            'description' => 'Jaket hoodie olahraga Nike Tech Fleece yang ringan dengan kehangatan luar biasa, saku ritsleting lengan, dan desain pas modern.',
            'thumbnail' => 'https://images.unsplash.com/photo-1556821840-3a63f95609a7?w=400',
            'type' => 'physical',
            'is_active' => true,
        ]);

        // Uniqlo Ultra Light Down Jacket (Jaket)
        Product::create([
            'category_id' => $cats['Fashion']->id,
            'sub_category_id' => $subCats['Fashion_Jaket']->id,
            'brand_id' => $brands['Uniqlo']->id,
            'name' => 'Uniqlo Ultra Light Down Jacket',
            'slug' => 'uniqlo-ultra-light-down-jacket',
            'SKU' => 'UQ-ULD-JKT',
            'price' => 999000.00,
            'stock' => 25,
            'weight' => 255.0,
            'description' => 'Jaket bulu angsa Uniqlo Ultra Light Down yang sangat ringan, hangat, tahan air gerimis, dan dapat dilipat kecil ke dalam kantong pembungkus.',
            'thumbnail' => 'https://images.unsplash.com/photo-1544923246-77307dd654cb?w=400',
            'type' => 'physical',
            'is_active' => true,
        ]);

        // ==========================================
        // 7. COUPONS
        // ==========================================
        Coupon::create([
            'code' => 'UDENNEW',
            'type' => 'fixed',
            'value' => 50000.00,
            'min_spend' => 200000.00,
            'start_date' => Carbon::now()->subDay(),
            'end_date' => Carbon::now()->addMonth(),
            'max_uses' => 100,
            'uses_count' => 0,
            'is_active' => true,
        ]);

        Coupon::create([
            'code' => 'SALE10',
            'type' => 'percent',
            'value' => 10.00,
            'min_spend' => 500000.00,
            'start_date' => Carbon::now()->subDay(),
            'end_date' => Carbon::now()->addMonth(),
            'max_uses' => 200,
            'uses_count' => 0,
            'is_active' => true,
        ]);

        // ==========================================
        // 8. BANNERS
        // ==========================================
        Banner::create([
            'title' => 'Promo Spesial Gadget Terbaru',
            'subtitle' => 'Dapatkan iPhone 15 Pro Max dengan cashback hingga 1 Juta Rupiah!',
            'image' => 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=1200&q=80',
            'link' => '/shop?category=gadget',
            'type' => 'hero',
            'sort_order' => 1
        ]);

        Banner::create([
            'title' => 'Upgrade Setup Gaming Kamu',
            'subtitle' => 'Diskon bundle menarik aksesoris gaming ROG.',
            'image' => 'https://images.unsplash.com/photo-1542751371-adc38448a05e?w=1200&q=80',
            'link' => '/shop?category=komputer',
            'type' => 'hero',
            'sort_order' => 2
        ]);

        Banner::create([
            'title' => 'Cashback 10% untuk Pengguna Baru',
            'subtitle' => 'Gunakan kode kupon SALE10 pada saat checkout.',
            'image' => 'https://images.unsplash.com/photo-1557804506-669a67965ba0?w=600&q=80',
            'link' => '/promo',
            'type' => 'promo',
            'sort_order' => 1
        ]);

        // ==========================================
        // 9. BLOGS
        // ==========================================
        Blog::create([
            'author_id' => $adminUser->id,
            'category_id' => $cats['Komputer']->id,
            'title' => 'Tips Memilih Keyboard Mekanis untuk Produktivitas',
            'slug' => 'tips-memilih-keyboard-mekanis-untuk-produktivitas',
            'content' => '<p>Memilih mechanical keyboard yang tepat dapat meningkatkan kenyamanan mengetik dan produktivitas Anda sehari-hari. Mulai dari tactile switch, linear switch, hingga clicky switch...</p>',
            'thumbnail' => 'https://images.unsplash.com/photo-1587829741301-dc798b83add3?w=600',
            'tags' => ['Keyboard', 'Mechanical', 'Gadget', 'Productivity'],
            'is_published' => true,
        ]);

        Blog::create([
            'author_id' => $adminUser->id,
            'category_id' => $cats['Gadget']->id,
            'title' => 'Review Lengkap iPhone 15 Pro Max di Tahun 2026',
            'slug' => 'review-lengkap-iphone-15-pro-max-di-tahun-2026',
            'content' => '<p>Apakah iPhone 15 Pro Max masih layak dibeli di tahun 2026? Dengan performa chip A17 Pro yang kencang dan konstruksi titanium yang ringan...</p>',
            'thumbnail' => 'https://images.unsplash.com/photo-1695048133142-1a20484d2569?w=600',
            'tags' => ['Apple', 'iPhone', 'Gadget', 'Review'],
            'is_published' => true,
        ]);

        // ==========================================
        // 10. REVIEWS
        // ==========================================
        Review::create([
            'user_id' => $customerUser->id,
            'product_id' => $iphone->id,
            'rating' => 5,
            'comment' => 'Barang sangat bagus, pengemasan rapi dan cepat sekali sampainya! Recomended seller!',
            'photos' => ['https://images.unsplash.com/photo-1510557880182-3d4d3cba35a5?w=200'],
            'is_approved' => true,
        ]);

        Review::create([
            'user_id' => $customerUser->id,
            'product_id' => $keyboard->id,
            'rating' => 4,
            'comment' => 'Suara switch ROG NX Red empuk, layout OLED sangat membantu untuk cek batre keyboard.',
            'photos' => null,
            'is_approved' => true,
        ]);
    }
}
