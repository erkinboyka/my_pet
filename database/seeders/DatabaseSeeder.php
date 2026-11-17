<?php

namespace Database\Seeders;

use App\Models\Auction;
use App\Models\Bid;
use App\Models\Category;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    // демо-данные, даты лотов считаются от текущего момента,
    // поэтому после запуска на сайте всегда есть живые и конченые лоты
    public function run(): void
    {
        // категории

        $categories = [];
        foreach ([
            ['Электроника', 'electronics'],
            ['Автомобили', 'auto'],
            ['Недвижимость', 'realty'],
            ['Одежда и аксессуары', 'fashion'],
            ['Спорт и отдых', 'sport'],
            ['Коллекционирование', 'books'],
        ] as [$name, $slug]) {
            $categories[$slug] = Category::firstOrCreate(['slug' => $slug], ['name' => $name]);
        }

        // пользователи

        $admin = $this->makeUser('admin@auction.tj', 'Азиз Каримов', 'admin');
        $moderator1 = $this->makeUser('moderator1@auction.tj', 'Фаррух Сафаров', 'moderator');
        $moderator2 = $this->makeUser('moderator2@auction.tj', 'Малика Рахимова', 'moderator');

        $clients = [
            $this->makeUser('client1@auction.tj', 'Рустам Комилов', 'client'),
            $this->makeUser('client2@auction.tj', 'Мадина Юсупова', 'client'),
            $this->makeUser('client3@auction.tj', 'Зуфар Тураев', 'client'),
            $this->makeUser('client4@auction.tj', 'Сайрам Азизова', 'client'),
            $this->makeUser('client5@auction.tj', 'Бахтиёра Саидов', 'client'),
        ];

        // заблокированный, для проверки админки
        $this->makeUser('blocked@auction.tj', 'Дилшод Назаров', 'client', false);

        // лоты

        // сидер можно перезапускать, старые лоты и ставки удаляем
        Bid::query()->delete();
        Auction::query()->delete();

        $lot = function (array $data) use ($admin) {
            return Auction::create(array_merge([
                'created_by' => $admin->id,
                'status' => 'active',
                'minimum_bid_step' => 100,
            ], $data));
        };

        $bid = function (Auction $auction, User $user, float $amount, Carbon $at) {
            $model = $auction->bids()->create([
                'user_id' => $user->id,
                'amount' => $amount,
            ]);

            Bid::whereKey($model->id)->update([
                'created_at' => $at,
                'updated_at' => $at,
            ]);
        };

        // 1. Идёт сейчас, есть ставки
        $a1 = $lot([
            'title' => 'Смартфон Samsung Galaxy S24, 256 ГБ',
            'description' => 'Новый смартфон в заводской упаковке. Полный комплект, гарантия 12 месяцев. Доставка по Душанбе курьером.',
            'category_id' => $categories['electronics']->id,
            'starting_price' => 5500,
            'minimum_bid_step' => 200,
            'current_price' => 6100,
            'start_at' => now()->subHours(2),
            'end_at' => now()->addHours(6),
            'status' => 'active',
            'moderator_id' => $moderator1->id,
        ]);
        $bid($a1, $clients[1], 5500, now()->subMinutes(95));
        $bid($a1, $clients[2], 5800, now()->subMinutes(60));
        $bid($a1, $clients[0], 6100, now()->subMinutes(25));

        // 2. Закрывается скоро
        $a2 = $lot([
            'title' => 'Кроссовки Nike Air Max, размер 42',
            'description' => 'Размер 42 (27 см), цвет чёрно-белый. Носились один сезон, состояние отличное.',
            'category_id' => $categories['fashion']->id,
            'starting_price' => 800,
            'minimum_bid_step' => 50,
            'current_price' => 950,
            'start_at' => now()->subHour(),
            'end_at' => now()->addMinutes(45),
            'status' => 'active',
            'moderator_id' => $moderator1->id,
        ]);
        $bid($a2, $clients[3], 800, now()->subMinutes(50));
        $bid($a2, $clients[4], 875, now()->subMinutes(30));
        $bid($a2, $clients[0], 950, now()->subMinutes(10));

        // 3. Только открылся, ставок нет
        $a3 = $lot([
            'title' => 'Ноутбук ASUS VivoBook 15, Core i5',
            'description' => '16 ГБ ОЗУ, SSD 512 ГБ, экран 15.6". Б/у, состояние нового. Батарея держит около 6 часов.',
            'category_id' => $categories['electronics']->id,
            'starting_price' => 4800,
            'minimum_bid_step' => 300,
            'start_at' => now()->subMinutes(15),
            'end_at' => now()->addDays(2),
            'status' => 'active',
            'moderator_id' => $moderator2->id,
        ]);

        // 4. Откроется завтра
        $lot([
            'title' => 'Автомобиль Toyota Camry 2018, 2.5 литра',
            'description' => 'Пробег 96 000 км, один владелец, полный сервис. Комплект зимней резины в подарок.',
            'category_id' => $categories['auto']->id,
            'starting_price' => 145000,
            'minimum_bid_step' => 5000,
            'start_at' => now()->addDay(),
            'end_at' => now()->addDays(3),
            'status' => 'scheduled',
            'moderator_id' => $moderator2->id,
        ]);

        // 5. Откроется через несколько часов
        $lot([
            'title' => 'Дача в Гиссаре, 6 соток с домом',
            'description' => 'Дом 80 м², два этажа, газ, вода, электричество. Ухоженный участок с плодовыми деревьями.',
            'category_id' => $categories['realty']->id,
            'starting_price' => 320000,
            'minimum_bid_step' => 10000,
            'start_at' => now()->addHours(5),
            'end_at' => now()->addDays(4),
            'status' => 'scheduled',
            'moderator_id' => $moderator1->id,
        ]);

        // 6. Завершён, победитель определён
        $a6 = $lot([
            'title' => 'Винтажная гитара 1978 года',
            'description' => 'Советская гитара в рабочем состоянии, реставрирована. Коллекционная редкость.',
            'category_id' => $categories['books']->id,
            'starting_price' => 2000,
            'minimum_bid_step' => 100,
            'current_price' => 2400,
            'start_at' => now()->subDays(3),
            'end_at' => now()->subDay(),
            'status' => 'finished',
            'moderator_id' => $moderator1->id,
            'winner_id' => $clients[1]->id,
            'winning_price' => 2400,
        ]);
        $bid($a6, $clients[0], 2000, now()->subDays(2)->subHours(3));
        $bid($a6, $clients[3], 2200, now()->subDays(2));
        $bid($a6, $clients[1], 2400, now()->subDay()->subHours(5));

        // 7. Завершён давно
        $a7 = $lot([
            'title' => 'Фотоаппарат Canon EOS 90D с объективом',
            'description' => 'Китовый объектив 18-55 мм, дополнительный аккумулятор и сумка в комплекте.',
            'category_id' => $categories['electronics']->id,
            'starting_price' => 7000,
            'minimum_bid_step' => 500,
            'current_price' => 8500,
            'start_at' => now()->subDays(10),
            'end_at' => now()->subDays(5),
            'status' => 'finished',
            'moderator_id' => $moderator2->id,
            'winner_id' => $clients[2]->id,
            'winning_price' => 8500,
        ]);
        $bid($a7, $clients[4], 7500, now()->subDays(9));
        $bid($a7, $clients[2], 8000, now()->subDays(7));
        $bid($a7, $clients[1], 8500, now()->subDays(6));

        // 8. Завершён без единой ставки
        $lot([
            'title' => 'Монетный набор «Мир Средневековья»',
            'description' => 'Коллекционный набор из 24 монет в подарочной упаковке.',
            'category_id' => $categories['books']->id,
            'starting_price' => 1500,
            'minimum_bid_step' => 100,
            'start_at' => now()->subDays(8),
            'end_at' => now()->subDays(6),
            'status' => 'finished',
            'moderator_id' => $moderator1->id,
        ]);

        // 9. Черновик модератора
        $lot([
            'title' => 'Картина местного художника (холст, масло)',
            'description' => 'Авторская работа 2024 года, 60×80 см. Ожидает выставки на сайте.',
            'category_id' => $categories['books']->id,
            'starting_price' => 3500,
            'start_at' => now()->addDays(5),
            'end_at' => now()->addDays(7),
            'status' => 'draft',
            'moderator_id' => $moderator1->id,
            'created_by' => $moderator1->id,
        ]);

        // 10. Ждёт одобрения администратора
        $lot([
            'title' => 'Горный велосипед Trek Marlin 7',
            'description' => 'Рама M, колёса 29", дисковые тормоза. Пробег минимальный, обслужен.',
            'category_id' => $categories['sport']->id,
            'starting_price' => 6200,
            'minimum_bid_step' => 300,
            'start_at' => now()->addDays(2),
            'end_at' => now()->addDays(5),
            'status' => 'pending_moderation',
            'moderator_id' => $moderator2->id,
            'created_by' => $moderator2->id,
        ]);

        // 11. Отменённый лот
        $lot([
            'title' => 'Телефон iPhone 13, 128 ГБ',
            'description' => 'Продавец снял лот с аукциона — продал по объявлению.',
            'category_id' => $categories['electronics']->id,
            'starting_price' => 4200,
            'start_at' => now()->subDays(4),
            'end_at' => now()->subDays(2),
            'status' => 'cancelled',
            'moderator_id' => $moderator1->id,
        ]);

        // 12. Отклонённый лот
        $lot([
            'title' => 'Лот нарушает правила публикации',
            'description' => 'Лот отклонён модерацией: описание не соответствует требованиям сайта.',
            'category_id' => $categories['fashion']->id,
            'starting_price' => 1000,
            'start_at' => now()->addDays(3),
            'end_at' => now()->addDays(5),
            'status' => 'rejected',
            'moderator_id' => $moderator1->id,
        ]);
    }

    private function makeUser(string $email, string $name, string $role, bool $active = true): User
    {
        return User::updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => 'password',
                'role' => $role,
                'is_active' => $active,
            ]
        );
    }
}
