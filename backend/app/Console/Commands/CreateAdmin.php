<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;

use function Laravel\Prompts\password;
use function Laravel\Prompts\text;

#[Signature('admin:create {email? : Login email} {--name= : Display name}')]
#[Description('Create (or reactivate) an admin panel user; the password is asked interactively')]
class CreateAdmin extends Command
{
    public function handle(): int
    {
        $email = $this->argument('email') ?? text('Email', required: true);
        $name = $this->option('name') ?? text('Name', default: 'Super Admin', required: true);
        $password = password('Password (min 12 characters)', required: true);

        $validator = Validator::make(
            compact('email', 'name', 'password'),
            ['email' => ['required', 'email'], 'name' => ['required', 'max:255'], 'password' => ['required', 'min:12', 'max:72']],
        );

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }

            return self::FAILURE;
        }

        User::updateOrCreate(['email' => $email], ['name' => $name, 'password' => $password, 'status' => true]);
        $this->info("Admin {$email} is ready.");

        return self::SUCCESS;
    }
}
