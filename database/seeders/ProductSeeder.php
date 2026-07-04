<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\BranchStock;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * A curated, realistic catalog (not randomized word-salad) so the site
     * looks like a real herbal store rather than a Faker demo. Each product
     * is mapped to the category it actually belongs to.
     */
    private const PRODUCTS = [
        'Jamu' => [
            ['name' => 'Jamu Kunyit Asam Original 250ml', 'price' => 12000, 'weight' => 300, 'desc' => 'Jamu kunyit asam tradisional dengan rasa asam segar, dibuat dari kunyit pilihan dan asam jawa asli. Membantu menjaga daya tahan tubuh dan melancarkan pencernaan.', 'top' => true],
            ['name' => 'Jamu Beras Kencur Segar 250ml', 'price' => 12000, 'weight' => 300, 'desc' => 'Racikan beras kencur klasik, hangat di tenggorokan dan menyegarkan badan setelah beraktivitas seharian.'],
            ['name' => 'Jamu Temulawak Madu 250ml', 'price' => 14000, 'weight' => 300, 'desc' => 'Perpaduan temulawak asli dan madu murni, cocok diminum rutin untuk menjaga nafsu makan dan kesehatan liver.'],
            ['name' => 'Jamu Sinom Manis Segar 250ml', 'price' => 13000, 'weight' => 300, 'desc' => 'Jamu sinom dari daun asam muda pilihan, rasa manis segar yang pas untuk diminum setiap hari.'],
            ['name' => 'Jamu Pahitan Daun Sambiloto 250ml', 'price' => 15000, 'weight' => 300, 'desc' => 'Jamu pahitan tradisional dari daun sambiloto, dipercaya membantu menjaga kadar gula darah tetap normal.'],
            ['name' => 'Jamu Kunci Suruh Wanita 250ml', 'price' => 16000, 'weight' => 300, 'desc' => 'Ramuan kunci suruh turun-temurun untuk menjaga kebersihan dan kesehatan area kewanitaan.'],
            ['name' => 'Jamu Cabe Puyang Pegal Linu 250ml', 'price' => 14000, 'weight' => 300, 'desc' => 'Jamu cabe puyang klasik untuk meredakan pegal linu dan capek setelah bekerja atau berolahraga.'],
        ],
        'Minyak Herbal' => [
            ['name' => 'Minyak Kayu Putih Murni 60ml', 'price' => 18000, 'weight' => 90, 'desc' => 'Minyak kayu putih murni tanpa campuran, menghangatkan tubuh dan meredakan masuk angin serta perut kembung.', 'top' => true],
            ['name' => 'Minyak Telon Bayi Alami 60ml', 'price' => 22000, 'weight' => 90, 'desc' => 'Minyak telon lembut berbahan alami, aman untuk kulit bayi dan membantu menghangatkan badan si kecil.'],
            ['name' => 'Minyak Zaitun Herbal untuk Pijat 100ml', 'price' => 35000, 'weight' => 150, 'desc' => 'Minyak zaitun murni yang diformulasikan khusus untuk pijat, melembapkan kulit dan melancarkan peredaran darah.'],
            ['name' => 'Minyak Habbatussauda Premium 30ml', 'price' => 45000, 'weight' => 60, 'desc' => 'Minyak habbatussauda premium hasil ekstraksi jintan hitam pilihan, diminum rutin untuk menjaga stamina tubuh.'],
            ['name' => 'Minyak Urut Jahe Merah Tradisional 100ml', 'price' => 28000, 'weight' => 150, 'desc' => 'Minyak urut dengan ekstrak jahe merah, terasa hangat dan cocok untuk pijat otot yang pegal.'],
            ['name' => 'Minyak Serai Wangi Aromaterapi 30ml', 'price' => 25000, 'weight' => 60, 'desc' => 'Minyak atsiri serai wangi untuk aromaterapi, membantu menenangkan pikiran dan mengusir nyamuk secara alami.'],
        ],
        'Teh Herbal' => [
            ['name' => 'Teh Herbal Daun Kelor Celup (25 Kantong)', 'price' => 20000, 'weight' => 100, 'desc' => 'Teh celup daun kelor kaya nutrisi, praktis diseduh kapan saja untuk mendukung pola hidup sehat.', 'top' => true],
            ['name' => 'Teh Rosella Kering Premium 100g', 'price' => 24000, 'weight' => 120, 'desc' => 'Kelopak bunga rosella kering pilihan, diseduh menjadi teh dengan rasa asam segar khas dan warna merah alami.'],
            ['name' => 'Teh Daun Sirsak Celup (20 Kantong)', 'price' => 22000, 'weight' => 100, 'desc' => 'Teh celup daun sirsak kering, diolah dengan proses pengeringan alami tanpa bahan pengawet.'],
            ['name' => 'Teh Jahe Merah Instan (10 Sachet)', 'price' => 18000, 'weight' => 150, 'desc' => 'Teh jahe merah instan yang praktis, cukup diseduh air panas untuk menghangatkan badan kapan saja.'],
            ['name' => 'Teh Kayu Manis & Cengkeh Celup', 'price' => 23000, 'weight' => 100, 'desc' => 'Perpaduan kayu manis dan cengkeh pilihan dalam kemasan teh celup, aromanya hangat dan menenangkan.'],
            ['name' => 'Teh Hijau Herbal Pelangsing', 'price' => 27000, 'weight' => 100, 'desc' => 'Teh hijau herbal dengan tambahan rempah pilihan, cocok menemani program diet dan pola makan sehat.'],
        ],
        'Suplemen Herbal' => [
            ['name' => 'Kapsul Kunyit Ekstrak 500mg (60 Kapsul)', 'price' => 55000, 'weight' => 80, 'desc' => 'Ekstrak kunyit dalam bentuk kapsul praktis, memudahkan konsumsi harian tanpa rasa pahit.', 'top' => true],
            ['name' => 'Kapsul Habbatussauda Ekstrak (60 Kapsul)', 'price' => 60000, 'weight' => 80, 'desc' => 'Kapsul jintan hitam berkualitas tinggi, diformulasikan untuk menjaga daya tahan tubuh sehari-hari.'],
            ['name' => 'Madu Hutan Asli 500ml', 'price' => 95000, 'weight' => 700, 'desc' => 'Madu hutan asli tanpa campuran gula, dipanen langsung dari peternak lebah lokal terpercaya.', 'top' => true],
            ['name' => 'Propolis Tetes Alami 30ml', 'price' => 85000, 'weight' => 70, 'desc' => 'Propolis tetes murni, praktis dikonsumsi dengan diteteskan ke air minum atau langsung ke mulut.'],
            ['name' => 'Kapsul Daun Kelor Ekstrak (60 Kapsul)', 'price' => 50000, 'weight' => 80, 'desc' => 'Ekstrak daun kelor dalam kapsul, kaya vitamin dan mineral untuk melengkapi kebutuhan nutrisi harian.'],
            ['name' => 'Sari Kurma Herbal 350ml', 'price' => 45000, 'weight' => 450, 'desc' => 'Sari kurma kental alami tanpa bahan pengawet, sumber energi alami yang cocok untuk seluruh keluarga.'],
        ],
        'Rempah & Bumbu' => [
            ['name' => 'Kunyit Bubuk Organik 100g', 'price' => 15000, 'weight' => 110, 'desc' => 'Bubuk kunyit organik hasil penggilingan halus, cocok untuk campuran jamu maupun masakan sehari-hari.'],
            ['name' => 'Jahe Merah Bubuk 100g', 'price' => 16000, 'weight' => 110, 'desc' => 'Jahe merah kering yang digiling halus, praktis untuk membuat wedang jahe hangat kapan saja.'],
            ['name' => 'Kayu Manis Bubuk Ceylon 100g', 'price' => 22000, 'weight' => 110, 'desc' => 'Bubuk kayu manis Ceylon asli dengan aroma khas yang lembut, cocok untuk minuman maupun kue.'],
            ['name' => 'Cengkeh Kering Pilihan 100g', 'price' => 20000, 'weight' => 110, 'desc' => 'Cengkeh kering kualitas pilihan, dipetik dan dikeringkan secara alami tanpa bahan tambahan.'],
            ['name' => 'Serai Kering Bubuk 100g', 'price' => 14000, 'weight' => 110, 'desc' => 'Serai kering yang dihaluskan menjadi bubuk, memudahkan pembuatan wedang serai tanpa perlu memarut.'],
            ['name' => 'Paket Bumbu Rempah Jamu Komplit', 'price' => 35000, 'weight' => 300, 'desc' => 'Paket lengkap rempah pilihan untuk membuat jamu sendiri di rumah — kunyit, jahe, kayu manis, dan cengkeh dalam satu paket.', 'top' => true],
        ],
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = Category::query()->pluck('id', 'name');
        $inactiveSlugs = ['minyak-serai-wangi-aromaterapi-30ml', 'teh-hijau-herbal-pelangsing'];

        foreach (self::PRODUCTS as $categoryName => $products) {
            foreach ($products as $data) {
                $slug = Str::slug($data['name']);

                Product::query()->updateOrCreate(
                    ['slug' => $slug],
                    [
                        'category_id' => $categories[$categoryName],
                        'name' => $data['name'],
                        'description' => $data['desc'],
                        'price' => $data['price'],
                        'weight' => $data['weight'],
                        'image_path' => null,
                        'shopee_url' => fake()->boolean(55) ? 'https://shopee.co.id/product/' . fake()->numberBetween(100000, 999999) : null,
                        'tiktok_url' => fake()->boolean(35) ? 'https://www.tiktok.com/@tokoherbal/product/' . fake()->numberBetween(100000, 999999) : null,
                        'order_now_url' => fake()->boolean(15) ? 'https://tokopedia.com/tokoherbal/' . $slug : null,
                        'is_active' => ! in_array($slug, $inactiveSlugs, true),
                        'is_top_pick' => (bool) ($data['top'] ?? false),
                    ]
                );
            }
        }

        // Give every product healthy stock at every active branch, so the
        // catalog shows in-stock items and checkout can resolve any branch as
        // the fulfilling one out of the box. A few are left at 0 so the
        // "Stok Habis" state is still exercised.
        $branches = Branch::query()->active()->get();

        Product::query()->each(function (Product $product) use ($branches) {
            foreach ($branches as $branch) {
                BranchStock::query()->updateOrCreate(
                    ['branch_id' => $branch->id, 'product_id' => $product->id],
                    ['stock' => fake()->boolean(85) ? fake()->numberBetween(15, 150) : 0],
                );
            }
        });
    }
}
