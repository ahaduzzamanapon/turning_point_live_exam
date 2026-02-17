<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Menu;

class WalletMenuSeeder extends Seeder
{
    public function run()
    {
        // Check if already exists to avoid duplicates
        if (Menu::where('title', 'Wallet Management')->exists()) {
            return;
        }

        Menu::create([
            'title' => 'Wallet Management',
            'route' => 'admin.wallet.index',
            'icon' => 'fas fa-wallet', // Using FontAwesome icon
            'order' => 5, // Adjust order as needed
        ]);
    }
}
