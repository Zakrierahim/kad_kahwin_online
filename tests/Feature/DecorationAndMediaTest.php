<?php

namespace Tests\Feature;

use App\Models\Wedding;
use App\Support\InvitationTheme;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\Concerns\InteractsWithWeddings;
use Tests\TestCase;

class DecorationAndMediaTest extends TestCase
{
    use InteractsWithWeddings;
    use RefreshDatabase;

    public function test_theme_settings_persist(): void
    {
        $this->actingAdmin();

        $this->post(route('admin.weddings.store'), $this->weddingPayload([
            'slug' => 'tema-merah-jambu',
            'theme' => 'dusty_pink',
            'primary_color' => '#A85A6A',
            'secondary_color' => '#F3D5D8',
            'background_color' => '#FFF6F7',
            'font' => 'serif',
            'floral_style' => 'jasmine',
        ]))->assertRedirect();

        $wedding = Wedding::query()->where('slug', 'tema-merah-jambu')->firstOrFail();

        $this->assertSame('dusty_pink', $wedding->decoration_settings['theme']);
        $this->assertSame('#A85A6A', $wedding->decoration_settings['primary_color']);
    }

    public function test_different_weddings_display_different_themes(): void
    {
        $pink = Wedding::factory()->create([
            'groom_full_name' => 'Amir bin Ismail',
            'decoration_settings' => [
                'theme' => 'dusty_pink',
                'primary_color' => '#A85A6A',
                'secondary_color' => '#F3D5D8',
                'background_color' => '#FFF6F7',
                'font' => 'serif',
                'floral_style' => 'jasmine',
            ],
        ]);
        $green = Wedding::factory()->create([
            'groom_full_name' => 'Hafiz bin Zulkifli',
            'decoration_settings' => [
                'theme' => 'sage_green',
                'primary_color' => '#6E8B74',
                'secondary_color' => '#D7E4D8',
                'background_color' => '#F6F8F4',
                'font' => 'sans',
                'floral_style' => 'jasmine',
            ],
        ]);

        $this->get(route('invitation.show', $pink->slug))
            ->assertOk()
            ->assertSee('#A85A6A', false)
            ->assertSee('data-theme="dusty_pink"', false)
            ->assertDontSee('Hafiz bin Zulkifli');

        $this->get(route('invitation.show', $green->slug))
            ->assertOk()
            ->assertSee('#6E8B74', false)
            ->assertSee('data-theme="sage_green"', false)
            ->assertDontSee('Amir bin Ismail');
    }

    public function test_default_theme_is_used_when_settings_are_missing(): void
    {
        $wedding = Wedding::factory()->create(['decoration_settings' => null]);
        $theme = InvitationTheme::resolve(null);

        $this->get(route('invitation.show', $wedding->slug))
            ->assertOk()
            ->assertSee($theme['primary_color'], false)
            ->assertSee('data-theme="elegant_cream"', false);
    }

    public function test_valid_cover_image_is_stored_and_invalid_files_are_rejected(): void
    {
        Storage::fake('public');
        $this->actingAdmin();

        $this->post(route('admin.weddings.store'), $this->weddingPayload([
            'slug' => 'dengan-gambar',
            'cover_image' => UploadedFile::fake()->image('cover.jpg', 800, 1000),
        ]))->assertRedirect();

        $wedding = Wedding::query()->where('slug', 'dengan-gambar')->firstOrFail();
        Storage::disk('public')->assertExists($wedding->cover_image_path);

        $this->get(route('invitation.show', $wedding->slug))
            ->assertOk()
            ->assertSee('property="og:image"', false);

        $this->post(route('admin.weddings.store'), $this->weddingPayload([
            'slug' => 'fail-rosak',
            'cover_image' => UploadedFile::fake()->create('shell.php', 12, 'application/x-php'),
        ]))->assertSessionHasErrors('cover_image');

        $this->assertDatabaseMissing('weddings', ['slug' => 'fail-rosak']);
    }

    public function test_valid_music_upload_is_used_and_missing_media_is_safe(): void
    {
        Storage::fake('public');
        $this->actingAdmin();

        $this->post(route('admin.weddings.store'), $this->weddingPayload([
            'slug' => 'dengan-muzik',
            'music_enabled' => '1',
            'music_file' => UploadedFile::fake()->create('lagu.mp3', 120, 'audio/mpeg'),
        ]))->assertRedirect();

        $wedding = Wedding::query()->where('slug', 'dengan-muzik')->firstOrFail();
        Storage::disk('public')->assertExists($wedding->bg_music_path);

        $this->get(route('invitation.show', $wedding->slug))
            ->assertOk()
            ->assertSee('data-music', false);

        $missing = Wedding::factory()->create([
            'cover_image_path' => 'weddings/covers/hilang.jpg',
            'bg_music_path' => 'weddings/music/hilang.mp3',
            'decoration_settings' => [
                'theme' => 'elegant_cream',
                'primary_color' => '#B08D57',
                'secondary_color' => '#EADCC8',
                'background_color' => '#FFF9F0',
                'font' => 'serif',
                'floral_style' => 'classic',
                'music_enabled' => true,
            ],
        ]);

        $this->get(route('invitation.show', $missing->slug))
            ->assertOk()
            ->assertDontSee('og:image', false)
            ->assertDontSee('data-music', false);
    }

    public function test_youtube_music_uses_the_youtube_player(): void
    {
        $wedding = Wedding::factory()->create([
            'bg_music_url' => 'https://www.youtube.com/watch?v=sBiSqt8k3U4&list=RDsBiSqt8k3U4&start_radio=1',
            'decoration_settings' => InvitationTheme::resolve([
                'theme' => 'elegant_cream',
                'music_enabled' => true,
            ]),
        ]);

        $this->get(route('invitation.show', $wedding->slug))
            ->assertOk()
            ->assertSee('data-youtube="sBiSqt8k3U4"', false)
            ->assertDontSee('<audio', false)
            ->assertDontSee('youtube.com/watch', false);
    }
}
