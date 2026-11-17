<?php

namespace Tests\Unit;

use App\Support\InvitationTheme;
use App\Support\MalayFormatter;
use App\Support\MalaysianPhone;
use App\Support\YouTube;
use Illuminate\Support\Carbon;
use PHPUnit\Framework\TestCase;

class InvitationSupportTest extends TestCase
{
    public function test_malaysian_phone_numbers_are_normalised(): void
    {
        $this->assertSame('60123456789', MalaysianPhone::toWaMe('0123456789'));
        $this->assertSame('60123456789', MalaysianPhone::toWaMe('+60 12-345 6789'));
        $this->assertSame('60123456789', MalaysianPhone::toWaMe('60123456789'));
        $this->assertNull(MalaysianPhone::toWaMe('abc'));
        $this->assertNull(MalaysianPhone::toWaMe(''));
    }

    public function test_malay_date_for_the_sample_saturday(): void
    {
        $this->assertSame(
            'Sabtu, 21 November 2026',
            MalayFormatter::date(Carbon::parse('2026-11-21')),
        );
    }

    public function test_theme_resolver_falls_back_safely(): void
    {
        $theme = InvitationTheme::resolve(null);

        $this->assertSame('elegant_cream', $theme['theme']);
        $this->assertSame('#B08D57', $theme['primary_color']);

        $unsafe = InvitationTheme::resolve([
            'theme' => 'tidak_wujud',
            'primary_color' => 'red;background:url(javascript:alert(1))',
            'font' => 'comic-sans',
            'floral_style' => '<script>',
        ]);

        $this->assertSame('elegant_cream', $unsafe['theme']);
        $this->assertSame('#B08D57', $unsafe['primary_color']);
        $this->assertSame('serif', $unsafe['font']);
        $this->assertSame('classic', $unsafe['floral_style']);
    }

    public function test_youtube_links_resolve_to_a_video_id(): void
    {
        $this->assertSame(
            'sBiSqt8k3U4',
            YouTube::videoId('https://www.youtube.com/watch?v=sBiSqt8k3U4&list=RDsBiSqt8k3U4&start_radio=1'),
        );
        $this->assertSame('sBiSqt8k3U4', YouTube::videoId('https://youtu.be/sBiSqt8k3U4'));
        $this->assertSame('sBiSqt8k3U4', YouTube::videoId('https://www.youtube.com/shorts/sBiSqt8k3U4'));
        $this->assertNull(YouTube::videoId('https://example.com/watch?v=sBiSqt8k3U4'));
        $this->assertNull(YouTube::videoId('javascript:alert(1)'));
    }
}
