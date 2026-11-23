<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$endpoints = [
    ['method' => 'GET', 'uri' => '/', 'label' => 'Landing Page (Publik)'],
    ['method' => 'GET', 'uri' => '/login', 'label' => 'Halaman Login Akun'],
];

echo "BENCHMARK HASIL PENGUJIAN PERFORMA RESPON WAKTU (LATENCY)\n";
echo "=========================================================\n";

foreach ($endpoints as $ep) {
    $latencies = [];
    $statusCode = 200;

    for ($i = 0; $i < 5; $i++) {
        $request = Illuminate\Http\Request::create($ep['uri'], $ep['method']);
        $start = microtime(true);
        $response = $kernel->handle($request);
        $duration = (microtime(true) - $start) * 1000;
        $latencies[] = $duration;
        $statusCode = $response->getStatusCode();
        $kernel->terminate($request, $response);
    }

    $avg = round(array_sum($latencies) / count($latencies), 2);
    $min = round(min($latencies), 2);
    $max = round(max($latencies), 2);

    printf("%-25s | HTTP %d | Rata-rata: %6.2f ms | Min: %6.2f ms | Max: %6.2f ms\n", 
        $ep['label'], $statusCode, $avg, $min, $max
    );
}

echo "=========================================================\n";
