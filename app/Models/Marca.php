<?php

/**
 * Para criar um model, controller, migration e popular controller com metodos padrões de uma só fez usar comando:
 * php artisan make:model --migrate --controller --resource NomeModel
 * ou abreviação
 * php artisan make:model -mcr Nome
 * ou comando all, que neste caso cria uma factory e um seeder tambem
 * php artisan make:model (--all) ou (--a) Nome
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Marca extends Model
{
    use HasFactory;
}
