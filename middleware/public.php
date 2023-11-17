<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpNotFoundException;

$staticPublic = function (Request $request, Response $response, array $args, $pathPrefix, $contentType): Response {
    $folder = $args['folder'] ?? "";
    $fileName = $args['fileName'] ?? "";
    $filePath = __DIR__ . "../../public/{$pathPrefix}/{$folder}/{$fileName}";

    if ($fileName == "") $filePath = trim(rtrim($filePath, '/'));

    if (file_exists($filePath)) {
        $response = $response->withHeader('Content-Type', $contentType);
        $response->getBody()->write(file_get_contents($filePath));
    } else {
        $response = $response->withStatus(404);
        $response->getBody()->write("File not found: {$filePath}");
    }

    return $response;
};

return [
    'html' => function (Request $request, Response $response, array $args) use ($staticPublic) {
        return $staticPublic($request, $response, $args, 'html', 'text/html; charset=UTF-8');
    },
    'css' => function (Request $request, Response $response, array $args) use ($staticPublic) {
        return $staticPublic($request, $response, $args, 'css', 'text/css; charset=UTF-8');
    },
    'js' => function (Request $request, Response $response, array $args) use ($staticPublic) {
        return $staticPublic($request, $response, $args, 'js', 'application/javascript; charset=UTF-8');
    },
    'upload' => function (Request $request, Response $response, array $args) {
        $folder = $args['folder'] ?? "";
        $fileName = $args['fileName'] ?? "";
        $filePath = __DIR__ . "../../public/upload/{$folder}/{$fileName}";

        if ($fileName == "") $filePath = trim(rtrim($filePath, '/'));

        if (file_exists($filePath)) {
            $fileExtension = pathinfo(!empty($fileName) ? $fileName : $folder, PATHINFO_EXTENSION);
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'txt'];
            if (!in_array($fileExtension, $allowedExtensions)) {
                throw new HttpNotFoundException($request, "File type not allowed: {$fileExtension}");
            }

            $fileContent = file_get_contents($filePath);
            $contentType = mime_content_type($filePath);
            $response = $response->withHeader('Content-Type', $contentType);
            $response->getBody()->write($fileContent);
            return $response;
        }

        throw new HttpNotFoundException($request, "File not found: {$filePath}");
    }
];
