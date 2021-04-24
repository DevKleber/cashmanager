<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@100;300;400;500;700;900&display=swap" rel="stylesheet">
<style>
    * {
        /* width: 100vw; */
        font-family: 'Roboto', sans-serif;
    }

    .text-center {
        text-align: center;
    }

    .etapa {
        width: 20%;
        text-align: center;
        border: 1px solid black;
        position: absolute;
        top: 0;
        right: 0;
        font-weight: 900;
    }

    .title {
        text-align: center;
        font-size: 1.4em;
        font-weight: 500;
    }

    .float-left {
        float: left;
    }

    .clearLeft {
        clear: left;
    }

    .produtor {
        font-size: 0.8em;
    }

    .nomeProdutor span {
        font-weight: 500;
    }

    .nomeProdutor {
        width: 75% !important;
    }

    .apelido span {
        font-weight: 500;
    }

    .apelido {
        width: 25% !important;
    }

    .propriedade span {
        font-weight: 500;
    }

    .propriedade {
        width: 70% !important;
    }

    .municipio span {
        font-weight: 500;
    }

    .municipio {
        width: 30% !important;
    }

    .ie span {
        font-weight: 500;
    }

    .ie {
        width: 33.33% !important;
    }

    .cpf span {
        font-weight: 500;
    }

    .cpf {
        width: 33.33% !important;
    }

    .fone span {
        font-weight: 500;
    }

    .fone {
        width: 33.33% !important;
    }

    .endereco span {
        font-weight: 500;
    }

    .endereco {
        width: 75% !important;
    }

    .cep span {
        font-weight: 500;
    }

    .cep {
        width: 25% !important;
    }

    .nota span {
        font-weight: 500;
    }

    .nota {
        width: 35% !important;
    }

    .revendedor span {
        font-weight: 500;
    }

    .revendedor {
        width: 65% !important;
    }

    .morte {
        margin-top: 15px;
    }

    .morte span.description {
        font-weight: 300;
        font-size: 0.9em;
    }

    .antiaftosa h1 {
        font-size: 1.3em;
    }

    .antiaftosa span.description {
        font-size: 0.65em;
    }

    .tableMorte {
        width: 100%;
        font-size: 0.7em;
    }

    .ro1 {
        height: 12.81pt;
    }

    .ca {
        width: 60px;
    }
</style>

<div class="" style="width: 100%; text-align: center;">
    <img src="https://www.agrodefesa.go.gov.br/images/FotosNoticias/Fevereiro2020/Fotologogeral.PNG"
        alt="logoAgrodefesa" width="200px">
</div>
<div class="etapa">
    ETAPA <br>NOVEMBRO
</div>

<h1 class="title">
    DECLARAÇÃO DE VACINAÇÃO CONTRA FEBRE AFTOSA E RAIVA
</h1>

<div class="produtor">

    <div class="clearLeft">
        <div class="float-left nomeProdutor">Nome do Produtor: <span>{{$empresa['no_empresa']}}</span> </div>
        <div class="float-left apelido">Apelido.: ____________</div><br>
    </div>

    <div class="clearLeft">

        <div class="float-left propriedade">Propriedade.: <span>{{$propriedade['no_propriedade']}}</span> </div>
        <div class="float-left municipio">Município.: <span>{{$propriedade['no_municipio']}} /
                {{$propriedade['sg_estado']}}</span> </div>
    </div>
    <div class="clearLeft">

        <div class="float-left ie">Insc. Est.: <span>{{$propriedade['nu_inscricaoestadual']}}</span></div>
        <div class="float-left cpf">CPF.: <span>{{$empresa['nu_cpfcnpj']}}</span></div>
        <div class="float-left fone">Fone.: <span>{{$propriedade['nu_telefone']}}</span></div>
    </div>
    <div class="clearLeft">

        <div class="float-left endereco">Endereço para contato.: <span>{{$propriedade['ds_roteiroacesso']}}</span>
        </div>
        <div class="float-left cep">CEP.: <span></span> </div>
    </div>
    <div class="clearLeft">

        <div class="float-left nota">Nº Nota Fiscal.: <span>______________________</span> </div>
        <div class="float-left revendedor">Revendedor.: <span>______________________</span> </div>
    </div>
</div>

<div class="morte">
    <span class="description">
        Se morreram animais na propriedade, nos últimos 06 (seis) meses, preencha o quadro abaixo:
    </span>

    <table border="1" cellspacing="0" cellpadding="0" class="tableMorte">
        <tr class="ro1">
            <td rowspan="2" class="text-center ca">
                Especie
            </td>
            <td colspan="2" class="text-center ca">
                0-12m
            </td>
            <td colspan="2" class="text-center ca">
                13-24m
            </td>
            <td colspan="2" class="text-center ca">
                25-36m
            </td>
            <td colspan="2" class="text-center ca">
                + de 36m
            </td>
            <td colspan="2" class="text-center ca">
                Total
            </td>
            <td rowspan="2" class="text-center">
                Causa da morte
            </td>
        </tr>
        <tr class="ro1">
            <td class="text-center">
                M
            </td>
            <td class="text-center">
                F
            </td>
            <td class="text-center">
                M
            </td>
            <td class="text-center">
                F
            </td>
            <td class="text-center">
                M
            </td>
            <td class="text-center">
                F
            </td>
            <td class="text-center">
                M
            </td>
            <td class="text-center">
                F
            </td>
            <td class="text-center">
                M
            </td>
            <td class="text-center">
                F
            </td>
        </tr>
        <tr class="ro1">
            <td class="text-center">
                Bovina
            </td>
            <td class="text-center"></td>
            <td class="text-center"></td>
            <td class="text-center"></td>
            <td class="text-center"></td>
            <td class="text-center"></td>
            <td class="text-center"></td>
            <td class="text-center"></td>
            <td class="text-center"></td>
            <td class="text-center"></td>
            <td class="text-center"></td>
            <td class="text-center"></td>
        </tr>
        <tr class="ro1">
            <td class="text-center">
                Bubalina
            </td>
            <td class="text-center"></td>
            <td class="text-center"></td>
            <td class="text-center"></td>
            <td class="text-center"></td>
            <td class="text-center"></td>
            <td class="text-center"></td>
            <td class="text-center"></td>
            <td class="text-center"></td>
            <td class="text-center"></td>
            <td class="text-center"></td>
            <td class="text-center"></td>
        </tr>
    </table>
</div>

<div class="antiaftosa">
    <h1 class="text-center">VACINAÇÃO ANTIAFTOSA</h1>
    <span class="description" style="margin-left: 35px;">
        <b>DECLARO</b> a veracidade quanto aos animais discriminados abaixo e que os bovinos e bubalinos citados na
        linha de
        <b>Vacinados</b>
        foram imunizados contra Febre Aftosa no dia ____ de _________ de 20______ .N° partida _________ Venc. _________
        Laboratório _________ N° Doses _________
    </span>
</div>
