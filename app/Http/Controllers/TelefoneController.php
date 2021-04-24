<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Helpers;


class TelefoneController extends Controller
{
    
    
    public function index()
    {
        $Telefone = \App\Telefone::all();
        if(!$Telefone){
            return response(["response"=>"Não existe Telefone"],400);
        }
        return response(["dados"=>$Telefone]);
    }

    
    public function store(Request $request)
    {
        
        
        
        $Telefone = \App\Telefone::create($request->all());
        if(!$Telefone){
            return  response(["response"=>"Erro ao salvar Telefone"],400); 
        }
        return response(["response"=>"Salvo com sucesso",'dados'=>$Telefone]);
        
    }

    
    public function show($id)
    {
        $Telefone =\App\Telefone::find($id);
        if(!$Telefone){
            return response(["response"=>"Não existe Telefone"],400);
        }
        return response($Telefone);
    }

    
    public function update(Request $request, $id)
    {
        $Telefone =  \App\Telefone::find($id);
        
        if(!$Telefone){
            return response(['response'=>'Telefone Não encontrado'],400);
        }
        $Telefone = Helpers::processarColunasUpdate($Telefone,$request->all());
        
        if(!$Telefone->update()){
            return response(['response'=>'Erro ao alterar'],400);
        }
        return response(['response'=>'Atualizado com sucesso']);
      
    }
    

    public function destroy($id)
    {
        $Telefone =  \App\Telefone::find($id);
        
        if(!$Telefone){
            return response(['response'=>'Telefone Não encontrado'],400);
        }
        $Telefone->bo_ativo = false;
        if(!$Telefone->save()){
            return response(["response"=>"Erro ao deletar Telefone"],400);
        }
        return response(['response'=>'Telefone Inativado com sucesso']);
    }
}