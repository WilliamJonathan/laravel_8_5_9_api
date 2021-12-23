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
    protected $fillable = ['nome', 'imagem'];

    public function rules()
    {
        return [
            'nome' => 'required|unique:marcas,nome,'.$this->id,
            'imagem' => 'required|file|mimes:png'
        ];
    }

    public function feedback()
    {
        return [
            'required' => 'O campo :attribute é obrigatorio',
            'imagem.mimes' => 'O arquivo deve ser uma imagem do tipo PNG',
            'nome.unique' => 'O Nome da marca já existe'
        ];
    }

    public function modelos()
    {
        #UMA MARCA PODE TER VARIOS MODELOS
        return $this->hasMany('App\Models\Modelo');
    }

}
