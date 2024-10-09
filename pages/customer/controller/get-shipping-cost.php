<?php
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

$courier = $data['courier'] ?? '';
$origin = $data['origin'] ?? '';
$destination = $data['destination'] ?? '';
$weight = $data['weight'] ?? '';

if ($courier && $origin && $destination && $weight) {
    $result = getShippingCost($origin, $destination, $weight, $courier);
    if ($result && isset($result['rajaongkir']['results'][0]['costs'][0]['cost'][0]['value'])) {
        $cost = $result['rajaongkir']['results'][0]['costs'][0]['cost'][0]['value'];
        echo json_encode(['success' => true, 'cost' => $cost]);
    } else {
        echo json_encode(['success' => false]);
    }
} else {
    echo json_encode(['success' => false]);
}

function getShippingCost($origin, $destination, $weight, $courier)
{
    $apiKey = 'f9a8570f2fff930d0455aa28d8c03173';
    $url = "https://api.rajaongkir.com/starter/cost";
    $postData = http_build_query([
        'origin' => $origin,
        'destination' => $destination,
        'weight' => $weight,
        'courier' => $courier
    ]);

    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $postData,
        CURLOPT_HTTPHEADER => [
            "key: $apiKey",
            "Content-Type: application/x-www-form-urlencoded"
        ]
    ]);
    $response = curl_exec($curl);
    curl_close($curl);

    return json_decode($response, true);
}
