<?php

namespace App;

use Helpers;
use Illuminate\Database\Eloquent\Model;

class Funcionario extends Model
{
    protected $table = 'pessoa.funcionario';
    protected $primaryKey = 'id_pessoa';
    protected $fillable = ['id_pessoa', 'id_empresa', 'id_cargo', 'id_statusfuncionario', 'dt_inicio', 'dt_final', 'created_at', 'updated_at', 'login', 'password', 'id_base', 'bo_mudar_senha'];

    public static function getEmployeeByEmailAndCpf($email, $cpf)
    {
        return self::join('pessoa.pessoa_email as pe', 'pessoa.funcionario.id_pessoa', '=', 'pe.id_pessoa')
            ->join('pessoa.email as e', 'e.id_email', '=', 'pe.id_email')
            ->join('pessoa.pessoa as p', 'p.id_pessoa', '=', 'pessoa.funcionario.id_pessoa')
            ->where('e.ee_email', $email)
            ->where('pessoa.funcionario.login', $cpf)
            ->first()
        ;
    }

    public static function getEmailsEmployeeById($id_pessoa)
    {
        return \App\Email::get();
    }

    public static function getEmployee($id_pessoa = null)
    {
        // select * from pessoa.telefone t ;
        // select * from pessoa.pessoa_telefone pt ;
        $emails = \App\PessoaEmail::getEmailsByIdPessoa($id_pessoa);
        $documents = \App\PessoaDocumento::getDocumentsByIdPessoa($id_pessoa);
        $addresses = \App\PessoaEndereco::getAddressesByIdPessoa($id_pessoa);
        $phoneNumbers = \App\PessoaTelefone::getPhoneNumbersByIdPessoa($id_pessoa);
        $avatar = \App\Pessoa::getAvatar($id_pessoa);

        $employee = self::join('pessoa.cargo as c', 'c.id_cargo', '=', 'pessoa.funcionario.id_cargo')
            ->join('pessoa.statusfuncionario as s', 's.id_statusfuncionario', '=', 'pessoa.funcionario.id_statusfuncionario')
            ->join('pessoa.pessoa as p', 'p.id_pessoa', '=', 'pessoa.funcionario.id_pessoa')
            ->join('pessoa.pessoa_telefone as tel_pt', 'tel_pt.id_pessoa', '=', 'p.id_pessoa')
            ->join('pessoa.telefone as tel_t', 'tel_t.id_telefone', '=', 'tel_pt.id_telefone')
            ->where('p.id_pessoa', $id_pessoa)
            ->select(
                'pessoa.funcionario.id_pessoa',
                'pessoa.funcionario.id_empresa',
                'pessoa.funcionario.id_cargo',
                'pessoa.funcionario.id_statusfuncionario',
                'pessoa.funcionario.dt_inicio',
                'pessoa.funcionario.dt_final',
                'pessoa.funcionario.created_at as func_created_at',
                'pessoa.funcionario.updated_at as func_updated_at',
                'pessoa.funcionario.login',
                'pessoa.funcionario.id_base',
                'pessoa.funcionario.bo_mudar_senha',
                'p.no_pessoa',
                'p.ds_usuario',
                'p.created_at as pessoa_created_at',
                'p.updated_at as pessoa_updated_at',
                'c.no_cargo',
                's.ds_statusfuncionario'
            )
            ->first()
            ;

        return ['employee' => $employee, 'avatar' => $avatar, 'emails' => $emails, 'documents' => $documents, 'addresses' => $addresses, 'phoneNumbers' => $phoneNumbers];
    }

    public static function getAllEmployeeActive()
    {
        return self::getAllEmployee(['pessoa.funcionario.id_statusfuncionario' => 1]);
    }

    public static function getAllEmployeeInactive()
    {
        return self::getAllEmployee(['pessoa.funcionario.id_statusfuncionario' => 2]);
    }

    public static function getAllEmployee($coluns = [])
    {
        $query = self::join('pessoa.cargo as c', 'c.id_cargo', '=', 'pessoa.funcionario.id_cargo')
            ->join('pessoa.statusfuncionario as s', 's.id_statusfuncionario', '=', 'pessoa.funcionario.id_statusfuncionario')
            ->join('pessoa.pessoa as p', 'p.id_pessoa', '=', 'pessoa.funcionario.id_pessoa')
            ->join('pessoa.pessoa_telefone as tel_pt', 'tel_pt.id_pessoa', '=', 'p.id_pessoa')
            ->join('pessoa.telefone as tel_t', 'tel_t.id_telefone', '=', 'tel_pt.id_telefone')
            ->select(
                'pessoa.funcionario.id_pessoa',
                'pessoa.funcionario.id_empresa',
                'pessoa.funcionario.id_cargo',
                'pessoa.funcionario.id_statusfuncionario',
                'pessoa.funcionario.dt_inicio',
                'pessoa.funcionario.dt_final',
                'pessoa.funcionario.created_at as func_created_at',
                'pessoa.funcionario.updated_at as func_updated_at',
                'pessoa.funcionario.login',
                'pessoa.funcionario.id_base',
                'pessoa.funcionario.bo_mudar_senha',
                'p.no_pessoa',
                'p.ds_usuario',
                'p.created_at as pessoa_created_at',
                'p.updated_at as pessoa_updated_at',
                'c.no_cargo',
                's.ds_statusfuncionario'
            )
        ;

        foreach ($coluns as $key => $value) {
            $query->where("{$key}", $value);
        }

        $whereJoin = ['p.no_pessoa','c.no_cargo'];
        $query = Filter::searchWhere($query, __CLASS__, $whereJoin);
        $query = Filter::orderBy($query);

        return Filter::paginate($query);
    }

    public static function updateEmployee($request, $id)
    {
        $funcionario = self::find($id);

        if (!$funcionario) {
            return response(['response' => 'Funcionario Não encontrado'], 400);
        }

        $funcionario = Helpers::processarColunasUpdate($funcionario, $request->all());

        if (!$funcionario->update()) {
            return response(['response' => 'Erro ao alterar'], 400);
        }

        return true;
    }
}
