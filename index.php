<?php
declare(strict_types=1);

$command = 'nvidia-smi --id=0 --query --display=UTILIZATION,MEMORY,POWER,CLOCK';

\exec($command, $result, $resultCode);

$content = \array_reduce(
    $result,
    static function ($carry, $item) {
        return $carry . $item . PHP_EOL;
    },
);

if (0 !== $resultCode) {
    echo \str_replace(PHP_EOL, '<br>', $content);
    exit(1);
}

function getValue(string $pattern, string $content): ?string
{
    \preg_match_all($pattern, $content, $output_array);

    if (0 === \count($output_array[1])) {
        return null;
    }

    return $output_array[1][0];
}

$values = [
    'version' => [
        'driver' => getValue('/Driver Version[^:]+: ([\d].+)\n/', $content),
        'cuda' => getValue('/CUDA Version[^:]+: ([\d].+)\n/', $content),
    ],
    'utilization' => [
        'gpu' => (float) getValue('/Utilization[^.]*Gpu[^:]*: (\d+)[^%]/', $content),
        'memory' => (float) getValue('/Utilization[^.]*Memory[^:]*: (\d+)[^%]/', $content),
        'encoder' => (float) getValue('/Utilization[^.]*Encoder[^:]*: (\d+)[^%]/', $content),
        'decoder' => (float) getValue('/Utilization[^.]*Decoder[^:]*: (\d+)[^%]/', $content),
    ],
    'utilization_samples' => [
        'gpu' => (float) getValue('/GPU Utilization Samples[^A]*[^:]*: (\d+) %/', $content),
        'memory' => (float) getValue('/Memory Utilization Samples[^Avg]*Avg[^:]*: (\d+) %/', $content),
        'encoder' => (float) getValue('/ENC Utilization Samples[^Avg]*Avg[^:]*: (\d+) %/', $content),
        'decoder' => (float) getValue('/DEC Utilization Samples[^Avg]*Avg[^:]*: (\d+) %/', $content),
    ],
    'power' => [
        'actual' => (float) getValue('/Power Samples[^Avg]*Avg[^:]*: ([\d.]+) W/', $content),
        'limit' => (float) getValue('/Power Readings[^L]+Limit[^:]*: ([\d.]+) W/', $content),
    ],
    'clock_samples' => [
        'sm' => (float) getValue('/SM Clock Samples[^A]+Avg[^:]*: (\d+) MHz/', $content),
        'memory' => (float) getValue('/Memory Clock Samples[^A]+Avg[^:]*: (\d+) MHz/', $content),
    ],
];

echo \json_encode($values, JSON_PRETTY_PRINT);
