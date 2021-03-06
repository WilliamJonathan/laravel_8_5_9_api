<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use App\Models\Modelo;
use Illuminate\Http\Request;

class ModeloController extends Controller
{

    public function __construct(Modelo $modelo) {
        $this->modelo = $modelo;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $modelos = array();

        //pesquisar campos especificos da tabela marca
        if ($request->has('atributos_marca')) {
            $atributos_marca = $request->atributos_marca;
            $modelos = $this->modelo->with('marca:id,'.$atributos_marca);
        }else{
            $modelos = $this->modelo->with('marca');
        }

        //clausula where, tem que ficar depois da atribuição do modelo
        if ($request->has('filtro')) {

            $filtros = explode(';', $request->filtro);
            foreach ($filtros as $key => $condicao) {
                $c = explode(':', $condicao);
                $modelos = $modelos->where($c[0], $c[1], $c[2]);
            }

        }

        //pesquisar campos especificos da tabela modelos
        if ($request->has('atributos')) {
            $atributos = $request->atributos;
            $modelos = $modelos->selectRaw($atributos)->get();
        }else{
            $modelos = $modelos->get();
        }

        //$modelo = $this->modelo->with('marca')->get();
        return response()->json($modelos, 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate($this->modelo->rules());


        $imagem = $request->file('imagem');
        $imagem_urn =  $imagem->store('imagens/modelos', 'public');

        //$marca =  $this->marca->create($request->all());
        $modelo =  $this->modelo->create([
            'marca_id' => $request->marca_id,
            'nome' => $request->nome,
            'imagem' => $imagem_urn,
            'numero_portas' => $request->numero_portas,
            'lugares' => $request->lugares,
            'air_bag' => $request->air_bag,
            'abs' => $request->abs
        ]);
        return response()->json($modelo, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param Integer
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $modelo = $this->modelo->with('marca')->find($id);
        if($modelo === null){
            return response()->json(['erro' => 'Recurso pesquisado não existe'], 404);
        }
        return $modelo;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Modelo  $modelo
     * @return \Illuminate\Http\Response
     */
    public function edit(Modelo $modelo)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Integer
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $modelo = $this->modelo->find($id);
        if($modelo === null){
            return response()->json(['erro' => 'Impossivel realizar a atualização, indice inexistente'], 404);
        }

        if($request->method() === 'PATCH'){

            $regrasDinamicas = array();

            //percorre todas as regras definas no model
            foreach ($modelo->rules() as $input => $regra) {
                //coleta apenas as regras aplicaveis aos parametros parciais da requisição
                if (array_key_exists($input, $request->all())) {
                    $regrasDinamicas[$input] = $regra;
                }
            }

            $request->validate($regrasDinamicas);

        }else{
            $request->validate($modelo->rules());
        }

        //remove um arquivo antigo caso um novo arquivo tenha sido enviado no request
        if ($request->file('imagem')) {
            Storage::disk('public')->delete($modelo->imagem);
        }

        $imagem = $request->file('imagem');
        $imagem_urn = null;
        if ($imagem != null) {
            $imagem_urn =  $imagem->store('imagens/modelos', 'public');
        }

        //preencher o objeto marca com os dados do request
        $modelo->fill($request->all());
        if ($imagem_urn != null) {
            $modelo->imagem = $imagem_urn;
        }
        $modelo->save();

        /*
        $modelo->update([
            'marca_id' => $request->marca_id,
            'nome' => $request->nome,
            'imagem' => $imagem_urn,
            'numero_portas' => $request->numero_portas,
            'lugares' => $request->lugares,
            'air_bag' => $request->air_bag,
            'abs' => $request->abs
        ]);
        */
        return $modelo;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Integer
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $modelo = $this->modelo->find($id);
        if($modelo === null){
            return response()->json(['erro' => 'Impossivel realizar a exclusão. O recurso solicitado não existe'], 404);
        }

        //remove um arquivo antigo caso um novo arquivo tenha sido enviado no request
        Storage::disk('public')->delete($modelo->imagem);

        $modelo->delete();
        return ['msg' => 'O modelo foi removido com sucesso'];
    }
}
