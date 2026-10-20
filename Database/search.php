#!/usr/bin/php
<?php
require_once('../path.inc');
require_once('../get_host_info.inc');
require_once('../rabbitMQLib.inc');

function handleRecipeRequest($request) {
    echo "--- [DEBUG] Incoming Request --- \n";
    var_dump($request);

    $sql = new mysqli("localhost", "dbuser", "dbpass", "users");
    
    if ($sql->connect_error) {
        return ['status' => 'error', 'message' => 'Database connection failed: ' . $sql->connect_error];
    }

    $sessionKey = $request['session_key'] ?? null;
    $userId = null;

    if ($sessionKey) {
        $stmt = $sql->prepare("SELECT id FROM logininfo WHERE sessionkey = ?");
        $stmt->execute([$sessionKey]);
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $userId = $row['id'];
        }
    }

    echo "--- [DEBUG] Resolved User ID: --- \n";
    var_dump($userId);

    if (!$userId) {
        return ['status' => 'error', 'message' => 'Invalid session or user not found'];
    }

    // Fetch the user's diets as a raw array (empty if they aren't on a diet)
    $stmt = $sql->prepare("SELECT diet FROM userdiet WHERE userid = ?");
    $stmt->execute([$userId]);
    $result = $stmt->get_result();
    
    $diets = [];
    while ($row = $result->fetch_assoc()) {
        $diets[] = $row['diet'];
    }

    echo "--- [DEBUG] Fetched User Diets: --- \n";
    var_dump($diets);

    $searchQuery = '';

    switch ($request['type']) {
        case 'recipeSearch':
            // User typed a specific search query
            $searchQuery = $request['query'] ?? '';
            echo "--- [DEBUG] recipeSearch Query: --- \n";
            var_dump($searchQuery);
            break;

        case 'fridgeSearch':
            // User wants to search using items from their fridge
            $stmt = $sql->prepare("SELECT ingredient FROM userfridge WHERE userid = ?");
            $stmt->execute([$userId]);
            $result = $stmt->get_result();
            
            $fridgeItems = [];
            while ($row = $result->fetch_assoc()) {
                // Note: using 'ingredient' based on your SELECT column, 
                // adjusted from 'item_name' to match your query column.
                $fridgeItems[] = $row['ingredient']; 
            }
            
            echo "--- [DEBUG] Fetched Fridge Items: --- \n";
            var_dump($fridgeItems);

            $searchQuery = $fridgeItems; // Pass raw array for DMZ to handle
            break;

        default:
            return ['status' => 'error', 'message' => 'Unknown request type'];
    }

    // Forward the payload to the DMZ server via RabbitMQ client
    $dmzClient = new rabbitMQClient("../apiBroker.ini", "dmzServer");
    $dmzPayload = [
        'type' => 'recipeSearch',
        'ingredients' => $searchQuery,
        'diet' => $diets
    ];

    echo "--- [DEBUG] Outgoing DMZ Payload: --- \n";
    var_dump($dmzPayload);

    $dmzResponse = $dmzClient->send_request($dmzPayload);

    echo "--- [DEBUG] Incoming DMZ Response: --- \n";
    var_dump($dmzResponse);

    // Return results back to the frontend/client
    return [
        'status' => 'success',
        'recipes' => $dmzResponse['recipeSearch'] ?? []
    ];
}

echo "Database recipe server is running and listening for requests...\n";
$server = new rabbitMQServer("../apiBroker.ini", "dataServer");
$server->process_requests('handleRecipeRequest');
?>
