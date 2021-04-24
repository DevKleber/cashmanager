<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PessoaEmail extends Model
{
    protected $table = 'pessoa.pessoa_email as pe';
    protected $primaryKey = 'id_pessoaemail';
    protected $fillable = ['id_pessoaemail', 'id_pessoa', 'id_email', 'created_at', 'updated_at'];

    public static function getEmailsByIdPessoa($id_pessoa)
    {
        return self::where('id_pessoa', $id_pessoa)
            ->join('pessoa.email as e', 'e.id_email', '=', 'pe.id_email')
            ->join('pessoa.tipo_email as te', 'te.id_tipoemail', '=', 'e.id_tipoemail')
            ->select('pe.*', 'e.ee_email', 'te.id_tipoemail', 'te.ds_tipoemail')
            ->get()
        ;
    }

    public static function insertEmailsByIdPerson($request, $id_pessoa)
    {
        foreach ($request as $key => $value) {
            $value['id_pessoa'] = $id_pessoa;
            $value['email']['id_pessoa'] = $value['id_pessoa'];
            $value['email']['id_tipoemail'] = $value['id_tipoemail'];
            $value['email']['ee_email'] = $value['ee_email'];
            $email = \App\Email::create($value['email']);
            $value['emailPerson']['id_pessoa'] = $value['id_pessoa'];
            $value['emailPerson']['id_email'] = $email->id_email;
            $emailPeson = self::create($value['emailPerson']);
            if (!$emailPeson || !$email) {
                return  response(['response' => 'Erro ao salvar ProdutoCardapio'], 400);
            }
        }

        return true;
    }

    public static function updateEmailsByidPerson($request, $id_pessoa)
    {
        $emailsPerson = self::where('id_pessoa', $id_pessoa)->get();
        foreach ($emailsPerson as $key => $value) {
            self::where('id_pessoaemail', $value['id_pessoaemail'])->delete();
            \App\Email::where('id_email', $value['id_email'])->delete();
        }

        return self::insertEmailsByIdPerson($request->emails, $id_pessoa);
    }
}
