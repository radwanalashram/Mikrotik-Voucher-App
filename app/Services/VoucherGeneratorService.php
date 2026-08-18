<?php

namespace App\Services;

use App\Models\Voucher;
use RouterOS\Client;
use RouterOS\Query;

class VoucherGeneratorService
{
    protected array $profileMapping = [
        'H' => '10_HOURS_PROFILE',
        'D' => '1_DAY_PROFILE',
        'W' => '1_WEEK_PROFILE',
        'M' => '1_MONTH_PROFILE',
    ];

    public function generateAndSave(Client $client, string $middleChar, int $count = 10): array
    {
        if (!isset($this->profileMapping[$middleChar])) {
            throw new \InvalidArgumentException("رمز الفئة غير مدعوم.");
        }

        $profileName = $this->profileMapping[$middleChar];
        $createdVouchers = [];

        for ($i = 0; $i < $count; $i++) {
            $code = $this->generateCode(7, $middleChar);

            $query = (new Query('/user-manager/user/add'))
                ->equal('name', $code)
                ->equal('passphrase', $code)
                ->equal('group', $profileName)
                ->equal('comment', 'Generated via App');

            $client->query($query)->read();

            $voucher = Voucher::create([
                'code'        => $code,
                'password'    => $code,
                'profile'     => $profileName,
                'middle_char' => $middleChar,
                'status'      => 'active',
            ]);

            $createdVouchers[] = $voucher;
        }

        return $createdVouchers;
    }

    private function generateCode(int $length, string $middleChar): string
    {
        $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
        $code = '';
        for ($i = 0; $i < $length; $i++) {
            $code .= $chars[random_int(0, strlen($chars) - 1)];
        }
        $middleIndex = (int) floor($length / 2);
        $code[$middleIndex] = $middleChar;

        return $code;
    }
}
