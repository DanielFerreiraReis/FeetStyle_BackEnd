<?php
// ============================================================================
// BOOTSTRAP + SEGURANÇA
// ============================================================================
require_once __DIR__ . '/configs/bootstrap.php';
require_once __DIR__ . '/configs/security/jwt.php';
require_once __DIR__ . '/configs/security/api_key.php';

//este bloco de codigo normaliza tudo para lowerCase (as rotas devem ser passadas em LowerCase)

$method = $_SERVER['REQUEST_METHOD'];
$path   = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// normaliza para minúsculo
$path   = strtolower($path);
$base   = strtolower('/backendlojadesapatos/index.php');

// remove o prefixo base
if (strpos($path, $base) === 0) {
    $route = substr($path, strlen($base));
} else {
    $route = $path;
}

// remove barras extras
$route = rtrim($route, '/');


// ============================================================================
// ROTAS
// ============================================================================
switch ("$method $route") {

    // ------------------- PÚBLICAS -------------------
    case 'POST /login':
        require_once __DIR__ . '/api/login.php';
        break;
    
    //---------------------sub-rotas------------------- 
    case 'POST /login/verificar-funcionario':
        // validateJwt();
        checkApiKey();
        require_once __DIR__ . '/api/verifyFuncionario.php';
        break;

    case 'POST /login/cadastrar-login':
        // validateJwt();
        checkApiKey();
        require_once __DIR__ . '/api/createLogin.php';
        break;

    // ------------------- USER -------------------
    case 'GET /user/preferences':
        validateJwt();
        require_once __DIR__ . '/controllers/PreferencesController.php';
        Src\Controllers\PreferencesController::getPreferences();
        break;

    case 'PUT /user/preferences/theme':
        validateJwt();
        require_once __DIR__ . '/controllers/PreferencesController.php';
        Src\Controllers\PreferencesController::updateTheme();
        break;

    // ------------------- ADMIN -------------------
    case 'POST /admin/register':
        validateJwt();
        checkApiKey();
        require_once __DIR__ . '/api/cadastrarFuncionario.php';
        break;
    
    case 'POST /admin/cadastro':
        validateJwt();
        checkApiKey();
        require_once __DIR__ . '/api/cadastrarCliente.phpp';
        break;


    // case 'GET /admin/dashboard':
    //     validateJwt();
    //     checkApiKey();
    //     require_once __DIR__ . '/controllers/admin/dashboard.php';
    //     break;

    case 'POST /admin/cadastrarcalcado':
        validateJwt();
        checkApiKey();
        require_once __DIR__ . '/api/cadastrarCalcados.php';
        break;

    // Listagem de entidades (marca/tipo/modelo)
    case 'GET /admin/marca':
        validateJwt();
        checkApiKey();
        require_once __DIR__ . '/api/marca.php';
        break;

    case 'GET /admin/tipo':
        validateJwt();
        checkApiKey();
        require_once __DIR__ . '/api/tipo.php';
        break;

    case 'GET /admin/modelo':
        validateJwt();
        checkApiKey();
        require_once __DIR__ . '/api/modelo.php';
        break;

    // cadastro de entidades
    case 'POST /admin/marca':
        validateJwt();
        checkApiKey();
        require_once __DIR__ . '/api/marca.php';
        break;

    case 'POST /admin/tipo':
        validateJwt();
        checkApiKey();
        require_once __DIR__ . '/api/tipo.php';
        break;

    case 'POST /admin/modelo':
        validateJwt();
        checkApiKey();
        require_once __DIR__ . '/api/modelo.php';
        break;


    // ------------------- VENDEDOR -------------------
    // case 'GET /vendedor/vendas':
    //     validateJwt();
    //     checkApiKey();
    //     require_once __DIR__ . '/controllers/vendedor/vendas.php';
    //     break;

    // case 'POST /vendedor/finalizar-venda':
    //     validateJwt();
    //     checkApiKey();
    //     require_once __DIR__ . '/controllers/vendedor/finalizar_venda.php';
    //     break;    

    // ------------------- DEFAULT -------------------
    default:
        http_response_code(404);
        echo json_encode(['error' => 'Rota não encontrada']);
        break;
}
