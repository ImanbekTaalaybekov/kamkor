<?php

namespace Database\Seeders;

use App\Models\AdminUser;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $accounts = [
            ['name' => 'admin_bishkek_main', 'plain_password' => 'Sj0G$#7b', 'region' => 'г. Бишкек', 'district' => null, 'uvd_code' => null, 'role' => 'region'],
            ['name' => 'admin_osh_city_main', 'plain_password' => 'e!dMKF7L', 'region' => 'г. Ош', 'district' => null, 'uvd_code' => null, 'role' => 'region'],
            ['name' => 'admin_issyk_kul_main', 'plain_password' => '!I#F7j*$', 'region' => 'Иссык-Кульская область', 'district' => null, 'uvd_code' => null, 'role' => 'region'],
            ['name' => 'admin_naryn_main', 'plain_password' => 'RM#hrBh3', 'region' => 'Нарынская область', 'district' => null, 'uvd_code' => null, 'role' => 'region'],
            ['name' => 'admin_osh_region_main', 'plain_password' => '!*tX88WH', 'region' => 'Ошская область', 'district' => null, 'uvd_code' => null, 'role' => 'region'],
            ['name' => 'admin_batken_main', 'plain_password' => '2$%qD5iH', 'region' => 'Баткенская область', 'district' => null, 'uvd_code' => null, 'role' => 'region'],
            ['name' => 'admin_chuy_main', 'plain_password' => 'Iw2J#Ip#', 'region' => 'Чуйская область', 'district' => null, 'uvd_code' => null, 'role' => 'region'],
            ['name' => 'admin_talas_main', 'plain_password' => 'Z3u4$EVW', 'region' => 'Таласская область', 'district' => null, 'uvd_code' => null, 'role' => 'region'],
            ['name' => 'admin_jalal_abad_main', 'plain_password' => '2KR1#uc%', 'region' => 'Джалал-Абадская область', 'district' => null, 'uvd_code' => null, 'role' => 'region'],
            ['name' => 'operator_101', 'plain_password' => '7y8#XFzn', 'region' => 'г. Бишкек', 'district' => 'Ленинский район', 'uvd_code' => '101', 'role' => 'local'],
            ['name' => 'operator_102_1', 'plain_password' => '&C3wSHu4', 'region' => 'г. Бишкек', 'district' => 'Первомайский район', 'uvd_code' => '102', 'role' => 'local'],
            ['name' => 'operator_102_2', 'plain_password' => 'MoZ%p4oW', 'region' => 'г. Бишкек', 'district' => 'Первомайский район', 'uvd_code' => '102', 'role' => 'local'],
            ['name' => 'operator_103', 'plain_password' => 'cT!I4kQ5', 'region' => 'г. Бишкек', 'district' => 'Свердловский район', 'uvd_code' => '103', 'role' => 'local'],
            ['name' => 'operator_155', 'plain_password' => '$P$sR%3P', 'region' => 'г. Бишкек', 'district' => 'Октябрьский район', 'uvd_code' => '155', 'role' => 'local'],
            ['name' => 'operator_214', 'plain_password' => 'Zzv#66AG', 'region' => 'Иссык-Кульская область', 'district' => 'Жети-Огузский район', 'uvd_code' => '214', 'role' => 'local'],
            ['name' => 'operator_215', 'plain_password' => '$l$4i!8V', 'region' => 'Иссык-Кульская область', 'district' => 'Иссык-Кульский район', 'uvd_code' => '215', 'role' => 'local'],
            ['name' => 'operator_216', 'plain_password' => 'z@HMxV2*', 'region' => 'Иссык-Кульская область', 'district' => 'Каракол (город)', 'uvd_code' => '216', 'role' => 'local'],
            ['name' => 'operator_217', 'plain_password' => '8*PL*7z1', 'region' => 'Иссык-Кульская область', 'district' => 'Балыкчы (город)', 'uvd_code' => '217', 'role' => 'local'],
            ['name' => 'operator_218', 'plain_password' => 'X%o92hBu', 'region' => 'Иссык-Кульская область', 'district' => 'Тонский район', 'uvd_code' => '218', 'role' => 'local'],
            ['name' => 'operator_219', 'plain_password' => '!$2Q5vwP', 'region' => 'Иссык-Кульская область', 'district' => 'Тюпский район', 'uvd_code' => '219', 'role' => 'local'],
            ['name' => 'operator_259', 'plain_password' => '3Ieq8HM$', 'region' => 'Иссык-Кульская область', 'district' => 'Ак-Суйский район', 'uvd_code' => '259', 'role' => 'local'],
            ['name' => 'operator_321', 'plain_password' => 'As6Q&fOL', 'region' => 'Нарынская область', 'district' => 'Ак-Талинский район', 'uvd_code' => '321', 'role' => 'local'],
            ['name' => 'operator_322', 'plain_password' => 'Zr%6a0kj', 'region' => 'Нарынская область', 'district' => 'Ат-Башинский район', 'uvd_code' => '322', 'role' => 'local'],
            ['name' => 'operator_323', 'plain_password' => '$93PrG1!', 'region' => 'Нарынская область', 'district' => 'Жумгальский район', 'uvd_code' => '323', 'role' => 'local'],
            ['name' => 'operator_324', 'plain_password' => '*s4V5gyf', 'region' => 'Нарынская область', 'district' => 'Кочкорский район', 'uvd_code' => '324', 'role' => 'local'],
            ['name' => 'operator_326', 'plain_password' => 'l2$u#M9X', 'region' => 'Нарынская область', 'district' => 'Нарынский район', 'uvd_code' => '326', 'role' => 'local'],
            ['name' => 'operator_360', 'plain_password' => 'D1cGkx#9', 'region' => 'Нарынская область', 'district' => 'Нарын (город)', 'uvd_code' => '360', 'role' => 'local'],
            ['name' => 'operator_429', 'plain_password' => '@eCfh4!A', 'region' => 'Ошская область', 'district' => 'Алайский район', 'uvd_code' => '429', 'role' => 'local'],
            ['name' => 'operator_430', 'plain_password' => 'E&Fdz8Q6', 'region' => 'Ошская область', 'district' => 'Араванский район', 'uvd_code' => '430', 'role' => 'local'],
            ['name' => 'operator_434', 'plain_password' => 'PxG@E@6d', 'region' => 'Ошская область', 'district' => 'Кара-Сууйский район', 'uvd_code' => '434', 'role' => 'local'],
            ['name' => 'operator_440', 'plain_password' => 'bsM3K#X!', 'region' => 'Ошская область', 'district' => 'Ноокатский район', 'uvd_code' => '440', 'role' => 'local'],
            ['name' => 'operator_442', 'plain_password' => 'M7E@fcPW', 'region' => 'Ошская область', 'district' => 'Кара-Кулжинский район', 'uvd_code' => '442', 'role' => 'local'],
            ['name' => 'operator_447', 'plain_password' => 'Vg27x8@n', 'region' => 'Ошская область', 'district' => 'Узгенский район', 'uvd_code' => '447', 'role' => 'local'],
            ['name' => 'operator_476', 'plain_password' => 't22h2Fv$', 'region' => 'Ошская область', 'district' => 'Чон-Алайский район', 'uvd_code' => '476', 'role' => 'local'],
            ['name' => 'operator_431', 'plain_password' => '5#9kmVQ3', 'region' => 'Баткенская область', 'district' => 'Баткенский район', 'uvd_code' => '431', 'role' => 'local'],
            ['name' => 'operator_436', 'plain_password' => 'La2S@jxR', 'region' => 'Баткенская область', 'district' => 'Кызыл-Кия (город)', 'uvd_code' => '436', 'role' => 'local'],
            ['name' => 'operator_438', 'plain_password' => 'I7!i9b5Z', 'region' => 'Баткенская область', 'district' => 'Лейлекский район', 'uvd_code' => '438', 'role' => 'local'],
            ['name' => 'operator_444', 'plain_password' => '58uIU%H9', 'region' => 'Баткенская область', 'district' => 'Сулюкта (город)', 'uvd_code' => '444', 'role' => 'local'],
            ['name' => 'operator_448', 'plain_password' => 'Ei1r6!gV', 'region' => 'Баткенская область', 'district' => 'Кадамжайский район', 'uvd_code' => '448', 'role' => 'local'],
            ['name' => 'operator_493', 'plain_password' => 'Pr#1tDcy', 'region' => 'Баткенская область', 'district' => 'Баткен (город)', 'uvd_code' => '493', 'role' => 'local'],
            ['name' => 'operator_441', 'plain_password' => '2k9@K4TO', 'region' => 'г. Ош', 'district' => 'Сулайман-Тоо / Ак-Буура', 'uvd_code' => '441', 'role' => 'local'],
            ['name' => 'operator_504', 'plain_password' => 'vT!#0zeC', 'region' => 'Чуйская область', 'district' => 'Жайылский район', 'uvd_code' => '504', 'role' => 'local'],
            ['name' => 'operator_505', 'plain_password' => '!501O9xf', 'region' => 'Чуйская область', 'district' => 'Ысык-Атинский район', 'uvd_code' => '505', 'role' => 'local'],
            ['name' => 'operator_506', 'plain_password' => '$GT1ekuD', 'region' => 'Чуйская область', 'district' => 'Кеминский район', 'uvd_code' => '506', 'role' => 'local'],
            ['name' => 'operator_508', 'plain_password' => 'sU!Tj8*7', 'region' => 'Чуйская область', 'district' => 'Московский район', 'uvd_code' => '508', 'role' => 'local'],
            ['name' => 'operator_509', 'plain_password' => 'g&U6hloc', 'region' => 'Чуйская область', 'district' => 'Сокулукский район', 'uvd_code' => '509', 'role' => 'local'],
            ['name' => 'operator_511', 'plain_password' => '7EvbTQG*', 'region' => 'Чуйская область', 'district' => 'Токмок (город)', 'uvd_code' => '511', 'role' => 'local'],
            ['name' => 'operator_512', 'plain_password' => 'Q8dl#10m', 'region' => 'Чуйская область', 'district' => 'Чуйский район', 'uvd_code' => '512', 'role' => 'local'],
            ['name' => 'operator_558', 'plain_password' => '7jY%tbX3', 'region' => 'Чуйская область', 'district' => 'Аламудунский район', 'uvd_code' => '558', 'role' => 'local'],
            ['name' => 'operator_563', 'plain_password' => 'zDe!9!9U', 'region' => 'Чуйская область', 'district' => 'Панфиловский район', 'uvd_code' => '563', 'role' => 'local'],
            ['name' => 'operator_707', 'plain_password' => '%1kzoi*U', 'region' => 'Таласская область', 'district' => 'Айтматовский район', 'uvd_code' => '707', 'role' => 'local'],
            ['name' => 'operator_710', 'plain_password' => '67$s0S*C', 'region' => 'Таласская область', 'district' => 'Таласский район', 'uvd_code' => '710', 'role' => 'local'],
            ['name' => 'operator_764', 'plain_password' => 'dx2*NExO', 'region' => 'Таласская область', 'district' => 'Бакай-Атинский район', 'uvd_code' => '764', 'role' => 'local'],
            ['name' => 'operator_767', 'plain_password' => 'QT5N4Vz%', 'region' => 'Таласская область', 'district' => 'Манасский район', 'uvd_code' => '767', 'role' => 'local'],
            ['name' => 'operator_768', 'plain_password' => 'POX#9fPN', 'region' => 'Таласская область', 'district' => 'Талас (город)', 'uvd_code' => '768', 'role' => 'local'],
            ['name' => 'operator_825', 'plain_password' => 'TL2@w8%a', 'region' => 'Джалал-Абадская область', 'district' => 'Тогуз-Тороуский район', 'uvd_code' => '825', 'role' => 'local'],
            ['name' => 'operator_828', 'plain_password' => '7L$ugJSE', 'region' => 'Джалал-Абадская область', 'district' => 'Ала-Букинский район', 'uvd_code' => '828', 'role' => 'local'],
            ['name' => 'operator_832', 'plain_password' => 'lNPC3%fr', 'region' => 'Джалал-Абадская область', 'district' => 'Манас (город)', 'uvd_code' => '832', 'role' => 'local'],
            ['name' => 'operator_833', 'plain_password' => 'AhumX*v1', 'region' => 'Джалал-Абадская область', 'district' => 'Аксыйский район', 'uvd_code' => '833', 'role' => 'local'],
            ['name' => 'operator_837', 'plain_password' => 'g!$y%5xN', 'region' => 'Джалал-Абадская область', 'district' => 'Ноокенский район', 'uvd_code' => '837', 'role' => 'local'],
            ['name' => 'operator_839', 'plain_password' => 'anE4$exn', 'region' => 'Джалал-Абадская область', 'district' => 'Майлуу-Суу (город)', 'uvd_code' => '839', 'role' => 'local'],
            ['name' => 'operator_843', 'plain_password' => 'g3c*VXCu', 'region' => 'Джалал-Абадская область', 'district' => 'Сузакский район', 'uvd_code' => '843', 'role' => 'local'],
            ['name' => 'operator_845', 'plain_password' => 'a5NaZIJ$', 'region' => 'Джалал-Абадская область', 'district' => 'Таш-Кумыр (город)', 'uvd_code' => '845', 'role' => 'local'],
            ['name' => 'operator_846', 'plain_password' => '17Ql%FYk', 'region' => 'Джалал-Абадская область', 'district' => 'Токтогульский район', 'uvd_code' => '846', 'role' => 'local'],
            ['name' => 'operator_865', 'plain_password' => '4@nzY7gy', 'region' => 'Джалал-Абадская область', 'district' => 'Кара-Куль (город)', 'uvd_code' => '865', 'role' => 'local'],
            ['name' => 'operator_866', 'plain_password' => 'CB9Xq!n4', 'region' => 'Джалал-Абадская область', 'district' => 'Базар-Коргонский район', 'uvd_code' => '866', 'role' => 'local'],
            ['name' => 'operator_869', 'plain_password' => '1dfcV%G!', 'region' => 'Джалал-Абадская область', 'district' => 'Чаткальский район', 'uvd_code' => '869', 'role' => 'local']
        ];

        DB::transaction(function () use ($accounts): void {
            foreach ($accounts as $account) {
                // Ничего не удаляем. Повторный запуск не создаёт дубликат по имени.
                AdminUser::query()->updateOrCreate(
                    ['name' => $account['name']],
                    [
                        'password' => Hash::make($account['plain_password']),
                        'region' => $account['region'],
                        'district' => $account['district'],
                        'uvd_code' => $account['uvd_code'],
                        'fcm_token' => null,
                        'role' => $account['role'],
                    ]
                );
            }
        });
    }
}
