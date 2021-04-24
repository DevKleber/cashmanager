<?php

namespace App\Http\Controllers;

use Helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Validator;

class FuncionarioController extends Controller
{
    public function index()
    {
        $funcionario = \App\Funcionario::getAllEmployee();
        if (!$funcionario) {
            return response(['response' => 'Não existe Funcionario'], 400);
        }

        return response(['dados' => $funcionario]);
    }

    public function getAllEmployeeActive()
    {
        $funcionario = \App\Funcionario::getAllEmployeeActive();
        if (!$funcionario) {
            return response(['response' => 'Não existe Funcionario'], 400);
        }

        return response(['dados' => $funcionario]);
    }

    public function getAllEmployeeInactive()
    {
        $funcionario = \App\Funcionario::getAllEmployeeInactive();
        if (!$funcionario) {
            return response(['response' => 'Não existe Funcionario'], 400);
        }

        return response(['dados' => $funcionario]);
    }

    public function store(Request $request)
    {
        $cpf = \App\Documento::getCpfInArray($request->documents);
        $cpfFormated = Helpers::removerCaracteresEspeciaisEspacos($cpf);

        if (null == $cpfFormated) {
            return  response(['response' => 'CPF não pode ser nulo'], 400);
        }

        if (!Helpers::validarCpf($cpfFormated)) {
            return  response(['response' => 'CPF inválido'], 400);
        }

        if (\App\Documento::cpfExists($cpfFormated)) {
            return  response(['response' => 'CPF informado já consta em nossa base de dados'], 400);
        }

        \DB::beginTransaction();

        // $upload = $this->uploadFile($request);
        // if (isset($upload['error'])) {
        //     \DB::rollBack();

        //     return $upload['error'];
        // }

        // $request['id_arquivo'] = $upload['file']->id_arquivo ?? null;

        $insertPerson = \App\Pessoa::insertPerson($request);
        if (!$insertPerson->id_pessoa) {
            \DB::rollBack();

            return $insertPerson;
        }
        $password = Str::random(8);
        $request['id_empresa'] = auth()->user()->id_empresa;
        $request['id_statusfuncionario'] = 1;
        $request['login'] = $cpfFormated;
        $request['password'] = \Hash::make(($password));
        $request['password_decript'] = $password;
        $request['id_pessoa'] = $insertPerson->id_pessoa;

        $funcionario = \App\Funcionario::create($request->all());
        if (!$funcionario) {
            return  response(['response' => 'Erro ao salvar Funcionario'], 400);
        }

        $insertEmails = \App\PessoaEmail::insertEmailsByidPerson($request->emails, $insertPerson->id_pessoa);
        if (true !== $insertEmails) {
            \DB::rollBack();

            return $insertEmails;
        }
        $insertDocuments = \App\PessoaDocumento::insertDocumentsByidPerson($request->documents, $insertPerson->id_pessoa);
        if (true !== $insertDocuments) {
            \DB::rollBack();

            return $insertDocuments;
        }

        $insertAddresses = \App\PessoaEndereco::insertAddressesByidPerson($request->addresses, $insertPerson->id_pessoa);
        if (true !== $insertAddresses) {
            \DB::rollBack();

            return $insertAddresses;
        }
        $insertPhoneNumbers = \App\PessoaTelefone::insertPhoneNumbersByidPerson($request->phoneNumbers, $insertPerson->id_pessoa);
        if (true !== $insertPhoneNumbers) {
            \DB::rollBack();

            return $insertPhoneNumbers;
        }

        \DB::commit();
        \App\Email::sendEmailNewUser($request);

        return response(['response' => 'Salvo com sucesso', 'dados' => $funcionario]);
    }

    public function show($id)
    {
        $funcionario = \App\Funcionario::getEmployee($id);
        if (!$funcionario) {
            return response(['response' => 'Não existe Funcionario'], 400);
        }

        return response($funcionario);
    }

    public function update(Request $request, $id)
    {
        $cpf = \App\Documento::getCpfInArray($request->documents);
        $cpfFormated = Helpers::removerCaracteresEspeciaisEspacos($cpf);
        if (null == $cpfFormated) {
            return  response(['response' => 'CPF não pode ser nulo'], 400);
        }

        if (!Helpers::validarCpf($cpfFormated)) {
            return  response(['response' => 'CPF inválido'], 400);
        }
        if (\App\Documento::cpfExists($cpfFormated, $id)) {
            return  response(['response' => 'CPF informado já consta em nossa base de dados'], 400);
        }

        \DB::beginTransaction();

        $request['id_empresa'] = auth()->user()->id_empresa;
        $request['login'] = $cpfFormated;

        $updateEmployee = \App\Funcionario::updateEmployee($request, $id);
        if (true !== $updateEmployee) {
            \DB::rollBack();

            return $updateEmployee;
        }
        $updatePerson = \App\Pessoa::updatePerson($request, $id);
        if (true !== $updatePerson) {
            \DB::rollBack();

            return $updateEmployee;
        }

        if (!empty($request->emails)) {
            $updateEmails = \App\PessoaEmail::updateEmailsByidPerson($request, $id);
            if (true !== $updateEmails) {
                \DB::rollBack();

                return $updateEmployee;
            }
        }
        if (!empty($request->documents)) {
            $updateDocuments = \App\PessoaDocumento::updateDocumentsByidPerson($request, $id);
            if (true !== $updateDocuments) {
                \DB::rollBack();

                return $updateDocuments;
            }
        }
        if (!empty($request->addresses)) {
            $updateAddresses = \App\PessoaEndereco::updateAddressesByidPerson($request, $id);
            if (true !== $updateAddresses) {
                \DB::rollBack();

                return $updateAddresses;
            }
        }
        if (!empty($request->phoneNumbers)) {
            $updatePhoneNumbers = \App\PessoaTelefone::updatePhoneNumbersByidPerson($request, $id);
            if (true !== $updatePhoneNumbers) {
                \DB::rollBack();

                return $updatePhoneNumbers;
            }
        }
        // \DB::rollBack();
        \DB::commit();

        return response(['response' => 'Atualizado com sucesso']);
    }

    public function destroy($id)
    {
        $funcionario = \App\Funcionario::find($id);

        if (!$funcionario) {
            return response(['response' => 'Funcionario Não encontrado'], 400);
        }
        $funcionario->id_statusfuncionario = 2;
        if (!$funcionario->save()) {
            return response(['response' => 'Erro ao deletar Funcionario'], 400);
        }

        return response(['response' => 'Funcionário inativado com sucesso']);
    }

    public function uploadFile(Request $request)
    {
        if ($request->hasFile('profileImg')) {
            $validator = Validator::make($request->all(), ['profileImg' => 'mimes:jpeg,png,jpg,gif,svg,webp']);
            $res['id_arquivo'] = null;
            if ($validator->fails()) {
                $res['error'] = response(['response' => 'A imagem precisa ser do tipo: jpeg, png, jpg, gif, svg, webp'], 400);

                return $res;
            }

            $insertFile = \App\Arquivo::saveFile($request->file('profileImg'), 'profile');
            if (!$insertFile) {
                $res['eror'] = 'Erro ao enviar a imagem';

                return $res;
            }

            $res['file'] = $insertFile;

            return $insertFile;

            return $res;
        }
    }

    public function alterAvatarProfile(Request $request, $id)
    {
        if ($request->hasFile('profileImg')) {
            $validator = Validator::make($request->all(), ['profileImg' => 'mimes:jpeg,png,jpg,gif,svg,webp']);
            $res['id_arquivo'] = null;
            if ($validator->fails()) {
                $res['error'] = response(['response' => 'A imagem precisa ser do tipo: jpeg, png, jpg, gif, svg, webp'], 400);

                return $res;
            }
            \DB::beginTransaction();
            $employee = \App\Pessoa::where('id_pessoa', $id)->first();
            \App\Arquivo::deleteFileById($employee->id_arquivo);
            $insertFile = \App\Arquivo::saveFile($request->file('profileImg'), 'profile');
            if (!$insertFile) {
                \DB::rollBack();

                return  response(['response' => 'Erro ao enviar a imagem'], 400);
            }
            \DB::commit();
            // $request['id_arquivo'] = $insertFile->id_arquivo;
            return $insertFile;
        }
    }
}
