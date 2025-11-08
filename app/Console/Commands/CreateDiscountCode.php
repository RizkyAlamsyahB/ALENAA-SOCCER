<?php

namespace App\Console\Commands;

use App\Models\Discount;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class CreateDiscountCode extends Command
{
    protected $signature = 'discount:create {--code=} {--name=} {--type=percentage} {--value=} {--min=0} {--max=} {--limit=} {--user-limit=1} {--days=30} {--type-for=all}';
    protected $description = 'Create a new discount code';

    public function handle()
    {
        $code = $this->option('code') ?: strtoupper(Str::random(8));
        $name = $this->option('name') ?: "Diskon {$code}";
        $type = $this->option('type');
        $value = $this->option('value');
        $minOrder = $this->option('min');
        $maxDiscount = $this->option('max');
        $usageLimit = $this->option('limit');
        $userLimit = $this->option('user-limit');
        $days = $this->option('days');
        $applicableTo = $this->option('type-for');

        if (!$value) {
            $value = $this->ask('Masukkan nilai diskon (persentase atau nominal)');
        }

        if (!in_array($type, ['percentage', 'fixed'])) {
            $type = 'percentage';
        }

        $discount = Discount::create([
            'code' => $code,
            'name' => $name,
            'description' => "Diskon {$type} {$value}" . ($type === 'percentage' ? '%' : ' Rupiah'),
            'type' => $type,
            'value' => $value,
            'min_order' => $minOrder,
            'max_discount' => $maxDiscount,
            'applicable_to' => $applicableTo,
            'usage_limit' => $usageLimit,
            'user_usage_limit' => $userLimit,
            'start_date' => now(),
            'end_date' => now()->addDays($days),
            'is_active' => true,
        ]);

        $this->info("Kode diskon berhasil dibuat!");
        $this->table(
            ['Kode', 'Nama', 'Tipe', 'Nilai', 'Min Order', 'Max Diskon', 'Berlaku Sampai', 'Untuk'],
            [[
                $discount->code,
                $discount->name,
                $discount->type,
                $discount->value . ($discount->type === 'percentage' ? '%' : ''),
                'Rp ' . number_format($discount->min_order, 0, ',', '.'),
                $discount->max_discount ? 'Rp ' . number_format($discount->max_discount, 0, ',', '.') : '-',
                $discount->end_date->format('d M Y'),
                $discount->applicable_to
            ]]
        );

        return Command::SUCCESS;
    }
}
