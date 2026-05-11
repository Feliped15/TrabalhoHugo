<?php
require_once './vendor/autoload.php';

use Slim\Factory\AppFactory;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Model\Jogos;
use Ramsey\Uuid\Uuid;

$app = AppFactory::create();

$scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
$basePath = dirname($scriptName);
if ($basePath !== '/' && $basePath !== '\\' && $basePath !== '.') {
    $app->setBasePath($basePath);
}

$app->get('/', function (Request $request, Response $response) {
    $response->getBody()->write(json_encode([
        'message' => 'API is running',
        'routes' => ['/login', '/jogos', '/jogos/{id}', '/jogos (POST)', '/jogos/{id} (PUT)', '/jogos/{id} (DELETE)']
    ]));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/login', function (Request $request, Response $response, $args) {
    $data = json_decode($request->getBody()->getContents(), true);
    if (!$data || !isset($data['email']) || !isset($data['password'])) {
        $response->getBody()->write(json_encode(['error' => 'Dados inválidos. Certifique-se de fornecer email, password.']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
    }

    if($data['email']== "" || $data['email'] == null) {
        $response->getBody()->write(json_encode(['error' => 'Email não pode ser vazio']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
    }

    if($data['password']== "" || $data['password'] == null) {
        $response->getBody()->write(json_encode(['error' => 'Password não pode ser vazio']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
    }

    if($data['email'] === "usuario@esoft.com" && $data['password'] === "Abc123") {
        $response->getBody()->write(json_encode(['token' => Uuid::uuid4()->toString()]));
        return $response->withHeader('Content-Type', 'application/json');
    } else {
        $response->getBody()->write(json_encode(['error' => 'Email ou Password incorretos']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
    }

});

$app->get('/jogos', function (Request $request, Response $response, $args) {
    $jogos = new Jogos();
    $data = $jogos->find()->fetch(true);

    $jogos= null;

    if ($data === null) {
        $response->getBody()->write(json_encode([]));
        return $response->withHeader('Content-Type', 'application/json');
    }

    foreach ($data as $item) {
        $jogo = [
            "id" => $item->id,
            "nome" => $item->nome,
            "tipo" => $item->tipo,
            "nota" => $item->nota,
            "review" => $item->review
        ];
        $jogos[] = $jogo;
    }

    $response->getBody()->write(json_encode($jogos));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/jogos/{id}', function (Request $request, Response $response, $args) {
    $id = (int)$args['id'];
    
    $jogos = new Jogos();
    $data = $jogos->findById($id);

    if((!$data) || ($data->id !== $id)) {
        $error = ["error" => "Jogo não encontrado"];
        $response->getBody()->write(json_encode($error));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
    }

    $jogo = [
        "id" => $data->id,
        "nome" => $data->nome,
        "tipo" => $data->tipo,
        "nota" => $data->nota,
        "review" => $data->review
    ];

    $response->getBody()->write(json_encode($jogo));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/jogos', function (Request $request, Response $response, $args) {
    $data = json_decode($request->getBody()->getContents(), true);
    if (!$data || !isset($data['nome']) || !isset($data['tipo']) || !isset($data['nota']) || !isset($data['review']) ) {
        $response->getBody()->write(json_encode(['error' => 'Dados inválidos. Certifique-se de fornecer nome, tipo, nota e review.']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
    }

    if($data['nome']== "" || $data['nome'] == null) {
        $response->getBody()->write(json_encode(['error' => 'Nome não pode ser vazio']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
    }

    if($data['tipo']== "" || $data['tipo'] == null) {
        $response->getBody()->write(json_encode(['error' => 'Tipo não pode ser vazio']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
    }

    if($data['nota']== "" || $data['nota'] == null) {
        $response->getBody()->write(json_encode(['error' => 'Nota não pode ser vazio']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
    }

    if($data['review']== "" || $data['review'] == null) {
        $response->getBody()->write(json_encode(['error' => 'Review não pode ser vazio']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
    }

    $jogos = new Jogos();
    
    $jogos->nome = $data['nome'];
    $jogos->tipo = $data['tipo'];
    $jogos->nota = $data['nota'];
    $jogos->review = $data['review'];

    $jogosId = $jogos->save();

    $jogo = [
        "id" => $jogos->id,
        "nome" => $jogos->nome,
        "tipo" => $jogos->tipo,
        "nota" => $jogos->nota,
        "review" => $jogos->review
    ];

    $response->getBody()->write(json_encode($jogo));    
    return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
});

$app->put('/jogos/{id}', function (Request $request, Response $response, $args) {
    $id = (int)$args['id'];

    $data = json_decode($request->getBody()->getContents(), true);
    if (!$data || !isset($data['nome']) || !isset($data['tipo']) || !isset($data['nota']) || !isset($data['review']) ) {
        $response->getBody()->write(json_encode(['error' => 'Dados inválidos. Certifique-se de fornecer nome, tipo, nota e review.']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
    }

    if($data['nome']== "" || $data['nome'] == null) {
        $response->getBody()->write(json_encode(['error' => 'Nome não pode ser vazio']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
    }

    if($data['tipo']== "" || $data['tipo'] == null) {
        $response->getBody()->write(json_encode(['error' => 'Tipo não pode ser vazio']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
    }

    if($data['nota']== "" || $data['nota'] == null) {
        $response->getBody()->write(json_encode(['error' => 'Nota não pode ser vazio']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
    }

    if($data['review']== "" || $data['review'] == null) {
        $response->getBody()->write(json_encode(['error' => 'Review não pode ser vazio']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
    }
    
    $dataBD = (new Jogos())->findById($id);

    if((!$data) || ($dataBD->id !== $id)) {
        $error = ["error" => "Jogo não encontrado"];
        $response->getBody()->write(json_encode($error));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
    }

    $dataBD->nome = $data['nome'];
    $dataBD->tipo = $data['tipo'];
    $dataBD->nota = $data['nota'];
    $dataBD->review = $data['review'];

    $jogosId = $dataBD->save();

    $jogo = [
        "id" => $dataBD->id,
        "nome" => $dataBD->nome,
        "tipo" => $dataBD->tipo,
        "nota" => $dataBD->nota,
        "review" => $dataBD->review
    ];

    $response->getBody()->write(json_encode($jogo));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->delete('/jogos/{id}', function (Request $request, Response $response, $args) {
    $id = (int)$args['id'];
    
    $jogos = new Jogos();
    $data = $jogos->findById($id);

    if((!$data) || ($data->id !== $id)) {
        $error = ["error" => "Jogo não encontrado"];
        $response->getBody()->write(json_encode($error));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
    }

    $data->destroy();

    $response->getBody()->write("");
    return $response->withHeader('Content-Type', 'application/json')->withStatus(204);
});


$app->run();
?>
