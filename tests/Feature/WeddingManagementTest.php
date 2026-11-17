<?php

namespace Tests\Feature;

use App\Models\Rsvp;
use App\Models\Wedding;
use App\Models\Wish;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\InteractsWithWeddings;
use Tests\TestCase;

class WeddingManagementTest extends TestCase
{
    use InteractsWithWeddings;
    use RefreshDatabase;

    public function test_admin_can_create_a_wedding(): void
    {
        $this->actingAdmin();

        $this->post(route('admin.weddings.store'), $this->weddingPayload())
            ->assertRedirect();

        $this->assertDatabaseHas('weddings', [
            'slug' => 'ali-dan-fatimah',
            'groom_full_name' => 'Ali bin Ahmad',
            'bride_full_name' => 'Fatimah binti Hassan',
            'is_active' => true,
        ]);
    }

    public function test_slug_is_generated_when_left_blank(): void
    {
        $this->actingAdmin();
        $payload = $this->weddingPayload();
        unset($payload['slug']);

        $this->post(route('admin.weddings.store'), $payload)->assertRedirect();

        $this->assertDatabaseHas('weddings', ['slug' => 'ali-dan-fatimah']);
    }

    public function test_hashtag_is_saved_and_shown_on_the_invitation(): void
    {
        $this->actingAdmin();

        $this->post(route('admin.weddings.store'), $this->weddingPayload([
            'slug' => 'iman-dan-syifa',
            'hashtag' => 'imandalamsyifa',
        ]))->assertRedirect();

        $this->assertDatabaseHas('weddings', [
            'slug' => 'iman-dan-syifa',
            'hashtag' => '#imandalamsyifa',
        ]);

        $this->get(route('invitation.show', 'iman-dan-syifa'))
            ->assertOk()
            ->assertSee('#imandalamsyifa');

        $this->post(route('admin.weddings.store'), $this->weddingPayload([
            'slug' => 'hashtag-rosak',
            'hashtag' => 'iman dalam syifa!',
        ]))->assertSessionHasErrors('hashtag');
    }

    public function test_admin_can_update_a_wedding(): void
    {
        $this->actingAdmin();
        $wedding = Wedding::factory()->create(['slug' => 'asal-slug']);

        $this->put(route('admin.weddings.update', $wedding), $this->weddingPayload([
            'slug' => 'asal-slug',
            'location_name' => 'Dewan Perdana',
        ]))->assertRedirect(route('admin.weddings.edit', $wedding));

        $this->assertSame('Dewan Perdana', $wedding->fresh()->location_name);
    }

    public function test_duplicate_slug_is_rejected(): void
    {
        $this->actingAdmin();
        Wedding::factory()->create(['slug' => 'ali-dan-fatimah']);

        $this->post(route('admin.weddings.store'), $this->weddingPayload())
            ->assertSessionHasErrors('slug');
    }

    public function test_reserved_and_unsafe_urls_are_rejected(): void
    {
        $this->actingAdmin();

        $this->post(route('admin.weddings.store'), $this->weddingPayload([
            'slug' => 'admin',
            'maps_url' => 'javascript:alert(1)',
        ]))->assertSessionHasErrors(['slug', 'maps_url']);
    }

    public function test_admin_can_deactivate_an_invitation(): void
    {
        $this->actingAdmin();
        $wedding = Wedding::factory()->create(['is_active' => true]);

        $this->patch(route('admin.weddings.status', $wedding), ['is_active' => 0])
            ->assertRedirect();

        $this->assertFalse($wedding->fresh()->is_active);
        $this->get(route('invitation.show', $wedding->slug))
            ->assertNotFound()
            ->assertSee('tidak tersedia');
        $this->get(route('admin.weddings.preview', $wedding))
            ->assertOk()
            ->assertSee($wedding->groom_full_name);
    }

    public function test_admin_can_delete_a_wedding_and_related_records(): void
    {
        $this->actingAdmin();
        $wedding = Wedding::factory()->create();
        $wedding->rsvps()->create([
            'guest_name' => 'Siti',
            'phone_number' => '0123456789',
            'status' => Rsvp::HADIR,
            'total_pax' => 2,
        ]);
        $wedding->wishes()->create([
            'guest_name' => 'Siti',
            'message' => 'Tahniah.',
        ]);

        $this->delete(route('admin.weddings.destroy', $wedding))
            ->assertRedirect(route('admin.weddings.index'));

        $this->assertDatabaseMissing('weddings', ['id' => $wedding->id]);
        $this->assertDatabaseMissing('rsvps', ['wedding_id' => $wedding->id]);
        $this->assertDatabaseMissing('wishes', ['wedding_id' => $wedding->id]);
    }

    public function test_dashboard_uses_database_counts(): void
    {
        $this->actingAdmin();
        $wedding = Wedding::factory()->create(['is_active' => true, 'date' => now()->addWeek()->toDateString()]);
        Wedding::factory()->inactive()->create();
        $wedding->rsvps()->create([
            'guest_name' => 'Siti',
            'phone_number' => '0123456789',
            'status' => Rsvp::HADIR,
            'total_pax' => 3,
        ]);
        $wedding->wishes()->create([
            'guest_name' => 'Siti',
            'message' => 'Tahniah.',
        ]);

        $this->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Jumlah Keseluruhan Pasangan')
            ->assertSee('>2<', false)
            ->assertSee($wedding->coupleLabel());
    }
}
