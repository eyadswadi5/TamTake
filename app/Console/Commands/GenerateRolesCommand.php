<?php

namespace App\Console\Commands;

use App\Models\Role;
use Illuminate\Console\Command;

class GenerateRolesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'roles:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate roles';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->newLine();
        
        $roles = [
            ["role" => "admin"],
            ["role" => "user"],
            ["role" => "business"],
            ["role" => "driver"],
        ];

        foreach($roles as $r) {
            Role::create([
                "role" => $r["role"],
            ]);
        }
        $this->info('[user, admin, business, driver] roles added successfully');

    }
}
