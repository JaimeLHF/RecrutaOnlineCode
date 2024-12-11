<?php

//////////////////// Tabelas em MySQL ///////////////////////////


///Salvar Dados Empresas

add_action('rest_api_init', function () {
    register_rest_route('empresas_post/v1', '/submit', array(
        'methods' => 'POST',
        'callback' => 'process_form_data',
    ));
});

function process_form_data($request) {
    global $wpdb;

    try {
        $cnpj = sanitize_text_field($request['CNPJ']);
        $razao_social = sanitize_text_field($request['RazaoSocial']);
        $nome_fantasia = sanitize_text_field($request['NomeFantasia']);
        $email = sanitize_email($request['Email']);
        $telefone = sanitize_text_field($request['Telefone']);
        $whatsapp = sanitize_text_field($request['WhatsApp']);
        $cep = sanitize_text_field($request['CEP']);
        $pais = sanitize_text_field($request['País']);
        $uf = sanitize_text_field($request['UF']);
        $cidade = sanitize_text_field($request['Cidade']);
        $setor_atuacao = sanitize_text_field($request['SetorAtuação']);
        $descricao = sanitize_textarea_field($request['Descrição']);
		$situacao = sanitize_textarea_field($request['Situação']);

        $table_name = $wpdb->prefix . 'cadastros_empresas';

        $result = $wpdb->insert($table_name, array(
            'cnpj' => $cnpj,
            'razao_social' => $razao_social,
            'nome_fantasia' => $nome_fantasia,
            'email' => $email,
            'telefone' => $telefone,
            'whatsapp' => $whatsapp,
            'cep' => $cep,
            'pais' => $pais,
            'uf' => $uf,
            'cidade' => $cidade,
            'setor_atuacao' => $setor_atuacao,
            'descricao' => $descricao,
			'situacao' => $situacao,
            'created_at' => current_time('mysql')
        ));

        if ($result === false) {
            throw new Exception('Erro ao inserir os dados no banco de dados.');
        }

        return new WP_REST_Response(array(
            'success' => true,
            'message' => 'Empresa cadastrada com sucesso!'
        ), 200);

    } catch (Exception $e) {
        return new WP_REST_Response(array(
            'success' => false,
            'message' => 'Erro: ' . $e->getMessage()
        ), 500);
    }
}

function create_custom_table() {
    global $wpdb;

    $table_name = $wpdb->prefix . 'cadastros_empresas';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
        cnpj VARCHAR(255) NOT NULL,
        razao_social VARCHAR(255) NOT NULL,
        nome_fantasia VARCHAR(255) NOT NULL,
        telefone VARCHAR(255) NOT NULL,
        whatsapp VARCHAR(255) DEFAULT NULL,
        email VARCHAR(255) NOT NULL,
        cep VARCHAR(255) NOT NULL,
        pais VARCHAR(100) NOT NULL,
        uf CHAR(255) NOT NULL,
        cidade VARCHAR(255) NOT NULL,
        setor_atuacao VARCHAR(255) NOT NULL,
        descricao TEXT DEFAULT NULL,
		situacao VARCHAR(255) DEFAULT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL,
        PRIMARY KEY (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

add_action('after_setup_theme', 'create_custom_table');

function display_empresas() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'cadastros_empresas';

    $results = $wpdb->get_results("SELECT * FROM $table_name");

    // Define o estilo para a classe CSS
    $output = '<style>
        .contratando {            
            color: green;
        }
    </style>';

    // Gera a tabela com os dados
    $output .= '<table>';
    $output .= '<tr>
                    <th>ID</th>
                    <th>CNPJ</th>
                    <th>RazaoSocial</th>
                    <th>Município</th>
                    <th>Contato</th>
                    <th>Setor de Atuação</th>
                    <th>Situação</th>
                </tr>';
    foreach ($results as $row) {
        // Adiciona a classe CSS condicionalmente
        $class = ($row->situacao === 'CONTRATANDO') ? 'contratando' : '';

        $output .= "<tr>
                        <td>{$row->id}</td>
                        <td>{$row->cnpj}</td>
                        <td>{$row->razao_social}</td>
                        <td>{$row->cidade}</td>
                        <td>{$row->whatsapp}</td>
                        <td>{$row->setor_atuacao}</td>
                        <td class='$class'>{$row->situacao}</td>
                    </tr>";
    }
    $output .= '</table>';

    return $output;
}

function display_empresas_mobile() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'cadastros_empresas';

    $results = $wpdb->get_results("SELECT * FROM $table_name");

    // Define o estilo para a classe CSS
    $output = '<style>
        .contratando {            
            color: green;
        }
    </style>';

    // Gera a tabela com os dados
    $output .= '<table>';
    $output .= '<tr>
                    <th>RazaoSocial</th>
                    <th>Situação</th>
                </tr>';
    foreach ($results as $row) {
        // Adiciona a classe CSS condicionalmente
        $class = ($row->situacao === 'CONTRATANDO') ? 'contratando' : '';

        $output .= "<tr>
                        <td>{$row->razao_social}</td>
                        <td class='$class'>{$row->situacao}</td>
                    </tr>";
    }
    $output .= '</table>';

    return $output;
}


add_shortcode('listagem_empresas', 'display_empresas');
add_shortcode('listagem_empresas_mobile', 'display_empresas_mobile');



// Salvar Novo Profissional

add_action('rest_api_init', function () {
    register_rest_route('profissionais_post/v1', '/submit', array(
        'methods' => 'POST',
        'callback' => 'cadastro_profissionais',
    ));
});

function cadastro_profissionais($request)
{
    global $wpdb;

    $nome = sanitize_text_field($request['Nome']);
    $cpf = sanitize_text_field($request['CPF']);
    $rg = sanitize_text_field($request['RG']);
    $data_nascimento = sanitize_text_field($request['DataNascimento']);
    $telefone = sanitize_text_field($request['Telefone']);
    $whatsapp = sanitize_text_field($request['WhatsApp']);
    $email = sanitize_email($request['Email']);
    $cep = sanitize_text_field($request['CEP']);
    $pais = sanitize_text_field($request['Pais']);
    $uf = sanitize_text_field($request['UF']);
    $cidade = sanitize_text_field($request['Município']);
    $setor_atuacao = sanitize_text_field($request['SetorAtuação']);
    $descricao = sanitize_textarea_field($request['Descrição']);
    $curso = sanitize_text_field($request['Curso']);
    $instituicao = sanitize_text_field($request['Instituição']);
    $data_inicio = sanitize_text_field($request['DataÍnicio']);
    $data_final = sanitize_text_field($request['DataFinal']);
    $experiencias = sanitize_textarea_field($request['Experiencia']);

    $table_name = $wpdb->prefix . 'cadastros_profissionais';

    // Inserir os dados na tabela personalizada
    $result = $wpdb->insert($table_name, array(
        'nome' => $nome,
        'cpf' => $cpf,
        'rg' => $rg,
        'data_nascimento' => $data_nascimento,
        'telefone' => $telefone,
        'whatsapp' => $whatsapp,
        'email' => $email,
        'cep' => $cep,
        'pais' => $pais,
        'uf' => $uf,
        'cidade' => $cidade,
        'setor_atuacao' => $setor_atuacao,
        'descricao' => $descricao,
        'curso' => $curso,
        'instituicao' => $instituicao,
        'dataInicio' => $data_inicio,
        'dataFinal' => $data_final,
        'experiencias' => $experiencias,
        'created_at' => current_time('mysql')
    ));

    if ($result) {
        return new WP_REST_Response('Profissional cadastrado com sucesso!', 200);
    } else {
        return new WP_REST_Response('Erro ao cadastrar profissional.', 500);
    }
}

// Função para criar a tabela
function create_custom_table_profissionais()
{
    global $wpdb;

    $table_name = $wpdb->prefix . 'cadastros_profissionais'; // Nome da tabela
    $charset_collate = $wpdb->get_charset_collate();

    // SQL para criar a tabela
    $sql = "CREATE TABLE $table_name (
        id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
        nome VARCHAR(255),
        cpf VARCHAR(14),
        rg VARCHAR(20),
        data_nascimento DATE,
        telefone VARCHAR(15),
        whatsapp VARCHAR(15) DEFAULT NULL,
        email VARCHAR(255),
        cep VARCHAR(9),
        pais VARCHAR(100),
        uf CHAR(2),
        cidade VARCHAR(255),
        setor_atuacao VARCHAR(255),
        descricao TEXT DEFAULT NULL,
        curso VARCHAR(255),
        instituicao VARCHAR(255),
        dataInicio DATE,
        dataFinal DATE,
        experiencias TEXT DEFAULT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id)
    ) $charset_collate;";

    // Garante que a tabela será criada ou atualizada corretamente
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

add_action('after_setup_theme', 'create_custom_table_profissionais');


function display_profissionais() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'cadastros_profissionais';

    $results = $wpdb->get_results("SELECT * FROM $table_name");

    $output = '<table>';
    $output .= '<tr><th>Nome Completo</th><th>Área de Atuação</th><th>Município</th><th>Ações</th></tr>';
    foreach ($results as $row) {
        $output .= "<tr><td>{$row->nome}</td><td>{$row->setor_atuacao}</td><td>{$row->cidade}</td><td><button>Detalhes</button></td></tr>";
    }
    $output .= '</table>';

    return $output;
}

function display_profissionais_mobile() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'cadastros_profissionais';

    $results = $wpdb->get_results("SELECT * FROM $table_name");

    $output = '<table>';
    $output .= '<tr><th>Nome Completo</th><th>Área de Atuação</th></tr>';
    foreach ($results as $row) {
        $output .= "<tr><td>{$row->nome}</td><td>{$row->setor_atuacao}</td></tr>";
    }
    $output .= '</table>';

    return $output;
}

add_shortcode('listagem_profissionais_mobile', 'display_profissionais_mobile');
add_shortcode('listagem_profissionais', 'display_profissionais');



///Salvar Nova Oportunidade

add_action('rest_api_init', function () {
    register_rest_route('oportunidade_new/v1', '/submit', array(
        'methods' => 'POST',
        'callback' => 'oportunidade_post',
        'permission_callback' => '__return_true',
    ));
});

function oportunidade_post($request) {
    global $wpdb;
        $cargo = sanitize_text_field($request['Cargo']);
		$empresa = sanitize_text_field($request['Empresa']);
		$descricao = sanitize_text_field($request['Descricao']);

        $table_name = $wpdb->prefix . 'cadastros_oportunidades';

        $result = $wpdb->insert($table_name, array(
            'cargo' => $cargo,
			'empresa' => $empresa,
			'descricao' => $descricao,
            'created_at' => current_time('mysql')
        ));

        return new WP_REST_Response(array(
            'success' => true,
            'message' => 'Oportunidade cadastrada com sucesso!'
        ), 200);

}

function create_custom_table_oportunidades() {
    global $wpdb;

    $table_name = $wpdb->prefix . 'cadastros_oportunidades';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
        cargo VARCHAR(255) NOT NULL,
		empresa VARCHAR(255) NOT NULL,
		descricao VARCHAR(255) NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL,
        PRIMARY KEY (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

add_action('after_setup_theme', 'create_custom_table_oportunidades');

function display_oportunidades() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'cadastros_oportunidades';

    $results = $wpdb->get_results("SELECT * FROM $table_name");

    $output = '<table>';
    $output .= '<tr><th>ID</th><th>Cargo</th><th>Empresa</th><th>Ações</th></tr>';
    foreach ($results as $row) {
        $output .= "<tr><td>{$row->id}</td><td>{$row->cargo}</td><td>{$row->empresa}</td><td><button>Detalhes</button></td></tr>";
    }
    $output .= '</table>';

    return $output;
}

add_shortcode('listagem_oportunidades', 'display_oportunidades');
