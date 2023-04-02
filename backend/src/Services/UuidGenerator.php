<?php

namespace AiMuseum\Services;

class UuidGenerator
{
    public function generate(): string
    {
        // Generate 16 bytes of random data
        $data = random_bytes(16);

        // Set the version bits to 0100 (UUID v4)
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);

        // Set the variant bits to 10
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);

        // Format the UUID string
        return sprintf(
            '%s-%s-%s-%s-%s',
            bin2hex(substr($data, 0, 4)),
            bin2hex(substr($data, 4, 2)),
            bin2hex(substr($data, 6, 2)),
            bin2hex(substr($data, 8, 2)),
            bin2hex(substr($data, 10, 6))
        );
    }
}
