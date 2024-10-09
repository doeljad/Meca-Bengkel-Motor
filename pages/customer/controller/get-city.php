<?php
$apiKey = 'f9a8570f2fff930d0455aa28d8c03173'; // API key
$url = 'https://api.rajaongkir.com/starter/city';

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "key: $apiKey"
]);

$response = curl_exec($ch);
curl_close($ch);

$data = json_decode($response, true);

$solorayaCities = ['Surakarta', 'Sukoharjo', 'Karanganyar', 'Boyolali', 'Klaten', 'Sragen', 'Wonogiri', 'Yogyakarta'];
$filteredCities = [];

if (isset($data['rajaongkir']['results'])) {
    foreach ($data['rajaongkir']['results'] as $city) {
        if (in_array($city['city_name'], $solorayaCities)) {
            $filteredCities[] = $city;
        }
    }
}

header('Content-Type: application/json');

if (!empty($filteredCities)) {
    echo json_encode(['rajaongkir' => ['results' => $filteredCities]], JSON_PRETTY_PRINT);
} else {
    echo json_encode(['rajaongkir' => ['results' => []]], JSON_PRETTY_PRINT); // Output empty array if no cities match
}
