<?php

namespace Tests\Feature;

use App\Models\Wedding;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class InvitationTest extends TestCase
{
    use RefreshDatabase;

    public function test_active_wedding_page_loads_the_correct_invitation(): void
    {
        $ali = Wedding::factory()->create([
            'slug' => 'ali-dan-fatimah',
            'groom_short_name' => 'Ali',
            'bride_short_name' => 'Fatimah',
            'groom_full_name' => 'Ali bin Ahmad',
            'bride_full_name' => 'Fatimah binti Hassan',
            'date' => '2026-11-21',
            'time_start' => '11:00:00',
            'location_name' => 'Dewan Seri Melati',
        ]);
        $amir = Wedding::factory()->create([
            'slug' => 'amir-aisyah',
            'groom_full_name' => 'Amir bin Ismail',
            'bride_full_name' => 'Aisyah binti Rahman',
            'location_name' => 'Dewan Perdana',
        ]);

        $response = $this->get(route('invitation.show', $ali->slug));

        $response->assertOk();
        $response->assertSee('Walimatulurus Ali &amp; Fatimah', false);
        $response->assertSee('Bismillahirrahmanirrahim');
        $response->assertSee('Assalamualaikum Warahmatullahi Wabarakatuh');
        $response->assertSee('Undangan Majlis Kesyukuran Perkahwinan');
        $response->assertSee('Ali bin Ahmad');
        $response->assertSee('dengan');
        $response->assertSee('Fatimah binti Hassan');
        $response->assertSee('Sabtu, 21 November 2026');
        $response->assertSee('Dewan Seri Melati');
        $response->assertSee('property="og:title"', false);
        $response->assertSee('property="og:description"', false);
        $response->assertSee('property="og:type"', false);
        $response->assertSee('property="og:url"', false);
        $response->assertSee(route('invitation.show', $ali->slug), false);
        $response->assertSee('2026-11-21T11:00:00+08:00', false);
        $response->assertSee('https://wa.me/60123456789', false);
        $response->assertSee('Buka Google Maps');
        $response->assertSee('Buka Waze');
        $response->assertDontSee('Amir bin Ismail');
        $response->assertDontSee('Dewan Perdana');
        $response->assertDontSee('og:image', false);

        $this->get(route('invitation.show', $amir->slug))
            ->assertOk()
            ->assertSee('Amir bin Ismail')
            ->assertDontSee('Ali bin Ahmad');
    }

    public function test_invalid_slug_returns_not_found(): void
    {
        config(['app.debug' => false]);

        $this->get('/tidak-wujud-langsung')
            ->assertNotFound()
            ->assertSee('tidak dijumpai');
    }

    public function test_inactive_invitation_cannot_be_accessed(): void
    {
        $wedding = Wedding::factory()->inactive()->create([
            'slug' => 'danial-dan-nurul',
            'location_name' => 'Dewan Rahsia',
        ]);

        $this->get(route('invitation.show', $wedding->slug))
            ->assertNotFound()
            ->assertSee('tidak tersedia')
            ->assertDontSee('Dewan Rahsia');
    }
}
