<?php

// Définir le type de contenu comme JSON
header('Content-Type: application/json');

// CORS: Autoriser l'origine http://localhost:5175 (le frontend) à accéder à l'API
// Cette ligne est essentielle pour que les requêtes venant du frontend React puissent accéder à l'API PHP
header("Access-Control-Allow-Origin: http://localhost:5175"); // Ou précisez http://localhost:5175 pour plus de sécurité

// CORS: Spécifier les méthodes HTTP autorisées (GET, POST, PUT, DELETE, OPTIONS)
// Cela permet d'accepter certaines actions sur l'API, comme récupérer des données ou les envoyer.
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");

// CORS: Spécifier les en-têtes autorisés dans la requête HTTP
// Par exemple, le frontend peut envoyer un en-tête Content-Type pour indiquer le type des données (JSON)
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Si la méthode HTTP est OPTIONS (pré-flight request), répondre directement avec un code 200 OK
// Cette requête OPTIONS est envoyée par le navigateur avant toute autre demande HTTP pour vérifier les permissions CORS.
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200); // Répondre avec un code 200 OK
    exit(); // Terminer l'exécution de l'API pour cette requête OPTIONS
}

/**
 * Un exemple d'API en PHP définition des endpoints 
 */

// Paramètres de connexion à la base de données MySQL
$host = 'db'; // Nom d'hôte de la base de données défini dans le docker-compose (nom du service)
$dbname = 'db'; // Nom de la base de données
$username = 'root'; // Utilisateur de la base de données
$password = 'admin'; // Mot de passe de la base de données

// Connexion à la base de données via PDO
// Si la connexion échoue, on affiche un message d'erreur en format JSON
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Activer les erreurs détaillées
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // Mode de récupération des résultats
    ]);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]); // Retourner un message d'erreur en JSON
    exit; // Terminer l'exécution du script
}

// Récupérer la méthode HTTP utilisée (GET, POST, etc.) et l'URI de la requête
$method = $_SERVER['REQUEST_METHOD']; // méthode HTTP (GET, POST, ...)
$path = trim($_SERVER['REQUEST_URI'], '/'); // URI de la requête sans le '/' au début

// Endpoint pour récupérer tous les utilisateurs (GET /users)
if ($method === 'GET' && $path === 'users') {
    // Ici, vous pouvez ajouter une requête pour récupérer les utilisateurs depuis la base de données
    // Exemple: $stmt = $pdo->query("SELECT * FROM users");
    // $users = $stmt->fetchAll();
    echo json_encode(['users' => '', 'path' => $path]); // Réponse JSON vide pour cet exemple
    exit;
}

// Endpoint pour récupérer les messages (GET /messages)
if ($method === 'GET' && $path === 'messages') {
    // Exemple de réponse simulée avec des messages
    echo json_encode([
        'status' => 'success',
        'message' => [
            [
                'id' => 1,
                'content' => 'Je cherche un musicien qui fait de la guitarre',
                'user_id' => 6
            ],
            [
                'id' => 2,
                'content' => 'Je cherche un musicien qui fait de la batterie',
                'user_id' => 7
            ],
        ]
    ]);
    exit;
}

// Endpoint pour ajouter un message (POST /message)
if ($method === 'POST' && $path === 'message') {
    // Récupérer les données JSON envoyées dans le corps de la requête
    // Ici, on attend que le frontend envoie un JSON avec un message et un user_id
    $data = json_decode(file_get_contents("php://input"), true); // Récupérer les données en JSON
    // Insérer le message dans la base de données
    $stmt = $pdo->prepare("INSERT INTO messages (content, user_id) VALUES (:content, :user_id)");
    $stmt->execute(['content' => $data['content'], 'user_id' => $data['user_id']]);

    // Répondre avec un message JSON confirmant l'ajout du message
    echo json_encode(['status' => 'success', 'message' => 'Message ajouté']);
    exit;
}

// Si aucune des routes n'a été trouvée, répondre avec un code 404 (non trouvé)
http_response_code(404);
echo json_encode(['status' => 'error', 'message' => 'Route non trouvée']);
