<?php

namespace Tests\Unit;

use App\Models\Videotron;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class VideotronAspectRatioTest extends TestCase
{
    /**
     * @return array<string, array{0: string, 1: string, 2: string, 3: ?string, 4: string}>
     */
    public static function resolutionProvider(): array
    {
        return [
            'campus 960x480' => ['960x480', 'landscape', '960 × 480', '2:1', 'Landscape (2:1)'],
            'campus 2352x672' => ['2352x672', 'landscape', '2352 × 672', '7:2', 'Landscape (7:2)'],
            'campus 1200x720' => ['1200x720', 'landscape', '1200 × 720', '5:3', 'Landscape (5:3)'],
            'campus 1152x768' => ['1152x768', 'landscape', '1152 × 768', '3:2', 'Landscape (3:2)'],
            'campus 1680x960' => ['1680x960', 'landscape', '1680 × 960', '7:4', 'Landscape (7:4)'],
            'standard full hd landscape' => ['1920x1080', 'landscape', '1920 × 1080', '16:9', 'Landscape (16:9)'],
            'standard full hd portrait' => ['1080x1920', 'portrait', '1080 × 1920', '9:16', 'Portrait (9:16)'],
            'spaced uppercase separator' => ['1920 X 1080', 'landscape', '1920 × 1080', '16:9', 'Landscape (16:9)'],
            'multiplication sign separator' => ['960 × 480', 'landscape', '960 × 480', '2:1', 'Landscape (2:1)'],
            'non-reducible landscape decimal fallback' => ['1366x768', 'landscape', '1366 × 768', '1.78:1', 'Landscape (1.78:1)'],
            'non-reducible portrait decimal fallback' => ['768x1366', 'portrait', '768 × 1366', '1:1.78', 'Portrait (1:1.78)'],
            'non-numeric fallback' => ['Full HD', 'landscape', 'Full HD', null, 'Landscape'],
        ];
    }

    #[DataProvider('resolutionProvider')]
    public function test_it_computes_formatted_resolution_and_aspect_ratio_accurately(
        string $resolution,
        string $orientation,
        string $expectedFormattedResolution,
        ?string $expectedRatio,
        string $expectedOrientationWithRatio
    ): void {
        $videotron = new Videotron([
            'resolution' => $resolution,
            'orientation' => $orientation,
        ]);

        $this->assertSame($expectedFormattedResolution, $videotron->formattedResolution());
        $this->assertSame($expectedRatio, $videotron->aspectRatioLabel());
        $this->assertSame($expectedOrientationWithRatio, $videotron->orientationWithRatioLabel());
    }
}
